<?php

namespace Workdo\NoticeBoard\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Workdo\NoticeBoard\Models\Notice;
use Workdo\NoticeBoard\Models\NoticeComment;
use Workdo\NoticeBoard\Models\NoticeRead;
use Workdo\NoticeBoard\Models\NoticeTarget;

use Workdo\NoticeBoard\Http\Requests\StoreNoticeRequest;
use Workdo\NoticeBoard\Http\Requests\UpdateNoticeRequest;

use Workdo\NoticeBoard\Events\CreateNotice;
use Workdo\NoticeBoard\Events\UpdateNotice;
use Workdo\NoticeBoard\Events\DestroyNotice;
use Workdo\NoticeBoard\Events\PublishNotice;
use Workdo\NoticeBoard\Events\CriticalNoticePublished;
use Workdo\NoticeBoard\Events\DeactivateNotice;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NoticeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-notices')) {
            $notices = Notice::query()
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-notices')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-notices')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('title'), fn($q) => $q->where('title', 'like', '%' . request('title') . '%'))
                ->when(request('priority') !== null && request('priority') !== '', fn($q) => $q->where('priority', request('priority')))
                ->when(request('target_type') !== null && request('target_type') !== '', fn($q) => $q->where('target_type', request('target_type')))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->orderByDesc('is_pinned')
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->withCount(['reads', 'comments'])
                ->with('targets')
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('NoticeBoard/Notices/Index', [
                'notices' => $notices,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function targetOptions()
    {
        if (Auth::user()->can('create-notices') || Auth::user()->can('edit-notices')) {
            $type = request('type');

            if ($type === 'department' && Module_is_active('Hrm')) {
                $options = \Workdo\Hrm\Models\Department::where('created_by', creatorId())->select('id', 'department_name as name')->get();
            } elseif ($type === 'role') {
                $options = Role::where('created_by', creatorId())->select('id', 'label as name')->get();
            } elseif ($type === 'specific_users') {
                $options = User::where('created_by', creatorId())->select('id', 'name')->get();
            } else {
                $options = collect();
            }

            return response()->json($options);
        } else {
            return response()->json([]);
        }
    }

    public function store(StoreNoticeRequest $request)
    {
        if (Auth::user()->can('create-notices')) {
            $validated                           = $request->validated();
            $validated['require_acknowledgment'] = $request->boolean('require_acknowledgment', false);
            $validated['allow_comments']         = $request->boolean('allow_comments', false);

            $notice                         = new Notice();
            $notice->title                  = $validated['title'];
            $notice->description            = $validated['description'];
            $notice->attachments            = $validated['attachments'] ?? [];
            $notice->start_date             = $validated['start_date'];
            $notice->expiry_date            = $validated['expiry_date'] ?? null;
            $notice->priority               = $validated['priority'];
            $notice->require_acknowledgment = $validated['require_acknowledgment'];
            $notice->target_type            = $validated['target_type'];
            $notice->allow_comments         = $validated['allow_comments'];
            $notice->creator_id             = Auth::id();
            $notice->created_by             = creatorId();
            $notice->save();

            if ($validated['target_type'] !== 'all' && !empty($validated['target_ids'])) {
                $this->insertTargets($notice->id, $validated['target_type'], $validated['target_ids']);
            }

            CreateNotice::dispatch($request, $notice);

            return redirect()->back()->with('success', __('The notice has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateNoticeRequest $request, Notice $notice)
    {
        if (Auth::user()->can('edit-notices')) {
            $validated                           = $request->validated();
            $validated['require_acknowledgment'] = $request->boolean('require_acknowledgment', false);
            $validated['allow_comments']         = $request->boolean('allow_comments', false);

            $notice->title                  = $validated['title'];
            $notice->description            = $validated['description'];
            $notice->attachments            = $validated['attachments'] ?? [];
            $notice->start_date             = $validated['start_date'];
            $notice->expiry_date            = $validated['expiry_date'] ?? null;
            $notice->priority               = $validated['priority'];
            $notice->require_acknowledgment = $validated['require_acknowledgment'];
            $notice->target_type            = $validated['target_type'];
            $notice->allow_comments         = $validated['allow_comments'];
            $notice->save();

            $notice->targets()->delete();
            if ($validated['target_type'] !== 'all' && !empty($validated['target_ids'])) {
                $this->insertTargets($notice->id, $validated['target_type'], $validated['target_ids']);
            }

            UpdateNotice::dispatch($request, $notice);

            return redirect()->back()->with('success', __('The notice details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    private function insertTargets(int $noticeId, string $targetType, array $ids): void
    {
        $columnMap = [
            'department'     => 'department_id',
            'role'           => 'role_id',
            'specific_users' => 'user_id',
        ];

        $column = $columnMap[$targetType] ?? null;

        if (!$column) {
            return;
        }

        $now     = now();
        $targets = array_map(fn($id) => [
            'notice_id'   => $noticeId,
            'target_type' => $targetType,
            $column       => $id,
            'created_at'  => $now,
            'updated_at'  => $now,
        ], $ids);

        NoticeTarget::insert($targets);
    }

    public function togglePin(Notice $notice)
    {
        if (Auth::user()->can('pin-unpin-notices')) {
            $notice->is_pinned = !$notice->is_pinned;
            $notice->save();

            return back()->with(
                'success',
                $notice->is_pinned
                ? __('The notice has been pinned.')
                : __('The notice has been unpinned.')
            );
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function publish(Notice $notice)
    {
        if (Auth::user()->can('manage-notice-status')) {

            if ($notice->start_date->format('Y-m-d') > now()->format('Y-m-d')) {
                return back()->with('error', __('The notice cannot be published because the start date is in the future.'));
            }

            if ($notice->expiry_date && $notice->expiry_date->format('Y-m-d') < now()->format('Y-m-d')) {
                return back()->with('error', __('The notice cannot be published because the expiry date has passed.'));
            }

            $notice->status = 'published';
            $notice->save();

            PublishNotice::dispatch($notice);

            // Send real-time notification if the notice is critical
            if ($notice->priority === 'critical') {
                $targetUserIds = $this->getTargetUserIds($notice);

                if (!empty($targetUserIds) && admin_setting('pusher_app_key')) {
                    CriticalNoticePublished::dispatch($notice, $targetUserIds);
                }
            }

            return back()->with('success', __('The notice has been published successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function deactivate(Notice $notice)
    {
        if (Auth::user()->can('manage-notice-status')) {
            $notice->status = 'deactivated';
            $notice->save();

            DeactivateNotice::dispatch($notice);

            return back()->with('success', __('The notice has been deactivated.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Notice $notice)
    {
        if (Auth::user()->can('delete-notices')) {
            DestroyNotice::dispatch($notice);

            $notice->delete();

            return redirect()->back()->with('success', __('The notice has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function show(Request $request, Notice $notice)
    {
        $isVisible = Notice::where('id', $notice->id)->visibleTo()->exists();

        if (!$isVisible) {
            $canAccess = Notice::where('id', $notice->id)
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-notices')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-notices')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->exists();

            if (!$canAccess) {
                $from = in_array($request->query('from'), ['board', 'notices'])
                    ? $request->query('from')
                    : 'board';

                $backRoute = $from === 'notices' && Auth::user()->canAny(['manage-any-notices', 'manage-own-notices'])
                    ? route('notice-board.notices.index')
                    : route('notice-board.board');

                return redirect($backRoute)->with('error', __('Permission denied'));
            }
        }

        if ($notice->creator_id !== Auth::id()) {
            NoticeRead::firstOrCreate(
                ['notice_id' => $notice->id, 'user_id' => Auth::id()],
                ['read_at' => now(), 'created_at' => now()]
            );
        }

        $userRead = NoticeRead::where('notice_id', $notice->id)
            ->where('user_id', Auth::id())
            ->value('acknowledged_at');

        return Inertia::render('NoticeBoard/Notices/Show', [
            'notice'           => array_merge($notice->load('creator')->toArray(), [
                'target_names' => $this->resolveTargetNames($notice),
            ]),
            'readStats'        => $this->buildReadStats($notice),
            'comments'         => $this->buildComments($notice),
            'userAcknowledged' => !is_null($userRead),
        ]);
    }

    private function resolveTargetNames(Notice $notice): array
    {
        if ($notice->target_type === 'department' && Module_is_active('Hrm')) {
            return \Workdo\Hrm\Models\Department::whereIn(
                'id',
                $notice->targets()->whereNotNull('department_id')->pluck('department_id')
            )->pluck('department_name')->toArray();
        }

        if ($notice->target_type === 'role') {
            return Role::whereIn(
                'id',
                $notice->targets()->whereNotNull('role_id')->pluck('role_id')
            )->pluck('label')->toArray();
        }

        return [];
    }

    private function buildReadStats(Notice $notice): ?array
    {
        if (Auth::user()->can('read-stats-notices') && (Auth::user()->can('manage-any-notices') || (Auth::user()->can('manage-own-notices') && $notice->creator_id === Auth::id()))) {
            $targetUserIds = $this->getTargetUserIds($notice);

            $readRecords = NoticeRead::where('notice_id', $notice->id)
                ->whereNotNull('read_at')
                ->with('user:id,name,avatar')
                ->orderByDesc('read_at')
                ->get();

            $readUserIds = $readRecords->pluck('user_id')->toArray();

            return [
                'read'   => $readRecords->map(fn($r) => [
                    'user'            => $r->user,
                    'read_at'         => $r->read_at,
                    'acknowledged_at' => $r->acknowledged_at,
                ]),

                'unread' => User::whereIn('id', $targetUserIds)
                    ->whereNotIn('id', $readUserIds)
                    ->select('id', 'name', 'avatar')
                    ->get(),
            ];
        } else {
            return null;
        }
    }

    private function buildComments(Notice $notice)
    {
        if ($notice->allow_comments) {
            return NoticeComment::where('notice_id', $notice->id)
                ->whereNull('parent_id')
                ->when(!Auth::user()->can('manage-any-notices-comments'), fn($q) => $q->where('creator_id', Auth::id()))
                ->with([
                    'user:id,name,avatar',
                    'replies' => fn($q) => $q->orderBy('created_at'),
                ])
                ->orderByDesc('created_at')
                ->get();
        } else {
            return null;
        }
    }

    private function getTargetUserIds(Notice $notice): array
    {
        switch ($notice->target_type) {
            case 'all':
                return User::where('created_by', $notice->created_by)
                    ->where('id', '!=', $notice->creator_id)
                    ->pluck('id')
                    ->toArray();

            case 'specific_users':
                return $notice->targets()
                    ->whereNotNull('user_id')
                    ->where('user_id', '!=', $notice->creator_id)
                    ->pluck('user_id')
                    ->toArray();

            case 'role':
                return User::whereHas('roles', fn($q) => $q->whereIn(
                    'roles.id',
                    $notice->targets()->whereNotNull('role_id')->pluck('role_id')
                ))
                    ->where('created_by', $notice->created_by)
                    ->where('id', '!=', $notice->creator_id)
                    ->pluck('id')
                    ->toArray();

            case 'department':
                if (!Module_is_active('Hrm')) {
                    return [];
                }

                return \Workdo\Hrm\Models\Employee::whereIn(
                    'department_id',
                    $notice->targets()->whereNotNull('department_id')->pluck('department_id')
                )
                    ->whereNotNull('user_id')
                    ->where('user_id', '!=', $notice->creator_id)
                    ->pluck('user_id')
                    ->toArray();
        }

        return [];
    }

    public function criticalAlerts()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $readNoticeIds = NoticeRead::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->pluck('notice_id');

        $notices = Notice::query()
            ->visibleTo()
            ->where('priority', 'critical')
            ->whereNotIn('id', $readNoticeIds)
            ->where('creator_id', '!=', Auth::id())
            ->select('id', 'title', 'priority', 'description', 'attachments', 'require_acknowledgment')
            ->get();

        return response()->json($notices);
    }

    public function board()
    {
        if (Auth::user()->can('manage-notice-board')) {

            $notices = Notice::query()
                ->visibleTo()
                ->when(request('priority'), fn($q) => $q->where('priority', request('priority')))
                ->orderByDesc('is_pinned')
                ->latest()
                ->withCount(['reads', 'comments'])
                ->get();

            $draftNotices = collect();
            if (Auth::user()->can('manage-any-notices') || Auth::user()->can('manage-own-notices')) {
                $draftNotices = Notice::query()
                    ->where('status', 'draft')
                    ->where(function ($q) {
                        if (Auth::user()->can('manage-any-notices')) {
                            $q->where('created_by', creatorId());
                        } elseif (Auth::user()->can('manage-own-notices')) {
                            $q->where('creator_id', Auth::id());
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    })
                    ->when(request('priority'), fn($q) => $q->where('priority', request('priority')))
                    ->orderByDesc('is_pinned')
                    ->latest()
                    ->withCount(['reads', 'comments'])
                    ->get();
            }

            $allNoticeIds = $notices->pluck('id')->merge($draftNotices->pluck('id'));

            $readNoticeIds = NoticeRead::whereIn('notice_id', $allNoticeIds)
                ->where('user_id', Auth::id())
                ->whereNotNull('read_at')
                ->pluck('notice_id')
                ->toArray();

            $ackNoticeIds = $notices->where('require_acknowledgment', true)->pluck('id');

            $acknowledgedNoticeIds = $ackNoticeIds->isNotEmpty()
                ? NoticeRead::whereIn('notice_id', $ackNoticeIds)
                    ->where('user_id', Auth::id())
                    ->whereNotNull('acknowledged_at')
                    ->pluck('notice_id')
                    ->toArray()
                : [];

            return Inertia::render('NoticeBoard/Notices/Board', [
                'notices'               => $notices,
                'draftNotices'          => $draftNotices,
                'readNoticeIds'         => $readNoticeIds,
                'acknowledgedNoticeIds' => $acknowledgedNoticeIds,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function acknowledge(Notice $notice)
    {
        $read = NoticeRead::firstOrCreate(
            ['notice_id' => $notice->id, 'user_id' => Auth::id()],
            ['read_at' => now(), 'created_at' => now()]
        );

        if (is_null($read->acknowledged_at)) {
            $read->acknowledged_at = now();
            $read->save();
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('The notice has been acknowledged.'));
    }

    public function markRead(Notice $notice)
    {
        NoticeRead::firstOrCreate(
            ['notice_id' => $notice->id, 'user_id' => Auth::id()],
            ['read_at' => now(), 'created_at' => now()]
        );

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('The notice has been marked as read.'));
    }
}
