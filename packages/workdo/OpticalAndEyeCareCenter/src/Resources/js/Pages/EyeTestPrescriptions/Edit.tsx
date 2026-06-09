import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditEyeTestPrescriptionProps, EditEyeTestPrescriptionFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function EditEyeTestPrescription({ eyetestprescription, onSuccess }: EditEyeTestPrescriptionProps) {
    const { eyepatients, opticaldoctors } = usePage<any>().props;
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditEyeTestPrescriptionFormData>(eyetestprescription);

    useEffect(() => {
        if (data.patient_id) {
            const patient = eyepatients.find((p: any) => p.id.toString() === data.patient_id.toString());
            if (patient?.preferred_doctor && !eyetestprescription.doctor_name) {
                setData('doctor_name', patient.preferred_doctor.toString());
            }
        }
    }, [data.patient_id]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('optical-and-eye-care-center.eye-test-prescriptions.update', eyetestprescription.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Eye Test Prescription')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="patient_id" required>{t('Patient')}</Label>
                    <Select value={data.patient_id?.toString() || ''} onValueChange={(value) => setData('patient_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Patient')} />
                        </SelectTrigger>
                        <SelectContent>
                            {eyepatients.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.patient_name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.patient_id} />
                </div>
                
                <div>
                    <Label htmlFor="doctor_name" required>{t('Doctor Name')}</Label>
                    <Select value={data.doctor_name?.toString() || ''} onValueChange={(value) => setData('doctor_name', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Doctor')} />
                        </SelectTrigger>
                        <SelectContent>
                            {opticaldoctors.filter((doctor: any) => doctor.name).map((doctor: any) => (
                                <SelectItem key={doctor.id} value={doctor.id.toString()}>
                                    {doctor.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.doctor_name} />
                </div>
                
                <div>
                    <Label required>{t('Test Date')}</Label>
                    <DatePicker
                        value={data.test_date}
                        onChange={(date) => setData('test_date', date)}
                        placeholder={t('Select Test Date')}
                    />
                    <InputError message={errors.test_date} />
                </div>
                
                <div>
                    <Label htmlFor="test_results">{t('Test Results')}</Label>
                    <Textarea
                        id="test_results"
                        value={data.test_results}
                        onChange={(e) => setData('test_results', e.target.value)}
                        placeholder={t('Enter Test Results')}
                        rows={3}
                    />
                    <InputError message={errors.test_results} />
                </div>
                
                <div>
                    <Label htmlFor="prescription_details">{t('Prescription Details')}</Label>
                    <Textarea
                        id="prescription_details"
                        value={data.prescription_details}
                        onChange={(e) => setData('prescription_details', e.target.value)}
                        placeholder={t('Enter Prescription Details')}
                        rows={3}
                    />
                    <InputError message={errors.prescription_details} />
                </div>
                
                <div>
                    <Label>{t('Prescription Expiry Date')}</Label>
                    <DatePicker
                        value={data.prescription_expiry_date}
                        onChange={(date) => setData('prescription_expiry_date', date)}
                        placeholder={t('Select Prescription Expiry Date')}
                    />
                    <InputError message={errors.prescription_expiry_date} />
                </div>
                
                <div>
                    <Label htmlFor="notes">{t('Notes')}</Label>
                    <Textarea
                        id="notes"
                        value={data.notes}
                        onChange={(e) => setData('notes', e.target.value)}
                        placeholder={t('Enter Notes')}
                        rows={3}
                    />
                    <InputError message={errors.notes} />
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
