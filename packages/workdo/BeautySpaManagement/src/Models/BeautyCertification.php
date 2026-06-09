<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\BeautySpaManagement\Models\BeautyTraining;

class BeautyCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'certificate_name',
        'issued_date',
        'expiry_date',
        'training_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expiry_date' => 'date'
        ];
    }



    public function training()
    {
        return $this->belongsTo(BeautyTraining::class);
    }
}