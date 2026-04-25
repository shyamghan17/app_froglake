<?php

namespace Workdo\Khalti\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Entities\BeautyBooking;
use Workdo\BeautySpaManagement\Entities\BeautyReceipt;
use Workdo\BeautySpaManagement\Entities\BeautyService;
use Workdo\Bookings\Entities\BookingsCustomer;
use Workdo\Bookings\Entities\BookingsPackage;
use Workdo\EventsManagement\Entities\EventBookingOrder;
use Workdo\EventsManagement\Entities\EventsBookings;
use Workdo\EventsManagement\Entities\EventsMange;
use Workdo\Holidayz\Entities\BookingCoupons;
use Workdo\Holidayz\Entities\HotelCustomer;
use Workdo\Holidayz\Entities\Hotels;
use Workdo\Holidayz\Entities\RoomBooking;
use Workdo\Holidayz\Entities\RoomBookingCart;
use Workdo\Holidayz\Entities\RoomBookingOrder;
use Workdo\Holidayz\Entities\UsedBookingCoupons;
use Workdo\Holidayz\Events\CreateRoomBooking;
use Workdo\Khalti\Events\KhaltiPaymentStatus;
use Workdo\Khalti\Khalti\Khalti;
use Illuminate\Support\Facades\Crypt;
use Workdo\Account\Entities\BankAccount;
use Workdo\Bookings\Entities\BookingsAppointment;
use Workdo\Facilities\Entities\FacilitiesBooking;
use Workdo\Facilities\Entities\FacilitiesReceipt;
use Workdo\Facilities\Entities\FacilitiesService;
use Workdo\GymManagement\Entities\AssignMembershipPlan;
use Workdo\BeautySpaManagement\Entities\BeautyServiceOffer;

