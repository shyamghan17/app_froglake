import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditBeautyMembershipProps, EditBeautyMembershipFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditBeautyMembership({ beautymembership, onSuccess }: EditBeautyMembershipProps) {
    const { beautyservices } = usePage<any>().props;
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditBeautyMembershipFormData>({
        name: beautymembership.name ?? '',
        duration: beautymembership.duration ?? '',
        benefits: beautymembership.benefits ?? '',
        price: beautymembership.price ?? '',
        description: beautymembership.description ?? '',
        included_services_id: beautymembership.included_services_id?.toString() || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.beauty-memberships.update', beautymembership.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Membership')}</DialogTitle>
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
                    <Label htmlFor="included_services_id" required>{t('Included Services')}</Label>
                    <Select 
                        value={data.included_services_id?.toString() || ''} 
                        onValueChange={(value) => setData('included_services_id', value)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Included Services')} />
                        </SelectTrigger>
                        <SelectContent>
                            {beautyservices?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.included_services_id} />
                </div>
                
                <div>
                    <Label htmlFor="duration" required>{t('Duration')}</Label>
                    <Input
                        id="duration"
                        type="number"
                        step="1"
                        min="0"
                        value={data.duration}
                        onChange={(e) => setData('duration', e.target.value)}
                        placeholder="0"
                    />
                    <InputError message={errors.duration} />
                </div>
                
                <div>
                    <CurrencyInput
                        label={t('Price')}
                        value={data.price}
                        onChange={(value) => setData('price', value)}
                        error={errors.price} required
                    />
                </div>
                
                <div>
                    <Label htmlFor="benefits">{t('Benefits')}</Label>
                    <Textarea
                        id="benefits"
                        value={data.benefits}
                        onChange={(e) => setData('benefits', e.target.value)}
                        placeholder={t('Enter Benefits')}
                        rows={3}
                    />
                    <InputError message={errors.benefits} />
                </div>
                
                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter Description')}
                        rows={3}
                    />
                    <InputError message={errors.description} />
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