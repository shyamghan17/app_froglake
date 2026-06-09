<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Performance\Events\CreateEmployeeReview;
use Workdo\SMS\Services\SendSMS;

class CreateEmployeeReviewLis
{
    public function handle(CreateEmployeeReview $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Employee Review') == 'on') {
            $review = $event->review;
            if ($review->user && $review->user->mobile_no) {
                $uArr = [
                    'employee_name' => $review->user->name ?? '',
                    'reviewer_name' => $review->reviewer->name ?? '',
                    'company_name' => $review->createdBy->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Employee Review', $review->user->mobile_no, $review->created_by);
            }
        }
    }
}
