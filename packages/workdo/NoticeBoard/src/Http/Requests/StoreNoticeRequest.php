<?php

namespace Workdo\NoticeBoard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                  => 'required|string|max:255',
            'description'            => 'required|string',
            'attachments'            => 'nullable|array',
            'attachments.*'          => 'string',
            'start_date'             => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'expiry_date'            => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'priority'               => 'required|in:normal,urgent,critical',
            'require_acknowledgment' => 'boolean',
            'target_type'            => 'required|in:all,department,role,specific_users',
            'target_ids'             => 'required_unless:target_type,all|array',
            'target_ids.*'           => 'integer',
            'allow_comments'         => 'boolean',
        ];
    }
}
