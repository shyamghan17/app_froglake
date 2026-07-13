<?php

namespace Workdo\NoticeBoard\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\NoticeBoard\Models\Notice;
use Workdo\NoticeBoard\Models\NoticeComment;
use Workdo\NoticeBoard\Models\NoticeRead;
use Carbon\Carbon;

class DemoNoticeCommentSeeder extends Seeder
{
    private function commentMap(): array
    {
        return [
            'Cafeteria Menu Update'                    => [
                [
                    'comment' => 'Great to see healthier options finally! Looking forward to trying the new menu.',
                    'replies' => [
                        'Agreed! The salad bar addition is a welcome change.',
                        'Hope they keep the pricing reasonable too.',
                    ],
                ],
                [
                    'comment' => 'Will the old menu items still be available alongside the new ones?',
                    'replies' => [
                        'Yes, the classic items will remain. Only new options are added.',
                    ],
                ],
                [
                    'comment' => 'Can we get a printed copy of the updated menu at the cafeteria entrance?',
                    'replies' => [],
                ],
            ],

            'Mandatory Compliance Training'            => [
                [
                    'comment' => 'Is the training available online or do we need to attend in person?',
                    'replies' => [
                        'It is fully online. Login with your employee credentials on the learning portal.',
                        'The link was shared via email last Monday. Check your inbox.',
                    ],
                ],
                [
                    'comment' => 'I completed mine this morning. Takes about 45 minutes to finish.',
                    'replies' => [
                        'Thanks for the heads up on the time estimate!',
                    ],
                ],
                [
                    'comment' => 'What happens if someone misses the Friday deadline due to leave?',
                    'replies' => [
                        'Please inform HR in advance and an extension may be granted.',
                    ],
                ],
            ],

            'Department Budget Review'                 => [
                [
                    'comment' => 'Should we bring soft copies or printed expense reports to the meeting?',
                    'replies' => [
                        'Both preferred. Soft copy for the shared drive, printed for discussion.',
                    ],
                ],
                [
                    'comment' => 'Will the meeting be recorded for those who cannot attend in person?',
                    'replies' => [
                        'Yes, it will be recorded and shared on the internal portal afterwards.',
                        'Please still send your expense reports before the meeting regardless.',
                    ],
                ],
                [
                    'comment' => 'Which format should the expense report be in — Excel or PDF?',
                    'replies' => [
                        'Excel is preferred so we can consolidate all data in one sheet.',
                    ],
                ],
            ],

            'HR Department: Payroll Submission'        => [
                [
                    'comment' => 'My timesheet access is locked. Who should I contact to fix this before EOD?',
                    'replies' => [
                        'Please reach out to IT helpdesk immediately. They can reset it quickly.',
                        'Alternatively, email your manager directly with your hours as a backup.',
                    ],
                ],
                [
                    'comment' => 'Does overtime logged this week get included in this payroll cycle?',
                    'replies' => [
                        'Yes, any hours logged until midnight today will be included.',
                    ],
                ],
            ],

            'Manager Update: New Reporting Template'   => [
                [
                    'comment' => 'Could you share the template link directly here for quick access?',
                    'replies' => [
                        'Check your email from HR dated Monday — direct download link is there.',
                        'Also available on the shared drive under /HR/Templates/2024.',
                    ],
                ],
                [
                    'comment' => 'Are there instructions on how to fill the new sections in the template?',
                    'replies' => [
                        'There is a guide document attached alongside the template in the email.',
                    ],
                ],
                [
                    'comment' => 'Should we use this template starting from this week or the next?',
                    'replies' => [
                        'Starting next Monday as mentioned in the notice.',
                    ],
                ],
            ],

            'Interview Panel Reminder'                 => [
                [
                    'comment' => 'Have the candidate profiles been shared yet? I have not received anything.',
                    'replies' => [
                        'Check the email from HR sent earlier today with the subject "Interview Brief".',
                        'If still not received, contact HR to resend it directly to you.',
                    ],
                ],
                [
                    'comment' => 'Is Conference Room B confirmed, or is there a chance it changes?',
                    'replies' => [
                        'Room is confirmed and booked. No changes expected.',
                    ],
                ],
            ],

            'Urgent: Client Presentation Prep'         => [
                [
                    'comment' => 'Should we coordinate on a single shared deck or prepare individual sections separately?',
                    'replies' => [
                        'Separate sections first, then we merge into one deck by Wednesday.',
                        'Use the company template available on the shared drive.',
                    ],
                ],
                [
                    'comment' => 'Who is the main point of contact for aligning the presentation content?',
                    'replies' => [
                        'Please coordinate directly with the project manager for this client.',
                    ],
                ],
                [
                    'comment' => 'Thursday EOD means 6 PM or end of business hours at 5 PM?',
                    'replies' => [
                        '5 PM sharp so the team has time to review before Friday.',
                    ],
                ],
            ],

            'Annual Company Picnic'                    => [
                [
                    'comment' => 'It was a fantastic event! The outdoor games were especially enjoyable.',
                    'replies' => [
                        'Completely agree! The tug of war was the highlight for our team.',
                        'Looking forward to an even bigger event next year.',
                    ],
                ],
                [
                    'comment' => 'A big thanks to the organizing committee for all the effort they put in.',
                    'replies' => [
                        'Yes, hats off to the team. Everything was well planned.',
                    ],
                ],
            ],

            'Upcoming: New Office Seating Arrangement' => [
                [
                    'comment' => 'Where can we view the updated seating chart before Monday?',
                    'replies' => [
                        'It will be posted on the physical notice board by Friday and also emailed.',
                    ],
                ],
                [
                    'comment' => 'Will personal items need to be moved over the weekend or on Monday morning?',
                    'replies' => [
                        'Please shift your belongings on Monday morning before 9 AM.',
                        'Boxes will be provided at the reception for packing.',
                    ],
                ],
                [
                    'comment' => 'Are there any dedicated quiet zones planned in the new arrangement?',
                    'replies' => [
                        'Yes, the east wing will be designated as a quiet working zone.',
                    ],
                ],
            ],
        ];
    }

