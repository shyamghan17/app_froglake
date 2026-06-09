import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { EditBulkSmsContactProps, EditBulkSmsContactFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditBulkSmsContact({ bulksmscontact, onSuccess }: EditBulkSmsContactProps) {
    const {  } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditBulkSmsContactFormData>({
        name: bulksmscontact.name ?? '',
        email: bulksmscontact.email ?? '',
        mobile_no: bulksmscontact.mobile_no ?? '',
        city: bulksmscontact.city ?? '',
        state: bulksmscontact.state ?? '',
        zip_code: bulksmscontact.zip_code ?? '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('bulk-s-m-s.bulk-sms-contacts.update', bulksmscontact.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Contact')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
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
                
                <div>
                    <PhoneInputComponent
                        label={t('Mobile No')}
                        value={data.mobile_no}
                        onChange={(value) => setData('mobile_no', value || '')}
                        error={errors.mobile_no}
                        required
                    />
                </div>
                
                <div>
                    <Label htmlFor="city">{t('City')}</Label>
                    <Input
                        id="city"
                        type="text"
                        value={data.city}
                        onChange={(e) => setData('city', e.target.value)}
                        placeholder={t('Enter City')}
                        required
                    />
                    <InputError message={errors.city} />
                </div>
                
                <div>
                    <Label htmlFor="state">{t('State')}</Label>
                    <Input
                        id="state"
                        type="text"
                        value={data.state}
                        onChange={(e) => setData('state', e.target.value)}
                        placeholder={t('Enter State')}
                        required
                    />
                    <InputError message={errors.state} />
                </div>
                
                <div>
                    <Label htmlFor="zip_code">{t('Zip Code')}</Label>
                    <Input
                        id="zip_code"
                        type="text"
                        value={data.zip_code}
                        onChange={(e) => setData('zip_code', e.target.value)}
                        placeholder={t('Enter Zip Code')}
                        required
                    />
                    <InputError message={errors.zip_code} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}