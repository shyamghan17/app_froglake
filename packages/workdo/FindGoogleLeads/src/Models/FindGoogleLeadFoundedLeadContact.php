<?php

namespace Workdo\FindGoogleLeads\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FindGoogleLeadFoundedLeadContact extends Model
{
    use HasFactory;

    protected $table = 'findgooglelead_founded_lead_contacts';

    protected $fillable = [
        'founded_lead_id',
        'is_lead',
        'is_sync',
        'name',
        'email',
        'mobile_no',
        'website',
        'address',
        'creator_id',
        'created_by',
    ];

    public function foundedLead()
    {
        return $this->belongsTo(FindGoogleLeadFoundedLead::class, 'founded_lead_id');
    }


}
