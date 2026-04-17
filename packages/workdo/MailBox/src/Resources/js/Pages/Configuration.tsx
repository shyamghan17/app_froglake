import { useState } from 'react';
import { Head, usePage, useForm, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useDeleteHandler } from '@/hooks/useDeleteHandler';
import { toast } from 'sonner';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Settings, Mail, Inbox, Send, FileText, Trash2, Archive, Star, Plus, Save, User, Server, Lock, Eye, EyeOff, RefreshCw } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

interface MailBoxCredential {
    id?: number;
    email: string;
    password: string;
    imap_host: string;
    imap_port: number;
    imap_encryption: string;
    smtp_host: string;
    smtp_port: number;
    smtp_encryption: string;
    from_name?: string;
    mail_driver?: string;
    is_active: boolean;
}

interface QuickSetupProvider {
    id: string;
    name: string;
    icon: string;
    domains: string[];
    description: string;
}

interface ConfigurationProps {
    credential: MailBoxCredential | null;
    credentials: MailBoxCredential[];
    quickSetupProviders: QuickSetupProvider[];
    auth: {
        user: {
            permissions: string[];
        };
    };
}

export default function Configuration() {
    const { t } = useTranslation();
    const { credential, credentials, quickSetupProviders, auth } = usePage<ConfigurationProps>().props;
    const [showPassword, setShowPassword] = useState(false);
    const [testing, setTesting] = useState(false);
    const [selectedProvider, setSelectedProvider] = useState<string | null>('gmail');
    const [deleteAccountData, setDeleteAccountData] = useState<{ id: number, email: string } | null>(null);

    useFlashMessages();

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } = useDeleteHandler({
        routeName: 'mailbox.credentials.delete.account',
        defaultMessage: t('Are you sure you want to delete the account?')
    });

    const { data, setData, post, processing, errors } = useForm({
        mail_username: credential?.email || '',
        mail_password: '',
        mail_host: credential?.imap_host || 'imap.gmail.com',
        incoming_port: credential?.imap_port || 993,
        outgoing_port: credential?.smtp_port || 465,
        mail_encryption: credential?.imap_encryption || 'ssl',
        mail_from_address: credential?.email || '',
        mail_from_name: credential?.from_name || '',
        mail_driver: credential?.mail_driver || 'smtp'
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (auth.user?.permissions?.includes('create-mailbox-settings')) {
            post(route('mailbox.credentials.store'));
        }
    };

    const handleTestConnection = async () => {
        setTesting(true);
        try {
            const response = await fetch(route('mailbox.credentials.test.connection'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                toast.error(t('The connection test failed due to a server error.'));
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                toast.error(t('The connection test failed due to an invalid server response.'));
                return;
            }

            const result = await response.json();

            if (result.success) {
                toast.success(result.message || t('The connection has been established successfully.'));
            } else {
                toast.error(result.message || t('The connection could not be established.'));
            }
        } catch (error) {
            toast.error(t('The connection test could not be completed. Please check your settings and try again.'));
        } finally {
            setTesting(false);
        }
    };

    const handleQuickSetup = (providerId: string) => {
        setSelectedProvider(providerId);

        const providerConfigs = {
            gmail: {
                mail_host: 'imap.gmail.com',
                incoming_port: 993,
                outgoing_port: 587,
                mail_encryption: 'ssl',
                mail_driver: 'smtp'
            },
            outlook: {
                mail_host: 'outlook.office365.com',
                incoming_port: 993,
                outgoing_port: 587,
                mail_encryption: 'ssl',
                mail_driver: 'smtp'
            },
            yahoo: {
                mail_host: 'imap.mail.yahoo.com',
                incoming_port: 993,
                outgoing_port: 587,
                mail_encryption: 'ssl',
                mail_driver: 'smtp'
            },
            icloud: {
                mail_host: 'imap.mail.me.com',
                incoming_port: 993,
                outgoing_port: 587,
                mail_encryption: 'ssl',
                mail_driver: 'smtp'
            },
            custom: {
                mail_host: '',
                incoming_port: 993,
                outgoing_port: 587,
                mail_encryption: 'ssl',
                mail_driver: 'smtp'
            }
        };

        const config = providerConfigs[providerId as keyof typeof providerConfigs];
        if (config) {
            setData({
                ...data,
                ...config
            });
        }
    };

    const isFieldDisabled = (fieldName: string) => {
        if (selectedProvider === 'custom' || selectedProvider === null) {
            return false; // All fields writable for custom or no selection
        }

        // For quick setup providers, disable server config fields
        const serverFields = ['mail_host', 'incoming_port', 'outgoing_port', 'mail_encryption', 'mail_driver'];
        return serverFields.includes(fieldName);
    };

    const handleQuickSetupSubmit = async (providerId: string) => {
        if (!data.mail_username || !data.mail_password) {
            toast.error(t('The email and password must be entered first.'));
            return;
        }

        try {
            const response = await fetch(route('mailbox.credentials.quick.setup'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    provider: providerId,
                    email: data.mail_username,
                    password: data.mail_password
                })
            });

            if (response.ok) {
                toast.success(t('The email has been configured successfully.'));
                router.get(route('mailbox.inbox'));
            } else {
                const result = await response.json();
                toast.error(result.message || t('The quick setup could not be completed.'));
            }
        } catch (error) {
            toast.error(t('The quick setup could not be completed. Please check your settings.'));
        }
    };

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

    const handleFolderChange = (folder: string) => {
        router.get(route(`mailbox.${folder}`));
    };

    const handleSwitchAccount = (credentialId: number) => {
        router.post(route('mailbox.credentials.switch.account', credentialId));
    };

    const handleDeleteAccount = (credentialId: number, email: string) => {
        setDeleteAccountData({ id: credentialId, email });
        openDeleteDialog(credentialId, t('Are you sure you want to delete the account {{email}}?', { email }));
    };

    const handleConfirmDelete = () => {
        if (deleteAccountData) {
            confirmDelete();
            setDeleteAccountData(null);
        }
    };

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('MailBox'), url: route('mailbox.inbox') },
                    { label: t('Configuration') }
                ]}
                pageTitle={t('Email Configuration')}
            >
                <Head title={t('Email Configuration')} />

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
                                    return (
                                        <Button
                                            key={folder.key}
                                            variant="ghost"
                                            className="w-full justify-start"
                                            onClick={() => handleFolderChange(folder.key)}
                                        >
                                            <Icon className="h-4 w-4 mr-2" />
                                            <span>{folder.label}</span>
                                        </Button>
                                    );
                                })}

                                {/* Configuration */}
                                <div className="pt-4 border-t">
                                    <Button
                                        variant="ghost"
                                        className="w-full justify-start bg-muted font-medium"
                                    >
                                        <Settings className="h-4 w-4 mr-2" />
                                        <span>{t('Configuration')}</span>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Content */}
                    <div className="flex-1">
                        <Card className="shadow-sm">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg font-semibold">
                                    <Mail className="h-5 w-5" />
                                    {t('Email Account Settings')}
                                </CardTitle>
                                <p className="text-sm text-muted-foreground mt-1">
                                    {t('Configure your email account settings for MailBox integration')}
                                </p>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {/* Quick Setup Section First */}
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-2">
                                            <Settings className="h-4 w-4 text-muted-foreground" />
                                            <Label className="font-medium">{t('Quick Setup')}</Label>
                                        </div>
                                        <div className="grid grid-cols-5 gap-2">
                                            <Button
                                                type="button"
                                                variant={selectedProvider === 'gmail' ? "default" : "outline"}
                                                onClick={() => handleQuickSetup('gmail')}
                                                className="w-full"
                                            >
                                                <Mail className="h-4 w-4 mr-2" />
                                                {t('Gmail')}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={selectedProvider === 'outlook' ? "default" : "outline"}
                                                onClick={() => handleQuickSetup('outlook')}
                                                className="w-full"
                                            >
                                                <Mail className="h-4 w-4 mr-2" />
                                                {t('Outlook')}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={selectedProvider === 'yahoo' ? "default" : "outline"}
                                                onClick={() => handleQuickSetup('yahoo')}
                                                className="w-full"
                                            >
                                                <Mail className="h-4 w-4 mr-2" />
                                                {t('Yahoo')}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={selectedProvider === 'icloud' ? "default" : "outline"}
                                                onClick={() => handleQuickSetup('icloud')}
                                                className="w-full"
                                            >
                                                <Mail className="h-4 w-4 mr-2" />
                                                {t('iCloud')}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={selectedProvider === 'custom' ? "default" : "outline"}
                                                onClick={() => handleQuickSetup('custom')}
                                                className="w-full"
                                            >
                                                <Settings className="h-4 w-4 mr-2" />
                                                {t('Custom')}
                                            </Button>
                                        </div>
                                    </div>

                                    {/* Basic Account Information */}
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-3 pb-3 border-b border-gray-200">
                                            <div className="p-2 bg-blue-50 rounded-lg">
                                                <User className="h-5 w-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <Label className="font-semibold text-lg text-gray-900">{t('Account Information')}</Label>
                                                <p className="text-sm text-gray-500 mt-0.5">{t('Your email credentials for authentication')}</p>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label htmlFor="mail_username" className="font-medium">{t('Email Address')}</Label>
                                                <Input
                                                    id="mail_username"
                                                    type="email"
                                                    value={data.mail_username}
                                                    onChange={(e) => setData('mail_username', e.target.value)}
                                                    placeholder={t('your-email@example.com')}
                                                    required
                                                />
                                                {errors.mail_username && (
                                                    <p className="text-sm text-red-600">{errors.mail_username}</p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="mail_password" className="font-medium">{t('Password')}</Label>
                                                <div className="relative">
                                                    <Input
                                                        id="mail_password"
                                                        type={showPassword ? "text" : "password"}
                                                        value={data.mail_password}
                                                        onChange={(e) => setData('mail_password', e.target.value)}
                                                        placeholder={t('Your email password or app password')}
                                                        className="pr-10"
                                                        required
                                                    />
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                                                        onClick={() => setShowPassword(!showPassword)}
                                                    >
                                                        {showPassword ? (
                                                            <EyeOff className="h-4 w-4 text-muted-foreground" />
                                                        ) : (
                                                            <Eye className="h-4 w-4 text-muted-foreground" />
                                                        )}
                                                    </Button>
                                                </div>
                                                {errors.mail_password && (
                                                    <p className="text-sm text-red-600">{errors.mail_password}</p>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Server Settings */}
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-3 pb-3 border-b border-gray-200">
                                            <div className="p-2 bg-green-50 rounded-lg">
                                                <Server className="h-5 w-5 text-green-600" />
                                            </div>
                                            <div>
                                                <Label className="font-semibold text-lg text-gray-900">{t('Server Settings')}</Label>
                                                <p className="text-sm text-gray-500 mt-0.5">{t('IMAP and SMTP server configuration')}</p>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div className="space-y-2">
                                                <Label htmlFor="mail_host" className="font-medium">{t('IMAP Host')}</Label>
                                                <Input
                                                    id="mail_host"
                                                    type="text"
                                                    value={data.mail_host}
                                                    onChange={(e) => setData('mail_host', e.target.value)}
                                                    placeholder={t('imap.gmail.com')}
                                                    disabled={isFieldDisabled('mail_host')}
                                                    className={isFieldDisabled('mail_host') ? 'bg-muted cursor-not-allowed' : ''}
                                                    required
                                                />
                                                {errors.mail_host && (
                                                    <p className="text-sm text-red-600">{errors.mail_host}</p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="mail_encryption" className="font-medium">{t('Encryption')}</Label>
                                                <Select
                                                    value={data.mail_encryption}
                                                    onValueChange={(value) => setData('mail_encryption', value)}
                                                    disabled={isFieldDisabled('mail_encryption')}
                                                >
                                                    <SelectTrigger className={isFieldDisabled('mail_encryption') ? 'bg-muted cursor-not-allowed' : ''}>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="ssl">SSL</SelectItem>
                                                        <SelectItem value="tls">TLS</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {errors.mail_encryption && (
                                                    <p className="text-sm text-red-600">{errors.mail_encryption}</p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="mail_driver" className="font-medium">{t('Mail Driver')}</Label>
                                                {['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'log'].includes(data.mail_driver) ? (
                                                    <Select
                                                        value={data.mail_driver}
                                                        onValueChange={(value) => {
                                                            if (value === 'custom') {
                                                                setData('mail_driver', '');
                                                            } else {
                                                                setData('mail_driver', value);
                                                            }
                                                        }}
                                                        disabled={isFieldDisabled('mail_driver')}
                                                    >
                                                        <SelectTrigger className={isFieldDisabled('mail_driver') ? 'bg-muted cursor-not-allowed' : ''}>
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="smtp">SMTP</SelectItem>
                                                            <SelectItem value="sendmail">Sendmail</SelectItem>
                                                            <SelectItem value="mailgun">Mailgun</SelectItem>
                                                            <SelectItem value="ses">Amazon SES</SelectItem>
                                                            <SelectItem value="postmark">Postmark</SelectItem>
                                                            <SelectItem value="log">Log (Testing)</SelectItem>
                                                            <SelectItem value="custom">{t('Custom...')}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                ) : (
                                                    <div className="flex gap-2">
                                                        <Input
                                                            id="mail_driver"
                                                            type="text"
                                                            value={data.mail_driver}
                                                            onChange={(e) => setData('mail_driver', e.target.value)}
                                                            placeholder={t('e.g., sparkpost, mandrill, resend')}
                                                            disabled={isFieldDisabled('mail_driver')}
                                                            className={isFieldDisabled('mail_driver') ? 'bg-muted cursor-not-allowed' : ''}
                                                        />
                                                        <Button
                                                            type="button"
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() => setData('mail_driver', 'smtp')}
                                                            disabled={isFieldDisabled('mail_driver')}
                                                        >
                                                            {t('Reset')}
                                                        </Button>
                                                    </div>
                                                )}
                                                {errors.mail_driver && (
                                                    <p className="text-sm text-red-600">{errors.mail_driver}</p>
                                                )}
                                                <p className="text-xs text-muted-foreground">
                                                    {t('For sending emails only. Common drivers: smtp, sendmail, mailgun, ses, postmark')}
                                                </p>
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="incoming_port" className="font-medium">{t('IMAP Port')}</Label>
                                                <Input
                                                    id="incoming_port"
                                                    type="number"
                                                    value={data.incoming_port}
                                                    onChange={(e) => setData('incoming_port', parseInt(e.target.value))}
                                                    placeholder={t('993')}
                                                    disabled={isFieldDisabled('incoming_port')}
                                                    className={isFieldDisabled('incoming_port') ? 'bg-muted cursor-not-allowed' : ''}
                                                    required
                                                />
                                                {errors.incoming_port && (
                                                    <p className="text-sm text-red-600">{errors.incoming_port}</p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="outgoing_port" className="font-medium">{t('SMTP Port')}</Label>
                                                <Input
                                                    id="outgoing_port"
                                                    type="number"
                                                    value={data.outgoing_port}
                                                    onChange={(e) => setData('outgoing_port', parseInt(e.target.value))}
                                                    placeholder={t('587')}
                                                    disabled={isFieldDisabled('outgoing_port')}
                                                    className={isFieldDisabled('outgoing_port') ? 'bg-muted cursor-not-allowed' : ''}
                                                    required
                                                />
                                                {errors.outgoing_port && (
                                                    <p className="text-sm text-red-600">{errors.outgoing_port}</p>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Sender Information */}
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-3 pb-3 border-b border-gray-200">
                                            <div className="p-2 bg-purple-50 rounded-lg">
                                                <Mail className="h-5 w-5 text-purple-600" />
                                            </div>
                                            <div>
                                                <Label className="font-semibold text-lg text-gray-900">{t('Sender Information')}</Label>
                                                <p className="text-sm text-gray-500 mt-0.5">{t('Display name and address for outgoing emails')}</p>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label htmlFor="mail_from_address" className="font-medium">{t('From Email Address')}</Label>
                                                <Input
                                                    id="mail_from_address"
                                                    type="email"
                                                    value={data.mail_from_address}
                                                    onChange={(e) => setData('mail_from_address', e.target.value)}
                                                    placeholder={t('noreply@example.com')}
                                                    required
                                                />
                                                {errors.mail_from_address && (
                                                    <p className="text-sm text-red-600">{errors.mail_from_address}</p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="mail_from_name" className="font-medium">{t('From Name')}</Label>
                                                <Input
                                                    id="mail_from_name"
                                                    type="text"
                                                    value={data.mail_from_name}
                                                    onChange={(e) => setData('mail_from_name', e.target.value)}
                                                    placeholder={t('Your Organization Name')}
                                                />
                                                {errors.mail_from_name && (
                                                    <p className="text-sm text-red-600">{errors.mail_from_name}</p>
                                                )}
                                            </div>
                                        </div>
                                    </div>



                                    <div className="flex justify-end gap-3 pt-4 border-t">
                                        {auth.user?.permissions?.includes('test-mailbox-connection') && (
                                            <Button
                                                type="button"
                                                variant="secondary"
                                                onClick={handleTestConnection}
                                                disabled={testing || processing}
                                            >
                                                {testing ? t('Testing...') : t('Test Connection')}
                                            </Button>
                                        )}


                                        {auth.user?.permissions?.includes('create-mailbox-settings') && (

                                            <Button type="submit" disabled={processing || testing}>
                                                {processing ? t('Saving...') : t('Save Configuration')}
                                            </Button>
                                        )}
                                    </div>
                                </form>
                            </CardContent>
                        </Card>

                        {/* Email Accounts History */}
                        {credentials && credentials.length > 0 && (
                            <Card className="shadow-sm mt-6">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-lg font-semibold">
                                        <Mail className="h-5 w-5" />
                                        {t('Email Accounts')}
                                    </CardTitle>
                                    <p className="text-sm text-muted-foreground mt-1">
                                        {t('Switch between your configured email accounts')}
                                    </p>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {credentials.map((cred) => (
                                            <div key={cred.id} className={`group relative flex items-center justify-between p-4 rounded-lg border transition-all duration-200 hover:shadow-sm ${cred.is_active
                                                ? 'border-green-200 bg-green-50/50 shadow-sm'
                                                : 'border-border bg-card hover:border-muted-foreground/20'
                                                }`}>
                                                <div className="flex items-center gap-3">
                                                    <div className={`w-3 h-3 rounded-full ${cred.is_active ? 'bg-green-500' : 'bg-gray-300'
                                                        }`}></div>
                                                    <div>
                                                        <div className="font-medium">{cred.email}</div>
                                                        <div className="text-sm text-gray-500">
                                                            {cred.imap_host}:{cred.imap_port} ({cred.imap_encryption?.toUpperCase()})
                                                        </div>
                                                    </div>
                                                    {cred.is_active && (
                                                        <span className="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">{t('Active')}</span>
                                                    )}
                                                </div>

                                                <div className="flex gap-2">
                                                    {!cred.is_active && auth.user?.permissions?.includes('switch-mailbox-settings') && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    size="sm"
                                                                    variant="outline"
                                                                    onClick={() => handleSwitchAccount(cred.id!)}
                                                                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50"
                                                                >
                                                                    <RefreshCw className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>{t('Switch to this account')}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                    {auth.user?.permissions?.includes('delete-mailbox-settings') && (
                                                        <Tooltip delayDuration={0}>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    size="sm"
                                                                    variant="outline"
                                                                    onClick={() => handleDeleteAccount(cred.id!, cred.email)}
                                                                    className="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                >
                                                                    <Trash2 className="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>{t('Delete account')}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    )}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </AuthenticatedLayout>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onOpenChange={closeDeleteDialog}
                title={t('Delete Email Account')}
                message={deleteState.message}
                confirmText={t('Delete')}
                onConfirm={handleConfirmDelete}
                variant="destructive"
            />
        </TooltipProvider>
    );
}