<?php

namespace Workdo\Esewa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Esewa extends Model
{
    use HasFactory;

    protected $fillable = [];
    private $merchant_id;
    private $env;
    private $base_url;

    public function __construct()
    {
        $this->merchant_id = config('esewa.scd');
        $this->env = config('esewa.env');
        $this->base_url = $this->env == 'Live' ? 'https://epay.esewa.com.np' : 'https://rc-epay.esewa.com.np';
    }

    public function esewaCheckout($amount, $tax_amount = 0, $service_charge = 0, $delivery_charge = 0, $order_id, $su, $fu)
    {
        try {
            $total_amount = $amount + $delivery_charge + $service_charge + $tax_amount;
            
            $payload = [
                'amount' => $total_amount,
                'failure_url' => $fu,
                'product_delivery_charge' => $delivery_charge,
                'product_service_charge' => $service_charge,
                'product_code' => $this->merchant_id,
                'signature' => $this->generateSignature($total_amount, $order_id),
                'signed_field_names' => 'total_amount,transaction_uuid,product_code',
                'success_url' => $su,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'transaction_uuid' => $order_id,
            ];

            return [
                'action' => $this->base_url . '/api/epay/main/v2/form',
                'method' => 'POST',
                'fields' => $payload
            ];
            
        } catch (\Exception $e) {
            throw new \Exception('eSewa payment initialization failed: ' . $e->getMessage());
        }
    }

    private function generateSignature($total_amount, $transaction_uuid)
    {
        $message = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$this->merchant_id}";
        return base64_encode(hash_hmac('sha256', $message, $this->getSecretKey(), true));
    }

    private function getSecretKey()
    {
        $admin_settings = getAdminAllSetting();
        return $admin_settings['esewa_secret_key'] ?? '';
    }
}
