<?php

namespace Workdo\NoticeBoard\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Workdo\NoticeBoard\Models\Notice;
use Workdo\NoticeBoard\Models\NoticeTarget;
use Workdo\NoticeBoard\Models\NoticeRead;
use Carbon\Carbon;

class DemoNoticeSeeder extends Seeder
{
    public function run($userId): void
    {
        if (Notice::where('created_by', $userId)->exists()) {
            return;
        }

        if (empty($userId)) {
            return;
        }

        $departmentIds = collect();
        $roleIds       = Role::where('created_by', $userId)->pluck('id');
        $userIds       = User::where('created_by', $userId)->where('id', '!=', $userId)->pluck('id');

        if (class_exists('\Workdo\Hrm\Models\Department')) {
            $departmentIds = \Workdo\Hrm\Models\Department::where('created_by', $userId)->pluck('id');
        }

        // Stable slices — no random()
        $deptIds    = $departmentIds->take(2)->values()->filter()->toArray();
        $roleIdList = $roleIds->take(2)->values()->filter()->toArray();
        $firstUsers = $userIds->take(3)->values()->filter()->toArray();
        $nextUsers  = $userIds->skip(3)->take(3)->values()->filter()->toArray() ?: $firstUsers;

        $today = Carbon::today();

        $noticeRecords = [
            // ---- DRAFT (future start dates) ----
            [
                'title'                  => 'Draft: Upcoming Team Building Event',
                'description'            => '<p>We are planning a team building event next month. Details will be shared soon. Please save the date and plan accordingly.</p>',
                'start_date'             => $today->copy()->addDays(18)->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(4)->addDays(9)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'draft',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(2),
            ],
            [
                'title'                  => 'Draft: Revised Leave Policy 2024',
                'description'            => '<p>The HR department has revised the leave policy effective from next quarter. Please review before it is published.</p>',
                'start_date'             => $today->copy()->addDays(11)->toDateString(),
                'expiry_date'            => null,
                'priority'               => 'urgent',
                'target_type'            => 'all',
                'status'                 => 'draft',
                'is_pinned'              => true,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(1),
            ],
            [
                'title'                  => 'Draft: Emergency Evacuation Drill Procedure',
                'description'            => '<p>A new emergency evacuation procedure has been drafted. All employees must read and acknowledge once published.</p>',
                'start_date'             => $today->copy()->addDays(6)->toDateString(),
                'expiry_date'            => null,
                'priority'               => 'critical',
                'target_type'            => 'all',
                'status'                 => 'draft',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => true,
                'date'                   => $today->copy()->subHours(6),
            ],

            // ---- PUBLISHED + LONG-LIVED (target: all) ----
            [
                'title'                  => 'Cafeteria Menu Update – New Options Available',
                'description'            => '<p>The cafeteria has introduced new healthy meal options starting this week. Visit the cafeteria notice board for the full updated menu.</p>',
                'start_date'             => $today->copy()->subDays(4)->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(6)->addDays(17)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(4),
            ],
            [
                'title'                  => 'Office WiFi Password Updated',
                'description'            => '<p>The office WiFi password has been updated for security purposes. Please contact IT support to get the new password.</p>',
                'start_date'             => $today->copy()->subDays(13)->toDateString(),
                'expiry_date'            => null,
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(13),
            ],
            [
                'title'                  => 'Mandatory Compliance Training – Deadline This Friday',
                'description'            => '<p>All employees must complete the annual compliance training by this Friday. Failure to complete may result in disciplinary action.</p>',
                'start_date'             => $today->copy()->subDays(3)->toDateString(),
                'expiry_date'            => $today->copy()->addYear()->subDays(25)->toDateString(),
                'priority'               => 'urgent',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => true,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(3),
            ],
            [
                'title'                  => 'Office Closed Tomorrow – Emergency Maintenance',
                'description'            => '<p><strong>IMPORTANT:</strong> The office will remain closed tomorrow due to emergency electrical maintenance. All employees must work from home.</p>',
                'start_date'             => $today->copy()->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(9)->addDays(4)->toDateString(),
                'priority'               => 'critical',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => true,
                'allow_comments'         => false,
                'require_acknowledgment' => true,
                'date'                   => $today->copy(),
            ],

            // ---- PUBLISHED + LONG-LIVED (target: department) ----
            [
                'title'                  => 'Department Budget Review – Q3 Report',
                'description'            => '<p>The Q3 budget review meeting is scheduled for next Monday at 10 AM. All department heads must come prepared with expense reports.</p>',
                'start_date'             => $today->copy()->subDays(8)->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(7)->addDays(22)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'department',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'target_ids'             => $deptIds,
                'date'                   => $today->copy()->subDays(8),
            ],
            [
                'title'                  => 'HR Department: Payroll Submission Deadline',
                'description'            => '<p>All timesheets must be submitted by end of day today. Late submissions will delay salary processing.</p>',
                'start_date'             => $today->copy()->toDateString(),
                'expiry_date'            => $today->copy()->addYears(2)->subDays(40)->toDateString(),
                'priority'               => 'urgent',
                'target_type'            => 'department',
                'status'                 => 'published',
                'is_pinned'              => true,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'target_ids'             => $deptIds,
                'date'                   => $today->copy(),
            ],
            [
                'title'                  => 'IT Department: Server Migration Tonight',
                'description'            => '<p><strong>CRITICAL:</strong> Server migration tonight from 11 PM to 3 AM. All services will be unavailable. Please save your work and log out before 10:45 PM.</p>',
                'start_date'             => $today->copy()->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(8)->addDays(11)->toDateString(),
                'priority'               => 'critical',
                'target_type'            => 'department',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => true,
                'target_ids'             => $deptIds,
                'date'                   => $today->copy(),
            ],

            // ---- PUBLISHED + LONG-LIVED (target: role) ----
            [
                'title'                  => 'Manager Update: New Reporting Template',
                'description'            => '<p>All managers are requested to use the new weekly reporting template starting from next Monday. Template shared via email.</p>',
                'start_date'             => $today->copy()->subDays(9)->toDateString(),
                'expiry_date'            => $today->copy()->addYears(2)->addDays(15)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'role',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'target_ids'             => $roleIdList,
                'date'                   => $today->copy()->subDays(9),
            ],
            [
                'title'                  => 'Admin Role: System Access Policy Change',
                'description'            => '<p>Effective immediately, all admin users must enable two-factor authentication (2FA). This is mandatory for security compliance within 48 hours.</p>',
                'start_date'             => $today->copy()->subDays(2)->toDateString(),
                'expiry_date'            => null,
                'priority'               => 'urgent',
                'target_type'            => 'role',
                'status'                 => 'published',
                'is_pinned'              => true,
                'allow_comments'         => false,
                'require_acknowledgment' => false,
                'target_ids'             => $roleIdList,
                'date'                   => $today->copy()->subDays(2),
            ],

            // ---- PUBLISHED + LONG-LIVED (target: specific_users) ----
            [
                'title'                  => 'Interview Panel Reminder – Tomorrow at 2 PM',
                'description'            => '<p>You are part of the interview panel scheduled for tomorrow at 2 PM in Conference Room B. Please review the candidate profiles shared earlier.</p>',
                'start_date'             => $today->copy()->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(6)->addDays(28)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'specific_users',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'target_ids'             => $firstUsers,
                'date'                   => $today->copy(),
            ],
            [
                'title'                  => 'Urgent: Client Presentation Prep – Action Required',
                'description'            => '<p>You have been identified as a key presenter for the upcoming client meeting on Friday. Please prepare your slides and share by Thursday EOD.</p>',
                'start_date'             => $today->copy()->subDays(2)->toDateString(),
                'expiry_date'            => $today->copy()->addYear()->addDays(19)->toDateString(),
                'priority'               => 'urgent',
                'target_type'            => 'specific_users',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'target_ids'             => $nextUsers,
                'date'                   => $today->copy()->subDays(2),
            ],

            // ---- DEACTIVATED (entirely in the past, won't surface on board) ----
            [
                'title'                  => 'Annual Company Picnic – Thank You All!',
                'description'            => '<p>Thank you to everyone who attended the annual company picnic last weekend. We hope to see even more participation next year!</p>',
                'start_date'             => $today->copy()->subMonths(3)->subDays(6)->toDateString(),
                'expiry_date'            => $today->copy()->subMonths(2)->addDays(3)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'deactivated',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subMonths(3)->subDays(6),
            ],
            [
                'title'                  => 'Previous Quarter Appraisal Submission – Now Closed',
                'description'            => '<p>The appraisal submission window for the previous quarter is now closed. Results will be shared within two weeks.</p>',
                'start_date'             => $today->copy()->subMonths(4)->addDays(2)->toDateString(),
                'expiry_date'            => $today->copy()->subMonths(3)->addDays(8)->toDateString(),
                'priority'               => 'urgent',
                'target_type'            => 'all',
                'status'                 => 'deactivated',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subMonths(4)->addDays(2),
            ],
            [
                'title'                  => 'Previous Emergency: Water Outage – Resolved',
                'description'            => '<p>The water supply issue reported earlier has been resolved. Normal operations have resumed. Kept as permanent record.</p>',
                'start_date'             => $today->copy()->subMonths(5)->subDays(3)->toDateString(),
                'expiry_date'            => $today->copy()->subMonths(4)->subDays(11)->toDateString(),
                'priority'               => 'critical',
                'target_type'            => 'all',
                'status'                 => 'deactivated',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => true,
                'date'                   => $today->copy()->subMonths(5)->subDays(3),
            ],

            // ---- PUBLISHED + LONG-LIVED (more 'all' notices, permanent) ----
            [
                'title'                  => 'Upcoming: New Office Seating Arrangement',
                'description'            => '<p>Starting next Monday, the office seating arrangement will be reorganized. Check the updated seating chart on the notice board.</p>',
                'start_date'             => $today->copy()->subDays(1)->toDateString(),
                'expiry_date'            => $today->copy()->addMonths(12)->addDays(6)->toDateString(),
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => true,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(1),
            ],
            [
                'title'                  => 'Company Values & Code of Conduct',
                'description'            => '<p>All employees are reminded to uphold our company values of integrity, collaboration, and excellence. Full document available in the HR portal.</p>',
                'start_date'             => $today->copy()->subDays(23)->toDateString(),
                'expiry_date'            => null,
                'priority'               => 'normal',
                'target_type'            => 'all',
                'status'                 => 'published',
                'is_pinned'              => false,
                'allow_comments'         => false,
                'require_acknowledgment' => false,
                'date'                   => $today->copy()->subDays(23),
            ],
        ];

        $columnMap = [
            'department'     => 'department_id',
            'role'           => 'role_id',
            'specific_users' => 'user_id',
        ];

        foreach ($noticeRecords as $record) {
            $notice = Notice::create([
                'title'                  => $record['title'],
                'description'            => $record['description'],
                'attachments'            => [],
                'start_date'             => $record['start_date'],
                'expiry_date'            => $record['expiry_date'],
                'priority'               => $record['priority'],
                'target_type'            => $record['target_type'],
                'status'                 => $record['status'],
                'is_pinned'              => $record['is_pinned'],
                'allow_comments'         => $record['allow_comments'],
                'require_acknowledgment' => $record['require_acknowledgment'],
                'creator_id'             => $userId,
                'created_by'             => $userId,
                'created_at'             => $record['date'],
                'updated_at'             => $record['date'],
            ]);

            // Insert multiple target rows
            $column    = $columnMap[$record['target_type']] ?? null;
            $targetIds = array_filter($record['target_ids'] ?? []);

            if ($column && !empty($targetIds)) {
                $rows = array_map(fn($id) => [
                    'notice_id'   => $notice->id,
                    'target_type' => $record['target_type'],
                    $column       => $id,
                    'created_at'  => $record['date'],
                    'updated_at'  => $record['date'],
                ], $targetIds);

                NoticeTarget::insert($rows);
            }

            if ($record['status'] === 'draft') {
                continue;
            }

            // Resolve actual target users for this notice (same logic as controller)
            $targetUserIds = $this->resolveTargetUserIds($notice, $userId);

            if (empty($targetUserIds)) {
                continue;
            }

            // Only mark a portion as read (not all), rest remain unread
            $readCount   = max(1, (int) ceil(count($targetUserIds) * 0.6));
            $readUserIds = array_slice($targetUserIds, 0, $readCount);

            // read_at spreads between notice date and 7 days after
            foreach ($readUserIds as $index => $targetUserId) {
                $readAt         = Carbon::parse($record['date'])->addHours(6 + ($index * 8));
                $acknowledgedAt = ($record['require_acknowledgment'] && $index % 2 === 0)
                    ? $readAt->copy()->addHours(2 + ($index * 3))
                    : null;

                NoticeRead::insertOrIgnore([
                    'notice_id'       => $notice->id,
                    'user_id'         => $targetUserId,
                    'read_at'         => $readAt,
                    'acknowledged_at' => $acknowledgedAt,
                    'created_at'      => $readAt,
                ]);
            }
        }
    }

    private function resolveTargetUserIds(Notice $notice, int $createdBy): array
    {
        switch ($notice->target_type) {
            case 'all':
                return User::where('created_by', $createdBy)
                    ->where('id', '!=', $notice->creator_id)
                    ->pluck('id')
                    ->toArray();

            case 'specific_users':
                return $notice->targets()
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();

            case 'role':
                return User::whereHas('roles', fn($q) => $q->whereIn(
                    'roles.id',
                    $notice->targets()->whereNotNull('role_id')->pluck('role_id')
                ))
                    ->where('created_by', $createdBy)
                    ->pluck('id')
                    ->toArray();

            case 'department':
                if (!class_exists('\Workdo\Hrm\Models\Employee')) {
                    return [];
                }
                return \Workdo\Hrm\Models\Employee::whereIn(
                    'department_id',
                    $notice->targets()->whereNotNull('department_id')->pluck('department_id')
                )
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
        }

        return [];
    }
}
