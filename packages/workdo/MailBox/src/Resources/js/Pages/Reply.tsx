import { Head, useForm, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { formatDateTime } from '@/utils/helpers';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Reply as ReplyIcon, ArrowLeft, Send, X } from "lucide-react";

interface Email {
    id: string;
    subject: string;
    from: string;
    from_name?: string;
    from_email?: string;
    to: string[];
    date: string;
    body: string;
}

interface ReplyProps {
    originalEmail: Email;
}

function Reply() {
    const { t } = useTranslation();
    const { originalEmail } = usePage<ReplyProps>().props;
    useFlashMessages();

    const { data, setData, post, processing, errors } = useForm({
        body: ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('mailbox.reply.store', originalEmail.id), {
            onSuccess: () => {
                router.get(route('mailbox.inbox'));
            }
        });
    };

    const handleCancel = () => {
        router.get(route('mailbox.show', originalEmail.id));
    };

    const getSenderInitials = () => {
        if (originalEmail.from_name) {
            return originalEmail.from_name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        }
        return originalEmail.from_email?.charAt(0).toUpperCase() || originalEmail.from.charAt(0).toUpperCase();
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('MailBox'), url: route('mailbox.inbox') },
                { label: t('View Email'), url: route('mailbox.show', originalEmail.id) },
                { label: t('Reply') }
            ]}
            pageTitle={`Re: ${originalEmail.subject}`}
        >
            <Head title={`Reply: ${originalEmail.subject}`} />

            <div className="space-y-6">
                {/* Reply Compose Card */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <ReplyIcon className="h-5 w-5" />
                            {t('Reply to')}: {originalEmail.subject}
                        </CardTitle>
                        <div className="text-sm text-gray-600">
                            <div><strong>{t('To')}:</strong> {originalEmail.from_name || originalEmail.from_email || originalEmail.from}</div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="body" className="text-sm font-medium">
                                    {t('Your Reply')}
                                </Label>
                                <Textarea
                                    id="body"
                                    value={data.body}
                                    onChange={(e) => setData('body', e.target.value)}
                                    placeholder={t('Type your reply here...')}
                                    rows={8}
                                    className="min-h-[200px] resize-y"
                                    required
                                />
                                {errors.body && (
                                    <p className="text-sm text-red-600">{errors.body}</p>
                                )}
                            </div>

                            <div className="flex justify-end gap-3 pt-4 border-t">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={handleCancel}
                                    disabled={processing}
                                >
                                    {t('Cancel')}
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? t('Sending...') : t('Send Reply')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                {/* Original Message Card */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="text-lg">{t('Original Message')}</CardTitle>
                        
                        <div className="flex items-start gap-4 pt-2">
                            {/* Sender Avatar */}
                            <Avatar className="h-10 w-10 flex-shrink-0">
                                <AvatarFallback className="bg-primary text-primary-foreground">
                                    {getSenderInitials()}
                                </AvatarFallback>
                            </Avatar>
                            
                            {/* Email Details */}
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2 mb-1">
                                    <span className="font-medium text-gray-900">
                                        {originalEmail.from_name || originalEmail.from_email || originalEmail.from}
                                    </span>
                                </div>
                                
                                <div className="text-sm text-gray-600 mb-2">
                                    {formatDateTime(originalEmail.date)}
                                </div>
                                
                                <div className="text-sm text-gray-600">
                                    <span className="font-medium">to</span> {originalEmail.to.join(', ')}
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent>
                        <div className="prose max-w-none">
                            <div 
                                className="email-content p-4 border rounded-lg bg-gray-50 max-h-96 overflow-y-auto"
                                dangerouslySetInnerHTML={{ __html: originalEmail.body }}
                                style={{
                                    fontFamily: 'Arial, sans-serif',
                                    lineHeight: '1.6',
                                    color: '#333'
                                }}
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}

export default Reply;