import { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Pin, ShieldAlert, CheckCheck, MessageSquare, User, ArrowLeft, Paperclip, FileText, Download, Eye, Users, Send, Reply, Trash2, ChevronDown, ChevronUp, Clock, BookOpen, Bell, CalendarDays, CheckCircle2, BadgeCheck } from 'lucide-react';
import { formatDate, getImagePath, formatDateTime } from '@/utils/helpers';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { NoticeShowProps, NoticeComment } from './types';

// ----------------------------------------------------------------
// Color maps
// ----------------------------------------------------------------
const priorityColorMap: Record<string, string> = {
    normal: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    urgent: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    critical: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
};

const statusColorMap: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    published: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
    deactivated: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
};

const priorityAccentMap: Record<string, { strip: string; headerBg: string; iconBg: string; iconColor: string }> = {
    normal: { strip: 'bg-blue-500', headerBg: 'bg-blue-50/30 dark:bg-blue-900/10', iconBg: 'bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-700', iconColor: 'text-blue-600 dark:text-blue-400' },
    urgent: { strip: 'bg-orange-500', headerBg: 'bg-orange-50/30 dark:bg-orange-900/10', iconBg: 'bg-orange-50 border-orange-200 dark:bg-orange-900/30 dark:border-orange-700', iconColor: 'text-orange-600 dark:text-orange-400' },
    critical: { strip: 'bg-red-500', headerBg: 'bg-red-50/30 dark:bg-red-900/10', iconBg: 'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-700', iconColor: 'text-red-600 dark:text-red-400' },
};

// ----------------------------------------------------------------
// Helpers
// ----------------------------------------------------------------
function getAttachmentMeta(file: string) {
    const fileName = file.split('/').pop() || file;
    const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(file);
    const isPdf = /\.pdf$/i.test(file);
    const isExcel = /\.(xls|xlsx)$/i.test(file);
    const isWord = /\.(doc|docx)$/i.test(file);
    const iconBg = isImage ? 'bg-blue-50 border-blue-100 dark:bg-blue-900/20 dark:border-blue-800' : isPdf ? 'bg-red-50 border-red-100 dark:bg-red-900/20 dark:border-red-800' : isExcel ? 'bg-emerald-50 border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800' : isWord ? 'bg-indigo-50 border-indigo-100 dark:bg-indigo-900/20 dark:border-indigo-800' : 'bg-slate-50 border-slate-100 dark:bg-slate-700 dark:border-slate-600';
    const iconColor = isImage ? 'text-blue-500 dark:text-blue-400' : isPdf ? 'text-red-500 dark:text-red-400' : isExcel ? 'text-emerald-500 dark:text-emerald-400' : isWord ? 'text-indigo-500 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500';
    return { fileName, isImage, iconBg, iconColor, url: getImagePath(file) };
}

