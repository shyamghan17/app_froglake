import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, usePage } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CreateEyePatientProps, CreateEyePatientFormData } from './types';

export default function Create({ onSuccess }: CreateEyePatientProps) {
    const { doctors } = usePage<any>().props;
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateEyePatientFormData>({
        patient_name: '',
        dob: '',
        gender: '0',
        contact_no: '',
        address: '',
        medical_history: '',
        previous_prescriptions: '',
        preferred_doctor: '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('optical-and-eye-care-center.eye-patients.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Eye Patient')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="patient_name">{t('Patient Name')}</Label>
                    <Input
                        id="patient_name"
                        type="text"
                        value={data.patient_name}
                        onChange={(e) => setData('patient_name', e.target.value)}
                        placeholder={t('Enter Patient Name')}
                        required
                    />
                    <InputError message={errors.patient_name} />
                </div>

                <div>
                    <Label required>{t('Dob')}</Label>
                    <DatePicker
                        value={data.dob}
                        onChange={(date) => setData('dob', date)}
                        placeholder={t('Select Dob')}
                        maxDate={new Date()}
                    />
                    <InputError message={errors.dob} />
                </div>

                <div>
                    <Label>{t('Gender')}</Label>
                    <RadioGroup value={data.gender?.toString() || '0'} onValueChange={(value) => setData('gender', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="gender_0" />
                            <Label htmlFor="gender_0" className="cursor-pointer">{t('male')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="gender_1" />
                            <Label htmlFor="gender_1" className="cursor-pointer">{t('female')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="gender_2" />
                            <Label htmlFor="gender_2" className="cursor-pointer">{t('other')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.gender} />
                </div>

                <div>
                    <PhoneInputComponent
                        label={t('Contact No')}
                        value={data.contact_no}
                        onChange={(value) => setData('contact_no', value || '')}
                        error={errors.contact_no}
                        required
                    />
                </div>

                <div>
                    <Label htmlFor="preferred_doctor" required>{t('Preferred Doctor')}</Label>
                    <Select value={data.preferred_doctor} onValueChange={(value) => setData('preferred_doctor', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Doctor')} />
                        </SelectTrigger>
                        <SelectContent>
                            {doctors?.map((doctor: any) => (
                                <SelectItem key={doctor.id} value={doctor.id.toString()}>
                                    {doctor.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.preferred_doctor} />
                </div>

                <div>
                    <Label htmlFor="address">{t('Address')}</Label>
                    <Textarea
                        id="address"
                        value={data.address}
                        onChange={(e) => setData('address', e.target.value)}
                        placeholder={t('Enter Address')}
                        rows={3}
                    />
                    <InputError message={errors.address} />
                </div>

                <div>
                    <Label htmlFor="medical_history">{t('Medical History')}</Label>
                    <Textarea
                        id="medical_history"
                        value={data.medical_history}
                        onChange={(e) => setData('medical_history', e.target.value)}
                        placeholder={t('Enter Medical History')}
                        rows={3}
                    />
                    <InputError message={errors.medical_history} />
                </div>

                <div>
                    <Label htmlFor="previous_prescriptions">{t('Previous Prescriptions')}</Label>
                    <Textarea
                        id="previous_prescriptions"
                        value={data.previous_prescriptions}
                        onChange={(e) => setData('previous_prescriptions', e.target.value)}
                        placeholder={t('Enter Previous Prescriptions')}
                        rows={3}
                    />
                    <InputError message={errors.previous_prescriptions} />
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
