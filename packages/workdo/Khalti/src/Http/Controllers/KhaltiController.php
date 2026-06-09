<?php

namespace Workdo\Khalti\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Workdo\Khalti\Events\KhaltiPaymentStatus;
use Workdo\Khalti\Services\KhaltiPaymentService;
use Workdo\Bookings\Models\BookingAppointment;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingPackage;
use Workdo\Bookings\Events\BookingAppointmentPayments;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyBookingReceipt;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;
use Workdo\BeautySpaManagement\Events\BeautyBookingPayments;
use Workdo\LMS\Models\LMSCart;
use Workdo\LMS\Models\LMSCoupon;
use Workdo\LMS\Models\LMSOrder;
use Workdo\LMS\Models\LMSOrderItem;
use Workdo\LMS\Events\LMSOrderPayments;
use Workdo\LaundryManagement\Models\LaundryRequest;
use Workdo\LaundryManagement\Models\LaundryInvoice;
use Workdo\LaundryManagement\Models\LaundryPayment;
use Workdo\LaundryManagement\Events\LaundryBookingPayments;
use Workdo\ParkingManagement\Models\ParkingBooking;
use Workdo\ParkingManagement\Events\ParkingBookingPayments;
use Workdo\EventsManagement\Models\Event;
use Workdo\EventsManagement\Models\EventBooking;
use Workdo\EventsManagement\Models\EventBookingPayment;
use Workdo\EventsManagement\Events\EventBookingPayments;
use Workdo\Holidayz\Models\HolidayzCart;
use Workdo\Holidayz\Models\HolidayzRoomBooking;
use Workdo\Holidayz\Models\HolidayzRoomBookingItem;
use Workdo\Holidayz\Models\HolidayzCoupon;
use Workdo\Holidayz\Models\HolidayzCouponUsage;
use Workdo\Holidayz\Events\HolidayzBookingPayments;
use Workdo\Holidayz\Helpers\HolidayzAvailabilityHelper;

class KhaltiController extends Controller
{
    // Plan Payments
    public function planPayWithKhalti(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        $user = User::find($request->user_id);
        $admin_settings = getAdminAllSetting();
        $admin_currancy = !empty($admin_settings['defaultCurrency']) ? $admin_settings['defaultCurrency'] : '';

        $user_counter = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
        $user_module = !empty($request->user_module_input) ? $request->user_module_input : '';
        $storage_limit = !empty($request->storage_limit_input) ? $request->storage_limit_input : 0;
        $duration = !empty($request->time_period) ? $request->time_period : 'Month';
        $user_module_price = 0;

        if (!empty($user_module) && $plan->custom_plan == 1) {
            $user_module_array = explode(',', $user_module);
            foreach ($user_module_array as $key => $value) {
                $temp = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                $user_module_price = $user_module_price + $temp;
            }
        }

        $user_price = 0;
        if ($user_counter > 0) {
            $temp = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
            $user_price = $user_counter * $temp;
        }

        $storage_price = 0;
        if ($storage_limit > 0 && $plan->custom_plan == 1) {
            $temp = ($duration == 'Year') ? $plan->price_per_storage_yearly : $plan->price_per_storage_monthly;
            $storage_price = $storage_limit * $temp;
        }

        $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
        $counter = [
            'user_counter' => $user_counter,
            'storage_limit' => $storage_limit,
        ];

        $orderID = strtoupper(substr(uniqid(), -12));

        if ($plan) {
            $plan->discounted_price = false;
            $price = $plan_price + $user_module_price + $user_price + $storage_price;

            if ($request->coupon_code) {
                $validation = applyCouponDiscount($request->coupon_code, $price, auth()->id());
                if ($validation['valid']) {
                    $price = $validation['final_amount'];
                }
            }

            if ($price <= 0) {
                $assignPlan = assignPlan($plan->id, $duration, $user_module, $counter, $request->user_id);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again.'));
                }
            }

            try {
                $khaltiService = new KhaltiPaymentService(
                    admin_setting('khalti_secret_key'),
                    admin_setting('khalti_mode') === 'sandbox',
                    $admin_currancy
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.plan.status',),
                    'amount' => $price,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => $plan->name ?? 'Basic Package',
                    'session' => [
                        'plan_id' => $plan->id,
                        'user_module' => $user_module,
                        'duration' => $duration,
                        'user_counter' => $counter['user_counter'],
                        'storage_limit' => $counter['storage_limit'],
                        'coupon_code' => $request->coupon_code,
                        'user_id' => $user->id,
                    ],
                ]);

                if ($response['success']) {

                    $order = new Order();
                    $order->order_id = $orderID;
                    $order->name = $user->name ?? '';
                    $order->email = $user->email ?? '';
                    $order->card_number = null;
                    $order->card_exp_month = null;
                    $order->card_exp_year = null;
                    $order->plan_name = !empty($plan->name) ? $plan->name : 'Basic Package';
                    $order->plan_id = $plan->id;
                    $order->price = !empty($price) ? $price : 0;
                    $order->currency = $admin_currancy;
                    $order->txn_id = $response['data']['pidx'] ?? null;
                    $order->payment_type = 'Khalti';
                    $order->payment_status = 'pending';
                    $order->created_by = $user->id;
                    $order->save();

                    return redirect()->away($response['data']['payment_url']);
                }

                throw new \Exception($response['data']['error'] ?? __('Khalti payment initiation failed'));
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        }