    public function run($userId): void
    {
        if (NoticeComment::where('created_by', $userId)->exists()) {
            return;
        }

        $notices = Notice::where('created_by', $userId)
            ->where('allow_comments', true)
            ->where('status', '!=', 'draft')
            ->get();

        foreach ($notices as $notice) {
            $matched = null;
            foreach ($this->commentMap() as $keyword => $threads) {
                if (str_contains($notice->title, $keyword)) {
                    $matched = $threads;
                    break;
                }
            }

            if (!$matched) {
                continue;
            }

            // Only users who have actually read this notice can comment
            $readUserIds = NoticeRead::where('notice_id', $notice->id)
                ->whereNotNull('read_at')
                ->pluck('user_id')
                ->toArray();

            if (empty($readUserIds)) {
                continue;
            }

            $userCount = count($readUserIds);
            $baseDate  = Carbon::parse($notice->created_at);

            foreach ($matched as $threadIndex => $thread) {
                $commenterIndex = $threadIndex % $userCount;
                $commentUserId  = $readUserIds[$commenterIndex];
                $commentAt      = $baseDate->copy()->addDays($threadIndex + 1)->addHours(9);

                $comment = NoticeComment::create([
                    'notice_id'  => $notice->id,
                    'user_id'    => $commentUserId,
                    'parent_id'  => null,
                    'comment'    => $thread['comment'],
                    'creator_id' => $commentUserId,
                    'created_by' => $userId,
                    'created_at' => $commentAt,
                    'updated_at' => $commentAt,
                ]);

                foreach ($thread['replies'] as $replyIndex => $replyText) {
                    $replyAt = $commentAt->copy()->addHours(2 + ($replyIndex * 3));

                    NoticeComment::create([
                        'notice_id'  => $notice->id,
                        'user_id'    => $userId,
                        'parent_id'  => $comment->id,
                        'comment'    => $replyText,
                        'creator_id' => $userId,
                        'created_by' => $userId,
                        'created_at' => $replyAt,
                        'updated_at' => $replyAt,
                    ]);
                }
            }
        }
    }
}
