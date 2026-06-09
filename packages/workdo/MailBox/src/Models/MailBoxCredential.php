<?php

namespace Workdo\MailBox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MailBoxCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'from_name',
        'mail_driver',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'imap_port' => 'integer',
        'smtp_port' => 'integer',
        'password' => 'encrypted'
    ];
}