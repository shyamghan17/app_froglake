import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CurrencyInput } from '@/components/ui/currency-input';
import { DatePicker } from '@/components/ui/date-picker';
import { EditGiftCardProps, EditGiftCardFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditGiftCard({ giftcard, onSuccess }: EditGiftCardProps) {

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditGiftCardFormData>({
        card_code: giftcard.card_code ?? '',
        customer: giftcard.customer ?? '',
        balance: giftcard.balance ?? '',
        expiry_date: giftcard.expiry_date || '',
        status: giftcard.status ?? false,
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.gift-cards.update', giftcard.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Gift Card')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="card_code">{t('Card Code')}</Label>
                    <Input
                        id="card_code"
                        type="text"
                        value={data.card_code}
                        onChange={(e) => setData('card_code', e.target.value)}
                        placeholder={t('Enter Card Code')}
                        required
                    />
                    <InputError message={errors.card_code} />
                </div>

                <div>
                    <Label htmlFor="customer">{t('Customer')}</Label>
                    <Input
                        id="customer"
                        type="text"
                        value={data.customer}
                        onChange={(e) => setData('customer', e.target.value)}
                        placeholder={t('Enter Customer Name')} required
                    />
                    <InputError message={errors.customer} />
                </div>

                <div>
                    <CurrencyInput
                        label={t('Balance')}
                        value={data.balance}
                        onChange={(value) => setData('balance', value)}
                        error={errors.balance} required
                    />
                </div>

                <div>
                    <Label required>{t('Expiry Date')}</Label>
                    <DatePicker
                        value={data.expiry_date}
                        onChange={(date) => setData('expiry_date', date)}
                        placeholder={t('Select Expiry Date')}
                        minDate={new Date()}
                    />
                    <InputError message={errors.expiry_date} />
                </div>

                <div>
                    <Label htmlFor="status" required>{t('Status')}</Label>
                    <Select value={data.status ? "1" : "0"} onValueChange={(value) => setData('status', value === "1")}>
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="1">{t('Active')}</SelectItem>
                            <SelectItem value="0">{t('Inactive')}</SelectItem>
                        </SelectContent>
                    </Select>
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