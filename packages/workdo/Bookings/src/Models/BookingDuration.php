<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;

class BookingDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'duration',
        'total_slots',
        'created_by',
        'creator_id',
    ];

    public function item()
    {
        return $this->belongsTo(ProductServiceItem::class, 'item_id');
    }
}