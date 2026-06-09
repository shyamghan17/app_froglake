<?php

namespace Workdo\RepairManagementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Models\ProductServiceItem;

class RepairPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'tax',
        'description',
        'creator_id',
        'created_by',
    ];

    public static function total_quantity($type, $quantity, $product_id)
    {
        if (Module_is_active('ProductService')) {
            $product = ProductServiceItem::find($product_id);
            if (!empty($product)) {
                if (($product->type == 'parts')) {
                    $pro_quantity = $product->quantity;
                    if ($type == 'minus') {
                        $product->quantity = $pro_quantity - $quantity;
                    } else {
                        $product->quantity = $pro_quantity + $quantity;
                    }
                    $product->save();
                }
            }
        }
    }

    public function repairOrderRequest()
    {
        return $this->belongsTo(RepairOrderRequest::class, 'repair_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductServiceItem::class, 'product_id');
    }
}