        return redirect()->route('plans.index')->with('error', __('The Plan has been deleted.'));
    }

    public function planGetKhaltiStatus(Request $request)
    {
        try {
            $pidx = $request->get('pidx');
            if ($request->status === 'Completed' && $pidx) {

                $khaltiService = new KhaltiPaymentService(
                    admin_setting('khalti_secret_key'),
                    admin_setting('khalti_mode') === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $order = Order::where('txn_id', $pidx)->first();

                    if ($order) {
                        $order->payment_status = 'succeeded';
                        $order->save();
                    }
                    $planData = Session::get($pidx);
                    $plan = Plan::find($planData['plan_id']);

                    $assignPlan = assignPlan(
                        $plan->id,
                        $planData['duration'],
                        $planData['user_module'],
                        [
                            'user_counter' => $planData['user_counter'] ?? 0,
                            'storage_counter' => $planData['storage_limit'] ?? 0,
                        ],
                        $planData['user_id']
                    );

                    if ($assignPlan['is_success']) {
                        if ($planData['coupon_code']) {
                            $coupon = Coupon::where('code', $planData['coupon_code'])->first();
                            if ($coupon) {
                                recordCouponUsage($coupon->id, $planData['user_id'], $order->order_id);
                            }
                        }

                        try {
                            KhaltiPaymentStatus::dispatch($plan, 'Subscription', $order);
                        } catch (\Exception $e) {
                        }

                        return redirect()->route('plans.index')->with('success', __('Plan activated successfully!'));
                    }
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again.'));
                }
                return redirect()->route('plans.index')->with('error', __('Your Payment has failed!'));
            }
            return redirect()->route('plans.index')->with('error', __('Invalid payment data.'));
        } catch (\Exception $exception) {
            return redirect()->route('plans.index')->with('error', $exception->getMessage());
        }
    }

    // Booking Payments
    public function bookingPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if (!$user) {
                throw new \Exception(__('User not found.'));
            }
            $package = BookingPackage::find($request->selectedPackageItem);
            if (!$package) {
                throw new \Exception(__('Package not found.'));
            }

            $price = $package->price ?? 0;
            if ($price <= 0) {
                return redirect()->route('booking.home', ['userSlug' => $userSlug])->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(substr(uniqid(), -12));
            $khaltiService = new KhaltiPaymentService(
                company_setting('khalti_secret_key', $package->created_by) ?? '',
                company_setting('khalti_mode', $package->created_by) === 'sandbox',
                company_setting('defaultCurrency', $package->created_by) ?? ''
            );

            $response = $khaltiService->initiatePayment([
                'return_url' => route('khalti.booking.payment.status', ['userSlug' => $userSlug]),
                'amount' => $price,
                'purchase_order_id' => $orderID,
                'purchase_order_name' => $package->name ?? 'Booking Service',
                'session' => [
                    'selectedDate' => $request->selectedDate,
                    'selectedStaff' => $request->selectedStaff,
                    'selectedItem' => $request->selectedItem,
                    'selectedPackageItem' => $request->selectedPackageItem,
                    'selectedTimeSlot' => [
                        'start_time' => $request->input('selectedTimeSlot.start_time'),
                        'end_time' => $request->input('selectedTimeSlot.end_time'),
                        'label' => $request->input('selectedTimeSlot.label')
                    ],
                    'formData' => [
                        'firstName' => $request->input('formData.firstName'),
                        'lastName' => $request->input('formData.lastName'),
                        'email' => $request->input('formData.email'),
                        'phone' => $request->input('formData.phone'),
                        'description' => $request->input('formData.description'),
                        'paymentOption' => $request->input('formData.paymentOption')
                    ]
                ]
            ]);

            if ($response['success'] && isset($response['data']['payment_url'])) {
                return redirect()->away($response['data']['payment_url']);
            }

            return redirect()->route('booking.home', ['userSlug' => $userSlug])
                ->with('error', $response['data']['error'] ?? __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->route('booking.home', ['userSlug' => $userSlug])
                ->with('error', $e->getMessage());
        }
    }

    public function bookingGetKhaltiStatus(Request $request, $userSlug)
    {
        try {
            $pidx = $request->get('pidx');
            $bookingData = Session::get($pidx);

            if ($request->status === 'Completed' && $bookingData && $pidx) {
                $package = BookingPackage::find($bookingData['selectedPackageItem']);
                $userId = $package->created_by ?? null;

                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $timeSlot = $bookingData['selectedTimeSlot'];
                    $customer = BookingCustomer::where('email', $bookingData['formData']['email'])
                        ->where('created_by', $userId)->first();

                    if (!$customer) {
                        $customer = new BookingCustomer();
                        $customer->first_name = $bookingData['formData']['firstName'];
                        $customer->last_name = $bookingData['formData']['lastName'];
                        $customer->email = $bookingData['formData']['email'];
                        $customer->mobile_number = $bookingData['formData']['phone'];
                        $customer->description = $bookingData['formData']['description'] ?? null;
                        $customer->created_by = $userId;
                        $customer->creator_id = $userId;
                        $customer->save();
                    }

                    $currentYear = date('Y');
                    $lastAppointment = BookingAppointment::where('created_by', $userId)
                        ->where('appointment_number', 'like', 'APT-' . $currentYear . '-' . $userId . '-%')
                        ->orderBy('appointment_number', 'desc')->first();

                    $nextNumber = $lastAppointment ? ((int) substr($lastAppointment->appointment_number, -4)) + 1 : 1;
                    $appointmentNumber = 'APT-' . $currentYear . '-' . $userId . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    $appointment = BookingAppointment::create([
                        'appointment_number' => $appointmentNumber,
                        'date' => $bookingData['selectedDate'],
                        'item_id' => $bookingData['selectedItem'],
                        'package_id' => $bookingData['selectedPackageItem'],
                        'staff_id' => $bookingData['selectedStaff'],
                        'customer_id' => $customer->id,
                        'start_time' => $timeSlot['start_time'],
                        'end_time' => $timeSlot['end_time'],
                        'payment' => 'Khalti',
                        'status' => 'pending',
                        'payment_status' => 'paid',
                        'online_payment_id' => $pidx,
                        'created_by' => $userId,
                        'creator_id' => $userId,
                    ]);

                    try {
                        BookingAppointmentPayments::dispatch($appointment);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('booking.home', ['userSlug' => $userSlug])->with('success', __('The Booking has been created successfully.'));
                }
                return redirect()->route('booking.home', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
            }
            return redirect()->route('booking.home', ['userSlug' => $userSlug])->with('error', __('Something went wrong, Please try again.'));
        } catch (\Exception $exception) {
            return redirect()->route('booking.home', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
        }
    }

    // Beauty Spa Payments
    public function beautySpaPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {
                $userId = $user ? $user->id : null;
                $service = BeautyService::where('id', $request->service)->where('created_by', $userId)->firstOrFail();
                $offers = BeautyServiceOffer::where('beauty_service_id', $service->id)
                    ->where('start_date', '<=', $request->date)
                    ->where('end_date', '>=', $request->date)
                    ->where('created_by', $userId)
                    ->get();

                $price = ($offers->isNotEmpty() ? $offers->sum('offer_price') : $service->price) * $request->person;

                if ($price <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(substr(uniqid(), -12));
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox',
                    company_setting('defaultCurrency', $userId) ?? ''
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.beauty-spa.payment.status', ['userSlug' => $userSlug]),
                    'amount' => $price,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => $service->name ?? 'Beauty Service',
                    'session' => [
                        'service' => $request->service,
                        'date' => $request->date,
                        'time_slot' => $request->time_slot,
                        'person' => $request->person,
                        'gender' => $request->gender,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone_number' => $request->phone_number,
                        'reference' => $request->reference,
                        'additional_notes' => $request->additional_notes,
                    ]
                ]);

                if ($response['success'] && isset($response['data']['payment_url'])) {
                    return redirect()->away($response['data']['payment_url']);
                }
            }
            return redirect()->back()->with('error', __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function beautySpaGetKhaltiStatus(Request $request, $userSlug = null)
    {
        $pidx = $request->get('pidx');
        $bookingData = Session::get($pidx);
        $user = User::where('slug', $userSlug)->first();

        if (
            $request->status === 'Completed'
            && $bookingData && $user && $pidx
        ) {
            $userId = $user->id;
            $service = BeautyService::where('id', $bookingData['service'])->where('created_by', $userId)->first();

            try {
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $offers = BeautyServiceOffer::where('beauty_service_id', $service->id)
                        ->where('start_date', '<=', $bookingData['date'])
                        ->where('end_date', '>=', $bookingData['date'])
                        ->where('created_by', $userId)
                        ->get();

                    $price = ($offers->isNotEmpty() ? $offers->sum('offer_price') : $service->price);
                    $servicePrice = $price * $bookingData['person'];
                    $times = explode('-', $bookingData['time_slot']);

                    $booking = new BeautyBooking();
                    $booking->name = $bookingData['name'];
                    $booking->email = $bookingData['email'];
                    $booking->phone_number = $bookingData['phone_number'];
                    $booking->service = $bookingData['service'];
                    $booking->date = $bookingData['date'];
                    $booking->start_time = $times[0];
                    $booking->end_time = $times[1];
                    $booking->person = $bookingData['person'];
                    $booking->price = $servicePrice;
                    $booking->gender = $bookingData['gender'];
                    $booking->reference = $bookingData['reference'];
                    $booking->notes = $bookingData['additional_notes'];
                    $booking->payment_option = 'Khalti';
                    $booking->payment_status = 'paid';
                    $booking->stage_id = 0;
                    $booking->creator_id = null;
                    $booking->created_by = $userId;
                    $booking->save();

                    $beautyreceipt = new BeautyBookingReceipt();
                    $beautyreceipt->beauty_booking_id = $booking->id;
                    $beautyreceipt->name = $booking->name;
                    $beautyreceipt->service = $booking->service;
                    $beautyreceipt->number = $booking->number;
                    $beautyreceipt->gender = $booking->gender;
                    $beautyreceipt->start_time = $booking->start_time;
                    $beautyreceipt->end_time = $booking->end_time;
                    $beautyreceipt->price = $booking->price;
                    $beautyreceipt->payment_type = 'Khalti';
                    $beautyreceipt->created_by = $booking->created_by;
                    $beautyreceipt->save();

                    try {
                        BeautyBookingPayments::dispatch($booking);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('beauty-spa.booking-success', ['userSlug' => $userSlug, 'id' => encrypt($booking->id)])->with('success', __('The booking has been created successfully.'));
                }
                return redirect()->route('beauty-spa.booking', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
            } catch (\Exception $exception) {
                return redirect()->route('beauty-spa.booking', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
            }
        }
        return redirect()->route('beauty-spa.booking', ['userSlug' => $userSlug])->with('error', __('Something went wrong, Please try again.'));
    }

    // Laundry Payments
    public function laundryPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {
                $userId = $user->id;
                $price = floatval($request->total ?? 0);

                if ($price <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(substr(uniqid(), -12));
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox',
                    company_setting('defaultCurrency', $userId) ?? ''
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.laundry.payment.status', ['userSlug' => $userSlug]),
                    'amount' => $price,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => 'Laundry Service',
                    'session' => [
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'location' => $request->location,
                        'numberOfItems' => $request->cloth_no,
                        'pickupDate' => $request->pickup_date,
                        'pickupTime' => $request->pickupTime,
                        'deliveryDate' => $request->delivery_date,
                        'deliveryTime' => $request->deliveryTime,
                        'specialInstructions' => $request->instructions,
                        'services' => json_decode($request->services, true) ?? [],
                        'total' => $request->total
                    ]
                ]);

                if ($response['success'] && isset($response['data']['payment_url'])) {
                    return redirect()->away($response['data']['payment_url']);
                }
            }
            return redirect()->back()->with('error', __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function laundryGetKhaltiStatus(Request $request, $userSlug = null)
    {
        try {
            $pidx = $request->get('pidx');
            $bookingData = Session::get($pidx);
            $user = User::where('slug', $userSlug)->first();

            if (
                $request->status === 'Completed'
                && $bookingData && $user && $pidx
            ) {
                $userId = $user->id;
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $booking = new LaundryRequest();
                    $booking->name = $bookingData['name'];
                    $booking->email = $bookingData['email'];
                    $booking->phone = $bookingData['phone'];
                    $booking->address = $bookingData['address'];
                    $booking->location = $bookingData['location'];
                    $booking->cloth_no = $bookingData['numberOfItems'];
                    $booking->instructions = $bookingData['specialInstructions'];
                    $booking->pickup_date = $bookingData['pickupDate'] . ' ' . $bookingData['pickupTime'];
                    $booking->delivery_date = $bookingData['deliveryDate'] . ' ' . $bookingData['deliveryTime'];
                    $booking->services = $bookingData['services'];
                    $booking->payment_method = 'Khalti';
                    $booking->payment_id = $pidx;
                    $booking->status = 2;
                    $booking->total = $bookingData['total'];
                    $booking->created_by = $userId;
                    $booking->creator_id = $userId;
                    $booking->save();

                    $invoice = new LaundryInvoice();
                    $invoice->laundry_request_id = $booking->id;
                    $invoice->amount = $booking->total;
                    $invoice->status = 1;
                    $invoice->creator_id = $userId;
                    $invoice->created_by = $userId;
                    $invoice->save();

                    if ($invoice->status == 1) {
                        $payment = new LaundryPayment();
                        $payment->payment_amount = $invoice->amount;
                        $payment->invoice_id = $invoice->id;
                        $payment->payment_date = date('Y-m-d H:i:s');
                        $payment->status = 'cleared';
                        $payment->creator_id = $userId;
                        $payment->created_by = $userId;
                        $payment->save();
                    }

                    try {
                        LaundryBookingPayments::dispatch($booking);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('laundry-management.frontend.booking-success', ['userSlug' => $userSlug, 'requestId' => encrypt($booking->id)])->with('success', __('The laundry booking has been created successfully.'));
                }
                return redirect()->route('laundry-management.frontend.booking', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
            }
            return redirect()->route('laundry-management.frontend.booking', ['userSlug' => $userSlug])->with('error', __('Something went wrong, Please try again.'));
        } catch (\Exception $exception) {
            return redirect()->route('laundry-management.frontend.booking', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
        }
    }

    // Parking Payments
    public function parkingPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {
                $userId = $user->id;
                $price = floatval($request->total_amount);

                if ($price <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(substr(uniqid(), -12));
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox',
                    company_setting('defaultCurrency', $userId) ?? ''
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.parking.payment.status', ['userSlug' => $userSlug]),
                    'amount' => $price,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => 'Parking Slot - ' . $request->slot_name,
                    'session' => [
                        'slot_name' => $request->slot_name,
                        'slot_type_id' => $request->slot_type_id,
                        'date' => $request->date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'customer_name' => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone,
                        'vehicle_name' => $request->vehicle_name,
                        'vehicle_number' => $request->vehicle_number,
                        'payment_option' => $request->payment_option,
                        'total_amount' => $request->total_amount
                    ]
                ]);

                if ($response['success'] && isset($response['data']['payment_url'])) {
                    return redirect()->away($response['data']['payment_url']);
                }
            }
            return redirect()->back()->with('error', __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function parkingGetKhaltiStatus(Request $request, $userSlug = null)
    {
        try {
            $pidx = $request->get('pidx');
            $bookingData = Session::get($pidx);
            $user = User::where('slug', $userSlug)->first();

            if (
                $request->status === 'Completed'
                && $bookingData && $user && $pidx
            ) {
                $userId = $user->id;
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $userId) ?? '',
                    company_setting('khalti_mode', $userId) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $booking = new ParkingBooking();
                    $booking->slot_name = $bookingData['slot_name'];
                    $booking->slot_type_id = $bookingData['slot_type_id'];
                    $booking->booking_date = $bookingData['date'];
                    $booking->start_time = $bookingData['start_time'];
                    $booking->end_time = $bookingData['end_time'];
                    $booking->customer_name = $bookingData['customer_name'];
                    $booking->customer_email = $bookingData['customer_email'];
                    $booking->customer_phone = $bookingData['customer_phone'];
                    $booking->vehicle_name = $bookingData['vehicle_name'];
                    $booking->vehicle_number = $bookingData['vehicle_number'];
                    $booking->total_amount = $bookingData['total_amount'];
                    $booking->payment_method = 'Khalti';
                    $booking->payment_status = 'paid';
                    $booking->booking_status = 'confirmed';
                    $booking->creator_id = $userId;
                    $booking->created_by = $userId;
                    $booking->save();

                    try {
                        ParkingBookingPayments::dispatch($booking);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('parking-management.frontend.booking-success', ['userSlug' => $userSlug, 'id' => encrypt($booking->id)])->with('success', __('The parking booking has been created successfully.'));
                }
                return redirect()->route('parking-management.frontend.booking', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
            }
            return redirect()->route('parking-management.frontend.booking', ['userSlug' => $userSlug])->with('error', __('Something went wrong, Please try again.'));
        } catch (\Exception $exception) {
            return redirect()->route('parking-management.frontend.booking', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
        }
    }

    // LMS Payments
    public function lmsPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            $student = auth('lms_student')->user();

            if ($user && $student) {
                $cartItems = LMSCart::where('created_by', $user->id)->where('student_id', $student->id)->with('course')->get();
                if (!$cartItems->isEmpty()) {
                    $originalTotal = $cartItems->sum('original_price');
                    $subtotal = $cartItems->sum('price');
                    $courseDiscount = $originalTotal - $subtotal;
                    $couponDiscount = 0;
                    $appliedCoupon = Session::get('applied_coupon');

                    if ($appliedCoupon) {
                        $coupon = LMSCoupon::where('id', $appliedCoupon['id'])->where('created_by', $user->id)->first();
                        if ($coupon && $coupon->isValid() && (!$coupon->minimum_amount || $subtotal >= $coupon->minimum_amount)) {
                            $couponDiscount = $coupon->type === 'percentage' ? ($subtotal * $coupon->value) / 100 : $coupon->value;
                            $couponDiscount = min($couponDiscount, $subtotal);
                        }
                    }

                    $total = $subtotal - $couponDiscount;
                    if ($total <= 0) {
                        return redirect()->back()->with('error', __('Invalid payment amount.'));
                    }

                    $orderID = strtoupper(substr(uniqid(), -12));
                    $khaltiService = new KhaltiPaymentService(
                        company_setting('khalti_secret_key', $user->id) ?? '',
                        company_setting('khalti_mode', $user->id) === 'sandbox',
                        company_setting('defaultCurrency', $user->id) ?? ''
                    );

                    $response = $khaltiService->initiatePayment([
                        'return_url' => route('khalti.lms.payment.status', ['userSlug' => $userSlug]),
                        'amount' => $total,
                        'purchase_order_id' => $orderID,
                        'purchase_order_name' => 'LMS Course Purchase',
                        'session' => [
                            'original_total' => $originalTotal,
                            'course_discount' => $courseDiscount,
                            'coupon_discount' => $couponDiscount,
                            'applied_coupon' => $appliedCoupon,
                            'total' => $total,
                            'subtotal' => $subtotal,
                        ]
                    ]);

                    if ($response['success'] && isset($response['data']['payment_url'])) {
                        return redirect()->away($response['data']['payment_url']);
                    }
                    return redirect()->back()->with('error', __('Payment initialization failed.'));
                }
                return redirect()->route('lms.frontend.cart', ['userSlug' => $userSlug])->with('error', __('Your cart is empty.'));
            }
            return redirect()->back()->with('error', __('User not found.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function lmsGetKhaltiStatus(Request $request, $userSlug = null)
    {
        try {
            $pidx = $request->get('pidx');
            $orderData = Session::get($pidx);
            $user = User::where('slug', $userSlug)->first();
            $student = auth('lms_student')->user();

            if (
                $request->status === 'Completed'
                && $orderData && $user && $student && $pidx
            ) {
                $cartItems = LMSCart::where('created_by', $user->id)->where('student_id', $student->id)->with('course')->get();

                if ($cartItems->isEmpty()) {
                    return redirect()->route('lms.frontend.cart', ['userSlug' => $userSlug])->with('error', __('Your cart is empty.'));
                }

                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $user->id) ?? '',
                    company_setting('khalti_mode', $user->id) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {

                    $order = new LMSOrder();
                    $order->order_number = LMSOrder::generateOrderNumber($user->id);
                    $order->student_id = $student->id;
                    $order->payment_method = 'Khalti';
                    $order->payment_status = 'paid';
                    $order->original_total = $orderData['original_total'];
                    $order->subtotal = $orderData['subtotal'];
                    $order->discount_amount = $orderData['course_discount'];
                    $order->coupon_discount = $orderData['coupon_discount'];
                    $order->total_discount = $orderData['course_discount'] + $orderData['coupon_discount'];
                    $order->total_amount = $orderData['total'];
                    $order->coupon_id = $orderData['applied_coupon'] ? $orderData['applied_coupon']['id'] : null;
                    $order->coupon_code = $orderData['applied_coupon'] ? $orderData['applied_coupon']['code'] : null;
                    $order->status = 'completed';
                    $order->order_date = now();
                    $order->payment_id = $pidx;
                    $order->creator_id = $user->id;
                    $order->created_by = $user->id;
                    $order->save();

                    foreach ($cartItems as $cartItem) {
                        $orderItem = new LMSOrderItem();
                        $orderItem->order_id = $order->id;
                        $orderItem->course_id = $cartItem->course_id;
                        $orderItem->quantity = $cartItem->quantity;
                        $orderItem->unit_price = $cartItem->price;
                        $orderItem->total_price = $cartItem->price * $cartItem->quantity;
                        $orderItem->save();
                    }

                    $cartItems->each->delete();
                    Session::forget('applied_coupon');

                    try {
                        LMSOrderPayments::dispatch($order);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('lms.frontend.home', ['userSlug' => $userSlug])->with('success', __('The order has been created successfully.'));
                }
            }
            return redirect()->route('lms.frontend.checkout', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
        } catch (\Exception $exception) {
            return redirect()->route('lms.frontend.checkout', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
        }
    }

    // Events Payments
    public function eventsPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {

                $price = floatval($request->total);
                if ($price <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(substr(uniqid(), -12));
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $user->id) ?? '',
                    company_setting('khalti_mode', $user->id) === 'sandbox',
                    company_setting('defaultCurrency', $user->id) ?? ''
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.events-management.payment.status', ['userSlug' => $userSlug]),
                    'amount' => $price,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => 'Event Booking',
                    'session' => [
                        'event_id' => $request->event_id,
                        'fullName' => $request->fullName,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'persons' => $request->persons,
                        'total' => $request->total,
                        'ticket_type_id' => $request->ticket_type_id,
                        'time_slot' => $request->time_slot,
                        'selected_date' => $request->selected_date
                    ]
                ]);

                if ($response['success'] && isset($response['data']['payment_url'])) {
                    return redirect()->away($response['data']['payment_url']);
                }
            }
            return redirect()->back()->with('error', __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function eventsGetKhaltiStatus(Request $request, $userSlug = null)
    {
        try {
            $pidx = $request->get('pidx');
            $bookingData = Session::get($pidx);
            $user = User::where('slug', $userSlug)->first();

            if (
                $request->status === 'Completed'
                && $bookingData && $user && $pidx
            ) {
                $event = Event::where('id', $bookingData['event_id'])->where('created_by', $user->id)->first();
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $user->id) ?? '',
                    company_setting('khalti_mode', $user->id) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $booking = new EventBooking();
                    $booking->event_id = $bookingData['event_id'];
                    $booking->ticket_type_id = $bookingData['ticket_type_id'];
                    $booking->time_slot = $bookingData['time_slot'];
                    $booking->name = $bookingData['fullName'];
                    $booking->email = $bookingData['email'];
                    $booking->mobile = $bookingData['phone'];
                    $booking->person = $bookingData['persons'];
                    $booking->date = $bookingData['selected_date'];
                    $booking->total_price = $bookingData['total'];
                    $booking->price = $bookingData['total'] / $bookingData['persons'];
                    $booking->status = 'confirmed';
                    $booking->created_by = $user->id;
                    $booking->creator_id = $user->id;
                    $booking->save();

                    $eventBookingPayment = new EventBookingPayment();
                    $eventBookingPayment->event_booking_id = $booking->id;
                    $eventBookingPayment->booking_number = $booking->booking_number;
                    $eventBookingPayment->event_name = $event->title;
                    $eventBookingPayment->customer_name = $bookingData['fullName'];
                    $eventBookingPayment->payment_date = now();
                    $eventBookingPayment->amount = $bookingData['total'];
                    $eventBookingPayment->payment_status = 'cleared';
                    $eventBookingPayment->payment_type = 'Khalti';
                    $eventBookingPayment->description = 'Payment via Khalti';
                    $eventBookingPayment->created_by = $user->id;
                    $eventBookingPayment->creator_id = $user->id;
                    $eventBookingPayment->save();

                    try {
                        EventBookingPayments::dispatch($booking, $eventBookingPayment);
                    } catch (\Exception $th) {
                    }

                    return redirect()->route('events-management.frontend.ticket', ['userSlug' => $userSlug, 'id' => $booking->id, 'paymentId' => $eventBookingPayment->id])->with('success', __('The event booking has been created successfully.'));
                }
            }
            return redirect()->route('events-management.frontend.payment', ['userSlug' => $userSlug, 'id' => $bookingData['event_id']])->with('error', __('Payment was cancelled or failed.'));
        } catch (\Exception $exception) {
            return redirect()->route('events-management.frontend.payment', ['userSlug' => $userSlug, 'id' => $bookingData['event_id'] ?? null])->with('error', $exception->getMessage());
        }
    }

    // Holidayz Payments
    public function holidayzPayWithKhalti(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            $customer = auth('holidayz_customer')->user();

            if ($user && $customer) {
                $cart = HolidayzCart::where('created_by', $user->id)
                    ->where('customer_id', $customer->id)
                    ->with(['items.room', 'items.facilities', 'items.taxes'])
                    ->first();

                if (!$cart || $cart->items->isEmpty()) {
                    return redirect()->route('hotel.frontend.cart', ['userSlug' => $userSlug])
                        ->with('error', __('Your cart is empty.'));
                }

                foreach ($cart->items as $cartItem) {
                    $availableRooms = HolidayzAvailabilityHelper::getAvailableRoomCount(
                        $cartItem->room_id,
                        $cartItem->check_in_date->format('Y-m-d'),
                        $cartItem->check_out_date->format('Y-m-d'),
                        null,
                        $user->id
                    );

                    if ($cartItem->quantity > $availableRooms) {
                        return redirect()->route('hotel.frontend.cart', ['userSlug' => $userSlug])
                            ->with('error', __('Room ":room" is no longer available for the selected dates. Only :available rooms available.', [
                                'room' => $cartItem->room->room_type,
                                'available' => $availableRooms
                            ]));
                    }
                }

                $subtotal = $cart->items->sum(function ($item) {
                    return $item->rent_per_night * $item->nights * $item->quantity;
                });

                $tax_amount = $cart->items->sum(function ($item) {
                    return $item->taxes->sum('pivot.tax_amount');
                });

                $facilities_amount = $cart->items->sum(function ($item) {
                    return $item->facilities->sum('pivot.total_amount');
                });

                $coupon_discount = 0;
                $applied_coupon = Session::get('applied_coupon');

                if ($applied_coupon && isset($applied_coupon['id'])) {
                    $coupon = HolidayzCoupon::find($applied_coupon['id']);

                    if ($coupon && $coupon->created_by == $user->id && $coupon->isValid()) {
                        $coupon_discount = $applied_coupon['discount'] ?? 0;
                        $coupon_discount = min($coupon_discount, $subtotal);
                    } else {
                        session()->forget('applied_coupon');
                        $applied_coupon = null;
                    }
                }

                $total = $subtotal + $tax_amount + $facilities_amount - $coupon_discount;

                if ($total <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(substr(uniqid(), -12));
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $user->id) ?? '',
                    company_setting('khalti_mode', $user->id) === 'sandbox',
                    company_setting('defaultCurrency', $user->id) ?? ''
                );

                $response = $khaltiService->initiatePayment([
                    'return_url' => route('khalti.holidayz.payment.status', ['userSlug' => $userSlug]),
                    'amount' => $total,
                    'purchase_order_id' => $orderID,
                    'purchase_order_name' => 'Hotel Booking',
                    'session' => [
                        'subtotal' => $subtotal,
                        'tax_amount' => $tax_amount,
                        'facilities_amount' => $facilities_amount,
                        'coupon_discount' => $coupon_discount,
                        'total' => $total,
                        'applied_coupon' => $applied_coupon,
                        'special_requests' => $request->special_requests
                    ]
                ]);

                if ($response['success'] && isset($response['data']['payment_url'])) {
                    $pidx = $response['data']['pidx'] ?? null;


                    return redirect()->away($response['data']['payment_url']);
                }
            }
            return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])->with('error', __('Payment initialization failed.'));
        } catch (\Exception $e) {
            return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])->with('error', $e->getMessage());
        }
    }

    public function holidayzGetKhaltiStatus(Request $request, $userSlug = null)
    {
        try {
            $pidx = $request->get('pidx');
            $orderData = Session::get($pidx);

            $user = User::where('slug', $userSlug)->first();
            $customer = auth('holidayz_customer')->user();

            if (
                $request->status === 'Completed'
                && isset($pidx) && $orderData && $user && $customer
            ) {
                $khaltiService = new KhaltiPaymentService(
                    company_setting('khalti_secret_key', $user->id) ?? '',
                    company_setting('khalti_mode', $user->id) === 'sandbox'
                );

                $verification = $khaltiService->verifyPayment($pidx);

                if ($verification['success']) {
                    $cart = HolidayzCart::where('created_by', $user->id)
                        ->where('customer_id', $customer->id)
                        ->with(['items.room', 'items.facilities', 'items.taxes'])
                        ->first();

                    $booking = new HolidayzRoomBooking();
                    $booking->booking_date = now();
                    $booking->customer_id = $customer->id;
                    $booking->adults = $cart->items->sum('adults');
                    $booking->children = $cart->items->sum('children');
                    $booking->total_guests = $cart->items->sum('adults') + $cart->items->sum('children');
                    $booking->subtotal = $orderData['subtotal'];
                    $booking->tax_amount = $orderData['tax_amount'];
                    $booking->coupon_id = $orderData['applied_coupon']['id'] ?? null;
                    $booking->discount_amount = $orderData['coupon_discount'];
                    $booking->total_amount = $orderData['total'];
                    $booking->paid_amount = $orderData['total'];
                    $booking->balance_amount = 0;
                    $booking->payment_method = 'Khalti';
                    $booking->status = 'paid';
                    $booking->special_requests = $orderData['special_requests'];
                    $booking->creator_id = $user->id;
                    $booking->created_by = $user->id;
                    $booking->save();

                    foreach ($cart->items as $cartItem) {
                        $bookingItem = new HolidayzRoomBookingItem();
                        $bookingItem->booking_id = $booking->id;
                        $bookingItem->room_id = $cartItem->room_id;
                        $bookingItem->check_in_date = $cartItem->check_in_date;
                        $bookingItem->check_out_date = $cartItem->check_out_date;
                        $bookingItem->quantity = $cartItem->quantity;
                        $bookingItem->adults = $cartItem->adults;
                        $bookingItem->children = $cartItem->children;
                        $bookingItem->rent_per_night = $cartItem->rent_per_night;
                        $bookingItem->nights = $cartItem->nights;
                        $bookingItem->discount_percentage = 0;
                        $bookingItem->discount_amount = 0;
                        $bookingItem->total_amount = $cartItem->rent_per_night * $cartItem->nights * $cartItem->quantity;
                        $bookingItem->save();

                        foreach ($cartItem->facilities as $facility) {
                            $bookingItem->facilities()->attach($facility->id, [
                                'price' => $facility->pivot->price,
                                'quantity' => $facility->pivot->quantity,
                                'total_amount' => $facility->pivot->total_amount
                            ]);
                        }

                        foreach ($cartItem->taxes as $tax) {
                            $bookingItem->taxes()->attach($tax->id, [
                                'tax_name' => $tax->pivot->tax_name ?? $tax->name,
                                'tax_rate' => $tax->pivot->tax_rate ?? $tax->rate,
                                'tax_amount' => $tax->pivot->tax_amount
                            ]);
                        }
                    }

                    if ($orderData['applied_coupon']) {
                        $couponId = $orderData['applied_coupon']['id'];
                        $coupon = HolidayzCoupon::find($couponId);
                        if ($coupon) {
                            $existingUsage = HolidayzCouponUsage::where('coupon_id', $couponId)
                                ->where('customer_id', $customer->id)
                                ->exists();

                            if (!$existingUsage) {
                                $couponUsage = new HolidayzCouponUsage();
                                $couponUsage->coupon_id = $couponId;
                                $couponUsage->customer_id = $customer->id;
                                $couponUsage->used_at = now();
                                $couponUsage->creator_id = $coupon->creator_id;
                                $couponUsage->created_by = $coupon->created_by;
                                $couponUsage->save();

                                $coupon->increment('used_count');
                            }
                        }
                    }

                    HolidayzCart::where('created_by', $user->id)
                        ->where('customer_id', $customer->id)
                        ->delete();

                    Session::forget('applied_coupon');

                    try {
                        HolidayzBookingPayments::dispatch($booking);
                    } catch (\Throwable $th) {
                    }

                    return redirect()->route('hotel.frontend.booking-confirm', [
                        'userSlug' => $userSlug,
                        'encryptedBooking' => encrypt($booking->id)
                    ])->with('success', __('The hotel booking has been created successfully.'));
                }
            }

            return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])->with('error', __('Payment was cancelled or failed.'));
        } catch (\Exception $exception) {
            return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])->with('error', $exception->getMessage());
        }
    }
}
