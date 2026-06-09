import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CreateOpticalDoctorProps, CreateOpticalDoctorFormData, OpticalDoctorsIndexProps } from './types';

export default function Create({ onSuccess }: CreateOpticalDoctorProps) {
    const { users, opticalspecializations, auth } = usePage<OpticalDoctorsIndexProps>().props;
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateOpticalDoctorFormData>({
        doctor_code: '',
        license_number: '',
        gender: '0',
        years_of_experience: '',
        consultation_fee: '',
        qualifications: '',
        status: '0',
        user_id: '',
        hospital_specialization_id: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('optical-and-eye-care-center.optical-doctors.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="lg">
            <DialogHeader>
                <DialogTitle>{t('Create Doctor')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="user_id" required>{t('User')}</Label>
                    <Select value={data.user_id?.toString() || ''} onValueChange={(value) => setData('user_id', value)} required>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select User')} />
                        </SelectTrigger>
                        <SelectContent>
                            {users.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name} ({item.email})
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.user_id} />
                    {users.length === 0 && auth?.user?.permissions?.includes('create-users') && (
                        <p className="text-xs text-gray-500 mt-1">
                            {t('Create user here.')} <button type="button" onClick={() => router.get(route('users.index'))} className="text-blue-600 hover:underline">{t('Create user')}</button>
                        </p>
                    )}
                    <p className="text-xs text-muted-foreground mt-1">
                        {t('Note: Only users with Doctor role who are not already assigned to other Doctor will appear in this list.')}
                    </p>
                </div>


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
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
