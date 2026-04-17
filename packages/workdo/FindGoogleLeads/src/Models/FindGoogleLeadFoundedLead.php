<?php

namespace Workdo\FindGoogleLeads\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FindGoogleLeadFoundedLead extends Model
{
    use HasFactory;

    protected $table = 'findgooglelead_founded_leads';

    protected $fillable = [
        'name',
        'keywords',
        'address',
        'contact',
        'creator_id',
        'created_by',
    ];

    public function contacts()
    {
        return $this->hasMany(FindGoogleLeadFoundedLeadContact::class, 'founded_lead_id');
    }


}