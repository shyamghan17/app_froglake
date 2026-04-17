import { Head, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Mail } from "lucide-react";

export default function Compose() {
    const { t } = useTranslation();
    useFlashMessages();

    const { data, setData, post, processing, errors } = useForm({
        to: '',
        cc: '',
        bcc: '',
        subject: '',
        body: ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('mailbox.send'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('MailBox'), url: route('mailbox.inbox') },
                { label: t('Compose') }
            ]}
            pageTitle={t('Compose Email')}
        >
            <Head title={t('Compose Email')} />

            <div className="max-w-4xl mx-auto">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Mail className="h-5 w-5" />
                            {t('New Email')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="to">{t('To')}</Label>
                                <Input
                                    id="to"
                                    type="email"
                                    value={data.to}
                                    onChange={(e) => setData('to', e.target.value)}
                                    placeholder={t('recipient@example.com')}
                                    required
                                />
                                {errors.to && (
                                    <p className="text-sm text-red-600">{errors.to}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="cc">{t('CC')}</Label>
                                    <Input
                                        id="cc"
                                        type="email"
                                        value={data.cc}
                                        onChange={(e) => setData('cc', e.target.value)}
                                        placeholder={t('cc@example.com')}
                                    />
                                    {errors.cc && (
                                        <p className="text-sm text-red-600">{errors.cc}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="bcc">{t('BCC')}</Label>
                                    <Input
                                        id="bcc"
                                        type="email"
                                        value={data.bcc}
                                        onChange={(e) => setData('bcc', e.target.value)}
                                        placeholder={t('bcc@example.com')}
                                    />
                                    {errors.bcc && (
                                        <p className="text-sm text-red-600">{errors.bcc}</p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="subject">{t('Subject')}</Label>
                                <Input
                                    id="subject"
                                    type="text"
                                    value={data.subject}
                                    onChange={(e) => setData('subject', e.target.value)}
                                    placeholder={t('Email subject')}
                                    required
                                />
                                {errors.subject && (
                                    <p className="text-sm text-red-600">{errors.subject}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="body">{t('Message')}</Label>
                                <Textarea
                                    id="body"
                                    value={data.body}
                                    onChange={(e) => setData('body', e.target.value)}
                                    placeholder={t('Type your message here...')}
                                    rows={10}
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
                                    onClick={() => window.history.back()}
                                >
                                    {t('Cancel')}
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? t('Sending...') : t('Send Email')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}