<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;

class BookingReview extends Model
{
    protected $fillable = [
        'item_id',
        'name',
        'email',
        'comment',
        'rating',
        'created_by',
        'creator_id'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

      public function item()
    {
        return $this->belongsTo(ProductServiceItem::class, 'item_id');
    }
}