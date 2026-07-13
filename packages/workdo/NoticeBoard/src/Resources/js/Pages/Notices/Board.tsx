import { useEffect, useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import NoRecordsFound from '@/components/no-records-found';
import { Bell, Pin, PinOff, ChevronRight, ShieldAlert, CalendarDays, CheckCheck, Settings, Plus, FileText, CheckCircle, Edit as EditIcon, Trash2, XCircle, Eye, MessageSquare, X, Info } from "lucide-react";
import { formatDate } from '@/utils/helpers';
import { Dialog } from "@/components/ui/dialog";
import Create from './Create';
import EditNotice from './Edit';
import { NoticeBoardProps, Notice, NoticeModalState } from './types';

// ----------------------------------------------------------------
// Helpers
// ----------------------------------------------------------------
const stripHtml = (html: string) => {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
};

// ----------------------------------------------------------------
// Priority config — single source of truth
// ----------------------------------------------------------------
const priorityColorMap: Record<string, string> = {
    normal: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    urgent: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    critical: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
};

// ----------------------------------------------------------------
// Notice Card
// ----------------------------------------------------------------
function NoticeCard({ notice, isRead, isAcknowledged, isDraft, auth, t, onMarkRead, onAcknowledge, onPublish, onEdit, onDeleteRequest, onDeactivate, onTogglePin }: {
    notice: Notice;
    isRead: boolean;
    isAcknowledged: boolean;
    isDraft?: boolean;
    auth: any;
    t: (key: string, opts?: any) => string;
    onMarkRead: (id: number) => void;
    onAcknowledge: (id: number) => void;
    onPublish?: (id: number) => void;
    onEdit?: (notice: Notice) => void;
    onDeleteRequest?: (id: number) => void;
    onDeactivate?: (id: number) => void;
    onTogglePin?: (id: number) => void;
}) {
    const description = stripHtml(notice.description || '');
    const isCreator = notice.creator_id === auth.user?.id;
    const canManage = auth.user?.permissions?.includes('manage-any-notices') || (auth.user?.permissions?.includes('manage-own-notices') && isCreator);
    const isUnread = !isRead && !isCreator && !isDraft;

    return (
        <Card className="border border-slate-200 dark:border-slate-700 shadow-none hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 flex flex-col bg-white dark:bg-slate-800 rounded-xl overflow-hidden h-full">
            <CardContent className="p-4 pb-3 flex-1 flex flex-col">

                {/* Header */}
                <div className="flex items-start justify-between gap-2 mb-3">
                    <div className="min-w-0 flex-1 order-1 rtl:order-2">
                        <h3 className={`text-sm font-bold line-clamp-2 leading-5 h-10 ${isUnread ? 'text-slate-900 dark:text-slate-50' : 'text-slate-700 dark:text-slate-200'}`}>
                            {notice.title}
                        </h3>
                    </div>
                    <div className="flex items-center gap-1.5 shrink-0 order-2 rtl:order-1">
                        {isUnread && (
                            <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 inline-flex items-center">
                                <span className="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse inline-block me-1.5" />
                                {t('New')}
                            </span>
                        )}
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center ${priorityColorMap[notice.priority] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'}`}>
                            {notice.priority ? t(notice.priority.charAt(0).toUpperCase() + notice.priority.slice(1)) : '-'}
                        </span>
                    </div>
                </div>

                {/* Description */}
                <p className="text-[12px] text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed mb-4 flex-1">
                    {description}
                </p>

                {/* Acknowledgment */}
                {notice.require_acknowledgment && !isCreator && !isDraft && (
                    isAcknowledged ? (
                        <div className="mb-2 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800 rounded-lg px-3 py-2 flex items-center gap-1.5">
                            <CheckCheck className="h-3.5 w-3.5 text-green-600 dark:text-green-400 shrink-0" />
                            <span className="text-[10px] font-bold text-green-700 dark:text-green-300">{t('You have acknowledged this notice.')}</span>
                        </div>
                    ) : (
                        <div className="mb-2 bg-orange-50 dark:bg-orange-900/30 border border-orange-100 dark:border-orange-800 rounded-lg px-3 py-2 flex items-center justify-between">
                            <span className="text-[10px] font-bold text-orange-600 dark:text-orange-300 flex items-center gap-1.5">
                                <ShieldAlert className="h-3.5 w-3.5" /> {t('Acknowledgment required')}
                            </span>
                            <Button size="sm" variant="ghost" className="h-6 text-[10px] font-bold text-orange-700 dark:text-orange-300 underline px-0" onClick={() => onAcknowledge(notice.id)}>
                                {t('Acknowledge')}
                            </Button>
                        </div>
                    )
                )}

                {/* Reads and Comments Count */}
                {canManage && (
                    <div className="flex items-center gap-2 mb-1">
                        <span className="text-xs text-muted-foreground flex items-center gap-1">
                            <Eye className="h-3 w-3" />
                            {notice.reads_count ?? 0}
                        </span>
                        {auth.user?.permissions?.includes('manage-any-notices-comments') && notice.allow_comments && (
                            <span className="text-xs text-muted-foreground flex items-center gap-1">
                                <MessageSquare className="h-3 w-3" />
                                {notice.comments_count ?? 0}
                            </span>
                        )}
                    </div>
                )}

                {/* Footer Actions */}
                <div className="flex items-center justify-between gap-2 mt-auto pt-3 border-t dark:border-slate-700">
                    <div className="flex items-center gap-1 text-xs text-muted-foreground rtl:order-1">
                        <CalendarDays className="h-3 w-3 shrink-0" />
                        <span>{formatDate(notice.start_date)}</span>
                    </div>
                    <TooltipProvider>
                        <div className="flex items-center gap-1">
                            {isDraft && canManage ? (
                                <>
                                    {auth.user?.permissions?.includes('manage-notice-status') && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-violet-600 hover:text-violet-700" onClick={() => onPublish?.(notice.id)}>
                                                    <CheckCircle className="h-3.5 w-3.5" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Publish')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                    {auth.user?.permissions?.includes('pin-unpin-notices') && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm" className={`h-7 w-7 p-0 ${notice.is_pinned ? 'text-orange-500 hover:text-orange-600' : 'text-gray-400 hover:text-gray-600'}`} onClick={() => onTogglePin?.(notice.id)}>
                                                    {notice.is_pinned ? <PinOff className="h-3.5 w-3.5" /> : <Pin className="h-3.5 w-3.5" />}
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{notice.is_pinned ? t('Unpin') : t('Pin')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-green-600 hover:text-green-700" onClick={() => router.get(route('notice-board.notices.show', notice.id), { from: 'board' })}>
                                                <Eye className="h-3.5 w-3.5" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent><p>{t('View')}</p></TooltipContent>
                                    </Tooltip>
                                    {auth.user?.permissions?.includes('edit-notices') && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-blue-600 hover:text-blue-700" onClick={() => onEdit?.(notice)}>
                                                    <EditIcon className="h-3.5 w-3.5" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Edit')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                    {auth.user?.permissions?.includes('delete-notices') && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-destructive hover:text-destructive" onClick={() => onDeleteRequest?.(notice.id)}>
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                </>
                            ) : (
                                <>
                                    {!isRead && !isCreator && !notice.require_acknowledgment && (
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-green-600 hover:text-green-700" onClick={() => onMarkRead(notice.id)}>
                                                    <CheckCheck className="h-3.5 w-3.5" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Mark as Read')}</p></TooltipContent>
                                        </Tooltip>
                                    )}
                                    {canManage ? (
                                        <>
                                            {auth.user?.permissions?.includes('manage-notice-status') && (
                                                <Tooltip delayDuration={0}>
                                                    <TooltipTrigger asChild>
                                                        <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-red-500 hover:text-red-600" onClick={() => onDeactivate?.(notice.id)}>
                                                            <XCircle className="h-3.5 w-3.5" />
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent><p>{t('Deactivate')}</p></TooltipContent>
                                                </Tooltip>
                                            )}
                                            {(auth.user?.permissions?.includes('pin-unpin-notices')) && (
                                                <Tooltip delayDuration={0}>
                                                    <TooltipTrigger asChild>
                                                        <Button variant="ghost" size="sm" className={`h-7 w-7 p-0 ${notice.is_pinned ? 'text-orange-500 hover:text-orange-600' : 'text-gray-400 hover:text-gray-600'}`} onClick={() => onTogglePin?.(notice.id)}>
                                                            {notice.is_pinned ? <PinOff className="h-3.5 w-3.5" /> : <Pin className="h-3.5 w-3.5" />}
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent><p>{notice.is_pinned ? t('Unpin') : t('Pin')}</p></TooltipContent>
                                                </Tooltip>
                                            )}
                                            <Tooltip delayDuration={0}>
                                                <TooltipTrigger asChild>
                                                    <Button variant="ghost" size="sm" className="h-7 w-7 p-0 text-green-600 hover:text-green-700" onClick={() => router.get(route('notice-board.notices.show', notice.id), { from: 'board' })}>
                                                        <Eye className="h-3.5 w-3.5" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent><p>{t('View')}</p></TooltipContent>
                                            </Tooltip>
                                        </>
                                    ) : (
                                        <Button variant="ghost" size="sm" className="h-7 text-xs px-2 shrink-0 gap-1" onClick={() => router.get(route('notice-board.notices.show', notice.id), { from: 'board' })}>
                                            {t('Read More')}<ChevronRight className="h-3 w-3" />
                                        </Button>
                                    )}
                                </>
                            )}
                        </div>
                    </TooltipProvider>
                </div>
            </CardContent>
        </Card>
    );
}

// ----------------------------------------------------------------
// Board
// ----------------------------------------------------------------
export default function Board() {
    const { t } = useTranslation();
    const { notices, draftNotices, readNoticeIds, acknowledgedNoticeIds, auth } = usePage<NoticeBoardProps>().props;
    const urlParams = new URLSearchParams(window.location.search);

    const [modalState, setModalState] = useState<NoticeModalState>({ isOpen: false, mode: '', data: null });
    const [priority, setPriority] = useState(urlParams.get('priority') || '');
    const [localReadIds, setLocalReadIds] = useState<number[]>(readNoticeIds ?? []);
    const [localAckIds, setLocalAckIds] = useState<number[]>(acknowledgedNoticeIds ?? []);

    useEffect(() => {
        setLocalReadIds(readNoticeIds ?? []);
        setLocalAckIds(acknowledgedNoticeIds ?? []);
    }, [JSON.stringify(readNoticeIds), JSON.stringify(acknowledgedNoticeIds)]);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'notice-board.notices.destroy',
        defaultMessage: t('Are you sure you want to delete this notice?'),
    });

    const [publishState, setPublishState] = useState<{ isOpen: boolean; id: number | null }>({ isOpen: false, id: null });
    const [deactivateState, setDeactivateState] = useState<{ isOpen: boolean; id: number | null }>({ isOpen: false, id: null });

    const handleFilter = (value: string) => {
        setPriority(value);
        router.get(route('notice-board.board'), { priority: value }, {
            preserveState: true,
            replace: true,
        });
    };

    const clearFilter = () => {
        setPriority('');
        router.get(route('notice-board.board'), {});
    };

    const openModal = (mode: 'add' | 'edit', data: Notice | null = null) => {
        setModalState({ isOpen: true, mode, data });
    };

    const closeModal = () => {
        setModalState({ isOpen: false, mode: '', data: null });
    };

    const handleAcknowledge = (noticeId: number) => {
        router.patch(
            route('notice-board.acknowledge', noticeId),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    setLocalAckIds(prev => [...prev, noticeId]);
                    setLocalReadIds(prev => prev.includes(noticeId) ? prev : [...prev, noticeId]);
                },
            }
        );
    };

    const handleMarkRead = (noticeId: number) => {
        router.patch(
            route('notice-board.mark-read', noticeId),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    setLocalReadIds(prev => [...prev, noticeId]);
                },
            }
        );
    };

    const pinnedNotices = (notices ?? []).filter((n: Notice) => n.is_pinned);
    const regularNotices = (notices ?? []).filter((n: Notice) => !n.is_pinned);
    const drafts = draftNotices ?? [];
    const totalCount = pinnedNotices.length + regularNotices.length + drafts.length;

    const stats = {
        total: notices?.length ?? 0,
        critical: (notices ?? []).filter((n: Notice) => n.priority === 'critical').length,
        drafts: drafts.length,
        pinned: pinnedNotices.length,
    };

    const noticeCardProps = (notice: Notice, isDraft = false) => ({
        notice,
        isRead: localReadIds.includes(notice.id),
        isAcknowledged: localAckIds.includes(notice.id),
        isDraft,
        auth,
        t,
        onMarkRead: handleMarkRead,
        onAcknowledge: handleAcknowledge,
        onPublish: (id: number) => setPublishState({ isOpen: true, id }),
        onEdit: (n: Notice) => openModal('edit', n),
        onDeleteRequest: openDeleteDialog,
        onDeactivate: (id: number) => setDeactivateState({ isOpen: true, id }),
        onTogglePin: (id: number) => router.patch(route('notice-board.notices.toggle-pin', id)),
    });

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t('Notice Board') }]}
            pageTitle={t('Notice Board')}
            pageActions={
                <div className="flex items-center gap-2">
                    {auth.user?.permissions?.includes('manage-notices') && (
                        <TooltipProvider>
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" variant="outline" onClick={() => router.get(route('notice-board.notices.index'))}>
                                        <Settings className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Manage Notices')}</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    )}
                    {auth.user?.permissions?.includes('create-notices') && (
                        <TooltipProvider>
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button size="sm" onClick={() => openModal('add')}>
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Create')}</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    )}
                </div>
            }
        >
            <Head title={t('Notice Board')} />

            <div className="w-full space-y-6">

                {/* ── Stats Bar ── */}
                <Card className="border-none shadow-sm">
                    <CardContent className="p-4 flex flex-wrap items-center gap-3">
                        <div className="text-sm font-bold shrink-0 dark:text-slate-200">
                            {t('Total')}: <span className="text-primary">{stats.total}</span>
                        </div>
                        <div className="h-5 w-px bg-slate-300 dark:bg-slate-600 shrink-0 hidden sm:block" />
                        <div className="flex items-center gap-2 flex-wrap">
                            <span className={`inline-flex items-center gap-1.5 px-3 h-7 rounded-full text-xs font-medium border ${stats.critical > 0 ? 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800' : 'bg-gray-50 text-gray-400 border-gray-200 dark:bg-slate-700 dark:text-slate-500 dark:border-slate-600'}`}>
                                <ShieldAlert className="h-3 w-3" /> {t('Critical')}: {stats.critical}
                            </span>
                            <span className={`inline-flex items-center gap-1.5 px-3 h-7 rounded-full text-xs font-medium border ${stats.pinned > 0 ? 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800' : 'bg-gray-50 text-gray-400 border-gray-200 dark:bg-slate-700 dark:text-slate-500 dark:border-slate-600'}`}>
                                <Pin className="h-3 w-3" /> {t('Pinned')}: {stats.pinned}
                            </span>
                            {drafts.length > 0 && (
                                <>
                                    <div className="h-5 w-px bg-slate-300 dark:bg-slate-600 shrink-0" />
                                    <div className="flex items-center gap-2">
                                        <span className="inline-flex items-center gap-1.5 px-3 h-7 rounded-full text-xs font-medium border border-dashed bg-slate-50 text-slate-500 border-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:border-slate-500">
                                            <FileText className="h-3 w-3" /> {t('Drafts')}: {stats.drafts}
                                        </span>
                                        <TooltipProvider>
                                            <Tooltip delayDuration={0}>
                                                <TooltipTrigger asChild>
                                                    <span className="inline-flex items-center justify-center h-5 w-5 rounded-full bg-slate-300 dark:bg-slate-600 cursor-default shrink-0">
                                                        <Info className="h-3 w-3 text-slate-600 dark:text-slate-300" />
                                                    </span>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>{t('Unpublished - not included in Total, Critical or Pinned counts')}</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        </TooltipProvider>
                                    </div>
                                </>
                            )}
                        </div>
                        <div className="ms-auto flex items-center gap-2">
                            <Select value={priority} onValueChange={handleFilter}>
                                <SelectTrigger className="w-44 h-9 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600">
                                    <SelectValue placeholder={t('All Priorities')} />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="normal">{t('Normal')}</SelectItem>
                                    <SelectItem value="urgent">{t('Urgent')}</SelectItem>
                                    <SelectItem value="critical">{t('Critical')}</SelectItem>
                                </SelectContent>
                            </Select>
                            {priority && (
                                <TooltipProvider>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button variant="outline" size="sm" onClick={clearFilter}>
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent><p>{t('Clear')}</p></TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            )}
                        </div>
                    </CardContent>
                </Card>

                {totalCount > 0 ? (
                    <div className="flex flex-col gap-8">

                        {/* ── Drafts ── */}
                        {drafts.length > 0 && (
                            <div className="w-full">
                                <div className="flex items-center gap-2 mb-4">
                                    <h2 className="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{t('Drafts')}</h2>
                                    <span className="inline-flex items-center px-1.5 h-5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">{drafts.length}</span>
                                    <div className="h-px bg-slate-200 dark:bg-slate-700 flex-1" />
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    {drafts.map((notice: Notice) => (
                                        <NoticeCard key={`draft-${notice.id}`} {...noticeCardProps(notice, true)} />
                                    ))}
                                </div>
                            </div>
                        )}

                        {drafts.length > 0 && (pinnedNotices.length > 0 || regularNotices.length > 0) && <Separator />}

                        {/* ── Pinned Notices ── */}
                        {pinnedNotices.length > 0 && (
                            <div className="w-full">
                                <div className="flex items-center gap-2 mb-4">
                                    <Pin className="h-4 w-4 text-amber-500" />
                                    <h2 className="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-slate-200">{t('Pinned Notices')}</h2>
                                    <span className="inline-flex items-center px-1.5 h-5 rounded-full text-[10px] font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300">{pinnedNotices.length}</span>
                                    <div className="h-px bg-slate-200 dark:bg-slate-700 flex-1" />
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    {pinnedNotices.map((notice: Notice) => (
                                        <NoticeCard key={notice.id} {...noticeCardProps(notice)} />
                                    ))}
                                </div>
                            </div>
                        )}

                        {pinnedNotices.length > 0 && regularNotices.length > 0 && <Separator />}

                        {/* ── All Notices ── */}
                        {regularNotices.length > 0 && (
                            <div className="w-full">
                                <div className="flex items-center gap-2 mb-4">
                                    <h2 className="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-slate-200">{t('All Notices')}</h2>
                                    <span className="inline-flex items-center px-1.5 h-5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{regularNotices.length}</span>
                                    <div className="h-px bg-slate-200 dark:bg-slate-700 flex-1" />
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    {regularNotices.map((notice: Notice) => (
                                        <NoticeCard key={notice.id} {...noticeCardProps(notice)} />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                ) : (
                    <Card className="shadow-sm">
                        <CardContent className="p-0">
                            <NoRecordsFound
                                icon={Bell}
                                title={t('No notices available')}
                                description={t('There are no notices for you at the moment.')}
                                hasFilters={!!priority}
                                onClearFilters={clearFilter}
                                createPermission="create-notices"
                                onCreateClick={() => openModal('add')}
                                createButtonText={t('Create Notice')}
                            />
                        </CardContent>
                    </Card>
                )}
            </div>

            {/* Create / Edit dialog */}
            <Dialog open={modalState.isOpen} onOpenChange={(open) => !open && closeModal()}>
                {modalState.mode === 'add' && <Create onSuccess={closeModal} />}
                {modalState.mode === 'edit' && modalState.data && (
                    <EditNotice notice={modalState.data} onSuccess={closeModal} />
                )}
            </Dialog>

            {/* Delete confirmation */}
            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Notice')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={confirmDelete}
                variant="destructive"
            />

            {/* Publish confirmation */}
            <ConfirmationDialog
                open={publishState.isOpen}
                onOpenChange={(open) => setPublishState({ isOpen: open, id: null })}
                title={t('Publish Notice')}
                message={t('Are you sure you want to publish this notice? Once published, it will be visible to all targeted users.')}
                confirmText={t('Publish')}
                onConfirm={() => {
                    router.patch(route('notice-board.notices.publish', publishState.id), {}, {
                        preserveScroll: true,
                        onSuccess: () => { setPublishState({ isOpen: false, id: null }); router.reload(); },
                    });
                }}
                variant="default"
            />

            {/* Deactivate confirmation */}
            <ConfirmationDialog
                open={deactivateState.isOpen}
                onOpenChange={(open) => setDeactivateState({ isOpen: open, id: null })}
                title={t('Deactivate Notice')}
                message={t('Are you sure you want to deactivate this notice?')}
                confirmText={t('Deactivate')}
                onConfirm={() => {
                    router.patch(route('notice-board.notices.deactivate', deactivateState.id), {}, {
                        preserveScroll: true,
                        onSuccess: () => { setDeactivateState({ isOpen: false, id: null }); router.reload(); },
                    });
                }}
                variant="destructive"
            />
        </AuthenticatedLayout>
    );
}
