import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { EditBeautyLoyaltyProgramProps, EditBeautyLoyaltyProgramFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditBeautyLoyaltyProgram({ beautyloyaltyprogram, onSuccess }: EditBeautyLoyaltyProgramProps) {
    const { } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditBeautyLoyaltyProgramFormData>({
        customer_name: beautyloyaltyprogram.customer_name ?? '',
        points_earned: beautyloyaltyprogram.points_earned ?? '',
        points_redeemed: beautyloyaltyprogram.points_redeemed ?? '',
        last_updated: beautyloyaltyprogram.last_updated || '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.beauty-loyalty-programs.update', beautyloyaltyprogram.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Loyalty Programs')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="customer_name">{t('Customer Name')}</Label>
                    <Input
                        id="customer_name"
                        type="text"
                        value={data.customer_name}
                        onChange={(e) => setData('customer_name', e.target.value)}
                        placeholder={t('Enter Customer Name')}
                        required
                    />
                    <InputError message={errors.customer_name} />
                </div>

                <div>
                    <Label htmlFor="points_earned">{t('Points Earned')}</Label>
                    <Input
                        id="points_earned"
                        type="number"
                        step="1"
                        min="0"
                        value={data.points_earned}
                        onChange={(e) => setData('points_earned', e.target.value)}
                        placeholder="0" required
                    />
                    <InputError message={errors.points_earned} />
                </div>

                <div>
                    <Label htmlFor="points_redeemed">{t('Points Redeemed')}</Label>
                    <Input
                        id="points_redeemed"
                        type="number"
                        step="1"
                        min="0"
                        value={data.points_redeemed}
                        onChange={(e) => setData('points_redeemed', e.target.value)}
                        placeholder="0"
                    />
                    <InputError message={errors.points_redeemed} />
                </div>

                <div>
                    <Label required>{t('Last Updated')}</Label>
                    <DatePicker
                        value={data.last_updated}
                        onChange={(date) => setData('last_updated', date)}
                        placeholder={t('Select Last Updated')}
                    />
                    <InputError message={errors.last_updated} />
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