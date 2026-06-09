import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CreateCertificationProps, CreateCertificationFormData } from './types';
import { useEffect, useState } from 'react';
import axios from 'axios';

interface CreateProps extends CreateCertificationProps {
    auth?: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Create({ onSuccess, auth }: CreateProps) {
    const { trainings, auth: pageAuth } = usePage<any>().props;
    const authData = auth || pageAuth;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateCertificationFormData>({
        employee_name: '',
        certificate_name: '',
        issued_date: '',
        expiry_date: '',
        training_id: '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.certifications.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Certification')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="training_id" required>{t('Training')}</Label>
                    <Select value={data.training_id?.toString() || ''} onValueChange={(value) => setData('training_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Training')} />
                        </SelectTrigger>
                        <SelectContent>
                            {trainings.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.training_name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.training_id} />
                    {trainings?.length === 0 && authData?.user?.permissions?.includes('create-beauty-trainings') && (
                        <p className="text-xs text-gray-500 mt-1">
                            {t('Create training here.')} <button type="button" onClick={() => router.get(route('beauty-spa-management.trainings.index'))} className="text-blue-600 hover:underline">{t('Create training')}</button>
                        </p>
                    )}

                </div>
                <div>
                    <Label htmlFor="employee_name">{t('Employee Name')}</Label>
                    <Input
                        id="employee_name"
                        type="text"
                        value={data.employee_name}
                        onChange={(e) => setData('employee_name', e.target.value)}
                        placeholder={t('Enter Employee Name')}
                        required
                    />
                    <InputError message={errors.employee_name} />
                </div>

                <div>
                    <Label htmlFor="certificate_name">{t('Certificate Name')}</Label>
                    <Input
                        id="certificate_name"
                        type="text"
                        value={data.certificate_name}
                        onChange={(e) => setData('certificate_name', e.target.value)}
                        placeholder={t('Enter Certificate Name')}
                        required
                    />
                    <InputError message={errors.certificate_name} />
                </div>

                <div>
                    <Label required>{t('Issued Date')}</Label>
                    <DatePicker
                        value={data.issued_date}
                        onChange={(date) => setData('issued_date', date)}
                        placeholder={t('Select Issued Date')}
                        minDate={new Date()}
                    />
                    <InputError message={errors.issued_date} />
                </div>

                <div>
                    <Label>{t('Expiry Date')}</Label>
                    <DatePicker
                        value={data.expiry_date}
                        onChange={(date) => setData('expiry_date', date)}
                        placeholder={t('Select Expiry Date')}
                        minDate={new Date()}
                    />
                    <InputError message={errors.expiry_date} />
                </div>


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