<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'name',
        'email',
        'mobile_no',
        'team_member_ids',
        'booking_start_date',
        'booking_end_date',
        'service_id',
        'price',
        'status',
        'payment_status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'team_member_ids'    => 'array',
            'booking_start_date' => 'datetime',
            'booking_end_date'   => 'datetime',
            'price'              => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->appointment_number)) {
                $appointment->appointment_number = static::generateAppointmentNumber($appointment->created_by);
            }
        });
    }

    public static function generateAppointmentNumber($created_by): string
    {
        $year  = date('Y');
        $month = date('m');
        $last  = static::where('appointment_number', 'like', "PSA-{$year}-{$month}-%")
            ->where('created_by', $created_by)
            ->orderBy('appointment_number', 'desc')
            ->first();

        $next = $last ? ((int) substr($last->appointment_number, -3)) + 1 : 1;

        return "PSA-{$year}-{$month}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function service()
    {
        return $this->belongsTo(PhotoStudioService::class, 'service_id');
    }

    public function teamMembers()
    {
        if (empty($this->team_member_ids)) return collect();
        return PhotoStudioTeamMember::with('user:id,name')
            ->whereIn('id', $this->team_member_ids)
            ->get();
    }
}
