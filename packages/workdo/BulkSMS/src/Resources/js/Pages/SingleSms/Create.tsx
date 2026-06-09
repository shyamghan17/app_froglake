import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { CreateSingleSmsProps, CreateSingleSmsFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function Create({ onSuccess }: CreateSingleSmsProps) {
    const { bulksmscontacts } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateSingleSmsFormData>({
        contact_id: '',
        mobile_number: '',
        sms: '',
    });

    useEffect(() => {
        if (data.contact_id) {
            const selectedContact = bulksmscontacts?.find((contact: any) => contact.id.toString() === data.contact_id);
            if (selectedContact?.mobile_no) {
                setData('mobile_number', selectedContact.mobile_no);
            }
        } else {
            setData('mobile_number', '');
        }
    }, [data.contact_id]);



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bulk-s-m-s.single-sms.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Send Single SMS')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="contact_id" required>{t('Contact')}</Label>
                    <Select value={data.contact_id?.toString() || ''} onValueChange={(value) => setData('contact_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Contact')} />
                        </SelectTrigger>
                        <SelectContent>
                            {bulksmscontacts?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.contact_id} />
                </div>
                
                <div>
                    <Label htmlFor="mobile_number">{t('Mobile Number')}</Label>
                    <Input
                        id="mobile_number"
                        type="text"
                        value={data.mobile_number}
                        onChange={(e) => setData('mobile_number', e.target.value)}
                        placeholder={t('Enter Mobile Number')}
                        disabled
                        required
                    />
                    <InputError message={errors.mobile_number} />
                </div>
                
                <div>
                    <Label htmlFor="sms" required>{t('Sms')}</Label>
                    <Textarea
                        id="sms"
                        value={data.sms}
                        onChange={(e) => setData('sms', e.target.value)}
                        placeholder={t('Enter Sms')}
                        rows={3}
                    />
                    <InputError message={errors.sms} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Sending...') : t('Send Sms')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}