import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/ui/currency-input';
import { DatePicker } from '@/components/ui/date-picker';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditBeautyServiceOfferProps, EditBeautyServiceOfferFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditBeautyServiceOffer({ beautyserviceoffer, onSuccess }: EditBeautyServiceOfferProps) {
    const { beautyservices } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditBeautyServiceOfferFormData>({
        title: beautyserviceoffer.title ?? '',
        name: beautyserviceoffer.name ?? '',
        price: beautyserviceoffer.price ?? '',
        start_date: beautyserviceoffer.start_date || '',
        end_date: beautyserviceoffer.end_date || '',
        discount: beautyserviceoffer.discount ?? '',
        offer_price: beautyserviceoffer.offer_price ?? '',
        description: beautyserviceoffer.description ?? '',
        beauty_service_id: beautyserviceoffer.beauty_service_id?.toString() || '',
    });

    const handleServiceChange = (value: string) => {
        const selectedService = beautyservices.find((service: any) => service.id.toString() === value);
        if (selectedService) {
            setData({
                ...data,
                beauty_service_id: value,
                price: selectedService.price || '',
                offer_price: selectedService.price || ''
            });
        } else {
            setData('beauty_service_id', value);
        }
    };

    const handleDiscountChange = (discountValue: string) => {
        const price = parseFloat(data.price) || 0;
        const discount = parseFloat(discountValue) || 0;
        const offerPrice = price - (price * discount / 100);

        setData({
            ...data,
            discount: discountValue,
            offer_price: offerPrice.toFixed(2)
        });
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.beauty-service-offers.update', beautyserviceoffer.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="max-w-4xl">
            <DialogHeader>
                <DialogTitle>{t('Edit Service Offer')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="title">{t('Title')}</Label>
                        <Input
                            id="title"
                            type="text"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            placeholder={t('Enter Title')}
                            required
                        />
                        <InputError message={errors.title} />
                    </div>

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
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="beauty_service_id" required>{t('Service')}</Label>
                        <Select value={data.beauty_service_id?.toString() || ''} onValueChange={handleServiceChange}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Service')} />
                            </SelectTrigger>
                            <SelectContent>
                                {beautyservices.map((item: any) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>
                                        {item.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.beauty_service_id} />
                    </div>

                    <div>
                        <CurrencyInput required
                            label={t('Price')}
                            value={data.price}
                            onChange={(value) => setData('price', value)}
                            error={errors.price} disabled
                        />
                        <InputError message={errors.price} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="discount">{t('Discount %')}</Label>
                        <Input
                            id="discount"
                            value={data.discount}
                            onChange={(e) => handleDiscountChange(e.target.value)}
                            placeholder={t('Enter Discount')} required
                        />
                        <InputError message={errors.discount} />
                    </div>

                    <div>
                        <CurrencyInput required
                            label={t('Offer Price')}
                            value={data.offer_price}
                            onChange={(value) => setData('offer_price', value)}
                            error={errors.offer_price} disabled
                        />
                        <InputError message={errors.offer_price} />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Start Date')}</Label>
                        <DatePicker
                            value={data.start_date}
                            onChange={(date) => setData('start_date', date)}
                            placeholder={t('Select Start Date')}
                            minDate={new Date()}
                        />
                        <InputError message={errors.start_date} />
                    </div>

                    <div>
                        <Label required>{t('End Date')}</Label>
                        <DatePicker
                            value={data.end_date}
                            onChange={(date) => setData('end_date', date)}
                            placeholder={t('Select End Date')}
                            minDate={new Date()}
                        />
                        <InputError message={errors.end_date} />
                    </div>
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