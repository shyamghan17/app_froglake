import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DatePicker } from '@/components/ui/date-picker';
import { Switch } from '@/components/ui/switch';
import { CreateLeadProps, CreateLeadFormData, LeadsIndexProps } from './types';
import { usePage } from '@inertiajs/react';
import { formatDate } from '@/utils/helpers';
import { useFormFields } from '@/hooks/useFormFields';
import { getCityOptions, getCountryOptions, getStateOptions } from '@/utils/locationOptions';

export default function Create({ onSuccess }: CreateLeadProps) {
    const { users, sources, auth } = usePage<LeadsIndexProps & { auth: any }>().props;
    const defaultUserId = auth?.user?.id ? auth.user.id.toString() : '';

    const { t } = useTranslation();
    const { data, setData, post, processing, errors, setError, clearErrors } = useForm<CreateLeadFormData>({
        subject: '',
        user_id: defaultUserId,
        name: '',
        email: '',
        phone: '',
        designation: '',
        company_name: '',
        pan_vat_number: '',
        organization_type: '',
        whatsapp_same_as_phone: true,
        whatsapp_viber_number: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        country: '',
        postal_code: '',
        sources: [],
        date: '',
    });

    const nameAI = useFormFields('aiField', data, setData, errors, 'create', 'name', 'Name', 'lead', 'lead');
    const subjectAI = useFormFields('aiField', data, setData, errors, 'create', 'subject', 'Subject', 'lead', 'lead');
    const customFields = useFormFields('getCustomFields', { ...data, module: 'Lead', sub_module: 'Lead' }, setData, errors, 'create', t);
    const selectedSource = data.sources[0] ?? '';
    const countryOptions = getCountryOptions(data.country);
    const stateOptions = getStateOptions(data.country, data.state);
    const cityOptions = getCityOptions(data.country, data.state, data.city);

    const handlePrimaryPhoneChange = (value: string) => {
        setData('phone', value);

        if (data.whatsapp_same_as_phone) {
            setData('whatsapp_viber_number', value);
        }
    };

    const handleMessagingToggleChange = (checked: boolean) => {
        setData('whatsapp_same_as_phone', checked);

        if (checked) {
            setData('whatsapp_viber_number', data.phone);
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        const requiredFieldMessages: Array<{
            field: 'sources' | 'address_line_1' | 'city' | 'state' | 'country';
            message: string;
        }> = [
            { field: 'sources', message: t('Source is required.') },
            { field: 'address_line_1', message: t('Address Line 1 is required.') },
            { field: 'city', message: t('City is required.') },
            { field: 'state', message: t('State is required.') },
            { field: 'country', message: t('Country is required.') },
        ];

        const missingRequiredFields = requiredFieldMessages.filter(({ field }) => {
            if (field === 'sources') {
                return data.sources.length === 0;
            }

            return data[field].trim() === '';
        });

        if (missingRequiredFields.length > 0) {
            clearErrors(...missingRequiredFields.map(({ field }) => field));
            missingRequiredFields.forEach(({ field, message }) => setError(field, message));

            return;
        }

        clearErrors(...requiredFieldMessages.map(({ field }) => field));

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
                <div className="space-y-4 rounded-lg border p-4">
                    <div>
                        <h3 className="text-sm font-semibold">{t('Basic Information')}</h3>
                        <p className="text-sm text-muted-foreground">{t('Capture the lead owner, subject, and primary contact details.')}</p>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
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
                            <Label htmlFor="email">{t('Email')}</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder={t('Enter Email')}
                            />
                            <InputError message={errors.email} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div className="flex gap-2 items-end">
                            <div className="flex-1">
                                <Label htmlFor="subject" required>{t('Subject')}</Label>
                                <Input
                                    id="subject"
                                    type="text"
                                    value={data.subject}
                                    onChange={(e) => setData('subject', e.target.value)}
                                    placeholder={t('Enter Subject')}
                                />
                                <InputError message={errors.subject} />
                            </div>
                            {subjectAI.map(field => <div key={field.id}>{field.component}</div>)}
                        </div>

                        <div>
                            <Label htmlFor="user_id" required>{t('User')}</Label>
                            <Select value={data.user_id} onValueChange={(value) => setData('user_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select User')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {users?.map((item) => (
                                        <SelectItem key={item.id} value={item.id.toString()}>
                                            {item.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.user_id} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <PhoneInputComponent
                                label={t('Phone No')}
                                value={data.phone}
                                onChange={handlePrimaryPhoneChange}
                                error={errors.phone}
                                timezone={auth?.user?.timezone}
                            />
                        </div>

                        <div>
                            <Label>{t('Follow Up Date')}</Label>
                            <DatePicker
                                value={data.date}
                                onChange={(date) => setData('date', formatDate(date))}
                                placeholder={t('Select Follow Up Date')}
                            />
                            <InputError message={errors.date} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="designation">{t('Designation')}</Label>
                            <Input
                                id="designation"
                                type="text"
                                value={data.designation}
                                onChange={(e) => setData('designation', e.target.value)}
                                placeholder={t('Enter designation')}
                            />
                            <InputError message={errors.designation} />
                        </div>

                        <div>
                            <Label htmlFor="company_name">{t('Company Name')}</Label>
                            <Input
                                id="company_name"
                                type="text"
                                value={data.company_name}
                                onChange={(e) => setData('company_name', e.target.value)}
                                placeholder={t('Enter company name')}
                            />
                            <InputError message={errors.company_name} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="pan_vat_number">{t('PAN / VAT Number')}</Label>
                            <Input
                                id="pan_vat_number"
                                type="text"
                                value={data.pan_vat_number}
                                onChange={(e) => setData('pan_vat_number', e.target.value)}
                                placeholder={t('Enter PAN / VAT number')}
                            />
                            <InputError message={errors.pan_vat_number} />
                        </div>

                        <div>
                            <Label htmlFor="organization_type">{t('Organization Type')}</Label>
                            <Input
                                id="organization_type"
                                type="text"
                                value={data.organization_type}
                                onChange={(e) => setData('organization_type', e.target.value)}
                                placeholder={t('Enter organization type')}
                            />
                            <InputError message={errors.organization_type} />
                        </div>
                    </div>

                    <div>
                        <Label htmlFor="source" required>{t('Source')}</Label>
                        <Select
                            value={selectedSource || undefined}
                            onValueChange={(value) => {
                                setData('sources', [value]);
                                clearErrors('sources');
                            }}
                        >
                            <SelectTrigger id="source">
                                <SelectValue placeholder={t('Select Source')} />
                            </SelectTrigger>
                            <SelectContent>
                                {sources?.map((source) => (
                                    <SelectItem key={source.id} value={source.id.toString()}>
                                        {source.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.sources} />
                    </div>
                </div>

                <div className="space-y-4 rounded-lg border p-4">
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 className="text-sm font-semibold">{t('Messaging')}</h3>
                            <p className="text-sm text-muted-foreground">{t('Use the same number for WhatsApp or Viber, or provide a dedicated messaging number.')}</p>
                        </div>

                        <div className="flex items-center gap-3 rounded-md border px-3 py-2">
                            <Switch
                                id="whatsapp_same_as_phone"
                                checked={data.whatsapp_same_as_phone}
                                onCheckedChange={handleMessagingToggleChange}
                            />
                            <Label htmlFor="whatsapp_same_as_phone" className="cursor-pointer">
                                {t('Messaging number matches phone')}
                            </Label>
                        </div>
                    </div>

                    {!data.whatsapp_same_as_phone && (
                        <PhoneInputComponent
                            id="whatsapp_viber_number"
                            label={t('WhatsApp / Viber Number')}
                            value={data.whatsapp_viber_number}
                            onChange={(value) => setData('whatsapp_viber_number', value)}
                            error={errors.whatsapp_viber_number}
                            timezone={auth?.user?.timezone}
                        />
                    )}
                </div>

                <div className="space-y-4 rounded-lg border p-4">
                    <div>
                        <h3 className="text-sm font-semibold">{t('Address')}</h3>
                        <p className="text-sm text-muted-foreground">{t('Store the lead address details for follow-up and qualification.')}</p>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="address_line_1" required>{t('Address Line 1')}</Label>
                            <Input
                                id="address_line_1"
                                type="text"
                                value={data.address_line_1}
                                onChange={(e) => {
                                    setData('address_line_1', e.target.value);
                                    clearErrors('address_line_1');
                                }}
                                placeholder={t('Enter address line 1')}
                                required
                            />
                            <InputError message={errors.address_line_1} />
                        </div>

                        <div>
                            <Label htmlFor="address_line_2">{t('Address Line 2')}</Label>
                            <Input
                                id="address_line_2"
                                type="text"
                                value={data.address_line_2}
                                onChange={(e) => setData('address_line_2', e.target.value)}
                                placeholder={t('Apartment, suite, unit, etc.')}
                            />
                            <InputError message={errors.address_line_2} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="city" required>{t('City')}</Label>
                            <Select
                                value={data.city}
                                onValueChange={(value) => {
                                    setData('city', value);
                                    clearErrors('city');
                                }}
                                disabled={!data.country || !data.state}
                                required
                            >
                                <SelectTrigger id="city">
                                    <SelectValue placeholder={!data.country ? t('Select Country first') : !data.state ? t('Select State first') : t('Select City')} />
                                </SelectTrigger>
                                <SelectContent searchable={true}>
                                    {cityOptions.map((city) => (
                                        <SelectItem key={city} value={city}>
                                            {city}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.city} />
                        </div>

                        <div>
                            <Label htmlFor="state" required>{t('State')}</Label>
                            <Select
                                value={data.state}
                                onValueChange={(value) => {
                                    setData('state', value);
                                    setData('city', '');
                                    clearErrors('state');
                                }}
                                disabled={!data.country}
                                required
                            >
                                <SelectTrigger id="state">
                                    <SelectValue placeholder={!data.country ? t('Select Country first') : t('Select State')} />
                                </SelectTrigger>
                                <SelectContent searchable={true}>
                                    {stateOptions.map((state) => (
                                        <SelectItem key={state} value={state}>
                                            {state}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.state} />
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="country" required>{t('Country')}</Label>
                            <Select
                                value={data.country}
                                onValueChange={(value) => {
                                    setData('country', value);
                                    setData('state', '');
                                    setData('city', '');
                                    clearErrors('country');
                                }}
                                required
                            >
                                <SelectTrigger id="country">
                                    <SelectValue placeholder={t('Select Country')} />
                                </SelectTrigger>
                                <SelectContent searchable={true}>
                                    {countryOptions.map((country) => (
                                        <SelectItem key={country} value={country}>
                                            {country}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.country} />
                        </div>

                        <div>
                            <Label htmlFor="postal_code">{t('Postal Code')}</Label>
                            <Input
                                id="postal_code"
                                type="text"
                                value={data.postal_code}
                                onChange={(e) => setData('postal_code', e.target.value)}
                                placeholder={t('Enter postal code')}
                            />
                            <InputError message={errors.postal_code} />
                        </div>
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
