import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { CreateBulkSmsContactProps, CreateBulkSmsContactFormData } from './types';
import { usePage } from '@inertiajs/react';
import { getCityOptions, getStateOptions } from '@/utils/locationOptions';

export default function Create({ onSuccess }: CreateBulkSmsContactProps) {
    const { companyAllSetting } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateBulkSmsContactFormData>({
        name: '',
        email: '',
        mobile_no: '',
        city: '',
        state: '',
        zip_code: '',
    });

    const countryName = companyAllSetting?.company_country;
    const stateOptions = getStateOptions(countryName, data.state);
    const cityOptions = getCityOptions(countryName, data.state || companyAllSetting?.company_state, data.city);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bulk-s-m-s.bulk-sms-contacts.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Contact')}</DialogTitle>
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
                        list="bulk_sms_contact_create_city_options"
                        value={data.city}
                        onChange={(e) => setData('city', e.target.value)}
                        placeholder={t('Enter City')}
                        required
                    />
                    <datalist id="bulk_sms_contact_create_city_options">
                        {cityOptions.map((city) => (
                            <option key={city} value={city} />
                        ))}
                    </datalist>
                    <InputError message={errors.city} />
                </div>
                
                <div>
                    <Label htmlFor="state">{t('State')}</Label>
                    <Input
                        id="state"
                        type="text"
                        list="bulk_sms_contact_create_state_options"
                        value={data.state}
                        onChange={(e) => {
                            const value = e.target.value;
                            setData((previousData) => ({
                                ...previousData,
                                state: value,
                                city:
                                    previousData.state.trim().toLowerCase() !== value.trim().toLowerCase()
                                        ? ''
                                        : previousData.city,
                            }));
                        }}
                        placeholder={t('Enter State')}
                        required
                    />
                    <datalist id="bulk_sms_contact_create_state_options">
                        {stateOptions.map((state) => (
                            <option key={state} value={state} />
                        ))}
                    </datalist>
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
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
