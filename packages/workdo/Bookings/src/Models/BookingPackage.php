<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;

class BookingPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'item_id',
        'services',
        'delivery_time',
        'delivery_period',
        'price',
        'created_by',
        'creator_id',
    ];

    public function item()
    {
        return $this->belongsTo(ProductServiceItem::class, 'item_id');
    }
}