<?php

namespace Workdo\NoticeBoard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => 'required|string|max:1000',
        ];
    }
}
