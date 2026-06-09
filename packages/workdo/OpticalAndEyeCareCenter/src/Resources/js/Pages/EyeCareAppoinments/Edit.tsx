import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { DateTimeRangePicker } from '@/components/ui/datetime-range-picker';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditEyeCareAppoinmentProps, EditEyeCareAppoinmentFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function EditEyeCareAppoinment({ eyecareappoinment, onSuccess }: EditEyeCareAppoinmentProps) {
    const { eyepatients, opticaldoctors } = usePage<any>().props;
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditEyeCareAppoinmentFormData>(eyecareappoinment);

    useEffect(() => {
        if (data.patient_id) {
            const patient = eyepatients.find((p: any) => p.id.toString() === data.patient_id.toString());
            if (patient?.preferred_doctor && !eyecareappoinment.doctor_name) {
                setData('doctor_name', patient.preferred_doctor.toString());
            }
        }
    }, [data.patient_id]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('optical-and-eye-care-center.eye-care-appoinments.update', eyecareappoinment.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Eye Care Appointment')}</DialogTitle>
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
                    <Label required>{t('Appointment Date & time')}</Label>
                    <DateTimeRangePicker
                        mode="single"
                        value={data.appointment_datetime}
                        onChange={(date) => setData('appointment_datetime', date)}
                        placeholder={t('Select Appointment Date & time')}
                    />
                    <InputError message={errors.appointment_datetime} />
                </div>

                <div>
                    <Label>{t('Status')}</Label>
                    <RadioGroup value={data.status?.toString() || '0'} onValueChange={(value) => setData('status', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="status_0" />
                            <Label htmlFor="status_0" className="cursor-pointer">{t('Scheduled')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="status_1" />
                            <Label htmlFor="status_1" className="cursor-pointer">{t('Confirmed')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="status_2" />
                            <Label htmlFor="status_2" className="cursor-pointer">{t('Completed')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="3" id="status_3" />
                            <Label htmlFor="status_3" className="cursor-pointer">{t('Cancelled')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.status} />
                </div>

                <div>
                    <Label>{t('Appointment Type')}</Label>
                    <RadioGroup value={data.appointment_type?.toString() || '0'} onValueChange={(value) => setData('appointment_type', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="appointment_type_0" />
                            <Label htmlFor="appointment_type_0" className="cursor-pointer">{t('Consultation')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="appointment_type_1" />
                            <Label htmlFor="appointment_type_1" className="cursor-pointer">{t('Follow-up')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="appointment_type_2" />
                            <Label htmlFor="appointment_type_2" className="cursor-pointer">{t('Emergency')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.appointment_type} />
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