function priorityBadge(value: string, t: (key: string) => string) {
    return (
        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${priorityColorMap[value] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'}`}>
            {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
        </span>
    );
}

function statusBadge(value: string, t: (key: string) => string) {
    return (
        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${statusColorMap[value] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'}`}>
            {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
        </span>
    );
}

function targetTypeLabel(value: string, t: (key: string) => string) {
    const map: Record<string, string> = {
        all: t('All'),
        department: t('Department'),
        role: t('Role'),
        specific_users: t('Specific Users'),
    };
    return map[value] || '-';
}

function countAllComments(comments: NoticeComment[] | null | undefined): number {
    if (!comments?.length) return 0;
    return comments.reduce((total, c) => total + 1 + (c.replies?.length ?? 0), 0);
}

// ----------------------------------------------------------------
// Avatar
// ----------------------------------------------------------------
function Avatar({ user, size = 'sm', className }: { user?: { name?: string; avatar?: string }; size?: 'sm' | 'md' | 'lg'; className?: string }) {
    const avatarUrl = user?.avatar ? getImagePath(user.avatar) : null;
    const initials = user?.name?.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || '?';
    const dim = size === 'lg' ? 'h-10 w-10 text-sm' : size === 'md' ? 'h-9 w-9 text-sm' : 'h-8 w-8 text-xs';

    return (
        <div className={`${dim} rounded-full bg-primary/10 text-primary font-semibold flex items-center justify-center shrink-0 ring-2 ring-white dark:ring-slate-800 overflow-hidden ${className || ''}`}>
            {avatarUrl
                ? <img src={avatarUrl} alt={user?.name} className="w-full h-full object-cover" />
                : initials
            }
        </div>
    );
}

// ----------------------------------------------------------------
// Section Card Header
// ----------------------------------------------------------------
function SectionHeader({ icon, iconBg, iconColor, title, subtitle, count }: {
    icon: React.ReactNode; iconBg: string; iconColor: string;
    title: string; subtitle?: string; count?: number;
}) {
    return (
        <div className="px-5 py-4 border-b dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
            <div className={`h-8 w-8 rounded-lg border flex items-center justify-center shrink-0 ${iconBg}`}>
                <span className={iconColor}>{icon}</span>
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2">
                    <h3 className="text-sm font-semibold text-slate-800 dark:text-slate-100">{title}</h3>
                    {count !== undefined && count > 0 && (
                        <span className="inline-flex items-center px-2 h-5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">
                            {count}
                        </span>
                    )}
                </div>
                {subtitle && <p className="text-[11px] text-muted-foreground mt-0.5">{subtitle}</p>}
            </div>
        </div>
    );
}

// ----------------------------------------------------------------
// Info Row
// ----------------------------------------------------------------
function InfoRow({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <div className="flex items-center justify-between gap-2">
            <span className="text-muted-foreground text-xs">{label}</span>
            <span className="font-medium text-end text-xs text-slate-800 dark:text-slate-200">{children}</span>
        </div>
    );
}

// ----------------------------------------------------------------
// Attachments Section
// ----------------------------------------------------------------
function AttachmentsSection({ attachments, t }: { attachments: string[]; t: (key: string) => string }) {
    const items = attachments.map(getAttachmentMeta);
    return (
        <Card className="border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-800">
            <SectionHeader
                icon={<Paperclip className="h-4 w-4" />}
                iconBg="bg-violet-50 border-violet-100 dark:bg-violet-900/20 dark:border-violet-800"
                iconColor="text-violet-600 dark:text-violet-400"
                title={t('Attachments')}
                subtitle={`${attachments.length} ${attachments.length === 1 ? t('file attached') : t('files attached')}`}
                count={attachments.length}
            />
            <CardContent className="p-4">
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    {items.map((item, index) => (
                        <div key={index} className="group flex flex-col rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-md transition-all duration-200">
                            {item.isImage ? (
                                <div className="h-20 overflow-hidden bg-slate-50 dark:bg-slate-700">
                                    <img src={item.url} alt={item.fileName} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                </div>
                            ) : (
                                <div className={`h-20 flex items-center justify-center border-b ${item.iconBg}`}>
                                    <FileText className={`h-8 w-8 ${item.iconColor}`} />
                                </div>
                            )}
                            <div className="flex items-center justify-center gap-0.5 py-1.5 bg-slate-50/60 dark:bg-slate-700/60 border-t border-slate-100 dark:border-slate-600">
                                <TooltipProvider>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button size="sm" variant="ghost" className="h-7 w-7 p-0 text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                                                onClick={() => window.open(item.url, '_blank')}>
                                                <Eye className="h-3.5 w-3.5" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent><p>{t('View')}</p></TooltipContent>
                                    </Tooltip>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button size="sm" variant="ghost" className="h-7 w-7 p-0 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                                onClick={() => { const a = document.createElement('a'); a.href = item.url; a.download = item.fileName; a.click(); }}>
                                                <Download className="h-3.5 w-3.5" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent><p>{t('Download')}</p></TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}

// ----------------------------------------------------------------
// Comment Composer
// ----------------------------------------------------------------
function CommentComposer({
    value, onChange, onSubmit, onCancel, placeholder, submitLabel, submitting, showCancel = false
}: {
    value: string;
    onChange: (v: string) => void;
    onSubmit: () => void;
    onCancel?: () => void;
    placeholder: string;
    submitLabel: string;
    submitting: boolean;
    showCancel?: boolean;
}) {
    const { t } = useTranslation();
    const maxLength = 1000;

    return (
        <div className="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary/40 transition-all">
            <Textarea
                value={value}
                onChange={e => onChange(e.target.value.slice(0, maxLength))}
                placeholder={placeholder}
                className="text-sm min-h-[80px] resize-none border-0 shadow-none focus-visible:ring-0 bg-transparent px-4 pt-3 pb-2"
            />
            <div className="flex items-center justify-between gap-3 px-3 py-2 bg-slate-50/80 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700">
                <div className="flex items-center gap-2 order-2 rtl:order-1">
                    {showCancel && onCancel && (
                        <Button variant="ghost" size="sm" className="h-7 text-xs" onClick={onCancel}>{t('Cancel')}</Button>
                    )}
                    <Button size="sm" className="h-7 text-xs gap-1.5" disabled={!value.trim() || submitting} onClick={onSubmit}>
                        <Send className="h-3 w-3 rtl:scale-x-[-1]" />
                        {submitting ? t('Sending...') : submitLabel}
                    </Button>
                </div>
                <span className="text-[11px] text-muted-foreground tabular-nums order-1 rtl:order-2">{value.length}/{maxLength}</span>
            </div>
        </div>
    );
}

// ----------------------------------------------------------------
// Comment Row
// ----------------------------------------------------------------
function CommentRow({
    comment, auth, noticeId, t, depth = 0
}: {
    comment: NoticeComment;
    auth: any;
    noticeId: number;
    t: (key: string, opts?: any) => string;
    depth?: number;
}) {
    const [showReplyBox, setShowReplyBox] = useState(false);
    const [replyText, setReplyText] = useState('');
    const [showReplies, setShowReplies] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [confirmDelete, setConfirmDelete] = useState(false);

    const isReply = depth > 0;
    const isOwnComment = comment.user_id === auth.user?.id;
    const canReply = auth.user?.permissions?.includes('reply-notices-comments') && depth === 0 && !isOwnComment;
    const canDelete = auth.user?.permissions?.includes('delete-any-notices-comments') || (auth.user?.permissions?.includes('delete-own-notices-comments') && comment.creator_id === auth.user?.id);
    const replyCount = comment.replies?.length ?? 0;

    const handleReply = () => {
        if (!replyText.trim()) return;
        setSubmitting(true);
        router.post(
            route('notice-board.notices.comments.reply', { notice: noticeId, comment: comment.id }),
            { comment: replyText.trim() },
            {
                preserveScroll: true,
                onSuccess: () => { setReplyText(''); setShowReplyBox(false); setSubmitting(false); },
                onError: () => setSubmitting(false),
            }
        );
    };

    const handleDelete = () => {
        router.delete(
            route('notice-board.notices.comments.destroy', { notice: noticeId, comment: comment.id }),
            { preserveScroll: true }
        );
    };

    return (
        <div>
            <div className={`flex gap-3 ${isReply ? 'ms-2' : ''}`}>
                <Avatar user={comment.user} size={isReply ? 'sm' : 'md'} className="shrink-0 mt-0.5" />

                <div className="flex-1 min-w-0 space-y-2">
                    <div className={`rounded-xl border p-3.5 ${isOwnComment ? 'bg-primary/5 dark:bg-slate-700 border-primary/15 dark:border-slate-500' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'}`}>
                        <div className="flex items-start justify-between gap-2 mb-1.5">
                            <div className="min-w-0 order-1 rtl:order-2">
                                <div className="flex items-center gap-2 flex-wrap">
                                    <span className="text-sm font-semibold text-slate-800 dark:text-slate-100">{comment.user?.name}</span>
                                    {isOwnComment && (
                                        <span className="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-primary/10 dark:bg-slate-600 text-primary dark:text-slate-200">
                                            {t('You')}
                                        </span>
                                    )}
                                </div>
                                <span className="text-[11px] text-muted-foreground flex items-center gap-1 mt-0.5">
                                    <Clock className="h-3 w-3 shrink-0" />
                                    <span>{formatDateTime(comment.created_at)}</span>
                                </span>
                            </div>

                            {canDelete && (
                                <div className="order-2 rtl:order-1 shrink-0">
                                    <TooltipProvider>
                                        <Tooltip delayDuration={0}>
                                            <TooltipTrigger asChild>
                                                <Button variant="ghost" size="sm"
                                                    className="h-7 w-7 p-0 text-destructive hover:text-destructive"
                                                    onClick={() => setConfirmDelete(true)}>
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent><p>{t('Delete')}</p></TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                </div>
                            )}
                        </div>

                        <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap break-words">
                            {comment.comment}
                        </p>

                        {!isReply && (canReply || replyCount > 0) && (
                            <div className="flex items-center flex-wrap gap-3 mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700">
                                {canReply && (
                                    <button
                                        type="button"
                                        className="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                                        onClick={() => setShowReplyBox(p => !p)}>
                                        <Reply className="h-3 w-3 shrink-0 rtl:scale-x-[-1]" />
                                        {showReplyBox ? t('Cancel reply') : t('Reply')}
                                    </button>
                                )}
                                {replyCount > 0 && (
                                    <button
                                        type="button"
                                        className="inline-flex items-center gap-1 text-xs font-medium text-muted-foreground hover:text-foreground transition-colors"
                                        onClick={() => setShowReplies(p => !p)}>
                                        {showReplies
                                            ? <><ChevronUp className="h-3 w-3 shrink-0" />{t('Hide replies')} ({replyCount})</>
                                            : <><ChevronDown className="h-3 w-3 shrink-0" />{t('View replies')} ({replyCount})</>
                                        }
                                    </button>
                                )}
                            </div>
                        )}
                    </div>

                    {showReplyBox && (
                        <div className="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/60 p-3 ms-2">
                            <p className="text-[11px] font-medium text-muted-foreground mb-2 flex items-center gap-1.5">
                                <Reply className="h-3 w-3 shrink-0 rtl:scale-x-[-1]" />
                                <span>{t('Replying to')}</span>
                                <span className="text-slate-700 dark:text-slate-200 font-semibold">{comment.user?.name}</span>
                            </p>
                            <CommentComposer
                                value={replyText}
                                onChange={setReplyText}
                                onSubmit={handleReply}
                                onCancel={() => { setShowReplyBox(false); setReplyText(''); }}
                                placeholder={t('Write a reply...')}
                                submitLabel={t('Reply')}
                                submitting={submitting}
                                showCancel
                            />
                        </div>
                    )}

                    {showReplies && replyCount > 0 && (
                        <div className="ps-4 ms-2 pe-0 me-0 border-s-2 border-slate-200 dark:border-slate-700 flex flex-col gap-4">
                            {comment.replies?.map((reply: NoticeComment) => (
                                <CommentRow key={reply.id} comment={reply} auth={auth} noticeId={noticeId} t={t} depth={depth + 1} />
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <ConfirmationDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title={t('Delete Comment')}
                message={t('Are you sure you want to delete this comment?')}
                confirmText={t('Delete')}
                onConfirm={handleDelete}
                variant="destructive"
            />
        </div>
    );
}

// ----------------------------------------------------------------
// Show Page
// ----------------------------------------------------------------
export default function Show() {
    const { t } = useTranslation();
    const { notice, auth, readStats, comments, userAcknowledged } = usePage<NoticeShowProps>().props;
    const urlParams = new URLSearchParams(window.location.search);
    const from = urlParams.get('from') || 'board';
    const canAccessBoard = auth.user?.permissions?.includes('manage-notice-board') || auth.user?.permissions?.includes('view-notice-board');
    const canAccessNotices = auth.user?.permissions?.includes('manage-notices');
    const backRoute = from === 'notices' && canAccessNotices
        ? route('notice-board.notices.index')
        : from === 'board' && canAccessBoard
            ? route('notice-board.board')
            : null;
    const backLabel = from === 'notices' && canAccessNotices ? t('Back to Notices') : t('Back to Board');

    const [newComment, setNewComment] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [acknowledged, setAcknowledged] = useState(userAcknowledged);
    const [acknowledging, setAcknowledging] = useState(false);

    useFlashMessages();

    const isCreator = notice.creator_id === auth.user?.id;
    const canManage = auth.user?.permissions?.includes('manage-any-notices') || (auth.user?.permissions?.includes('manage-own-notices') && isCreator);
    const canAcknowledge = notice.require_acknowledgment && !isCreator && !acknowledged && notice.status === 'published';
    const canViewNoticeInfo = canManage;
    const canViewReadStats = auth.user?.permissions?.includes('read-stats-notices') && readStats !== null;
    const canComment = notice.allow_comments && !isCreator;
    const totalCommentCount = countAllComments(comments);
    const accent = priorityAccentMap[notice.priority] ?? priorityAccentMap.normal;
    const hasAttachments = notice.attachments && Array.isArray(notice.attachments) && notice.attachments.length > 0;
    const hasSidebar = canViewNoticeInfo || canViewReadStats;
    const isExpired = notice.expiry_date && new Date(notice.expiry_date) < new Date();

    const handleAcknowledge = () => {
        setAcknowledging(true);
        router.patch(route('notice-board.acknowledge', notice.id), {}, {
            preserveScroll: true,
            onSuccess: () => { setAcknowledged(true); setAcknowledging(false); },
            onError: () => setAcknowledging(false),
        });
    };

    const handleComment = () => {
        if (!newComment.trim()) return;
        setSubmitting(true);
        router.post(
            route('notice-board.notices.comments.store', notice.id),
            { comment: newComment.trim() },
            {
                preserveScroll: true,
                onSuccess: () => { setNewComment(''); setSubmitting(false); },
                onError: () => setSubmitting(false),
            }
        );
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                ...(backRoute ? [{ label: from === 'notices' && canAccessNotices ? t('Manage Notices') : t('Notice Board'), url: backRoute }] : []),
                { label: notice.title },
            ]}
            pageTitle={t('Notice Detail')}
            pageActions={
                backRoute ? (
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button variant="outline" size="sm" onClick={() => router.get(backRoute)}>
                                    <ArrowLeft className="h-4 w-4 rtl:scale-x-[-1]" />
                                    <span>{t('Back')}</span>
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent><p>{backLabel}</p></TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                ) : undefined
            }
        >
            <Head title={`${t('Notice Detail')} - ${notice.title}`} />

            <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

                {/* ── Main Content ── */}
                <div className={`space-y-5 ${hasSidebar ? 'xl:col-span-2' : 'xl:col-span-3 xl:max-w-[90%] xl:mx-auto w-full'}`}>

                    {/* Notice Detail Card */}
                    <Card className="border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-800">

                        {/* Priority strip */}
                        <div className={`h-1.5 w-full ${accent.strip}`} />

                        {/* Header */}
                        <div className={`px-6 py-5 border-b dark:border-slate-700 ${accent.headerBg}`}>
                            <div className="flex items-start gap-4">
                                <div className={`h-11 w-11 rounded-xl border flex items-center justify-center shrink-0 ${accent.iconBg}`}>
                                    <Bell className={`h-5 w-5 ${accent.iconColor}`} />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <div className="flex items-center gap-2 flex-wrap mb-2">
                                        {notice.is_pinned && (
                                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                <Pin className="h-3 w-3 rtl:scale-x-[-1]" /> {t('Pinned')}
                                            </span>
                                        )}
                                        {priorityBadge(notice.priority, t)}
                                        {canManage && statusBadge(notice.status, t)}
                                    </div>
                                    <h1 className="text-xl font-bold leading-snug text-slate-900 dark:text-slate-50">{notice.title}</h1>
                                    <div className="flex items-center gap-3 flex-wrap mt-2">
                                        {notice.creator && (
                                            <span className="text-xs text-muted-foreground inline-flex items-center gap-1.5">
                                                <User className="h-3.5 w-3.5 shrink-0" />
                                                {t('Posted by')} <span className="font-medium text-slate-700 dark:text-slate-300">{notice.creator.name}</span>
                                            </span>
                                        )}
                                        <span className="text-xs text-muted-foreground inline-flex items-center gap-1.5">
                                            <CalendarDays className="h-3.5 w-3.5 shrink-0" />
                                            {formatDate(notice.start_date)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Acknowledgment banner */}
                        {notice.require_acknowledgment && !isCreator && (
                            <div className="mx-5 mt-5">
                                {canAcknowledge ? (
                                    <div className="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                        <div className="flex items-start gap-3 order-1 rtl:order-2 flex-1 sm:justify-between">
                                            <div className="flex items-start gap-3">
                                                <div className="h-9 w-9 rounded-lg bg-orange-100 dark:bg-orange-900/40 border border-orange-200 dark:border-orange-700 flex items-center justify-center shrink-0">
                                                    <ShieldAlert className="h-4 w-4 text-orange-600 dark:text-orange-400" />
                                                </div>
                                                <div>
                                                    <p className="text-sm font-semibold text-orange-800 dark:text-orange-300">{t('Acknowledgment Required')}</p>
                                                    <p className="text-xs text-orange-600 dark:text-orange-400 mt-0.5">{t('Please read and acknowledge this notice to confirm you have received it.')}</p>
                                                </div>
                                            </div>
                                            <Button
                                                size="sm"
                                                className="h-9 text-xs font-semibold shrink-0 gap-1.5 px-4 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white order-2 rtl:order-1"
                                                disabled={acknowledging}
                                                onClick={handleAcknowledge}>
                                                <CheckCheck className="h-3.5 w-3.5 shrink-0" />
                                                {acknowledging ? t('Acknowledging...') : t('I Acknowledge')}
                                            </Button>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl px-5 py-3.5 flex items-center gap-3">
                                        <div className="h-9 w-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-700 flex items-center justify-center shrink-0">
                                            <CheckCheck className="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{t('Acknowledged')}</p>
                                            <p className="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">{t('You have confirmed receipt of this notice.')}</p>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* Description */}
                        <CardContent className="p-6">
                            {notice.description ? (
                                <div
                                    className="prose prose-sm max-w-none text-sm leading-relaxed text-slate-700 dark:text-slate-300 dark:prose-invert"
                                    dangerouslySetInnerHTML={{ __html: notice.description }}
                                />
                            ) : (
                                <div className="flex flex-col items-center py-12 text-muted-foreground gap-2">
                                    <BookOpen className="h-8 w-8 opacity-30" />
                                    <p className="text-sm">{t('No description available.')}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Attachments */}
                    {hasAttachments && (
                        <AttachmentsSection attachments={notice.attachments!} t={t} />
                    )}

                    {/* Comments */}
                    {notice.allow_comments && (
                        <Card className="border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                            <SectionHeader
                                icon={<MessageSquare className="h-4 w-4" />}
                                iconBg="bg-blue-50 border-blue-100 dark:bg-blue-900/20 dark:border-blue-800"
                                iconColor="text-blue-600 dark:text-blue-400"
                                title={t('Comments')}
                                subtitle={totalCommentCount > 0
                                    ? `${totalCommentCount} ${totalCommentCount === 1 ? t('comment') : t('comments')}`
                                    : t('Share your thoughts on this notice')
                                }
                                count={totalCommentCount}
                            />
                            <CardContent className="p-0">
                                {canComment && (
                                    <div className="p-5 border-b dark:border-slate-700 bg-slate-50/30 dark:bg-slate-800/60">
                                        <div className="flex gap-3 items-start">
                                            <Avatar user={auth.user} size="lg" />
                                            <div className="flex-1 min-w-0">
                                                <p className="text-xs font-medium text-slate-700 dark:text-slate-300 mb-2">{t('Add a comment')}</p>
                                                <CommentComposer
                                                    value={newComment}
                                                    onChange={setNewComment}
                                                    onSubmit={handleComment}
                                                    placeholder={t('Write a comment...')}
                                                    submitLabel={t('Post Comment')}
                                                    submitting={submitting}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {comments && comments.length > 0 ? (
                                    <div className="p-5 space-y-5 max-h-[520px] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-slate-100 dark:scrollbar-track-slate-900">
                                        {comments.map((comment: NoticeComment) => (
                                            <CommentRow key={comment.id} comment={comment} auth={auth} noticeId={notice.id} t={t} depth={0} />
                                        ))}
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-12 px-6 text-center">
                                        <div className="h-12 w-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                            <MessageSquare className="h-6 w-6 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <p className="text-sm font-medium text-slate-700 dark:text-slate-300">{t('No comments yet')}</p>
                                        <p className="text-xs text-muted-foreground mt-1 max-w-xs">
                                            {canComment
                                                ? t('Be the first to share your thoughts on this notice.')
                                                : t('Comments will appear here once users start discussing.')
                                            }
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* ── Sidebar ── */}
                {hasSidebar && (
                    <div className="space-y-5 xl:sticky xl:top-6">

                        {/* Notice Info */}
                        {canViewNoticeInfo && (
                            <Card className="border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-800">
                                <SectionHeader
                                    icon={<BookOpen className="h-4 w-4" />}
                                    iconBg="bg-slate-100 dark:bg-slate-700 border-slate-200 dark:border-slate-600"
                                    iconColor="text-slate-500 dark:text-slate-400"
                                    title={t('Notice Info')}
                                />
                                <div className="px-4 py-4 space-y-2.5">
                                    <InfoRow label={t('Status')}>{statusBadge(notice.status, t)}</InfoRow>
                                    <InfoRow label={t('Priority')}>{priorityBadge(notice.priority, t)}</InfoRow>
                                    <InfoRow label={t('Target')}>{targetTypeLabel(notice.target_type, t)}</InfoRow>
                                    {['department', 'role'].includes(notice.target_type) && notice.target_names && notice.target_names.length > 0 && (
                                        <div className="space-y-1.5">
                                            <span className="text-muted-foreground text-xs">
                                                {notice.target_type === 'department' ? t('Departments') : t('Roles')}
                                            </span>
                                            <div className="flex flex-wrap gap-1">
                                                {notice.target_names.map((name, i) => (
                                                    <span key={i} className={`px-2 py-0.5 rounded-full text-xs font-medium ${notice.target_type === 'department' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300'}`}>
                                                        {name}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    <Separator className="my-1 dark:bg-slate-700" />
                                    <InfoRow label={t('Start Date')}>
                                        {notice.start_date ? formatDate(notice.start_date) : '-'}
                                    </InfoRow>
                                    <InfoRow label={t('Expiry')}>
                                        {notice.expiry_date
                                            ? <span className={isExpired ? 'text-red-600 dark:text-red-400 font-semibold' : 'dark:text-slate-200'}>
                                                {formatDate(notice.expiry_date)}
                                            </span>
                                            : <span className="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                                <CheckCircle2 className="h-3 w-3" /> {t('No expiry')}
                                            </span>
                                        }
                                    </InfoRow>
                                    <Separator className="my-1 dark:bg-slate-700" />
                                    <InfoRow label={t('Comments')}>
                                        <span className={notice.allow_comments ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground'}>
                                            {notice.allow_comments ? t('Enabled') : t('Disabled')}
                                        </span>
                                    </InfoRow>
                                    <InfoRow label={t('Acknowledgment')}>
                                        <span className={notice.require_acknowledgment ? 'text-orange-600 dark:text-orange-400' : 'text-muted-foreground'}>
                                            {notice.require_acknowledgment ? t('Required') : t('Not required')}
                                        </span>
                                    </InfoRow>
                                    {notice.is_pinned && (
                                        <InfoRow label={t('Pinned')}>
                                            <span className="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                                <Pin className="h-3 w-3" /> {t('Yes')}
                                            </span>
                                        </InfoRow>
                                    )}
                                </div>
                            </Card>
                        )}

                        {/* Read Statistics */}
                        {canViewReadStats && readStats && (() => {
                            const total = readStats.read.length + readStats.unread.length;
                            const readPercent = total > 0 ? Math.round((readStats.read.length / total) * 100) : 0;
                            const acknowledgedCount = readStats.read.filter((s: any) => s.acknowledged_at).length;
                            const ackPercent = total > 0 ? Math.round((acknowledgedCount / total) * 100) : 0;

                            return (
                                <Card className="border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-800">
                                    <SectionHeader
                                        icon={<Users className="h-4 w-4" />}
                                        iconBg="bg-blue-50 border-blue-100 dark:bg-blue-900/20 dark:border-blue-800"
                                        iconColor="text-blue-600 dark:text-blue-400"
                                        title={t('Read Statistics')}
                                    />
                                    <CardContent className="p-4 space-y-4">

                                        {/* Stat pills */}
                                        <div className={`grid gap-2 ${notice.require_acknowledgment ? 'grid-cols-3' : 'grid-cols-2'}`}>
                                            <div className="flex flex-col items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 py-3 gap-0.5">
                                                <span className="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{readStats.read.length}</span>
                                                <span className="text-[10px] text-emerald-700 dark:text-emerald-300 font-medium">{t('Read')}</span>
                                            </div>
                                            {notice.require_acknowledgment && (
                                                <div className="flex flex-col items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 py-3 gap-0.5">
                                                    <span className="text-xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">{acknowledgedCount}</span>
                                                    <span className="text-[10px] text-blue-700 dark:text-blue-300 font-medium">{t("Ack'd")}</span>
                                                </div>
                                            )}
                                            <div className="flex flex-col items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 py-3 gap-0.5">
                                                <span className="text-xl font-bold text-orange-500 dark:text-orange-400 tabular-nums">{readStats.unread.length}</span>
                                                <span className="text-[10px] text-orange-700 dark:text-orange-300 font-medium">{t('Not Read')}</span>
                                            </div>
                                        </div>

                                        {/* Progress bars */}
                                        {total > 0 && (
                                            <div className="space-y-2.5">
                                                <div className="space-y-1">
                                                    <div className="flex items-center justify-between text-[11px]">
                                                        <span className="text-muted-foreground">{t('Read rate')}</span>
                                                        <span className="font-semibold text-slate-700 dark:text-slate-300 tabular-nums">{readPercent}%</span>
                                                    </div>
                                                    <div className="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                        <div className="h-full rounded-full bg-emerald-400 dark:bg-emerald-500 transition-all duration-500" style={{ width: `${readPercent}%` }} />
                                                    </div>
                                                </div>
                                                {notice.require_acknowledgment && (
                                                    <div className="space-y-1">
                                                        <div className="flex items-center justify-between text-[11px]">
                                                            <span className="text-muted-foreground">{t('Ack rate')}</span>
                                                            <span className="font-semibold text-slate-700 dark:text-slate-300 tabular-nums">{ackPercent}%</span>
                                                        </div>
                                                        <div className="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                            <div className="h-full rounded-full bg-blue-400 dark:bg-blue-500 transition-all duration-500" style={{ width: `${ackPercent}%` }} />
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        )}

                                        {/* Read list */}
                                        {readStats.read.length > 0 && (
                                            <div className="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                                                <div className="px-3 py-2 bg-emerald-50/60 dark:bg-emerald-900/20 border-b border-slate-200 dark:border-slate-700 flex items-center gap-1.5">
                                                    <CheckCheck className="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" />
                                                    <span className="text-xs font-semibold text-emerald-700 dark:text-emerald-300">{t('Read')} ({readStats.read.length})</span>
                                                </div>
                                                <div className="divide-y divide-slate-100 dark:divide-slate-700 max-h-44 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-600">
                                                    {readStats.read.map((stat: any, index: number) => (
                                                        <div key={index} className="flex items-center gap-2.5 px-3 py-2 hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors">
                                                            <Avatar user={stat.user} size="sm" />
                                                            <div className="flex-1 min-w-0">
                                                                <p className="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">{stat.user?.name}</p>
                                                                <p className="text-[10px] text-muted-foreground">{formatDateTime(stat.read_at)}</p>
                                                            </div>
                                                            {notice.require_acknowledgment && (
                                                                <span className={`inline-flex items-center gap-0.5 text-[10px] font-medium shrink-0 px-1.5 py-0.5 rounded-full ${stat.acknowledged_at ? 'bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' : 'bg-orange-50 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300'}`}>
                                                                    {stat.acknowledged_at ? <CheckCheck className="h-2.5 w-2.5" /> : <ShieldAlert className="h-2.5 w-2.5" />}
                                                                    {stat.acknowledged_at ? t('Ack') : t('Pending')}
                                                                </span>
                                                            )}
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        )}

                                        {/* Unread list */}
                                        {readStats.unread.length > 0 && (
                                            <div className="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                                                <div className="px-3 py-2 bg-orange-50/60 dark:bg-orange-900/20 border-b border-slate-200 dark:border-slate-700 flex items-center gap-1.5">
                                                    <User className="h-3.5 w-3.5 text-orange-500 dark:text-orange-400" />
                                                    <span className="text-xs font-semibold text-orange-700 dark:text-orange-300">{t('Not Read')} ({readStats.unread.length})</span>
                                                </div>
                                                <div className="divide-y divide-slate-100 dark:divide-slate-700 max-h-44 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-600">
                                                    {readStats.unread.map((user: any, index: number) => (
                                                        <div key={index} className="flex items-center gap-2.5 px-3 py-2 hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors">
                                                            <Avatar user={user} size="sm" />
                                                            <p className="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">{user.name}</p>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        )}

                                        {/* All read */}
                                        {readStats.unread.length === 0 && readStats.read.length > 0 && (
                                            <div className="flex items-center gap-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 px-4 py-3">
                                                <div className="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-700 flex items-center justify-center shrink-0">
                                                    <BadgeCheck className="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <p className="text-xs font-semibold text-emerald-700 dark:text-emerald-300">{t('Everyone has read this notice.')}</p>
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>
                            );
                        })()}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
