import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DatePicker } from '@/components/ui/date-picker';
import { CreateLeadProps, CreateLeadFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { formatDate } from '@/utils/helpers';
import { useFormFields } from '@/hooks/useFormFields';
import { Switch } from '@/components/ui/switch';

export default function Create({ onSuccess }: CreateLeadProps) {
    const { users } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateLeadFormData>({
        subject: '',
        user_id: '',
        name: '',
        company_name: '',
        email: '',
        phone: '',
        date: '',
        website: '',
        category: '',
        address: '',
        district: '',
        province: '',
        remarks: '',
        is_live: false,
        company_pan: '',
        lead_status: '',
    });


    const nameAI = useFormFields('aiField', data, setData, errors, 'create', 'name', 'Name', 'lead', 'lead');
    const subjectAI = useFormFields('aiField', data, setData, errors, 'create', 'subject', 'Subject', 'lead', 'lead');
    const customFields = useFormFields('getCustomFields', { ...data, module: 'Lead', sub_module: 'Lead' }, setData, errors, 'create', t);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('lead.leads.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Create Lead')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div className="flex gap-2 items-end">
                        <div className="flex-1">
                            <Label htmlFor="name">{t('Name')}</Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder={t('Enter Name')}
                                required
                            />
                            <InputError message={errors.name} />
                        </div>
                        {nameAI.map(field => <div key={field.id}>{field.component}</div>)}
                    </div>

                    <div>
                        <Label htmlFor="company_name">{t('Company Name')}</Label>
                        <Input
                            id="company_name"
                            type="text"
                            value={data.company_name || ''}
                            onChange={(e) => setData('company_name', e.target.value)}
                            placeholder={t('Enter Company Name')}
                        />
                        <InputError message={errors.company_name} />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-4">
                    <div>
                        <Label htmlFor="email">{t('Email')}</Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder={t('Enter Email')}
                            required
                        />
                        <InputError message={errors.email} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <PhoneInputComponent
                            label={t('Phone No')}
                            value={data.phone}
                            onChange={(value) => setData('phone', value || '')}
                            error={errors.phone}
                        />
                    </div>

                    <div>
                        <Label htmlFor="website">{t('Website')}</Label>
                        <Input
                            id="website"
                            type="text"
                            value={data.website || ''}
                            onChange={(e) => setData('website', e.target.value)}
                            placeholder={t('Enter Website')}
                        />
                        <InputError message={errors.website} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="company_pan">{t('Company PAN')}</Label>
                        <Input
                            id="company_pan"
                            type="text"
                            value={data.company_pan || ''}
                            onChange={(e) => setData('company_pan', e.target.value)}
                            placeholder={t('Enter Company PAN')}
                        />
                        <InputError message={errors.company_pan} />
                    </div>
                    <div>
                        <div className="space-y-2">
                            <Label htmlFor="is_live">{t('Is Live')}</Label>
                            <div className="flex items-center gap-2">
                                <Switch
                                    id="is_live"
                                    checked={!!data.is_live}
                                    onCheckedChange={(checked) => setData('is_live', checked)}
                                />
                            </div>
                            <InputError message={errors.is_live} />
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="category">{t('Category')}</Label>
                        <Input
                            id="category"
                            type="text"
                            value={data.category || ''}
                            onChange={(e) => setData('category', e.target.value)}
                            placeholder={t('Enter Category')}
                        />
                        <InputError message={errors.category} />
                    </div>
                    <div>
                        <Label htmlFor="lead_status">{t('Lead Status')}</Label>
                        <Input
                            id="lead_status"
                            type="text"
                            value={data.lead_status || ''}
                            onChange={(e) => setData('lead_status', e.target.value)}
                            placeholder={t('Enter Lead Status')}
                        />
                        <InputError message={errors.lead_status} />
                    </div>
                </div>

                <div className="flex gap-2 items-end">
                    <div className="flex-1">
                        <Label htmlFor="subject">{t('Subject')}</Label>
                        <Input
                            id="subject"
                            type="text"
                            value={data.subject}
                            onChange={(e) => setData('subject', e.target.value)}
                            placeholder={t('Enter Subject')}
                            required
                        />
                        <InputError message={errors.subject} />
                    </div>
                    {subjectAI.map(field => <div key={field.id}>{field.component}</div>)}
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="province">{t('Province')}</Label>
                        <Input
                            id="province"
                            type="text"
                            value={data.province || ''}
                            onChange={(e) => setData('province', e.target.value)}
                            placeholder={t('Enter Province')}
                        />
                        <InputError message={errors.province} />
                    </div>
                    <div>
                        <Label htmlFor="district">{t('District')}</Label>
                        <Input
                            id="district"
                            type="text"
                            value={data.district || ''}
                            onChange={(e) => setData('district', e.target.value)}
                            placeholder={t('Enter District')}
                        />
                        <InputError message={errors.district} />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-4">
                    <div>
                        <Label htmlFor="address">{t('Address')}</Label>
                        <Textarea
                            id="address"
                            value={data.address || ''}
                            onChange={(e) => setData('address', e.target.value)}
                            placeholder={t('Enter Address')}
                            rows={2}
                        />
                        <InputError message={errors.address} />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-4">
                    <div>
                        <Label htmlFor="remarks">{t('Remarks')}</Label>
                        <Textarea
                            id="remarks"
                            value={data.remarks || ''}
                            onChange={(e) => setData('remarks', e.target.value)}
                            placeholder={t('Enter Remarks')}
                            rows={3}
                        />
                        <InputError message={errors.remarks} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label>{t('Follow Up Date')}</Label>
                        <DatePicker
                            value={data.date}
                            onChange={(date) => setData('date', formatDate(date))}
                            placeholder={t('Select Follow Up Date')}
                        />
                        <InputError message={errors.date} />
                    </div>
                    <div>
                        <Label htmlFor="user_id" required>{t('User')}</Label>
                        <Select value={data.user_id?.toString() || ''} onValueChange={(value) => setData('user_id', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select User')} />
                            </SelectTrigger>
                            <SelectContent>
                                {users?.map((item: any) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>
                                        {item.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.user_id} />
                    </div>
                </div>

                {customFields.length > 0 && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {customFields.map((field) => (
                                <div key={field.id}>
                                    {field.component}
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
