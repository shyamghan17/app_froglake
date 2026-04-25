<?php

namespace Workdo\Esewa\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Esewa\Events\EsewaPaymentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Workdo\Account\Entities\BankAccount;
use Workdo\Esewa\Entities\Esewa as EntitiesEsewa;

class EsewaController extends Controller
{
    public $currancy;
    public $invoiceData;
    public $esewa_payment_is_on;
    public $esewa_mode;
    public $esewa_merchant_id;
    public $is_esewa_enabled;

    public function setting(Request $request)
    {
        if (\Auth::user()->isAbleTo('esewa manage')) {
            if ($request->has('esewa_payment_is_on')) {
                $validator = \Validator::make($request->all(), [
                    'esewa_merchant_id' => 'required|string',
                    'esewa_secret_key'  => 'required|string',
                    'esewa_mode'        => 'required|string',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->getMessageBag()->first());
                }
            }

            $post = $request->all();
            unset($post['_token']);
            unset($post['_method']);

            if ($request->has('esewa_payment_is_on')) {
                foreach ($post as $key => $value) {
                    $data = [
                        'key'        => $key,
                        'workspace'  => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            } else {
                $data = [
                    'key'        => 'esewa_payment_is_on',
                    'workspace'  => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                Setting::updateOrInsert($data, ['value' => 'off']);
            }

            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', __('The esewa setting has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function makePayment($pay)
    {
        try {
            config([
                'esewa.scd' => $pay['merchant_id'],
                'esewa.env' => $pay['mode'],
            ]);

            $payment  = new EntitiesEsewa;
            $formData = $payment->esewaCheckout($pay['price'], 0, 0, 0, $pay['order_id'], $pay['successURL'], $pay['faildURL']);
            return ['status' => 'true', 'form_data' => $formData, 'message' => 'SUCCESS'];
        } catch (\Exception $e) {
            return ['status' => 'false', 'url' => $pay['faildURL'], 'message' => $e->getMessage()];
        }
    }

    public function paymentConfig($id = null, $workspace = Null)
    {
        $company_settings          = getCompanyAllSetting($id, $workspace);
        $this->currancy            = isset($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : 'NPR';
        $this->esewa_payment_is_on = isset($company_settings['esewa_payment_is_on']) ? $company_settings['esewa_payment_is_on'] : 'off';

        $admin_settings = getAdminAllSetting();
        config([
            'esewa.scd'        => $admin_settings['esewa_merchant_id'] ?? '',
            'esewa.env'        => ucfirst($admin_settings['esewa_mode'] ?? 'Sandbox'),
            'esewa.secret_key' => $admin_settings['esewa_secret_key'] ?? '',
        ]);
        
        $this->esewa_mode        = $admin_settings['esewa_mode'] ?? 'Sandbox';
        $this->esewa_merchant_id = $admin_settings['esewa_merchant_id'] ?? '';
    }

    // plan
    public function planPayWithESewa(Request $request)
    {
        $plan              = Plan::find($request->plan_id);
        $user_counter      = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
        $workspace_counter = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
        $user_module       = !empty($request->user_module_input) ? $request->user_module_input : '0';
        $duration          = !empty($request->time_period) ? $request->time_period : 'Month';
        $user_module_price = 0;
        if (!empty($user_module) && $plan->custom_plan == 1) {
            $user_module_array = explode(',', $user_module);
            foreach ($user_module_array as $key => $value) {
                $temp              = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                $user_module_price = $user_module_price + $temp;
            }
        }
        $user_price = 0;
        if ($user_counter > 0) {
            $temp       = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
            $user_price = $user_counter * $temp;
        }
        $workspace_price = 0;
        if ($workspace_counter > 0) {
            $temp            = ($duration == 'Year') ? $plan->price_per_workspace_yearly : $plan->price_per_workspace_monthly;
            $workspace_price = $workspace_counter * $temp;
        }
        $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
        $counter    = [
            'user_counter'      => $user_counter,
            'workspace_counter' => $workspace_counter,
        ];


        $admin_settings = getAdminAllSetting();
        $admin_currancy = !empty($admin_settings['defult_currancy']) ? $admin_settings['defult_currancy'] : 'INR';
        if ($admin_currancy != 'NPR' ) {
            return redirect()->back()->with('error', __('Currency is not supported.'));
        }

        $order_id       = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($plan) {
            if ($request->coupon_code) {
                $plan_price = CheckCoupon($request->coupon_code, $plan_price, $plan->id);
            }

            $price = $plan_price + $user_module_price + $user_price + $workspace_price;
            if ($price <= 0) {
                $assignPlan = DirectAssignPlan($plan->id, $duration, $user_module, $counter, 'ESEWA', $request->coupon_code);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again,'));
                }
            }
            $session                = $request->toArray();
            $session['amount']      = $price;
            $session['user_module'] = $user_module;
            $session['counter']     = $counter;
            $session['duration']    = $duration;
            $session['order_id']    = $order_id;
            $request->session()->put($order_id, $session);

            try {
                $paymentData = $this->makePayment([
                    'merchant_id' => $admin_settings['esewa_merchant_id'] ?? '',
                    'mode'        => ucfirst($admin_settings['esewa_mode'] ?? 'Sandbox'),
                    'successURL'  => route('plan.get.esewa.status', ['order_id' => $order_id, 'plan_id' => $plan, 'status' => 'success']),
                    'faildURL'    => route('plan.get.esewa.status', ['order_id' => $order_id, 'plan_id' => $plan, 'status' => 'faild']),
                    'price'       => $price,
                    'order_id'    => $order_id,
                ]);

                if (isset($paymentData['status']) && $paymentData['status'] === 'true') {
                    return view('esewa::payment.form', ['formData' => $paymentData['form_data']]);
                } else {
                    return redirect()->route('plans.index')->with('error', __('Payment failed.'));
                }
            } catch (\Exception $th) {
                return redirect()->route('plans.index')->with('error', $th->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetESewaStatus(Request $request)
    {
        $status      = $request->status;
        $paymentData = null;
        
        if (strpos($status, '?data=') !== false) {
            $parts  = explode('?data=', $status);
            $status = $parts[0];
            if (isset($parts[1])) {
                $paymentData = json_decode(base64_decode($parts[1]), true);
            }
        }
        
        if ($status == 'success') {
            $user = \Auth::user();
            $plan = Plan::find($request->plan_id);
            
            if ($plan) {
                $admin_settings = getAdminAllSetting();
                
                if (!$this->verifyEsewaPayment($request, $paymentData)) {
                    return redirect()->route('plans.index')->with('error', __('Payment verification failed.'));
                }
                
                try {
                    $session  = (object) $request->session()->get($request->order_id);
                    $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
                    $statuses = __('succeeded');

                    $order = Order::create([
                        'order_id'       => $orderID,
                        'name'           => null,
                        'email'          => null,
                        'card_number'    => null,
                        'card_exp_month' => null,
                        'card_exp_year'  => null,
                        'plan_name'      => !empty($plan->name) ? $plan->name : 'Basic Package',
                        'plan_id'        => $plan->id,
                        'price'          => !empty($paymentData['total_amount']) ? $paymentData['total_amount'] : 0,
                        'price_currency' => $admin_settings['defult_currancy'],
                        'txn_id'         => $paymentData['transaction_code'] ?? '',
                        'payment_type'   => __('eSewa'),
                        'payment_status' => $statuses,
                        'receipt'        => null,
                        'user_id'        => $user->id,
                    ]);

                    $type       = 'Subscription';
                    $user       = User::find($user->id);
                    $assignPlan = $user->assignPlan($plan->id, $session->duration, $session->user_module, $session->counter);

                    if ($request->transaction_code) {
                        UserCoupon($request->transaction_code, $orderID);
                    }
                    
                    request()->session()->forget($request->order_id);
                    event(new EsewaPaymentStatus($plan, $type, $order));
                    
                    if ($assignPlan['is_success']) {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Your Payment has failed!'));
        }
    }

    private function verifyEsewaPayment($request, $paymentData = null)
    {
        try {
            if ($paymentData && isset($paymentData['status'])) {
                return $paymentData['status'] === 'COMPLETE';
            }
            
            $admin_settings = getAdminAllSetting();
            $env            = $admin_settings['esewa_mode'] ?? 'Sandbox';
            $base_url       = $env == 'Live' ? 'https://epay.esewa.com.np' : 'https://rc-epay.esewa.com.np';
            
            $response = Http::post($base_url . '/api/epay/transaction/status/', [
                'product_code'     => $admin_settings['esewa_merchant_id'],
                'total_amount'     => $paymentData['total_amount'] ?? $request->total_amount,
                'transaction_uuid' => $paymentData['transaction_uuid'] ?? $request->transaction_uuid,
            ]);
            
            if ($response->successful()) {
                       $data             = $response->json();
                return $data['status'] === 'COMPLETE';
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // invoice and retainer
    public function invoicePayWithESewa(Request $request)
    {
        $admin_settings = getAdminAllSetting();

        if ($request->type == "invoice") {
            $invoice  = \App\Models\Invoice::find($request->invoice_id);
            $user_id  = $invoice->created_by;
            $wokspace = $invoice->workspace;
        } elseif ($request->type == "retainer") {
            $invoice  = \Workdo\Retainer\Entities\Retainer::find($request->invoice_id);
            $user_id  = $invoice->created_by;
            $wokspace = $invoice->workspace;
        }

        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
        self::paymentConfig($user_id, $wokspace);
        if ($this->currancy != 'NPR' ) {
            return redirect()->back()->with('error', __('Currency is not supported.'));
        }
        
        if (isset($this->esewa_payment_is_on) && $this->esewa_payment_is_on == 'on' && !empty($this->esewa_merchant_id)) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'amount'     => 'required|numeric',
                    'invoice_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $invoice_id = $request->input('invoice_id');
            if ($request->type == "invoice") {

                $invoice       = \App\Models\Invoice::find($invoice_id);
                $invoice_payID = $invoice->invoice_id;
                $invoiceID     = $invoice->id;
                $printID       = \App\Models\Invoice::invoiceNumberFormat($invoice_payID, $user_id, $wokspace);
            } elseif ($request->type == "retainer") {
                $invoice       = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
                $invoice_payID = $invoice->invoice_id;
                $invoiceID     = $invoice->id;
                $printID       = \Workdo\Retainer\Entities\Retainer::retainerNumberFormat($invoice_payID, $user_id, $wokspace);
            }

            if ($invoice) {

                $account = BankAccount::where(['created_by'=>$invoice->created_by,'workspace'=>$invoice->workspace])->where('payment_name','eSewa')->first();
                if(!$account)
                {
                    if ($request->type == 'invoice') {
                        return redirect()->route('pay.invoice', encrypt($invoiceID))->with('error', __('Bank account not connected with eSewa.'));
                    } elseif ($request->type == 'retainer') {
                        return redirect()->route('pay.retainer', encrypt($invoiceID))->with('error', __('Bank account not connected with eSewa.'));
                    }
                }

                $price = $request->amount;
                
                $session_data = [
                    'type'       => $request->type,
                    'invoice_id' => $invoiceID,
                    'amount'     => $price,
                    'order_id'   => $order_id
                ];
                $request->session()->put($order_id, $session_data);
                
                try {
                    $paymentData = $this->makePayment([
                        'merchant_id' => $admin_settings['esewa_merchant_id'] ?? '',
                        'mode'        => ucfirst($admin_settings['esewa_mode'] ?? 'Sandbox'),
                        'successURL'  => route('invoice.esewa.status', ['order_id' => $order_id, 'status' => 'success']),
                        'faildURL'    => route('invoice.esewa.status', ['order_id' => $order_id, 'status' => 'failed']),
                        'price'       => $price,
                        'order_id'    => $order_id,
                    ]);

                    if (isset($paymentData['status']) && $paymentData['status'] === 'true') {
                        return view('esewa::payment.form', ['formData' => $paymentData['form_data']]);
                    } else {
                        $request->session()->forget($order_id);
                        if ($request->type == 'invoice') {
                            return redirect()->route('pay.invoice', encrypt($invoiceID))->with('error', __('Payment failed.'));
                        } else {
                            return redirect()->route('pay.retainer', encrypt($invoiceID))->with('error', __('Payment failed.'));
                        }
                    }
                } catch (\Exception $e) {
                    $request->session()->forget($order_id);
                    if ($request->type == 'invoice') {
                        return redirect()->route('pay.invoice', encrypt($invoiceID))->with('error', $e->getMessage());
                    } else {
                        return redirect()->route('pay.retainer', encrypt($invoiceID))->with('error', $e->getMessage());
                    }
                }
            } else {
                if ($request->type == 'invoice') {

                    return redirect()->route('pay.invoice', encrypt($invoiceID))->with('error', __('Invoice is deleted.'));
                } elseif ($request->type == 'retainer') {

                    return redirect()->route('pay.retainer', encrypt($invoiceID))->with('error', __('Retainer is deleted.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Please Enter Esewa Details.'));
        }
    }

    public function invoiceGetESewaStatus(Request $request)
    {
        $order_id = $request->order_id;
        $status   = $request->status;
        
        $paymentData = null;
        if (strpos($status, '?data=') !== false) {
            $parts  = explode('?data=', $status);
            $status = $parts[0];
            if (isset($parts[1])) {
                $paymentData = json_decode(base64_decode($parts[1]), true);
            }
        }
        
        $session = $request->session()->get($order_id);
        if (!$session) {
            return redirect()->back()->with('error', __('Payment session expired.'));
        }
        
        $type       = $session['type'];
        $invoice_id = $session['invoice_id'];
        $amount     = $session['amount'];
        
        if ($status != 'success') {
            $request->session()->forget($order_id);
            if ($type == 'invoice') {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('error', __('Payment was cancelled or failed.'));
            } else {
                return redirect()->route('pay.retainer', encrypt($invoice_id))->with('error', __('Payment was cancelled or failed.'));
            }
        }
        
        if (!$this->verifyEsewaPayment($request, $paymentData)) {
            $request->session()->forget($order_id);
            if ($type == 'invoice') {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('error', __('Payment verification failed.'));
            } else {
                return redirect()->route('pay.retainer', encrypt($invoice_id))->with('error', __('Payment verification failed.'));
            }
        }

        if ($type == 'invoice') {
            $invoice = \App\Models\Invoice::find($invoice_id);
            $this->paymentConfig($invoice->created_by, $invoice->workspace);
            $this->invoiceData = $invoice;

            if ($invoice) {
                try {
                    $invoice_payment                 = new \App\Models\InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->account_id     = 0;
                    $invoice_payment->payment_method = 0;
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->order_id       = $order_id;
                    $invoice_payment->currency       = $this->currancy;
                    $invoice_payment->payment_type   = 'eSewa';
                    $invoice_payment->receipt        = '';
                    $invoice_payment->save();

                    $due = $invoice->getDue();
                    if ($due <= 0) {
                        $invoice->status = 4;
                        $invoice->save();
                    } else {
                        $invoice->status = 3;
                        $invoice->save();
                    }

                    $request->session()->forget($order_id);
                    event(new EsewaPaymentStatus($invoice, $type, $invoice_payment));
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully'));
                } catch (\Exception $e) {
                    $request->session()->forget($order_id);
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', $e->getMessage());
                }
            } else {
                $request->session()->forget($order_id);
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
            }
        } elseif ($type == 'retainer') {

            $retainer = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
            $this->paymentConfig($retainer->created_by, $retainer->workspace);

            $this->invoiceData = $retainer;
            if ($retainer) {
                
                try {
                    $retainer_payment                 = new \Workdo\Retainer\Entities\RetainerPayment();
                    $retainer_payment->retainer_id    = $invoice_id;
                    $retainer_payment->date           = Date('Y-m-d');
                    $retainer_payment->account_id     = 0;
                    $retainer_payment->payment_method = 0;
                    $retainer_payment->amount         = $amount;
                    $retainer_payment->order_id       = $order_id;
                    $retainer_payment->currency       = $this->currancy;
                    $retainer_payment->payment_type   = 'eSewa';
                    $retainer_payment->receipt        = '';
                    $retainer_payment->save();
                    $due = $retainer->getDue();

                    if ($due <= 0) {
                        $retainer->status = 5;
                        $retainer->save();
                    } else {
                        $retainer->status = 4;
                        $retainer->save();
                    }
                  
                    $request->session()->forget($order_id);
                    event(new EsewaPaymentStatus($retainer, $type, $retainer_payment));
                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully'));
                } catch (\Exception $e) {
                    $request->session()->forget($order_id);
                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', $e->getMessage());
                }
            } else {
                $request->session()->forget($order_id);
                return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Retainer not found.'));
            }
        } else {
            $request->session()->forget($order_id);
            return redirect()->back()->with('error', __('Invalid payment type.'));
        }
    }

    //lms cource payment
    public function coursePayWithEsewa(Request $request, $slug)
    {
        $cart = session()->get($slug);

        $store   = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
        $student = Auth::guard('students')->user();

        self::paymentConfig($store->created_by, $store->wokspace_id);
        if ($this->currancy != 'NPR' ) {
            return redirect()->back()->with('error', __('Currency is not supported.'));
        }

        $products       = $cart['products'];
        $sub_totalprice = 0;
        $totalprice     = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[]  = $product['product_name'];
            $product_id[]    = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }

        if (isset($cart['coupon'])) {
            $coupon = $cart['coupon']['coupon'];
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

        if ($totalprice <= 0) {
            $assignCourse = \Workdo\LMS\Entities\LmsUtility::DirectAssignCourse($store, 'Esewa');
            if ($assignCourse['is_success']) {
                return redirect()->route(
                    'store-complete.complete',
                    [
                        $store->slug,
                        \Illuminate\Support\Facades\Crypt::encrypt($assignCourse['courseorder_id']),
                    ]
                )->with('success', __('Transaction has been success'));
            } else {
                return redirect()->route('store.cart', $store->slug)->with('error', __('Something went wrong, Please try again,'));
            }
        }

        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
        
        $session_data = [
            'slug'       => $slug,
            'cart'       => $cart,
            'totalprice' => $totalprice,
            'order_id'   => $order_id
        ];
        $request->session()->put($order_id, $session_data);
        
        if ($products) {
            try {
                $admin_settings = getAdminAllSetting();
                $paymentData    = $this->makePayment([
                    'merchant_id' => $admin_settings['esewa_merchant_id'] ?? '',
                    'mode'        => ucfirst($admin_settings['esewa_mode'] ?? 'Sandbox'),
                    'successURL'  => route('course.esewa', ['order_id' => $order_id, 'status' => 'success']),
                    'faildURL'    => route('course.esewa', ['order_id' => $order_id, 'status' => 'failed']),
                    'price'       => $totalprice,
                    'order_id'    => $order_id,
                ]);

                if (isset($paymentData['status']) && $paymentData['status'] === 'true') {
                    return view('esewa::payment.form', ['formData' => $paymentData['form_data']]);
                } else {
                    $request->session()->forget($order_id);
                    return redirect()->route('store.cart', $store->slug)->with('error', __('Payment failed.'));
                }
            } catch (\Exception $e) {
                $request->session()->forget($order_id);
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('No products found.'));
        }
    }

    public function getCoursePaymentStatus(Request $request)
    {
        $order_id = $request->order_id;
        $status   = $request->status;
        
        $paymentData = null;
        if (strpos($status, '?data=') !== false) {
            $parts  = explode('?data=', $status);
            $status = $parts[0];
            if (isset($parts[1])) {
                $paymentData = json_decode(base64_decode($parts[1]), true);
            }
        }
        
        $session = $request->session()->get($order_id);
        if (!$session) {
            return redirect()->back()->with('error', __('Payment session expired.'));
        }
        
        $slug       = $session['slug'];
        $cart       = $session['cart'];
        $totalprice = $session['totalprice'];
        
        if ($status != 'success') {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Payment was cancelled or failed.'));
        }
        
        if (!$this->verifyEsewaPayment($request, $paymentData)) {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Payment verification failed.'));
        }
        
        try {
            $store      = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
            $coupon     = isset($cart['coupon']) ? $cart['coupon']['coupon'] : null;
            $products   = $cart['products'];
            $product_id = [];
            
            foreach ($products as $product) {
                $product_id[] = $product['id'];
            }            
            $student                      = Auth::guard('students')->user();
            $course_order                 = new \Workdo\LMS\Entities\CourseOrder();
            $course_order->order_id       = '#' . time();
            $course_order->name           = $student->name;
            $course_order->card_number    = '';
            $course_order->card_exp_month = '';
            $course_order->card_exp_year  = '';
            $course_order->student_id     = $student->id;
            $course_order->course         = json_encode($products);
            $course_order->price          = $totalprice;
            $course_order->coupon         = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
            $course_order->coupon_json    = json_encode(!empty($coupon) ? $coupon : '');
            $course_order->discount_price = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
            $course_order->price_currency = !empty(company_setting('defult_currancy', $store->created_by, $store->workspace_id)) ? company_setting('defult_currancy', $store->created_by, $store->workspace_id) : 'USD';
            $course_order->txn_id         = $paymentData['transaction_code'] ?? $order_id;
            $course_order->payment_type   = __('eSewa');
            $course_order->payment_status = 'success';
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
            
            if (!empty(company_setting('New Course Order', $store->created_by, $store->workspace_id)) && company_setting('New Course Order', $store->created_by, $store->workspace_id) == true) {
                $course      = \Workdo\LMS\Entities\Course::whereIn('id', $product_id)->get()->pluck('title');
                $course_name = implode(', ', $course->toArray());
                $user        = User::where('id', $store->created_by)->where('workspace_id', $store->workspace_id)->first();
                $uArr        = [
                    'student_name' => $student->name,
                    'course_name'  => $course_name,
                    'store_name'   => $store->name,
                    'order_url'    => route('user.order', [$store->slug, \Illuminate\Support\Facades\Crypt::encrypt($course_order->id),]),
                ];
                try {
                    EmailTemplate::sendEmailTemplate('New Course Order', [$user->id => $user->email], $uArr, $store->created_by);
                } catch (\Exception $e) {
                    return redirect()->back('error', $e->getMessage());
                }
            }
            
            $type = 'coursepayment';
            event(new EsewaPaymentStatus($store, $type, $course_order));

            $request->session()->forget($order_id);
            session()->forget($slug);

            return redirect()->route(
                'store-complete.complete',
                [
                    $store->slug,
                    \Illuminate\Support\Facades\Crypt::encrypt($course_order->id),
                ]
            )->with('success', __('Transaction has been success'));
            
        } catch (\Exception $e) {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Transaction has been failed.'));
        }
    }

    //Movie & TV Studio
    public function contentPayWithEsewa(Request $request, $slug)
    {
        $store    = \Workdo\TVStudio\Entities\TVStudioStore::where('slug', $slug)->first();
        $customer = Auth::guard('customers')->user();

        self::paymentConfig($store->created_by, $store->wokspace_id);
        if ($this->currancy != 'NPR' ) {
            return redirect()->back()->with('error', __('Currency is not supported.'));
        }

        $cart           = session()->get($slug);
        $products       = $cart['products'];
        $sub_totalprice = 0;
        $totalprice     = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[]  = $product['product_name'];
            $product_id[]    = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }
        if (isset($cart['coupon'])) {
            $coupon = $cart['coupon']['coupon'];
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

        if ($totalprice <= 0) {
            $assignCourse = \Workdo\TVStudio\Entities\TVStudioUtility::DirectAssignCourse($store, 'Esewa');
            if ($assignCourse['is_success']) {
                return redirect()->route(
                    'tv.store-complete.complete',
                    [
                        $store->slug,
                        \Illuminate\Support\Facades\Crypt::encrypt($assignCourse['courseorder_id']),
                    ]
                )->with('success', __('Transaction has been success'));
            } else {
                return redirect()->route('store.cart', $store->slug)->with('error', __('Something went wrong, Please try again,'));
            }
        }

        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
        
        $session_data = [
            'slug'       => $slug,
            'cart'       => $cart,
            'totalprice' => $totalprice,
            'order_id'   => $order_id
        ];
        $request->session()->put($order_id, $session_data);
        
        if ($products) {
            try {
                $admin_settings = getAdminAllSetting();
                $paymentData    = $this->makePayment([
                    'merchant_id' => $admin_settings['esewa_merchant_id'] ?? '',
                    'mode'        => ucfirst($admin_settings['esewa_mode'] ?? 'Sandbox'),
                    'successURL'  => route('content.esewa', ['order_id' => $order_id, 'status' => 'success']),
                    'faildURL'    => route('content.esewa', ['order_id' => $order_id, 'status' => 'failed']),
                    'price'       => $totalprice,
                    'order_id'    => $order_id,
                ]);

                if (isset($paymentData['status']) && $paymentData['status'] === 'true') {
                    return view('esewa::payment.form', ['formData' => $paymentData['form_data']]);
                } else {
                    $request->session()->forget($order_id);
                    return redirect()->route('store.cart', $store->slug)->with('error', __('Payment failed.'));
                }
            } catch (\Exception $e) {
                $request->session()->forget($order_id);
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('No products found.'));
        }
    }

    public function getContentPaymentStatus(Request $request)
    {
        $order_id = $request->order_id;
        $status   = $request->status;
        
        $paymentData = null;
        if (strpos($status, '?data=') !== false) {
            $parts  = explode('?data=', $status);
            $status = $parts[0];
            if (isset($parts[1])) {
                $paymentData = json_decode(base64_decode($parts[1]), true);
            }
        }
        
        $session = $request->session()->get($order_id);
        if (!$session) {
            return redirect()->back()->with('error', __('Payment session expired.'));
        }
        
        $slug       = $session['slug'];
        $cart       = $session['cart'];
        $totalprice = $session['totalprice'];
        
        if ($status != 'success') {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Payment was cancelled or failed.'));
        }
        
        if (!$this->verifyEsewaPayment($request, $paymentData)) {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Payment verification failed.'));
        }
        
        try {
            $store    = \Workdo\TVStudio\Entities\TVStudioStore::where('slug', $slug)->first();
            $coupon   = isset($cart['coupon']) ? $cart['coupon']['coupon'] : null;
            $products = $cart['products'];
            
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
            $content_order->txn_id         = $paymentData['transaction_code'] ?? $order_id;
            $content_order->payment_type   = __('eSewa');
            $content_order->payment_status = 'success';
            $content_order->receipt        = '';
            $content_order->store_id       = $store['id'];
            $content_order->save();

            foreach ($products as $course_id) {
                $purchased_content              = new \Workdo\TVStudio\Entities\TVStudioPurchasedContent();
                $purchased_content->content_id  = $course_id['product_id'];
                $purchased_content->customer_id = $customer->id;
                $purchased_content->order_id    = $content_order->id;
                $purchased_content->save();

                $customer_record              = \Workdo\TVStudio\Entities\TVStudioCustomer::where('id', $purchased_content->customer_id)->first();
                $customer_record->contents_id = $purchased_content->content_id;
                $customer_record->save();
            }

            $type = 'contentpayment';
            event(new EsewaPaymentStatus($store, $type, $content_order));

            $request->session()->forget($order_id);
            session()->forget($slug);

            return redirect()->route(
                'tv.store-complete.complete',
                [
                    $store->slug,
                    \Illuminate\Support\Facades\Crypt::encrypt($content_order->id),
                ]
            )->with('success', __('Transaction has been success'));
            
        } catch (\Exception $e) {
            $request->session()->forget($order_id);
            return redirect()->route('store.cart', $slug)->with('error', __('Transaction has been failed.'));
        }
    }
}

