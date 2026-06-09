<?php

namespace Workdo\Esewa\Services;

use Illuminate\Support\Facades\Session;


class EsewaService
{
    private $merchantId;
    private $secretKey;
    private $baseUrl;

    public function __construct($userId = null, $isPlan = false)
    {
        $setting = $isPlan ? getAdminAllSetting() : ($userId ? getCompanyAllSetting($userId) : []);

        $this->merchantId = $setting['esewa_merchant_id'] ?? '';
        $this->secretKey = $setting['esewa_secret_key'] ?? '';
        $this->baseUrl = (($setting['esewa_mode'] ?? '') === 'live')
            ? 'https://epay.esewa.com.np'
            : 'https://rc-epay.esewa.com.np';
    }

    public function checkout($amount, $callback_url, $orderID, $session = [])
    {
        try {
            $formData = [
                'amount' => $amount,
                'tax_amount' => 0,
                'total_amount' => $amount,
                'transaction_uuid' => $orderID,
                'product_code' => $this->merchantId,
                'product_service_charge' => 0,
                'product_delivery_charge' => 0,
                'success_url' => $callback_url,
                'failure_url' => $callback_url,
                'signed_field_names' => 'total_amount,transaction_uuid,product_code',
                'signature' => $this->generateSignature($amount, $orderID),
            ];

            Session::put($orderID, $session);

            return [
                'action_url' => $this->baseUrl . '/api/epay/main/v2/form',
                'method' => 'POST',
                'form_data' => $formData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' =>  __('eSewa payment initialization failed: ') . $e->getMessage()
            ];
        }
    }

    public function isPaymentSuccessful($request): bool
    {
        $paymentData = json_decode(base64_decode($request->get('data')), true) ?? null;
        return !empty($paymentData) &&  isset($paymentData['status']) && $paymentData['status'] === 'COMPLETE';
    }

    private function generateSignature($totalAmount, $orderID)
    {
        $message = "total_amount={$totalAmount},transaction_uuid={$orderID},product_code={$this->merchantId}";
        return base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));
    }
}
