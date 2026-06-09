import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import { EditOpticalDoctorProps, EditOpticalDoctorFormData } from './types';

export default function EditOpticalDoctor({ opticaldoctor, onSuccess }: EditOpticalDoctorProps) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditOpticalDoctorFormData>(opticaldoctor);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('optical-and-eye-care-center.optical-doctors.update', opticaldoctor.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="lg">
            <DialogHeader>
                <DialogTitle>{t('Edit Doctor')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="license_number">{t('License Number')}</Label>
                    <Input
                        id="license_number"
                        type="text"
                        value={data.license_number}
                        onChange={(e) => setData('license_number', e.target.value)}
                        placeholder={t('Enter License Number')}
                        required
                    />
                    <InputError message={errors.license_number} />
                </div>

                <div>
                    <Label>{t('Gender')}</Label>
                    <RadioGroup value={data.gender?.toString() || '0'} onValueChange={(value) => setData('gender', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="gender_0" />
                            <Label htmlFor="gender_0" className="cursor-pointer">{t('Male')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="gender_1" />
                            <Label htmlFor="gender_1" className="cursor-pointer">{t('Female')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="gender_2" />
                            <Label htmlFor="gender_2" className="cursor-pointer">{t('Other')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.gender} />
                </div>

                <div>
                    <Label htmlFor="years_of_experience">{t('Years Of Experience')}</Label>
                    <Input
                        id="years_of_experience"
                        type="number"
                        step="1"
                        min="0"
                        value={data.years_of_experience}
                        onChange={(e) => setData('years_of_experience', e.target.value)}
                        placeholder="0"
                        required
                    />
                    <InputError message={errors.years_of_experience} />
                </div>

                <div>
                    <CurrencyInput
                        label={t('Consultation Fee')}
                        value={data.consultation_fee}
                        onChange={(value) => setData('consultation_fee', value)}
                        error={errors.consultation_fee}
                        required
                    />
                </div>

                <div>
                    <Label htmlFor="qualifications">{t('Qualifications')}</Label>
                    <Textarea
                        id="qualifications"
                        value={data.qualifications}
                        onChange={(e) => setData('qualifications', e.target.value)}
                        placeholder={t('Enter Qualifications')}
                        rows={3}
                    />
                    <InputError message={errors.qualifications} />
                </div>

                <div>
                    <Label>{t('Status')}</Label>
                    <RadioGroup value={data.status?.toString() || '0'} onValueChange={(value) => setData('status', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="status_0" />
                            <Label htmlFor="status_0" className="cursor-pointer">{t('Active')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="status_1" />
                            <Label htmlFor="status_1" className="cursor-pointer">{t('On Leave')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="status_2" />
                            <Label htmlFor="status_2" className="cursor-pointer">{t('Busy')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="3" id="status_3" />
                            <Label htmlFor="status_3" className="cursor-pointer">{t('Inactive')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.status} />
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
