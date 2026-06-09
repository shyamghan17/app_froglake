<?php

namespace Workdo\RepairManagementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Models\ProductServiceTax;

class RepairOrderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_quantity',
        'customer_name',
        'customer_email',
        'customer_mobile_no',
        'date',
        'expiry_date',
        'repair_technician',
        'location',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'expiry_date' => 'date',
            'repair_technician' => 'integer',
            'status' => 'integer'
        ];
    }

    public function invoice()
    {
        return $this->hasOne(RepairInvoice::class, 'repair_id');
    }

    public function repairParts()
    {
        return $this->hasMany(RepairPart::class, 'repair_id');
    }

    public function repairTechnician()
    {
        return $this->belongsTo(RepairTechnician::class, 'repair_technician');
    }

    public function getRepairCharge()
    {
        $repairCharge = 0;
        $invoice = $this->repairInvoice;
        if($invoice) {
            $repairCharge = $invoice->repair_charge;
        }
        return $repairCharge;
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->repairParts as $product) {
            $subTotal += ($product->price * $product->quantity);
        }
        return $subTotal;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->repairParts as $product)
        {
            $totalDiscount += $product->discount;
        }
        return $totalDiscount;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach ($this->repairParts as $product)
        {
            if(Module_is_active('ProductService'))
            {
                $taxes = $this->totalTaxRate($product->tax);
            }
            else
            {
                $taxes = 0;
            }
            $totalTax += ($taxes / 100) * (($product->price * $product->quantity) - $product->discount);
        }
        return $totalTax;
    }

    public static function taxRate($taxRate, $price, $quantity, $discount = 0)
    {
        return ($taxRate / 100) * (($price * $quantity) - $discount);
    }

    public static function tax($taxes)
    {
        if(Module_is_active('ProductService'))
        {
            $taxArr = explode(',', $taxes);
            $taxes  = [];
            foreach($taxArr as $tax)
            {
                $taxes[] = ProductServiceTax::find($tax);
            }

            return $taxes;
        }
        else
        {
            return [];
        }
    }

    public static function totalTaxRate($taxes)
    {
        if(Module_is_active('ProductService'))
        {
            $taxArr  = explode(',', $taxes);
            $taxRate = 0;
            foreach($taxArr as $tax)
            {
                $tax     = ProductServiceTax::find($tax);
                $taxRate += !empty($tax->rate) ? $tax->rate : 0;
            }
            return $taxRate;
        }
        else
        {
            return 0;
        }
    }

    public function getTotal($repairCharge = null)
    {
        if($repairCharge === null) {
            $repairCharge = $this->getRepairCharge();
        }
        return ($this->getSubTotal() - $this->getTotalDiscount() + $this->getTotalTax() + $repairCharge);
    }

    public function getDue()
    {
        $due = 0;
        $payments = $this->hasMany(RepairInvoicePayment::class, 'repair_id')->get();
        foreach ($payments as $payment)
        {
            $due += $payment->amount;
        }
        return ($this->getTotal() - $due);
    }

    public function payments()
    {
        return $this->hasMany(RepairInvoicePayment::class, 'repair_id');
    }

    public function repairInvoice()
    {
        return $this->hasOne(RepairInvoice::class, 'repair_id');
    }

    public function movementHistories()
    {
        return $this->hasMany(RepairMovementHistory::class, 'repair_order_request_id');
    }

    public static function getStatuses()
    {
        return collect([
            ['id' => 0, 'name' => __('Pending')],
            ['id' => 1, 'name' => __('Start Repairing')],
            ['id' => 2, 'name' => __('End Repairing')],
            ['id' => 3, 'name' => __('Start Testing')],
            ['id' => 4, 'name' => __('End Testing')],
            ['id' => 5, 'name' => __('Irrepairable')],
            ['id' => 6, 'name' => __('Cancel')],
            ['id' => 7, 'name' => __('Invoice Created')]
        ]);
    }
}