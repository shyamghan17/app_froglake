import React, { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { formatDateTime } from '@/utils/helpers';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import {
    Reply,
    ArrowLeft,
    Star,
    Trash2,
    Archive,
    Flag,
    MailOpen,
    FolderOpen,
    MoreHorizontal,
    ReplyAll,
    Forward,
    Download as DownloadIcon,
    Paperclip,
    User
} from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";

interface Email {
    id: string;
    subject: string;
    from: string;
    from_name?: string;
    from_email?: string;
    to: string[];
    date: string;
    body: string;
    isRead: boolean;
    isStarred: boolean;
    hasAttachment: boolean;
}

interface ShowProps {
    email: Email;
    }

export default function Show() {
    const { t } = useTranslation();
    const { email, auth } = usePage<ShowProps>().props;
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    useFlashMessages();

    // Handle case when email is not loaded
    if (!email) {
        return (
            <AuthenticatedLayout
                breadcrumbs={[{ label: t('MailBox'), href: route('mailbox.inbox') }]}
                pageTitle={t('Loading...')}
            >
                <div className="flex items-center justify-center h-64">
                    <p>{t('Loading email...')}</p>
                </div>
            </AuthenticatedLayout>
        );
    }

    const handleReply = () => {
        router.get(route('mailbox.reply', email.id));
    };

    const handleBack = () => {
        router.get(route('mailbox.inbox'));
    };

    const handleAction = (action: string, folder?: string) => {
        if (action === 'delete') {
            setDeleteDialogOpen(true);
            return;
        }
        
        router.post(route('mailbox.action'), {
            action,
            emails: [email.id],
            folder
        }, {
            preserveState: false,
            onSuccess: () => {
                if (action === 'archive' || action === 'spam' || action === 'move') {
                    router.get(route('mailbox.inbox'));
                }
            }
        });
    };

    const handleConfirmDelete = () => {
        router.post(route('mailbox.action'), {
            action: 'delete',
            emails: [email.id]
        }, {
            preserveState: false,
            onSuccess: () => {
                setDeleteDialogOpen(false);
                router.get(route('mailbox.inbox'));
            },
            onError: () => {
                setDeleteDialogOpen(false);
            }
        });
    };

    const handleMoveTo = (folder: string) => {
        handleAction('move', folder);
    };

    const getSenderInitials = () => {
        if (email.from_name) {
            return email.from_name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        }
        return email.from_email?.charAt(0).toUpperCase() || t('U');
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('MailBox'), url: route('mailbox.inbox') },
                { label: t('View Email') }
            ]}
            pageTitle={email.subject}
            pageActions={
                <TooltipProvider>
                    <div className="flex gap-1">
                        <Tooltip>
                            <TooltipTrigger asChild>
                                <Button variant="ghost" size="sm" onClick={handleBack}>
                                    <ArrowLeft className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t('Back')}</p>
                            </TooltipContent>
                        </Tooltip>

                        {auth.user?.permissions?.includes('action-email-mailbox') && (
                            <>
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => handleAction('archive')}>
                                            <Archive className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Archive')}</p>
                                    </TooltipContent>
                                </Tooltip>

                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => handleAction('spam')}>
                                            <Flag className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Report Spam')}</p>
                                    </TooltipContent>
                                </Tooltip>

                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => handleAction('unread')}>
                                            <MailOpen className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('Mark as Unread')}</p>
                                    </TooltipContent>
                                </Tooltip>

                                <div className="h-6 w-px bg-gray-300 mx-1" />

                                <DropdownMenu>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="ghost" size="sm">
                                                    <FolderOpen className="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Move to')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                    <DropdownMenuContent>
                                        <DropdownMenuItem onClick={() => handleMoveTo('inbox')}>
                                            {t('Inbox')}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleMoveTo('archive')}>
                                            {t('Archive')}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleMoveTo('spam')}>
                                            {t('Spam')}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleMoveTo('trash')}>
                                            {t('Trash')}
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>

                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button variant="ghost" size="sm" onClick={() => handleAction(email.isStarred ? 'unstar' : 'star')}>
                                            <Star className={`h-4 w-4 ${email.isStarred ? 'fill-current text-yellow-500' : ''}`} />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{email.isStarred ? t('Unstar') : t('Star')}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </>
                        )}

                        {auth.user?.permissions?.includes('delete-email-mailbox') && (
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button variant="ghost" size="sm" onClick={() => handleAction('delete')}>
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Delete')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}

                        {(auth.user?.permissions?.includes('reply-email-mailbox') || true) && (
                            <DropdownMenu>
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="ghost" size="sm">
                                                <MoreHorizontal className="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t('More')}</p>
                                    </TooltipContent>
                                </Tooltip>
                                <DropdownMenuContent>
                                    {auth.user?.permissions?.includes('reply-email-mailbox') && (
                                        <>
                                            <DropdownMenuItem onClick={() => router.get(route('mailbox.reply', email.id))}>
                                                <Reply className="h-4 w-4 mr-2" />
                                                {t('Reply')}
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                        </>
                                    )}
                                    <DropdownMenuItem onClick={() => {
                                        // Create clean text content for download
                                        const cleanContent = `Subject: ${email.subject}\n` +
                                            `From: ${email.from}\n` +
                                            `To: ${email.to.join(', ')}\n` +
                                            `Date: ${formatDateTime(email.date)}\n\n` +
                                            `${email.body.replace(/<[^>]*>/g, '').replace(/&[^;]+;/g, ' ').trim()}\n`;

                                        const element = document.createElement('a');
                                        const file = new Blob([cleanContent], { type: 'text/plain' });
                                        element.href = URL.createObjectURL(file);
                                        element.download = `${email.subject.replace(/[^a-z0-9]/gi, '_')}.txt`;
                                        document.body.appendChild(element);
                                        element.click();
                                        document.body.removeChild(element);
                                    }}>
                                        <DownloadIcon className="h-4 w-4 mr-2" />
                                        {t('Download')}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>
                </TooltipProvider>
            }
        >
            <Head title={email.subject} />
            
            <ConfirmationDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                title={t('Delete Email')}
                message={t('Are you sure you want to delete this email? This action cannot be undone.')}
                confirmText={t('Delete')}
                onConfirm={handleConfirmDelete}
                variant="destructive"
            />

            <Card>
                <CardHeader className="pb-4">
                    <div className="flex items-center justify-end gap-2 mb-4">
                        {email.isStarred && <Star className="h-5 w-5 text-yellow-500 fill-current" />}
                        {email.hasAttachment && <Paperclip className="h-5 w-5 text-gray-500" />}
                    </div>

                    <div className="flex items-start gap-4">
                        <Avatar className="h-10 w-10 flex-shrink-0">
                            <AvatarFallback className="bg-blue-500 text-white">
                                {getSenderInitials()}
                            </AvatarFallback>
                        </Avatar>

                        <div className="flex-1 min-w-0">
                            <div className="flex items-center gap-2 mb-1">
                                <span className="font-medium text-gray-900">
                                    {email.from_name || email.from_email || email.from}
                                </span>
                            </div>

                            <div className="text-sm text-gray-600 mb-2">
                                {formatDateTime(email.date)}
                            </div>

                            <div className="text-sm text-gray-600">
                                <span className="font-medium">to</span> {email.to.join(', ')}
                            </div>

                            <details className="mt-3">
                                <summary className="cursor-pointer text-sm text-blue-600 hover:underline">
                                    {t('Show details')}
                                </summary>
                                <div className="mt-3 p-4 bg-gray-50 rounded-lg text-sm space-y-2">
                                    <div><strong>{t('from')}:</strong> {email.from}</div>
                                    <div><strong>{t('reply-to')}:</strong> {email.from}</div>
                                    <div><strong>{t('to')}:</strong> {email.to.join(', ')}</div>
                                    <div><strong>{t('date')}:</strong> {formatDateTime(email.date)}</div>
                                    <div><strong>{t('subject')}:</strong> {email.subject}</div>
                                    <div><strong>{t('mailing list')}:</strong> <span className="text-blue-600 cursor-pointer hover:underline">{t('Filter messages from this mailing list')}</span></div>
                                    <div><strong>{t('mailed-by')}:</strong> mail.server.com</div>
                                    <div><strong>{t('signed-by')}:</strong> {email.from_email?.split('@')[1] || t('domain.com')}</div>
                                    <div><strong>{t('security')}:</strong> 🔒 {t('Standard encryption (TLS)')} <span className="text-blue-600 cursor-pointer hover:underline">{t('Learn more')}</span></div>
                                </div>
                            </details>
                        </div>

                        {auth.user?.permissions?.includes('reply-email-mailbox') && (
                            <div className="flex items-center gap-1">
                                <Button variant="ghost" size="sm" onClick={handleReply}>
                                    <Reply className="h-4 w-4" />
                                </Button>
                            </div>
                        )}
                    </div>
                </CardHeader>

                <CardContent>
                    <div className="prose max-w-none">
                        <div
                            className="email-content"
                            dangerouslySetInnerHTML={{ __html: email.body }}
                            style={{
                                fontFamily: 'Arial, sans-serif',
                                lineHeight: '1.6',
                                color: '#333'
                            }}
                        />
                    </div>
                </CardContent>
            </Card>

            {auth.user?.permissions?.includes('reply-email-mailbox') && (
                <div className="mt-6 flex gap-3">
                    <Button onClick={handleReply} className="px-6">
                        <Reply className="h-4 w-4 mr-2" />
                        {t('Reply')}
                    </Button>
                </div>
            )}
        </AuthenticatedLayout>
    );
}