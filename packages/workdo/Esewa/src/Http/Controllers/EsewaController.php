<?php

namespace Workdo\Esewa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Workdo\Esewa\Services\EsewaService;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use Workdo\Esewa\Events\EsewaPaymentStatus;

use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingPackage;
use Workdo\Bookings\Models\BookingAppointment;
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
use Workdo\Holidayz\Helpers\HolidayzAvailabilityHelper;
use Workdo\Holidayz\Events\HolidayzBookingPayments;

class EsewaController extends Controller
{
    // Plan Payments
    public function planPayWithEsewa(Request $request)
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
            foreach ($user_module_array as $value) {
                $temp = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                $user_module_price += $temp;
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
                $esewaService = new EsewaService(null, true);

                $callback_url = route('esewa.plan.status', ['order_id' => $orderID]);

                $response = $esewaService->checkout(
                    $price,
                    $callback_url,
                    $orderID,
                    [
                        'plan_id' => $plan->id,
                        'duration' => $duration,
                        'user_module' => $user_module,
                        'user_id' => $request->user_id,
                        'counter' => $counter,
                        'coupon_code' => $request->coupon_code
                    ]
                );

                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->email = $user->email;
                $order->card_number = null;
                $order->card_exp_month = null;
                $order->card_exp_year = null;
                $order->plan_name = !empty($plan->name) ? $plan->name : 'Basic Package';
                $order->plan_id = $plan->id;
                $order->price = !empty($price) ? $price : 0;
                $order->currency = $admin_currancy;
                $order->txn_id = $response['transaction_uuid'] ?? null;
                $order->payment_type = 'Esewa';
                $order->payment_status = 'pending';
                $order->receipt = null;
                $order->created_by = $user->id;
                $order->save();

                return redirect()->route('esewa.checkout', encrypt($response));
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        }
        return redirect()->route('plans.index')->with('error', __('The Plan has been deleted.'));
    }

    public function planGetEsewaStatus(Request $request, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $planData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($planData) && $esewaService->isPaymentSuccessful($request)) {
                $Order = Order::where('order_id', $order_id)->first();
                if ($Order) {
                    $Order->payment_status = 'succeeded';
                    $Order->save();
                }

                $plan = Plan::find($planData['plan_id']);
                $counter = [
                    'user_counter' => $planData['counter']['user_counter'] ?? 0,
                    'storage_counter' => $planData['counter']['storage_limit'] ?? 0,
                ];

                $assignPlan = assignPlan($plan->id, $planData['duration'], $planData['user_module'], $counter, $planData['user_id']);

                if ($assignPlan['is_success']) {
                    if ($planData['coupon_code']) {
                        $coupon = Coupon::where('code', $planData['coupon_code'])->first();
                        if ($coupon) {
                            recordCouponUsage($coupon->id, $planData['user_id'], $order_id);
                        }
                    }

                    $type = 'Subscription';
                    try {
                        EsewaPaymentStatus::dispatch($plan, $type, $Order);
                    } catch (\Exception $e) {
                    }

                    return redirect()->route('plans.index')->with('success', __('Plan activated successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again.'));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('plans.index')->with('error', $exception->getMessage());
        }
    }

    // Booking Payments
    public function bookingPayWithEsewa(Request $request, $userSlug = null)
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

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($package->created_by);

            $callback_url = route('esewa.booking.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $price,
                $callback_url,
                $orderID,
                [
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
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->route('booking.home', ['userSlug' => $userSlug])
                ->with('error', $e->getMessage());
        }
    }

    public function bookingGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {

            $esewaService = new EsewaService();
            $bookingData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($bookingData) && $esewaService->isPaymentSuccessful($request)) {
                $package = BookingPackage::find($bookingData['selectedPackageItem']);
                $userId = $package->created_by ?? null;


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
                    'payment' => 'Esewa',
                    'status' => 'pending',
                    'payment_status' => 'paid',
                    'online_payment_id' => $order_id,
                    'created_by' => $userId,
                    'creator_id' => $userId,
                ]);

                try {
                    BookingAppointmentPayments::dispatch($appointment);
                } catch (\Exception $th) {
                }

                return redirect()->route('booking.home', ['userSlug' => $userSlug])
                    ->with('success', __('The Booking has been created successfully.'));
            } else {
                return redirect()->route('booking.home', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('booking.home', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }

    // Beauty Spa Payments
    public function beautySpaPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();

            if ($user) {
                $service = BeautyService::where('id', $request->service)
                    ->where('created_by', $user->id)
                    ->firstOrFail();

                $offers = BeautyServiceOffer::where('beauty_service_id', $service->id)
                    ->where('start_date', '<=', $request->date)
                    ->where('end_date', '>=', $request->date)
                    ->where('created_by', $user->id)
                    ->get();

                $price = $offers->isNotEmpty() ? $offers->sum('offer_price') : $service->price;
                $totalPrice = $price * $request->person;

                if ($totalPrice <= 0) {
                    return redirect()->back()->with('error', __('Invalid payment amount.'));
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $esewaService = new EsewaService($user->id);

                $callback_url = route('esewa.beauty-spa.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

                $response = $esewaService->checkout(
                    $totalPrice,
                    $callback_url,
                    $orderID,
                    [
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
                        'payment_option' => $request->payment_option
                    ]
                );

                return redirect()->route('esewa.checkout', encrypt($response));
            }

            return back()->with('error', __('User not found.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function beautySpaGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $bookingData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($bookingData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();

                $service = BeautyService::where('id', $bookingData['service'])
                    ->where('created_by', $user->id)
                    ->first();

                $offers = BeautyServiceOffer::where('beauty_service_id', $service->id)
                    ->where('start_date', '<=', $bookingData['date'])
                    ->where('end_date', '>=', $bookingData['date'])
                    ->where('created_by', $user->id)
                    ->get();

                $price = $offers->isNotEmpty() ? $offers->sum('offer_price') : $service->price;
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
                $booking->payment_option = 'Esewa';
                $booking->payment_status = 'paid';
                $booking->stage_id = 0;
                $booking->creator_id = null;
                $booking->created_by = $user->id;
                $booking->save();

                $beautyreceipt = new BeautyBookingReceipt();
                $beautyreceipt->beauty_booking_id = $booking->id;
                $beautyreceipt->name = $booking->name;
                $beautyreceipt->service = $booking->service;
                $beautyreceipt->number = $booking->phone_number;
                $beautyreceipt->gender = $booking->gender;
                $beautyreceipt->start_time = $booking->start_time;
                $beautyreceipt->end_time = $booking->end_time;
                $beautyreceipt->price = $booking->price;
                $beautyreceipt->payment_type = 'Esewa';
                $beautyreceipt->created_by = $booking->created_by;
                $beautyreceipt->save();

                try {
                    BeautyBookingPayments::dispatch($booking);
                } catch (\Throwable $th) {
                }

                return redirect()->route('beauty-spa.booking-success', ['userSlug' => $userSlug, 'id' => encrypt($booking->id)])
                    ->with('success', __('The booking has been created successfully.'));
            } else {
                return redirect()->route('beauty-spa.booking', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('beauty-spa.booking', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }

    // LMS Payments
    public function lmsPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            if (!$user) {
                return redirect()->back()->with('error', __('User not found.'));
            }

            $student = auth('lms_student')->user();
            if (!$student) {
                return redirect()->route('lms.frontend.login', ['userSlug' => $userSlug]);
            }

            $cartItems = LMSCart::where('created_by', $user->id)
                ->where('student_id', $student->id)
                ->with('course')
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('lms.frontend.cart', ['userSlug' => $userSlug])
                    ->with('error', __('Your cart is empty'));
            }

            $originalTotal = $cartItems->sum('original_price');
            $subtotal = $cartItems->sum('price');
            $courseDiscount = $originalTotal - $subtotal;
            $couponDiscount = 0;

            $appliedCoupon = Session::get('applied_coupon');
            if ($appliedCoupon) {
                $coupon = LMSCoupon::where('id', $appliedCoupon['id'])
                    ->where('created_by', $user->id)
                    ->first();

                if ($coupon && $coupon->isValid()) {
                    if (!$coupon->minimum_amount || $subtotal >= $coupon->minimum_amount) {
                        if ($coupon->type === 'percentage') {
                            $couponDiscount = ($subtotal * $coupon->value) / 100;
                        } else {
                            $couponDiscount = $coupon->value;
                        }
                        $couponDiscount = min($couponDiscount, $subtotal);
                    }
                }
            }

            $total = $subtotal - $couponDiscount;
            if ($total <= 0) {
                return redirect()->back()->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($user->id);

            $callback_url = route('esewa.lms.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $total,
                $callback_url,
                $orderID,
                [
                    'original_total' => $originalTotal,
                    'payment_method' => $request->payment_method,
                    'payment_note' => $request->payment_note,
                    'subtotal' => $subtotal,
                    'course_discount' => $courseDiscount,
                    'coupon_discount' => $couponDiscount,
                    'total' => $total,
                    'applied_coupon' => $appliedCoupon
                ]
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function lmsGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $orderData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($orderData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();
                $student = auth('lms_student')->user();

                if (!$orderData) {
                    return redirect()->route('lms.frontend.home', ['userSlug' => $userSlug])->with('error', __('Something went wrong, Please try again.'));
                }

                $cartItems = LMSCart::where('created_by', $user->id)
                    ->where('student_id', $student->id)
                    ->with('course')
                    ->get();

                $order = new LMSOrder();
                $order->order_number = LMSOrder::generateOrderNumber($user->id);
                $order->student_id = $student->id;
                $order->payment_method = 'Esewa';
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
                $order->notes = $orderData['payment_note'];
                $order->order_date = now();
                $order->payment_id = $order_id;
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
                } catch (\Throwable $th) {
                }

                return redirect()->route('lms.frontend.home', ['userSlug' => $userSlug])
                    ->with('success', __('The order has been created successfully.'));
            } else {
                return redirect()->route('lms.frontend.checkout', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('lms.frontend.checkout', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }

    // Laundry Payments
    public function laundryPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();
            $price = floatval($request->total ?? 0);

            if ($price <= 0) {
                return redirect()->back()->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($user->id);

            $callback_url = route('esewa.laundry.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $price,
                $callback_url,
                $orderID,
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'location' => $request->location,
                    'numberOfItems' => $request->cloth_no,
                    'specialInstructions' => $request->instructions,
                    'pickupDate' => $request->pickup_date,
                    'pickupTime' => $request->pickupTime,
                    'deliveryDate' => $request->delivery_date,
                    'deliveryTime' => $request->deliveryTime,
                    'services' => json_decode($request->services, true) ?? [],
                    'total' => $request->total
                ]
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function laundryGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $bookingData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($bookingData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();

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
                $booking->payment_method = 'Esewa';
                $booking->payment_id = $order_id;
                $booking->status = 2;
                $booking->total = $bookingData['total'];
                $booking->created_by = $user->id;
                $booking->creator_id = $user->id;
                $booking->save();

                $invoice = new LaundryInvoice();
                $invoice->laundry_request_id = $booking->id;
                $invoice->amount = $booking->total;
                $invoice->status = 1;
                $invoice->creator_id = $user->id;
                $invoice->created_by = $user->id;
                $invoice->save();
                
                if ($invoice->status == 1) {
                    $payment = new LaundryPayment();
                    $payment->payment_amount = $invoice->amount;
                    $payment->invoice_id = $invoice->id;
                    $payment->payment_date = date('Y-m-d H:i:s');
                    $payment->status = 'cleared';
                    $payment->creator_id = $user->id;
                    $payment->created_by = $user->id;
                    $payment->save();
                }

                try {
                    LaundryBookingPayments::dispatch($booking);
                } catch (\Throwable $th) {
                }

                return redirect()->route('laundry-management.frontend.booking-success', [
                    'userSlug' => $userSlug,
                    'requestId' => encrypt($booking->id)
                ]);
            } else {
                return redirect()->route('laundry-management.frontend.booking', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('laundry-management.frontend.booking', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }

    // Parking Payments
    public function parkingPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $user = User::where('slug', $userSlug)->first();

            $price = floatval($request->total_amount);
            if ($price <= 0) {
                return redirect()->back()->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($user->id);

            $callback_url = route('esewa.parking.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $price,
                $callback_url,
                $orderID,
                [
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
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function parkingGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $bookingData = Session::get($order_id);
            Session::forget($order_id);

            if (!empty($bookingData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();

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
                $booking->payment_method = 'Esewa';
                $booking->payment_status = 'paid';
                $booking->booking_status = 'confirmed';
                $booking->creator_id = $user->id;
                $booking->created_by = $user->id;
                $booking->save();

                try {
                    ParkingBookingPayments::dispatch($booking);
                } catch (\Throwable $th) {
                }

                return redirect()->route('parking-management.frontend.booking-success', ['userSlug' => $userSlug, 'id' => encrypt($booking->id)])
                    ->with('success', __('The parking booking has been created successfully.'));
            } else {
                return redirect()->route('parking-management.frontend.booking', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('parking-management.frontend.booking', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }

    // Events Payments
    public function eventsPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $userSlug = $request->route('userSlug');
            $user = User::where('slug', $userSlug)->first();
            if (!$user) {
                return redirect()->back()->with('error', __('User not found.'));
            }

            $eventId = $request->event_id;
            $price = floatval($request->total);

            if ($price <= 0) {
                return redirect()->back()->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($user->id);

            $callback_url = route('esewa.events-management.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $price,
                $callback_url,
                $orderID,
                [
                    'event_id' => $eventId,
                    'fullName' => $request->fullName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'persons' => $request->persons,
                    'total' => $request->total,
                    'ticket_type_id' => $request->ticket_type_id,
                    'time_slot' => $request->time_slot,
                    'selected_date' => $request->selected_date
                ]
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function eventsGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $bookingData = Session::get($order_id);
            Session::forget($order_id);
        
            if (!empty($bookingData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();

                $event = Event::where('id', $bookingData['event_id'])
                    ->where('created_by', $user->id)
                    ->first();

                $eventbooking = new EventBooking();
                $eventbooking->event_id = $bookingData['event_id'];
                $eventbooking->ticket_type_id = $bookingData['ticket_type_id'];
                $eventbooking->time_slot = $bookingData['time_slot'];
                $eventbooking->name = $bookingData['fullName'];
                $eventbooking->email = $bookingData['email'];
                $eventbooking->mobile = $bookingData['phone'];
                $eventbooking->person = $bookingData['persons'];
                $eventbooking->date = $bookingData['selected_date'];
                $eventbooking->total_price = $bookingData['total'];
                $eventbooking->price = $bookingData['total'] / $bookingData['persons'];
                $eventbooking->status = 'confirmed';
                $eventbooking->created_by = $user->id;
                $eventbooking->creator_id = $user->id;
                $eventbooking->save();

                $eventBookingPayment = new EventBookingPayment();
                $eventBookingPayment->event_booking_id = $eventbooking->id;
                $eventBookingPayment->booking_number = $eventbooking->booking_number;
                $eventBookingPayment->event_name = $event->title;
                $eventBookingPayment->customer_name = $bookingData['fullName'];
                $eventBookingPayment->payment_date = now();
                $eventBookingPayment->amount = $bookingData['total'];
                $eventBookingPayment->payment_status = 'cleared';
                $eventBookingPayment->payment_type = 'Esewa';
                $eventBookingPayment->description = 'Payment via Esewa';
                $eventBookingPayment->created_by = $user->id;
                $eventBookingPayment->creator_id = $user->id;
                $eventBookingPayment->save();

                try {
                    EventBookingPayments::dispatch($eventbooking, $eventBookingPayment);
                } catch (\Throwable $th) {
                }

                return redirect()->route('events-management.frontend.ticket', ['userSlug' => $userSlug, 'id' => $eventbooking->id, 'paymentId' => $eventBookingPayment->id])
                    ->with('success', __('The event booking has been created successfully.'));
            } else {
                return redirect()->route('events-management.frontend.payment', ['userSlug' => $userSlug, 'id' => $bookingData['event_id']])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('events-management.frontend.payment', ['userSlug' => $userSlug, 'id' => $bookingData['event_id']])
                ->with('error', $exception->getMessage());
        }
    }

    // Holidayz Payments
    public function holidayzPayWithEsewa(Request $request, $userSlug = null)
    {
        try {
            $userSlug = $request->route('userSlug');
            $user = User::where('slug', $userSlug)->first();
            if (!$user) {
                return redirect()->back()->with('error', __('User not found.'));
            }

            $customer = auth('holidayz_customer')->user();
            if (!$customer) {
                return redirect()->route('hotel.frontend.login', ['userSlug' => $userSlug]);
            }

            $cart = HolidayzCart::where('created_by', $user->id)
                ->where('customer_id', $customer->id)
                ->with(['items.room', 'items.facilities', 'items.taxes'])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('hotel.frontend.cart', ['userSlug' => $userSlug])
                    ->with('error', __('Your cart is empty'));
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
                        ->with('error', __('Room is no longer available for the selected dates.'));
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
                    Session::forget('applied_coupon');
                    $applied_coupon = null;
                }
            }

            $total = $subtotal + $tax_amount + $facilities_amount - $coupon_discount;

            if ($total <= 0) {
                return redirect()->back()->with('error', __('Invalid payment amount.'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $esewaService = new EsewaService($user->id);

            $callback_url = route('esewa.holidayz.payment.status', ['userSlug' => $userSlug, 'order_id' => $orderID]);

            $response = $esewaService->checkout(
                $total,
                $callback_url,
                $orderID,
                [
                    'payment_method' => 'Esewa',
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax_amount,
                    'facilities_amount' => $facilities_amount,
                    'coupon_discount' => $coupon_discount,
                    'total' => $total,
                    'applied_coupon' => $applied_coupon,
                    'special_requests' => $request->special_requests
                ]
            );

            return redirect()->route('esewa.checkout', encrypt($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function holidayzGetEsewaStatus(Request $request, $userSlug, $order_id)
    {
        try {
            $esewaService = new EsewaService();
            $orderData = Session::get($order_id);
            Session::forget($order_id);
        
            if (!empty($orderData) && $esewaService->isPaymentSuccessful($request)) {
                $user = User::where('slug', $userSlug)->first();
                $customer = auth('holidayz_customer')->user();
                if (!$user || !$customer) {
                    return redirect()->route('hotel.frontend.index', ['userSlug' => $userSlug])->with('error', __('Invalid session.'));
                }

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
                $booking->payment_method = 'Esewa';
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
                ])->with('success', __('Payment completed successfully! Booking #:number', ['number' => $booking->booking_number]));
            } else {
                return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])
                    ->with('error', __('Payment was cancelled or failed.'));
            }
        } catch (\Exception $exception) {
            return redirect()->route('hotel.frontend.checkout', ['userSlug' => $userSlug])
                ->with('error', $exception->getMessage());
        }
    }
}
