import React, { useState, useMemo, useCallback } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';

import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Mail, Inbox, Send, FileText, Trash2, Archive, Star, Settings, Plus, Eye, Reply, ChevronDown, ChevronUp, Clock, RefreshCw } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';
import { SearchInput } from "@/components/ui/search-input";
import { Badge } from "@/components/ui/badge";
import NoRecordsFound from '@/components/no-records-found';
import { cn } from '@/lib/utils';
import { Pagination } from "@/components/ui/pagination";
import { formatDateTime } from '@/utils/helpers';

interface Email {
    id: string;
    subject: string;
    from: string;
    date: string;
    isRead: boolean;
    isStarred: boolean;
    hasAttachment: boolean;
    body: string;
    avatar?: string;
    sender_name?: string;
}

interface MailBoxIndexProps {
    emails: {
        data: Email[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    folders: string[];
    currentFolder: string;
    credential: {
        email: string;
        from_name?: string;
        imap_host?: string;
        imap_port?: string;
        imap_encryption?: string;
        smtp_host?: string;
        smtp_port?: string;
        smtp_encryption?: string;
    } | null;
    filters: {
        search: string;
        sort: string;
        direction: string;
        per_page: number;
    };
    auth: {
        user: {
            permissions: string[];
        };
    };
}

export default function Index() {
    const { t } = useTranslation();
    const { emails, folders, currentFolder, credential, filters, auth } = usePage<MailBoxIndexProps>().props;
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedEmails, setSelectedEmails] = useState<string[]>([]);
    const [sortField, setSortField] = useState(filters.sort || 'date');
    const [sortDirection, setSortDirection] = useState(filters.direction || 'desc');
    const [emailsData, setEmailsData] = useState(emails.data);

    // Custom delete handler for mailbox
    const [deleteState, setDeleteState] = useState<{
        isOpen: boolean;
        itemId: string | null;
        message: string;
    }>({
        isOpen: false,
        itemId: null,
        message: ''
    });

    const openDeleteDialog = (emailId: string, message: string) => {
        setDeleteState({
            isOpen: true,
            itemId: emailId,
            message
        });
    };

    const closeDeleteDialog = () => {
        setDeleteState({
            isOpen: false,
            itemId: null,
            message: ''
        });
    };

    // Update emails data when props change
    React.useEffect(() => {
        setEmailsData(emails.data);
    }, [emails.data]);

    // Auto-refresh emails when returning from compose
    React.useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('refresh') === 'true') {
            // Remove refresh param and reload data
            const newUrl = window.location.pathname;
            window.history.replaceState({}, '', newUrl);

            // Refresh current folder data
            router.get(route(`mailbox.${currentFolder}`), {
                search: searchTerm,
                per_page: filters.per_page
            }, {
                preserveState: false,
                replace: true
            });
        }
    }, []);

    const sortedEmails = useMemo(() => {
        if (!emailsData || emailsData.length === 0) return [];

        const sorted = [...emailsData].sort((a, b) => {
            let valueA = a[sortField] || '';
            let valueB = b[sortField] || '';

            // Handle date sorting
            if (sortField === 'date') {
                const timeA = new Date(valueA).getTime();
                const timeB = new Date(valueB).getTime();
                return sortDirection === 'desc' ? timeB - timeA : timeA - timeB;
            }

            // Handle string sorting (from, subject)
            valueA = valueA.toString().toLowerCase();
            valueB = valueB.toString().toLowerCase();

            if (valueA < valueB) return sortDirection === 'desc' ? 1 : -1;
            if (valueA > valueB) return sortDirection === 'desc' ? -1 : 1;
            return 0;
        });

        return sorted;
    }, [emailsData, sortField, sortDirection]);


    useFlashMessages();


    const handleFolderChange = (folder: string) => {
        router.get(route(`mailbox.${folder}`), {
            search: searchTerm,
            per_page: filters.per_page
        }, {
            preserveState: false,
            replace: false
        });
    };

    const handleSearch = () => {
        router.get(route(`mailbox.${currentFolder}`), {
            search: searchTerm,
            per_page: filters.per_page,
            page: 1
        }, {
            preserveState: false,
            replace: true
        });
    };

    const handleSort = (field: string) => {
        const direction = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortField(field);
        setSortDirection(direction);
    };

    const handleEmailSelect = (emailId: string) => {
        setSelectedEmails(prev =>
            prev.includes(emailId)
                ? prev.filter(id => id !== emailId)
                : [...prev, emailId]
        );
    };

    const handleBulkAction = (action: string) => {
        if (selectedEmails.length === 0) return;

        router.post(route('mailbox.action'), {
            action,
            emails: selectedEmails,
            folder: currentFolder
        }, {
            preserveState: false,
            onSuccess: () => {
                setSelectedEmails([]);
            },
            onError: (errors) => {
            }
        });
    };

    const handleSingleEmailAction = (action: string, emailId: string) => {
        if (action === 'delete') {
            openDeleteDialog(emailId, t('Are you sure you want to delete this email?'));
            return;
        }

        router.post(route('mailbox.action'), {
            action,
            emails: [emailId],
            folder: currentFolder
        }, {
            preserveState: true,
            onSuccess: () => {
                router.reload();
            }
        });
    };

    const handleConfirmDelete = () => {
        if (deleteState.itemId) {
            router.post(route('mailbox.action'), {
                action: 'delete',
                emails: [deleteState.itemId],
                folder: currentFolder
            }, {
                preserveState: false,
                onSuccess: () => {
                    closeDeleteDialog();
                },
                onError: (errors) => {
                    closeDeleteDialog();
                }
            });
        }
    };

    const getSenderInitials = useCallback((from: string) => {
        if (!from || typeof from !== 'string') return 'UN';

        // Decode HTML entities
        const decodedFrom = from
            .replace(/&amp;/g, '&')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'");

        // Extract name from email format "Name <email@domain.com>" or just "email@domain.com"
        let name = decodedFrom;

        // If it contains < and >, extract the name part
        const nameMatch = decodedFrom.match(/^(.+?)\s*<.*>$/);
        if (nameMatch) {
            name = nameMatch[1].trim();
        } else if (decodedFrom.includes('@')) {
            // If it's just an email, use the part before @
            name = decodedFrom.split('@')[0];
        }

        // Clean up the name (remove quotes, extra spaces)
        name = name.replace(/["']/g, '').trim();

        // Generate initials
        const words = name.split(/\s+/).filter(word => word.length > 0);
        if (words.length === 0) return 'UN';

        if (words.length === 1) {
            // Single word - take first 2 characters
            return words[0].substring(0, 2).toUpperCase();
        } else {
            // Multiple words - take first letter of first 2 words
            return words.slice(0, 2).map(word => word[0]).join('').toUpperCase();
        }
    }, []);

    const getAvatarUrl = useCallback((email: Email) => {
        // Check if email has avatar field
        if (email.avatar) {
            return email.avatar;
        }

        // Extract domain from email for company logos
        const emailMatch = email.from.match(/<([^>]+)>/) || [null, email.from];
        const emailAddress = emailMatch[1] || email.from;

        if (emailAddress.includes('@')) {
            const domain = emailAddress.split('@')[1];
            // Use Gravatar or company logo services
            return `https://logo.clearbit.com/${domain}`;
        }

        return null;
    }, []);



    const mainFolders = [
        { key: 'inbox', label: t('Inbox'), icon: Inbox },
        { key: 'starred', label: t('Starred'), icon: Star },
        { key: 'sent', label: t('Sent'), icon: Send },
        { key: 'drafts', label: t('Drafts'), icon: FileText }
    ];

    const moreFolders = [
        { key: 'archive', label: t('Archive'), icon: Archive },
        { key: 'spam', label: t('Spam'), icon: Mail },
        { key: 'trash', label: t('Trash'), icon: Trash2 }
    ];

    // Check if user has any action permissions
    const hasActionPermissions = auth.user?.permissions?.some(permission =>
        ['view-mailbox-email', 'reply-email-mailbox', 'delete-email-mailbox'].includes(permission)
    );

    // Check if user has bulk action permissions (for checkbox column)
    const hasBulkActionPermissions = auth.user?.permissions?.some(permission =>
        ['action-email-mailbox', 'delete-email-mailbox'].includes(permission)
    );

    const tableColumns = [
        // Only show checkbox column if user has bulk action permissions
        ...(hasBulkActionPermissions ? [{
            key: 'select',
            header: (
                <input
                    type="checkbox"
                    onChange={(e) => {
                        if (e.target.checked) {
                            setSelectedEmails(sortedEmails.map(email => email.id));
                        } else {
                            setSelectedEmails([]);
                        }
                    }}
                    checked={selectedEmails.length === sortedEmails.length && sortedEmails.length > 0}
                />
            ),
            render: (_: any, email: Email) => (
                <input
                    type="checkbox"
                    checked={selectedEmails.includes(email.id)}
                    onChange={() => handleEmailSelect(email.id)}
                />
            ),
            className: 'w-12'
        }] : []),
        ...(auth.user?.permissions?.includes('action-email-mailbox') ? [{
            key: 'star',
            header: '',
            render: (_: any, email: Email) => (
                <TooltipProvider>
                    <Tooltip delayDuration={0}>
                        <TooltipTrigger asChild>
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => {
                                    router.post(route('mailbox.action'), {
                                        action: email.isStarred ? 'unstar' : 'star',
                                        emails: [email.id]
                                    }, {
                                        onSuccess: () => {
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 500);
                                        }
                                    });
                                }}
                                className="h-8 w-8 p-0"
                            >
                                <Star className={cn(
                                    "h-4 w-4",
                                    email.isStarred ? "text-yellow-500 fill-current" : "text-gray-300 hover:text-gray-500"
                                )} />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{email.isStarred ? t('Unstar') : t('Star')}</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            ),
            className: 'w-12'
        }] : []),
        {
            key: 'from',
            header: t('From'),
            sortable: true,
            render: (value: string, email: Email) => (
                <div
                    className="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 -m-2 rounded"
                    onClick={() => router.get(route('mailbox.show', email.id))}
                >
                    <Avatar className="h-8 w-8 flex-shrink-0">
                        <AvatarImage
                            src={getAvatarUrl(email)}
                            alt={email.sender_name || value}
                            className="object-cover"
                        />
                        <AvatarFallback className="bg-primary text-primary-foreground text-xs">
                            {getSenderInitials(value)}
                        </AvatarFallback>
                    </Avatar>
                    <div className={email.isRead ? 'font-normal text-gray-600' : 'font-bold text-black'}>
                        {value || t('Unknown Sender')}
                    </div>
                </div>
            )
        },
        {
            key: 'subject',
            header: t('Subject'),
            sortable: true,
            render: (value: string, email: Email) => (
                <div
                    className="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 -m-2 rounded"
                    onClick={() => router.get(route('mailbox.show', email.id))}
                >
                    <span className={email.isRead ? 'font-normal text-gray-600' : 'font-bold text-black'}>{value || t('No Subject')}</span>
                    {email.hasAttachment && <Mail className="h-4 w-4 text-gray-500" />}
                    {!email.isRead && <div className="w-2 h-2 bg-blue-600 rounded-full ml-auto"></div>}
                </div>
            )
        },
        {
            key: 'date',
            header: t('Date'),
            sortable: true,
            render: (value: string) => {
                return (
                    <span className="text-sm text-gray-500">
                        {formatDateTime(value)}
                    </span>
                );
            }
        },
        // Only include Actions column if user has any action permissions
        ...(hasActionPermissions ? [{
            key: 'actions',
            header: t('Actions'),
            render: (_: any, email: Email) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        {auth.user?.permissions?.includes('view-mailbox-email') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => {
                                            // Mark as read when viewing
                                            if (!email.isRead) {
                                                handleSingleEmailAction('read', email.id);
                                                setTimeout(() => {
                                                    router.get(route('mailbox.show', email.id));
                                                }, 100);
                                            } else {
                                                router.get(route('mailbox.show', email.id));
                                            }
                                        }}
                                        className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                    >
                                        <Eye className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('View')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('reply-email-mailbox') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => router.get(route('mailbox.reply', email.id))}
                                        className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                    >
                                        <Reply className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Reply')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('delete-email-mailbox') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => handleSingleEmailAction('delete', email.id)}
                                        className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t('Delete')}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            )
        }] : [])
    ];

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('MailBox'), url: route('mailbox.inbox') }
                ]}
                pageTitle={t('Email Management')}
            >
                <Head title={t('MailBox')} />

                <div className="flex flex-col md:flex-row gap-8">
                    {/* Sidebar Navigation */}
                    <div className="md:w-64 flex-shrink-0">
                        <div className="sticky top-4">
                            {/* Compose Button */}
                            {auth.user?.permissions?.includes('create-email-mailbox') && (
                                <div className="mb-4">
                                    <Button
                                        className="w-full"
                                        onClick={() => router.get(route('mailbox.compose'))}
                                    >
                                        <Plus className="h-4 w-4 mr-2" />
                                        {t('Compose')}
                                    </Button>
                                </div>
                            )}

                            <div className="space-y-1">
                                {/* All Folders */}
                                {[...mainFolders, ...moreFolders].map((folder) => {
                                    const Icon = folder.icon;
                                    const isActive = currentFolder === folder.key;
                                    return (
                                        <Button
                                            key={folder.key}
                                            variant="ghost"
                                            className={cn(
                                                'w-full justify-start',
                                                isActive ? 'bg-muted font-medium' : ''
                                            )}
                                            onClick={() => handleFolderChange(folder.key)}
                                        >
                                            <Icon className="h-4 w-4 mr-2" />
                                            <span>{folder.label}</span>
                                        </Button>
                                    );
                                })}

                                {/* Configuration */}
                                {auth.user?.permissions?.includes('manage-mailbox-settings') && (
                                    <div className="pt-4 border-t">
                                        <Button
                                            variant="ghost"
                                            className="w-full justify-start"
                                            onClick={() => router.get(route('mailbox.credentials.configuration'))}
                                        >
                                            <Settings className="h-4 w-4 mr-2" />
                                            <span>{t('Configuration')}</span>
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Main Content */}
                    <div className="flex-1">
                        <Card className="shadow-sm">
                            {/* Search & Controls Header */}
                            <CardContent className="p-6 border-b bg-gray-50/50">
                                <div className="flex items-center justify-between gap-4">
                                    <div className="flex-1 max-w-md">
                                        <SearchInput
                                            value={searchTerm}
                                            onChange={setSearchTerm}
                                            onSearch={handleSearch}
                                            placeholder={t('Search emails...')}
                                        />
                                    </div>
                                    <div className="flex items-center gap-3">
                                        {selectedEmails.length > 0 && (
                                            <div className="flex items-center gap-2">
                                                <Badge variant="secondary">
                                                    {selectedEmails.length} {t('selected')}
                                                </Badge>
                                                {auth.user?.permissions?.includes('action-email-mailbox') && (
                                                    <>
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                            onClick={() => handleBulkAction('read')}
                                                        >
                                                            {t('Mark Read')}
                                                        </Button>
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                            onClick={() => handleBulkAction('unread')}
                                                        >
                                                            {t('Mark Unread')}
                                                        </Button>
                                                    </>
                                                )}
                                                {auth.user?.permissions?.includes('delete-email-mailbox') && (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        onClick={() => handleBulkAction('delete')}
                                                    >
                                                        {t('Delete')}
                                                    </Button>
                                                )}
                                            </div>
                                        )}
                                        {auth.user?.permissions?.includes('manage-mailbox') && (
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        onClick={() => {
                                                            router.get(route(`mailbox.${currentFolder}`), {
                                                                search: searchTerm,
                                                                per_page: filters.per_page
                                                            }, {
                                                                preserveState: false,
                                                                replace: false
                                                            });
                                                        }}
                                                    >
                                                        <RefreshCw className="h-4 w-4" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>{t('Sync Emails')}</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        )}
                                    </div>
                                </div>
                            </CardContent>

                            {/* Email List */}
                            <CardContent className="p-0">
                                <div className="overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[70vh] rounded-none w-full">
                                    <div className="min-w-[800px]">
                                        <DataTable
                                            data={sortedEmails}
                                            columns={tableColumns}
                                            onSort={handleSort}
                                            sortKey={sortField}
                                            sortDirection={sortDirection as 'asc' | 'desc'}
                                            className="rounded-none"
                                            emptyState={
                                                <NoRecordsFound
                                                    icon={Mail}
                                                    title={t('No emails found')}
                                                    description={credential ? t('The inbox is empty.') : t('The email settings need to be configured first.')}
                                                    hasFilters={!!searchTerm}
                                                    onClearFilters={() => setSearchTerm('')}
                                                    className="h-auto"
                                                />
                                            }
                                        />
                                    </div>
                                </div>
                            </CardContent>

                            {/* Pagination Footer */}
                            <CardContent className="px-4 py-2 border-t bg-gray-50/30">
                                <Pagination
                                    data={emails}
                                    routeName={`mailbox.${currentFolder}`}
                                    filters={{
                                        search: searchTerm,
                                        per_page: filters.per_page
                                    }}
                                    preserveState={false}
                                />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </AuthenticatedLayout>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Email')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={handleConfirmDelete}
                variant="destructive"
            />
        </TooltipProvider>
    );
}