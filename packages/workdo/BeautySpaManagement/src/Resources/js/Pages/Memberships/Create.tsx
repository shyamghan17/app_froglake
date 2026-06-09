import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, usePage, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CreateBeautyMembershipProps, CreateBeautyMembershipFormData } from './types';
import { useEffect, useState } from 'react';
import axios from 'axios';

interface CreateProps extends CreateBeautyMembershipProps {
    auth?: {
        user?: {
            permissions?: string[];
        };
    };
}

export default function Create({ onSuccess, auth }: CreateProps) {
    const { beautyservices, auth: pageAuth } = usePage<any>().props;
    const authData = auth || pageAuth;
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateBeautyMembershipFormData>({
        name: '',
        duration: '',
        benefits: '',
        price: '',
        description: '',
        included_services_id: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.beauty-memberships.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Membership')}</DialogTitle>
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
                    {beautyservices?.length === 0 && authData?.user?.permissions?.includes('create-beauty-services') && (
                        <p className="text-xs text-gray-500 mt-1">
                            {t('Create service here.')} <button type="button" onClick={() => router.get(route('beauty-spa-management.services.index'))} className="text-blue-600 hover:underline">{t('Create service')}</button>
                        </p>
                    )}
                </div>

                <div>
                    <Label htmlFor="duration">{t('Duration')}</Label>
                    <Input
                        id="duration"
                        type="number"
                        step="1"
                        min="0"
                        value={data.duration}
                        onChange={(e) => setData('duration', e.target.value)}
                        placeholder="0" required
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
                        {processing ? t('Creating...') : t('Create')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}