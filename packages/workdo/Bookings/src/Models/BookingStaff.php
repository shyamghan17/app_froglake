<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;

class BookingStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'item_ids',
        'created_by',
        'creator_id',
    ];

    protected $appends = ['item_names'];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items()
    {
        $itemIds = explode(',', $this->item_ids);
        return ProductServiceItem::whereIn('id', $itemIds)->get();
    }

    public function getItemNamesAttribute()
    {
        return $this->items()->pluck('name')->implode(', ');
    }
}