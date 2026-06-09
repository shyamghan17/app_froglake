import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditCertificationProps, EditCertificationFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditCertification({ certification, onSuccess }: EditCertificationProps) {
    const { trainings } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditCertificationFormData>({
        employee_name: certification.employee_name ?? '',
        certificate_name: certification.certificate_name ?? '',
        issued_date: certification.issued_date || '',
        expiry_date: certification.expiry_date || '',
        training_id: certification.training_id?.toString() || '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.certifications.update', certification.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Certification')}</DialogTitle>
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
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}