class KhaltiController extends Controller
{
    public $currancy;
    public $is_khalti_enabled;
    public $khalti_public_key;
    public $khalti_secret_key;
        /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function setting(Request $request)
    {
        if(Auth::user()->isAbleTo('khalti payment manage'))
        {
            if ($request->has('khalti_payment_is_on')) {
                $validator = Validator::make($request->all(),
                [
                    'khalti_public_key' => 'required|string',
                    'khalti_secret_key' => 'required|string',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }
            $post = $request->all();
            unset($post['_token']);
            unset($post['_method']);
            if($request->has('khalti_payment_is_on')){
                foreach ($post as $key => $value) {
                        // Define the data to be updated or inserted
                    $data = [
                        'key'        => $key,
                        'workspace'  => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                        // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
            else
            {
                      // Define the data to be updated or inserted
               $data = [
                    'key'        => 'khalti_payment_is_on',
                    'workspace'  => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                    // Check if the record exists, and update or insert accordingly
                Setting::updateOrInsert($data, ['value' => 'off']);

            }

                // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', __('Khalti Setting save successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function planPayWithKhalti(Request $request)
    {
        $plan              = Plan::find($request->plan_id);
        $user_counter      = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
        $workspace_counter = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
        $user_module       = !empty($request->user_module_input) ? $request->user_module_input : '0';
        $duration          = !empty($request->time_period) ? $request->time_period : 'Month';
        $user_module_price = 0;
        if(!empty($user_module) && $plan->custom_plan == 1)
        {
            $user_module_array = explode(',',$user_module);
            foreach ($user_module_array as $key => $value)
            {
                $temp              = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                $user_module_price = $user_module_price + $temp;
            }
        }
        $user_price = 0;
        if($user_counter > 0)
        {
            $temp       = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
            $user_price = $user_counter * $temp;
        }
        $workspace_price = 0;
        if($workspace_counter > 0)
        {
            $temp            = ($duration == 'Year') ? $plan->price_per_workspace_yearly : $plan->price_per_workspace_monthly;
            $workspace_price = $workspace_counter * $temp;
        }
        $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
        $counter    = [
            'user_counter'      => $user_counter,
            'workspace_counter' => $workspace_counter,
        ];

        $admin_settings = getAdminAllSetting();
        config(
            [
                'khalti.public_key' => isset($admin_settings['khalti_public_key']) ? $admin_settings['khalti_public_key'] : '',
                'khalti.sck'        => isset($admin_settings['khalti_secret_key']) ? $admin_settings['khalti_secret_key'] : '',
            ]
        );

        if ($plan) {
            try {
                if($request->coupon_code)
                {
                    $plan_price = CheckCoupon($request->coupon_code,$plan_price,$plan->id);
                }
                $price = $plan_price + $user_module_price + $user_price + $workspace_price;

                if($price <= 0){
                    $assignPlan = DirectAssignPlan($plan->id,$duration,$user_module,$counter,'Khalti',$request->coupon_code);
                    if($assignPlan['is_success']){
                        $amount = $price;
                        return $amount;
                    }else{
                        return response()->json([
                            'success' => true,
                            'inputs'  => __('Something into warning.'),
                        ]);
                    }
                }
                $secret = !empty($admin_settings['khalti_secret_key'])?$admin_settings['khalti_secret_key']:'';


                $amount = $price;
                return $amount;

            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetKhaltiStatus(Request $request)
    {
        $admin_settings = getAdminAllSetting();
        $plan           = Plan::find($request->plan_id);
        $user           = User::find(Auth::user()->id);
        if($plan)
        {
            $user_counter      = !empty($request->user_counter) ? $request->user_counter : 0;
            $workspace_counter = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
            $user_module       = !empty($request->user_module) ? $request->user_module : '';
            $duration          = !empty($request->duration) ? $request->duration : 'Month';
            $user_module_price = 0;
            if(!empty($user_module)  && $plan->custom_plan == 1)
            {
                $user_module_array = explode(',',$user_module);
                foreach ($user_module_array as $key => $value)
                {
                    $temp              = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                    $user_module_price = $user_module_price + $temp;
                }
            }
            $user_price = 0;
            if($user_counter > 0)
            {
                $temp       = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
                $user_price = $user_counter * $temp;
            }
            $workspace_price = 0;
            if($workspace_counter > 0)
            {
                $temp            = ($duration == 'Year') ? $plan->price_per_workspace_yearly : $plan->price_per_workspace_monthly;
                $workspace_price = $workspace_counter * $temp;
            }
            $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
            $counter    = [
                'user_counter'      => $user_counter,
                'workspace_counter' => $workspace_counter,
            ];
            if($request->coupon_code)
            {
                $plan_price = CheckCoupon($request->coupon_code,$plan_price,$plan->id);
            }
            $price = $plan_price + $user_module_price + $user_price + $workspace_price;

            $payload  = $request->payload;
            $secret   = !empty($admin_settings['khalti_secret_key'])?$admin_settings['khalti_secret_key']:'';
            $token    = $payload['token'];
            $amount   = $payload['amount'];
            $khalti   = new Khalti();
            $response = $khalti->verifyPayment($secret,$token,$amount);

            try
            {
                if($response['status_code'] == '200')
                {
                    $product = !empty($plan->name) ? $plan->name : 'Basic Package';
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $order   = Order::create(
                        [
                            'order_id'       => $orderID,
                            'name'           => null,
                            'email'          => null,
                            'card_number'    => null,
                            'card_exp_month' => null,
                            'card_exp_year'  => null,
                            'plan_name'      => $product,
                            'plan_id'        => $plan->id,
                            'price'          => !empty($price) ? $price : 0,
                            'price_currency' => !empty($admin_settings['defult_currancy'])?$admin_settings['defult_currancy']:'',
                            'txn_id'         => '',
                            'payment_type'   => __('Khalti'),
                            'payment_status' => 'succeeded',
                            'receipt'        => null,
                            'user_id'        => $user->id,
                        ]
                    );
                    $type       = 'Subscription';
                    $user       = User::find($user->id);
                    $assignPlan = $user->assignPlan($plan->id,$request->duration,$request->user_module,$counter);
                    if($request->coupon_code){

                        UserCoupon($request->coupon_code,$orderID);
                    }

                    event(new KhaltiPaymentStatus($plan,$type,$order));

                    if ($assignPlan['is_success']) {
                        return $response;
                    }
                }
                else {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            } catch (\Exception $e) {
                return response()->json('failed');
            }
        } else {
            return response()->json('deleted');
        }
    }

    public function paymentConfig($id = null, $wokspace = Null)
    {
        if (!empty($id) && empty($wokspace)) {
            $company_settings = getCompanyAllSetting($id);
        } elseif (!empty($id) && !empty($wokspace)) {
            $company_settings = getCompanyAllSetting($id, $wokspace);
        } else {
            $company_settings = getCompanyAllSetting();
        }
        $this->currancy          = !empty($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : 'USD';
        $this->is_khalti_enabled = ($company_settings['khalti_payment_is_on']) ? $company_settings['khalti_payment_is_on'] : 'off';
        $this->khalti_public_key = ($company_settings['khalti_public_key']) ? $company_settings['khalti_public_key'] : '';
        $this->khalti_secret_key = ($company_settings['khalti_secret_key']) ? $company_settings['khalti_secret_key'] : '';
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        if($request->type == 'invoice')
        {
            $invoice = \App\Models\Invoice::find($request->invoice_id);
            $this->paymentConfig($invoice->created_by,$invoice->workspace);

            $payload  = $request->payload;
            $secret   = $this->khalti_secret_key;
            $token    = $payload['token'];
            $amount   = $payload['amount'];
            $khalti   = new Khalti();
            $response = $khalti->verifyPayment($secret,$token,$amount);
            if ($invoice) {
                if($response['status_code'] == '200')
                {
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    try {
                        $invoice_payment                 = new \App\Models\InvoicePayment();
                        $invoice_payment->invoice_id     = $request->invoice_id;
                        $invoice_payment->date           = Date('Y-m-d');
                        $invoice_payment->account_id     = 0;
                        $invoice_payment->payment_method = 0;
                        $invoice_payment->amount         = $request->amount;
                        $invoice_payment->order_id       = $orderID;
                        $invoice_payment->currency       = $this->currancy;
                        $invoice_payment->payment_type   = 'Khalti';
                        $invoice_payment->save();

                        $due = $invoice->getDue();
                        if ($due <= 0) {
                            $invoice->status = 4;
                            $invoice->save();
                        } else {
                            $invoice->status = 3;
                            $invoice->save();
                        }
                        event(new KhaltiPaymentStatus($invoice,$request->type,$invoice_payment));

                        return $response;

                    } catch (\Exception $e) {
                        return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success',$e->getMessage());
                    }
                }
                else {
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('error', __('Transaction has been failed.'));
                }
            } else {
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
            }

        }
        elseif($request->type  == 'retainer')
        {
            $retainer = \Workdo\Retainer\Entities\Retainer::find($request->invoice_id);
            $this->paymentConfig($retainer->created_by,$retainer->workspace);

            $payload  = $request->payload;
            $secret   = $this->khalti_secret_key;
            $token    = $payload['token'];
            $amount   = $payload['amount'];
            $khalti   = new Khalti();
            $response = $khalti->verifyPayment($secret,$token,$amount);

            if ($retainer)
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                if($response['status_code'] == '200')
                {
                    try {
                        $retainer_payment                 = new \Workdo\Retainer\Entities\RetainerPayment();
                        $retainer_payment->retainer_id    = $request->invoice_id;
                        $retainer_payment->date           = Date('Y-m-d');
                        $retainer_payment->account_id     = 0;
                        $retainer_payment->payment_method = 0;
                        $retainer_payment->amount         = $request->amount;
                        $retainer_payment->order_id       = $orderID;
                        $retainer_payment->currency       = $this->currancy;
                        $retainer_payment->payment_type   = 'Khalti';
                        $retainer_payment->save();
                        $due = $retainer->getDue();
                        if ($due <= 0) {
                            $retainer->status = 5;
                            $retainer->save();
                        } else {
                            $retainer->status = 4;
                            $retainer->save();
                        }

                        event(new KhaltiPaymentStatus($retainer,$request->type,$retainer_payment));

                        return $response;

                    } catch (\Exception $e) {
                        return redirect()->route('pay.retainer',  \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success',$e->getMessage());
                    }
                }
                else {
                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('error', __('Transaction has been failed.'));
                }
            } else {

                return redirect()->route('pay.retainer',  \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id))->with('success', __('Retainer not found.'));
            }
        }
    }

    public function coursePayWithKhalti(Request $request,$slug)
    {
        $store = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
        $cart  = session()->get($slug);

        $this->paymentConfig($store->created_by, $store->wokspace_id);
        $company_settings = getCompanyAllSetting($store->created_by, $store->workspace_id);
        if (!empty($cart)) {
            $products = $cart['products'];
        } else {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }
        if (isset($cart['coupon']['data_id'])) {
            $coupon = \Workdo\LMS\Entities\CourseCoupon::where('id', $cart['coupon']['data_id'])->first();
        } else {
            $coupon = '';
        }
        $product_name   = [];
        $product_id     = [];
        $totalprice     = 0;
        $sub_totalprice = 0;
        foreach ($products as $key => $product) {
            $product_name[]  = $product['product_name'];
            $product_id[]    = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }

        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }
        if ($products) {

            try {
                $payload  = $request->payload;
                $secret   = $this->khalti_secret_key;
                $token    = $payload['token'];
                $amount   = $payload['amount'];
                $khalti   = new Khalti();
                $response = $khalti->verifyPayment($secret,$token,$amount);
                if($response['status_code'] == '200')
                {
                    $orderID                      = strtoupper(str_replace('.', '', uniqid('', true)));
                    $student                      = Auth::guard('students')->user();
                    $course_order                 = new \Workdo\LMS\Entities\CourseOrder();
                    $course_order->order_id       = $orderID;
                    $course_order->name           = $student->name;
                    $course_order->card_number    = '';
                    $course_order->card_exp_month = '';
                    $course_order->card_exp_year  = '';
                    $course_order->student_id     = $student->id;
                    $course_order->course         = json_encode($products);
                    $course_order->price          = $totalprice;
                    $course_order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                    $course_order->coupon_json    = json_encode($coupon);
                    $course_order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $course_order->price_currency = $this->currancy;
                    $course_order->txn_id         = $payload['transaction_id'];
                    $course_order->payment_type   = 'Khalti';
                    $course_order->payment_status = isset($result['data']['status']) ? $result['data']['status'] : 'succeeded';
                    $course_order->receipt        = '';
                    $course_order->store_id       = $store['id'];
                    $course_order->save();
                    foreach ($products as $course_id) {
                        $purchased_course             = new \Workdo\LMS\Entities\PurchasedCourse();
                        $purchased_course->course_id  = $course_id['product_id'];
                        $purchased_course->student_id = $student->id;
                        $purchased_course->order_id   = $course_order->id;
                        $purchased_course->save();

                        $student             = \Workdo\LMS\Entities\Student::where('id', $purchased_course->student_id)->first();
                        $student->courses_id = $purchased_course->course_id;
                        $student->save();
                    }
                    $type = 'coursepayment';

                    if (!empty($company_settings['New Course Order']) && $company_settings['New Course Order']  == true) {
                        $course      = \Workdo\LMS\Entities\Course::whereIn('id',$product_id)->get()->pluck('title');
                        $course_name = implode(', ', $course->toArray());
                        $user        = User::where('id',$store->created_by)->where('workspace_id',$store->workspace_id)->first();
                        $uArr        = [
                            'student_name' => $student->name,
                            'course_name'  => $course_name,
                            'store_name'   => $store->name,
                            'order_url'    => route('user.order',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt($course_order->id),]),
                        ];
                        try
                        {
                                // Send Email
                            $resp = EmailTemplate::sendEmailTemplate('New Course Order', [$user->id => $user->email], $uArr,$store->created_by);
                        }
                        catch(\Exception $e)
                        {
                            $resp['error'] = $e->getMessage();
                        }
                    }

                    event(new KhaltiPaymentStatus($store,$type,$course_order));
                    session()->forget($slug);
                    $arr = [
                        'status_code'    => $response['status_code'],
                        'store_complete' => route('store-complete.complete',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt($course_order->id),]),
                    ];
                    return $arr;
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function BookingPayWithKhalti(Request $request,$slug)
    {
        $hotel = Hotels::where('slug',$slug)->first();
        if($hotel){
            $data = $request->all();

            $grandTotal = $couponsId = 0;
            if (!auth()->guard('holiday')->user()) {
                // if (!auth()->guard('web')->user()) {
                $Carts = Cookie::get('cart');
                $Carts = json_decode($Carts, true);
                foreach ($Carts as $key => $value) {
                        //
                    $toDate   = \Carbon\Carbon::parse($value['check_in']);
                    $fromDate = \Carbon\Carbon::parse($value['check_out']);

                    $days = $toDate->diffInDays($fromDate);
                        //
                    $grandTotal += $value['price'] * $value['room'] * $days;
                    $grandTotal += ($value['serviceCharge']) ? $value['serviceCharge'] : 0;
                }
            } else {
                $Carts = RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->get();
                foreach ($Carts as $key => $value) {
                    $grandTotal += $value->price;                                          // * $value->room
                    $grandTotal += ($value->service_charge) ? $value->service_charge : 0;
                }
            }

            $price     = $grandTotal;
            $coupon_id = 0;
            if (!empty($request->coupon)) {
                $coupons = BookingCoupons::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun     = $coupons->used_coupon();
                    $discount_value = ($price / 100) * $coupons->discount;
                    $price          = $price - $discount_value;
                    $coupon_id      = $coupons->id;
                    if ($coupons->limit == $usedCoupun) {
                        return response()->json([
                            'success' => false,
                            'message' => __('This coupon code has expired.'),
                            'inputs'  => '',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __('This coupon code is invalid or has expired.'),
                        'inputs'  => '',
                    ]);
                }
            }
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            $data =[
                'price' => $price,
            ];

            return $data;
        }else{
            return redirect()->back()->with('error', __('Hotel Not found'));
        }
    }

    public function GetBookingPaymentStatus(Request $request,$slug)
    {
        $hotel                = Hotels::where('slug',$slug)->first();
        $hotelPaymentSettings = self::paymentConfig($hotel->created_by, $hotel->workspace);
        if($hotel)
        {
            $payload          = $request->payload;
            $secret           = $this->khalti_secret_key;
            $token            = $payload['token'];
            $amount           = $payload['amount'];
            $khalti           = new Khalti();
            $response         = $khalti->verifyPayment($secret,$token,$amount);
            $company_settings = getCompanyAllSetting($hotel->created_by,$hotel->workspace);
            $coupon           = BookingCoupons::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
            if (!empty($coupons)) {
                $userCoupon              = new UsedBookingCoupons();
                $userCoupon->customer_id = isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0;
                $userCoupon->coupon_id   = $coupons->id;
                $userCoupon->save();
                $usedCoupun = $coupons->used_coupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active = 0;
                    $coupons->save();
                }
            }
            if($response['status_code'] == '200')
            {
                if (!auth()->guard('holiday')->user()) {
                    $Carts          = Cookie::get('cart');
                    $Carts          = json_decode($Carts, true);
                    $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                    $booking        = RoomBooking::create([
                        'booking_number' => $booking_number,
                        'user_id'        => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                        'payment_method' => __('PayFast'),
                        'payment_status' => 1,
                        'invoice'        => null,
                        'workspace'      => $hotel->workspace,
                        'created_by'     => $hotel->created_by,
                        'total'          => isset($request->amount) ? $request->amount : 0,
                        'coupon_id'      => ($coupon) ? $coupon->id : 0,
                        'first_name'     => $request->firstname,
                        'last_name'      => $request->lastname,
                        'email'          => $request->email,
                        'phone'          => $request->phone,
                        'address'        => $request->address,
                        'city'           => $request->city,
                        'country'        => ($request->country) ? $request->country : 'india',
                        'zipcode'        => $request->zipcode,
                    ]);
                    foreach ($Carts as $key => $value) {
                            //
                        $toDate   = \Carbon\Carbon::parse($value['check_in']);
                        $fromDate = \Carbon\Carbon::parse($value['check_out']);

                        $days = $toDate->diffInDays($fromDate);
                            //
                        $bookingOrder = RoomBookingOrder::create([
                            'booking_id'     => $booking->id,
                            'customer_id'    => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                            'room_id'        => $value['room_id'],
                            'workspace'      => $value['workspace'],
                            'check_in'       => $value['check_in'],
                            'check_out'      => $value['check_out'],
                            'price'          => $value['price'] * $value['room'] * $days,
                            'room'           => $value['room'],
                            'service_charge' => $value['serviceCharge'],
                            'services'       => $value['serviceIds'],
                        ]);
                        unset($Carts[$key]);
                    }
                    $cart_json = json_encode($Carts);
                    Cookie::queue('cart', $cart_json, 1440);

                } else {
                    $Carts          = RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->get();
                    $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                    $booking        = RoomBooking::create([
                        'booking_number' => $booking_number,
                        'user_id'        => auth()->guard('holiday')->user()->id,
                        'payment_method' => __('PayFast'),
                        'payment_status' => 1,
                        'invoice'        => null,
                        'workspace'      => $hotel->workspace,
                        'created_by'     => $hotel->created_by,
                        'total'          => isset($request->amount) ? $request->amount : 0,
                        'coupon_id'      => ($coupon) ? $coupon->id : 0,
                    ]);
                    foreach ($Carts as $key => $value) {
                        $bookingOrder = RoomBookingOrder::create([
                            'booking_id'     => $booking->id,
                            'customer_id'    => auth()->guard('holiday')->user()->id,
                            'room_id'        => $value->room_id,
                            'workspace'      => $value->workspace,
                            'check_in'       => $value->check_in,
                            'check_out'      => $value->check_out,
                            'price'          => $value->price,                          // * $value->room
                            'room'           => $value->room,
                            'service_charge' => $value->service_charge,
                            'services'       => $value->services,
                        ]);
                    }
                    RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->delete();

                }
                event(new CreateRoomBooking($request,$booking));
                $type = "roombookinginvoice";
                event(new KhaltiPaymentStatus($hotel,$type,$booking));
            }

                //Email notification
            if(!empty($company_settings['New Room Booking By Hotel Customer']) && $company_settings['New Room Booking By Hotel Customer']  == true)
            {
                $user     = User::where('id',$hotel->created_by)->first();
                $customer = HotelCustomer::find($booking->user_id);
                $room     = \Workdo\Holidayz\Entities\Rooms::find($bookingOrder->room_id);
                $uArr     = [
                    'hotel_customer_name' => isset($customer->name) ? $customer->name : $booking->first_name,
                    'invoice_number'      => $booking->booking_number,
                    'check_in_date'       => $bookingOrder->check_in,
                    'check_out_date'      => $bookingOrder->check_out,
                    'room_type'           => $room->type,
                    'hotel_name'          => $hotel->name,
                ];

                try
                {
                    $resp = EmailTemplate::sendEmailTemplate('New Room Booking By Hotel Customer', [$user->email],$uArr);
                }
                catch(\Exception $e)
                {
                    $resp['error'] = $e->getMessage();
                }

                $arr = [
                    'status_code'    => $response['status_code'],
                    'store_complete' => route('hotel.home', $slug),
                ];

            }
            $arr = [
                'status_code'    => $response['status_code'],
                'store_complete' => route('hotel.home', $slug),
            ];
            return $arr;


        }
    }

        //Beauty Spa Payment
    public function BeautySpaPayWithKhalti(Request $request, $slug )
    {
       
        $workspace = WorkSpace::where('slug', $slug)->first();
        $service   = BeautyService::find($request->service_id);
        $price     = $service->price ?? 0;
        $offer     = BeautyServiceOffer::where('service', $service->id)
                ->where('workspace', $workspace->id)
                ->where('start_date', '<=', $request->date)
                ->where('end_date', '>=', $request->date)
                ->first();
        if ($offer) {
                $price = $offer->offer_price;
        }
        $finalPrice = BeautyBooking::total_amount($request->person, $price);

        if ($service) {
            $price         = $finalPrice;
            $data          = $request->all();
            $data['price'] = $price;
            $orderID       = strtoupper(str_replace('.', '', uniqid('', true)));
            return $data;
        } else {
            $msg = __('Service is deleted.');
            return $msg;
        }
    }

    public function getBeautySpaPaymentStatus(Request $request, $slug)
    {
        $workspace = WorkSpace::where('slug', $slug)->first();
        $this->paymentConfig($workspace->created_by,$workspace->workspace);
        try {
            $payload = $request->payload;
            if ($payload['status'] == '200') {

                $secret   = $this->khalti_secret_key;
                $token    = $payload['token'];
                $amount   = $payload['amount'];
                $khalti   = new Khalti();
                $response = $khalti->verifyPayment($secret,$token,$amount);

                $service = BeautyService::find($request->service_id);            
                $price = $service->price ?? 0;

                $offer = BeautyServiceOffer::where('service', $service->id)
                        ->where('workspace', $workspace->id)
                        ->where('start_date', '<=', $request->date)
                        ->where('end_date', '>=', $request->date)
                        ->first();

                if ($offer) {
                        $price = $offer->offer_price;
                }
                $finalPrice = BeautyBooking::total_amount($request->person, $price);


                $beautybooking                 = new BeautyBooking();
                $beautybooking->name           = $request['name'];
                $beautybooking->service        = $request['service_id'];
                $beautybooking->date           = $request['date'];
                $beautybooking->number         = $request['number'];
                $beautybooking->email          = $request['email'];
                $beautybooking->stage_id       = 2;
                $beautybooking->person         = $request['person'];
                $beautybooking->gender         = $request['gender'];
                $beautybooking->start_time     = $request['start_time'];
                $beautybooking->end_time       = $request['end_time'];
                $beautybooking->payment_option = $request['payment_option'];
                $beautybooking->reference      = $request['reference'];
                $beautybooking->notes          = $request['notes'];
                $beautybooking->price          = $finalPrice;
                $beautybooking->workspace      = $workspace->id;
                $beautybooking->created_by     = $workspace->created_by;
                $beautybooking->save();


                $beautyreceipt               = new BeautyReceipt();
                $beautyreceipt->booking_id   = $beautybooking->id;
                $beautyreceipt->name         = $beautybooking->name;
                $beautyreceipt->service      = $beautybooking->service;
                $beautyreceipt->number       = $beautybooking->number;
                $beautyreceipt->gender       = $beautybooking->gender;
                $beautyreceipt->start_time   = $beautybooking->start_time;
                $beautyreceipt->end_time     = $beautybooking->end_time;
                $beautyreceipt->price        = $beautybooking->price;
                $beautyreceipt->payment_type = __('Khalti');
                $beautyreceipt->workspace    = $workspace->id;
                $beautyreceipt->created_by   = $workspace->created_by;
                $beautyreceipt->save();

                $type = 'beautypayment';

                event(new KhaltiPaymentStatus($beautybooking, $type, $beautyreceipt));
                $data = [
                    'status_code'   => $response['status_code'],
                    'evevt_booking' => route('beauty.spa.booking.confirm',['slug' => $slug,
                    'id'      => Crypt::encrypt($beautybooking->id),
                    ]),
                ];
                return $data;
            } else {

                $msg = __('Transaction has been failed');
                return redirect()->back()->with('msg', $msg);
            }
        } catch (\Exception $exception) {
            $msg = __('Transaction has been failed');
            return redirect()->back()->with('msg', $msg);
        }
    }

         //Bookings Payment
     public function BookingsPayWithKhalti(Request $request,$slug)
     {
        $package = BookingsPackage::find($request->package);
        if ($package)
        {
            try {
                $data          = $request->all();
                $data['price'] = $package->price;
                return $data;
             } catch (\Exception $e) {
                     \Log::debug($e->getMessage());
             }
             return view('bookings::Appointments.style',compact('stripe_session','package'));
         } else {
             $msg = __('Package is deleted.');
             return redirect()->back()->with('msg', $msg);
         }
     }

     public function getBookingsPaymentStatus(Request $request,$slug)
     {
        $workspace = WorkSpace::where('id', $slug)->first();
        try
        {
            $stripe         = new \Stripe\StripeClient(!empty(company_setting('stripe_secret',$workspace->created_by, $workspace->id)) ? company_setting('stripe_secret',$workspace->created_by, $workspace->id) : '');
            $paymentIntents = $stripe->paymentIntents->retrieve(
                $request->session()->get('stripe_session')->payment_intent,
                []
                );
                $receipt_url = $paymentIntents->charges->data[0]->receipt_url;
        }
        catch(\Exception $exception)
        {
            $receipt_url = "";
        }
        try {
            if ($request->return_type == 'success' && !empty(session()->get('bookings_variable')))
            {
                $data             = session()->get('bookings_variable');
                $package          = BookingsPackage:: find($data['package']);
                $bookingscustomer = BookingsCustomer::where('email', $data['email'])->first();
                if (!empty($bookingscustomer)) {

                    $bookingscustomer->name       = isset($data['name']) ? $data['name'] : $bookingscustomer->name;
                    $bookingscustomer->client_id  = isset($data['client_name']) ? $data['client_name'] : $bookingscustomer['client_id'];
                    $bookingscustomer->number     = isset($data['number']) ? $data['number'] : $bookingscustomer->number;
                    $bookingscustomer->customer   = isset($data['customer']) ? $data['customer'] : $bookingscustomer->customer;
                    $bookingscustomer->workspace  = $workspace->id;
                    $bookingscustomer->created_by = $workspace->created_by;
                    $bookingscustomer->save();
                } else {

                    $bookingscustomer             = new BookingsCustomer();
                    $bookingscustomer->name       = isset($data['name']) ? $data['name'] : '';
                    $bookingscustomer->client_id  = isset($data['client_name']) ? $data['client_name'] : '';
                    $bookingscustomer->number     = isset($data['number']) ? $data['number'] : '';
                    $bookingscustomer->email      = isset($data['email']) ? $data['email'] : '';
                    $bookingscustomer->customer   = isset($data['customer']) ? $data['customer'] : '';
                    $bookingscustomer->workspace  = $workspace->id;
                    $bookingscustomer->created_by = $workspace->created_by;
                    $bookingscustomer->save();
                }

                $bookingsappointment                 = new BookingsAppointment();
                $bookingsappointment->appointment_id = $this->AppointmentNumber($slug);

                $bookingsappointment->date          = isset($data['date']) ? $data['date'] :'-';
                $bookingsappointment->service       = isset($data['service']) ? $data['service'] :'-';
                $bookingsappointment->package       = isset($data['package']) ? $data['package'] :'-';
                $bookingsappointment->staff         = isset($data['staff']) ? $data['staff'] :'-';
                $bookingsappointment->client_id     = isset($data['client_id']) ? $data['client_id'] : $bookingscustomer->id ;
                $bookingsappointment->start_time    = isset($data['start_time']) ? $data['start_time'] :'-';
                $bookingsappointment->end_time      = isset($data['end_time']) ? $data['end_time'] :'-';
                $bookingsappointment->your_country  = isset($data['your_country']) ? $data['your_country'] :'-';
                $bookingsappointment->your_state    = isset($data['your_state']) ? $data['your_state'] :'-';
                $bookingsappointment->your_city     = isset($data['your_city']) ? $data['your_city'] :'-';
                $bookingsappointment->your_address  = isset($data['your_address']) ? $data['your_address'] :'-';
                $bookingsappointment->your_zip_code = isset($data['your_zip_code']) ? $data['your_zip_code'] :'-';
                $bookingsappointment->our_country   = isset($data['our_country']) ? $data['our_country'] :'-';
                $bookingsappointment->our_state     = isset($data['our_state']) ? $data['our_state'] :'-';
                $bookingsappointment->our_city      = isset($data['our_city']) ? $data['our_city'] :'-';
                $bookingsappointment->our_zip_code  = isset($data['our_zip_code']) ? $data['our_zip_code'] :'-';
                $bookingsappointment->payment       = 'Stripe';
                $bookingsappointment->stage_id      = 2;
                $bookingsappointment->workspace     = $workspace->id;
                $bookingsappointment->created_by    = $workspace->created_by;
                $bookingsappointment->save();

                $type = 'bookingspayment';

                event(new KhaltiPaymentStatus($package, $type ,$bookingsappointment));
                $msg = __('Payment has been success.');
                return redirect()->back()->with('msg', $msg);

            } else {
                $msg = __('Transaction has been failed');
                return redirect()->back()->with('msg', $msg);
            }
        } catch (\Exception $exception) {
                $msg = __('Transaction has been failed');
                return redirect()->back()->with('msg', $msg);
        }
    }

    public function contentPayWithKhalti(Request $request,$slug)
    {
        $store = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
        $cart  = session()->get($slug);

        $this->paymentConfig($store->created_by, $store->wokspace_id);
        $company_settings = getCompanyAllSetting($store->created_by, $store->workspace_id);
        if (!empty($cart)) {
            $products = $cart['products'];
        } else {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }
        if (isset($cart['coupon']['data_id'])) {
            $coupon = \Workdo\LMS\Entities\CourseCoupon::where('id', $cart['coupon']['data_id'])->first();
        } else {
            $coupon = '';
        }
        $product_name   = [];
        $product_id     = [];
        $totalprice     = 0;
        $sub_totalprice = 0;
        foreach ($products as $key => $product) {
            $product_name[]  = $product['product_name'];
            $product_id[]    = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }

        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }
        if ($products) {

            try {
                $payload  = $request->payload;
                $secret   = $this->khalti_secret_key;
                $token    = $payload['token'];
                $amount   = $payload['amount'];
                $khalti   = new Khalti();
                $response = $khalti->verifyPayment($secret,$token,$amount);
                if($response['status_code'] == '200')
                {
                    $customer                      = Auth::guard('customers')->user();
                    $content_order                 = new \Workdo\TVStudio\Entities\TVStudioOrder();
                    $content_order->order_id       = '#' . time();
                    $content_order->name           = $customer->name;
                    $content_order->card_number    = '';
                    $content_order->card_exp_month = '';
                    $content_order->card_exp_year  = '';
                    $content_order->customer_id    = $customer->id;
                    $content_order->content        = json_encode($products);
                    $content_order->price          = $totalprice;
                    $content_order->coupon         = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
                    $content_order->coupon_json    = json_encode(!empty($coupon) ? $coupon : '');
                    $content_order->discount_price = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $content_order->price_currency = !empty(company_setting('defult_currancy', $store->created_by, $store->workspace_id)) ? company_setting('defult_currancy', $store->created_by, $store->workspace_id) : 'USD';
                    $content_order->txn_id         = '';
                    $content_order->payment_type   = __('Khalti');
                    $content_order->payment_status = 'success';
                    $content_order->receipt        = null;
                    $content_order->store_id       = $store['id'];
                    $content_order->save();

                    foreach ($products as $course_id) {
                        $purchased_content              = new \Workdo\TVStudio\Entities\TVStudioPurchasedContent();
                        $purchased_content->content_id  = $course_id['product_id'];
                        $purchased_content->customer_id = $customer->id;
                        $purchased_content->order_id    = $content_order->id;
                        $purchased_content->save();

                        $student              = \Workdo\TVStudio\Entities\TVStudioCustomer::where('id', $purchased_content->customer_id)->first();
                        $student->contents_id = $purchased_content->content_id;
                        $student->save();
                    }
                    $type = 'coursepayment';

                    event(new KhaltiPaymentStatus($store,$type,$content_order));
                    session()->forget($slug);
                    $arr = [
                        'status_code'    => $response['status_code'],
                        'store_complete' => route('tv.store-complete.complete',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt($content_order->id),]),
                    ];
                    return $arr;
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function EventShowBookingPayWithKhalti(Request $request,$slug)
    {
        $event = EventsMange::find($request->event);
        if ($event) {
            $price         = $request->price * $request->person;
            $data['price'] = $price;
            return $data;
        } else {
            $msg = __('Service is deleted.');
            return $msg;
        }
    }

    public function getEventShowBookingPaymentStatus(Request $request,$slug)
    {
        $workspace = WorkSpace::where('slug', $slug)->first();
        $this->paymentConfig($workspace->created_by,$workspace->workspace);
        try {
            $payload = $request->payload;
            if ($payload['status'] == '200') {

                $secret   = $this->khalti_secret_key;
                $token    = $payload['token'];
                $price    = $request->price * $request->person;
                $amount   = $payload['amount'];
                $khalti   = new Khalti();
                $response = $khalti->verifyPayment($secret,$token,$amount);

                $orderID = crc32(uniqid('', true));

                $eventbooking                 = new EventsBookings();
                $eventbooking->name           = $request->name;
                $eventbooking->event          = $request->event;
                $eventbooking->date           = $request->date;
                $eventbooking->number         = $request->mobile_number;
                $eventbooking->email          = $request->email;
                $eventbooking->person         = $request->person;
                $eventbooking->start_time     = $request->start_time;
                $eventbooking->end_time       = $request->end_time;
                $eventbooking->payment_option = $request->payment_option;
                $eventbooking->workspace      = $workspace->id;
                $eventbooking->created_by     = $workspace->created_by;
                $eventbooking->save();




                $eventbookingorder               = new EventBookingOrder();
                $eventbookingorder->booking_id   = $eventbooking->id;
                $eventbookingorder->order_id     = $orderID;
                $eventbookingorder->name         = $eventbooking->name;
                $eventbookingorder->event_id     = $eventbooking->event;
                $eventbookingorder->number       = $eventbooking->number;
                $eventbookingorder->start_time   = $eventbooking->start_time;
                $eventbookingorder->end_time     = $eventbooking->end_time;
                $eventbookingorder->price        = $price;
                $eventbookingorder->payment_type = __('Khalti');
                $eventbookingorder->workspace    = $workspace->id;
                $eventbookingorder->created_by   = $workspace->created_by;
                $eventbookingorder->save();


                $type = 'eventshowpayment';

                event(new KhaltiPaymentStatus($eventbooking, $type, $eventbookingorder));
                $data = [
                    'status_code'   => $response['status_code'],
                    'evevt_booking' => route('event.print.ticket',['slug' => $slug,
                    'eventBooking'      => Crypt::encrypt($eventbooking->id),
                    'eventbookingorder' => Crypt::encrypt($eventbookingorder->id),
                ]),
                ];
                return $data;
            } else {
                $msg = __('Transaction has been failed');
                return redirect()->back()->with('msg', $msg);
            }
        } catch (\Exception $exception) {
            $msg = __('Transaction has been failed');
            return redirect()->back()->with('msg', $msg);
        }
    }

        //Facilities Payment
    public function FacilitiesPayWithKhalti(Request $request, $slug)
    {
        $service = FacilitiesService::find($request->service);
        $price   = $request->price;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($service)
        {
            $data['price'] = $price;
            return $data;
        } else {
            $msg = __('Plan is deleted.');
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function getFacilitiesPaymentStatus(Request $request, $slug)
    {
        $workspace = WorkSpace::where('id', $slug)->first();
        $this->paymentConfig($workspace->created_by,$workspace->workspace);
        try {
            $payload = $request->payload;
            if ($payload['status'] == '200') {

                $secret   = $this->khalti_secret_key;
                $token    = $payload['token'];
                $price    = $request->price * $request->person;
                $amount   = $payload['amount'];
                $khalti   = new Khalti();
                $response = $khalti->verifyPayment($secret,$token,$amount);

                $service = FacilitiesService::find($request['service_id']);

                $facilitiesBooking                 = new FacilitiesBooking();
                $facilitiesBooking->name           = $request->name;
                $facilitiesBooking->client_id      = 0;
                $facilitiesBooking->service        = $service->item_id ?? '';
                $facilitiesBooking->date           = $request->date;
                $facilitiesBooking->number         = $request->number;
                $facilitiesBooking->email          = $request->email;
                $facilitiesBooking->gender         = $request->gender;
                $facilitiesBooking->start_time     = $request->start_time;
                $facilitiesBooking->end_time       = $request->end_time;
                $facilitiesBooking->person         = $request->person;
                $facilitiesBooking->payment_option = $request->payment_option;
                $facilitiesBooking->stage_id       = 2;
                $facilitiesBooking->workspace      = $workspace->id;
                $facilitiesBooking->created_by     = $workspace->created_by;
                $facilitiesBooking->save();


                $facilitiesreceipt             = new FacilitiesReceipt();
                $facilitiesreceipt->booking_id = $facilitiesBooking->id;
                $facilitiesreceipt->name       = $facilitiesBooking->name;
                $facilitiesreceipt->client_id  = $facilitiesBooking->client_id;
                $facilitiesreceipt->service    = $facilitiesBooking->service;
                $facilitiesreceipt->number     = $facilitiesBooking->number;
                $facilitiesreceipt->gender     = $facilitiesBooking->gender;
                $facilitiesreceipt->start_time = $facilitiesBooking->start_time;
                $facilitiesreceipt->end_time   = $facilitiesBooking->end_time;
                $facilitiesreceipt->price      = $price;
                $facilitiesreceipt->workspace  = $workspace->id;
                $facilitiesreceipt->created_by = $workspace->created_by;
                $facilitiesreceipt->save();

                $type = 'facilitiespayment';

                event(new KhaltiPaymentStatus($facilitiesBooking, $type, $facilitiesreceipt));

                return $response;
            } else {
                $msg = __('Transaction has been failed');
                return redirect()->back()->with('msg', $msg);
            }
        } catch (\Exception $exception) {
            $msg = __('Transaction has been failed');
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function memberplanPayWithKhalti(Request $request)
    {
        $membershipplan = AssignMembershipPlan::where('id', $request->membershipplan_id)->first();

        $this->paymentConfig($membershipplan->created_by,$membershipplan->workspace);

        $payload  = $request->payload;
        $secret   = $this->khalti_secret_key;
        $token    = $payload['token'];
        $amount   = $payload['amount'];
        $khalti   = new Khalti();
        $response = $khalti->verifyPayment($secret,$token,$amount);

        if($response['status_code'] == '200')
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if (!empty($membershipplan))

            {
                try {
                    $membershipplan_payment               = new \Workdo\GymManagement\Entities\MembershipPlanPayment();
                    $membershipplan_payment->member_id    = !empty($membershipplan->member_id) ? $membershipplan->member_id : null;
                    $membershipplan_payment->user_id      = $membershipplan->user_id;
                    $membershipplan_payment->date         = date('Y-m-d');
                    $membershipplan_payment->amount       = isset($request->price) ? $request->price : 0;
                    $membershipplan_payment->order_id     = $orderID;
                    $membershipplan_payment->currency     = $this->currancy;
                    $membershipplan_payment->payment_type = __('Khalti');
                    $membershipplan_payment->receipt      = null;
                    $membershipplan_payment->save();

                    $type = 'membershipplan';

                    event(new KhaltiPaymentStatus($membershipplan, $type, $membershipplan_payment));
                    return $response;

                } catch (\Exception $e) {
                    return redirect()->route('pay.membership.plan', encrypt($membershipplan->user_id))->with('error', __('Transaction has been failed!'));
                }
            } else {


                return redirect()->route('pay.membership.plan', encrypt($membershipplan->user_id))->with('error', __('Membership Plan not found.'));
            }
        } else {

            return redirect()->back()->with('error', __('Transaction has been failed.'));
        }
    }

}
