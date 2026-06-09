import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import MediaPicker from '@/components/MediaPicker';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Repeater } from '@/components/ui/repeater';
import { CreateServiceProps, CreateServiceFormData } from './types';

interface CreateProps extends CreateServiceProps {
    auth?: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Create({ onSuccess, auth }: CreateProps) {
    const { beautyservicetypes, staff, auth: pageAuth } = usePage<any>().props;
    const authData = auth || pageAuth;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateServiceFormData>({
        name: '',
        service_type_id: '',
        price: '',
        max_bookable_persons: '',
        time: '',
        staff_id: null,
        service_image: '',
        description: '',
        included_services: [''],
    });





    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.services.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="max-w-4xl">
            <DialogHeader>
                <DialogTitle>{t('Create Service')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
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
                        <Label htmlFor="service_type_id" required>{t('Service Type')} </Label>
                        <Select value={data.service_type_id?.toString() || ''} onValueChange={(value) => setData('service_type_id', value)} required>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Service Type')} />
                            </SelectTrigger>
                            <SelectContent>
                                {beautyservicetypes.map((item: any) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>
                                        {item.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.service_type_id} />
                        {beautyservicetypes?.length === 0 && authData?.user?.permissions?.includes('create-beauty-service-types') && (
                            <p className="text-xs text-gray-500 mt-1">
                                {t('Create service type here.')} <button type="button" onClick={() => router.get(route('beauty-spa-management.service-types.index'))} className="text-blue-600 hover:underline">{t('Create service type')}</button>
                            </p>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <CurrencyInput required
                            label={t('Price')}
                            value={data.price}
                            onChange={(value) => setData('price', value)}
                            error={errors.price}
                        />
                    </div>

                    <div>
                        <Label htmlFor="max_bookable_persons">{t('Max Bookable Persons')}</Label>
                        <Input
                            id="max_bookable_persons"
                            type="number"
                            step="1"
                            min="0"
                            value={data.max_bookable_persons}
                            onChange={(e) => setData('max_bookable_persons', e.target.value)}
                            placeholder="0" required
                        />
                        <InputError message={errors.max_bookable_persons} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="time">{t('Time')}</Label>
                        <Input
                            id="time"
                            type="number"
                            step="0.01"
                            min="0"
                            value={data.time}
                            onChange={(e) => setData('time', e.target.value)}
                            placeholder={t('Enter Time')}
                            required
                        />
                        <p className="text-sm text-gray-500 mt-1">{t('Time Inputs (e.g., 1.20) as hours and minutes')}</p>
                        <InputError message={errors.time} />
                    </div>

                    <div>
                        <Label htmlFor="staff_id">{t('Staff')}</Label>
                        <Select value={data.staff_id?.toString() || 'null'} onValueChange={(value) => setData('staff_id', value === 'null' ? null : parseInt(value))}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Staff')} />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="null">{t('No Staff Assigned')}</SelectItem>
                                {staff?.map((member: any) => (
                                    <SelectItem key={member.id} value={member.id.toString()}>
                                        {member.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.staff_id} />
                    </div>
                </div>

                <div>
                    <MediaPicker
                        label={t('Service Image')}
                        value={data.service_image}
                        onChange={(value) => setData('service_image', Array.isArray(value) ? value[0] || '' : value)}
                        placeholder={t('Select Service Image...')}
                        showPreview={true}
                        multiple={false}
                    />
                    <InputError message={errors.service_image} />
                </div>

                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter Description')}
                        rows={3} required
                    />
                    <InputError message={errors.description} />
                </div>

                <div>
                    <Label className="mb-4 block">{t('Included Services')}</Label>
                    <Repeater
                        fields={[
                            {
                                name: 'service',
                                label: t('Service'),
                                type: 'text',
                                placeholder: t('Enter included service'),
                            }
                        ]}
                        value={data.included_services.map((service, index) => ({
                            id: `service-${index}`,
                            service: service || ''
                        }))}
                        onChange={(items) => {
                            const services = items.map(({ service }) => service);
                            setData('included_services', services);
                        }}
                        addButtonText={t('Add Service')}
                        deleteTooltipText={t('Remove Service')}
                        minItems={1}
                        errors={errors as any}
                    />
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