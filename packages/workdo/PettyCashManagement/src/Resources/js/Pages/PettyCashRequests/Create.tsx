import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { CreatePettyCashRequestProps, CreatePettyCashRequestFormData, PettyCashCategory } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function Create({ onSuccess }: CreatePettyCashRequestProps) {
    const { users, pettycashcategories } = usePage<any>().props;
    const [filteredCategories, setFilteredCategories] = useState(pettycashcategories || []);
    const [receiptFile, setReceiptFile] = useState<File | null>(null);
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, transform } = useForm<CreatePettyCashRequestFormData>({
        user_id: '',
        categorie_id: '',
        requested_amount: '',
        remarks: '',
        receipt_path: '',
    });

    transform((data) => {
        const formData = new FormData();
        formData.append('user_id', data.user_id);
        formData.append('categorie_id', data.categorie_id);
        formData.append('requested_amount', data.requested_amount);
        formData.append('remarks', data.remarks);
        if (receiptFile) {
            formData.append('receipt_path', receiptFile);
        }
        return formData;
    });

    useEffect(() => {
        const fallbackCategories: PettyCashCategory[] = pettycashcategories || [];

        if (data.user_id) {
            let isStale = false;

            axios.get(route('pettycashmanagement.users.categories', data.user_id))
                .then(response => {
                    if (isStale) {
                        return;
                    }

                    const nextCategories: PettyCashCategory[] = Array.isArray(response.data) ? response.data : [];
                    setFilteredCategories(nextCategories);

                    if (data.categorie_id && !nextCategories.some((item) => item.id.toString() === data.categorie_id)) {
                        setData('categorie_id', '');
                    }
                })
                .catch(() => {
                    if (isStale) {
                        return;
                    }

                    setFilteredCategories(fallbackCategories);

                    if (data.categorie_id && !fallbackCategories.some((item) => item.id.toString() === data.categorie_id)) {
                        setData('categorie_id', '');
                    }
                });

            return () => {
                isStale = true;
            };
        } else {
            setFilteredCategories(fallbackCategories);
            if (data.categorie_id) {
                setData('categorie_id', '');
            }
        }
    }, [data.user_id, data.categorie_id, pettycashcategories, setData]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('petty-cash-management.petty-cash-requests.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Petty Cash Request')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="user_id" required>{t('User')}</Label>
                    <Select value={data.user_id?.toString() || ''} onValueChange={(value) => setData('user_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select User')} />
                        </SelectTrigger>
                        <SelectContent>
                            {users?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.user_id} />
                </div>

                <div>
                    <Label htmlFor="categorie_id" required>{t('Category')}</Label>
                    <Select value={data.categorie_id?.toString() || ''} onValueChange={(value) => setData('categorie_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Category')} />
                        </SelectTrigger>
                        <SelectContent>
                            {filteredCategories?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.categorie_id} />
                </div>

                <div>
                    <CurrencyInput
                        label={t('Amount')}
                        value={data.requested_amount}
                        onChange={(value) => setData('requested_amount', value)}
                        error={errors.requested_amount}
                        required
                    />
                </div>

                <div>
                    <Label htmlFor="receipt">{t('Upload Bill Photo')}</Label>
                    <Input
                        id="receipt"
                        type="file"
                        accept=".png,.jpg,.jpeg,.pdf"
                        onChange={(e) => setReceiptFile(e.target.files?.[0] || null)}
                    />
                    {receiptFile && (
                        <div className="mt-2">
                            <p className="text-sm text-green-600 dark:text-green-400 mb-2">
                                {t('Selected')}: {receiptFile.name}
                            </p>
                            {receiptFile.type.startsWith('image/') && (
                                <img
                                    src={URL.createObjectURL(receiptFile)}
                                    alt="Receipt preview"
                                    className="max-w-xs max-h-32 object-contain border rounded"
                                />
                            )}
                        </div>
                    )}
                    <InputError message={errors.receipt_path} />
                </div>

                <div>
                    <Label htmlFor="remarks">{t('Remarks')}</Label>
                    <Textarea
                        id="remarks"
                        value={data.remarks}
                        onChange={(e) => setData('remarks', e.target.value)}
                        placeholder={t('Enter Remarks')}
                        rows={3}
                    />
                    <InputError message={errors.remarks} />
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
