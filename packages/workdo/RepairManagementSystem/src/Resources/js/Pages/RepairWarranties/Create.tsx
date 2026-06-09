import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

import { CreateRepairWarrantyProps, CreateRepairWarrantyFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function Create({ onSuccess }: CreateRepairWarrantyProps) {
    const { repairorderrequests, repairparts } = usePage<any>().props;
    const [filteredParts, setFilteredParts] = useState(repairparts || []);
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateRepairWarrantyFormData>({
        repair_order_id: '',
        part_id: '',
        warranty_number: '',
        warranty_period: '',
        warranty_terms: '',
        claim_status: '0',
    });

    useEffect(() => {
        if (data.repair_order_id) {
            axios.get(route('repair-management-system.repair-order-requests.parts', data.repair_order_id))
                .then(response => {
                    setFilteredParts(response.data);
                })
                .catch(() => {
                    setFilteredParts([]);
                });
        } else {
            setFilteredParts(repairparts || []);
            setData('part_id', '');
        }
    }, [data.repair_order_id]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('repair-management-system.repair-warranties.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Create Warranty')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label required >{t('Repair Order')}</Label>
                    <Select value={data.repair_order_id?.toString() || ''} onValueChange={(value) => setData('repair_order_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Repair Order')} />
                        </SelectTrigger>
                        <SelectContent>
                            {repairorderrequests.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.repair_order_id} />
                </div>
                
                <div>
                    <Label required>{t('Part')}</Label>
                    <Select 
                        value={data.part_id?.toString() || ''} 
                        onValueChange={(value) => setData('part_id', value)}
                        disabled={!data.repair_order_id}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder={data.repair_order_id ? t('Select Part') : t('Select Parts')} />
                        </SelectTrigger>
                        <SelectContent>
                            {filteredParts?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.part_id} />
                    
                    <p className="text-xs text-muted-foreground mt-1">
                        {t('Please add repair parts for warranty.')} {' '}
                        <button 
                            type="button" 
                            onClick={() => router.get(route('repair-management-system.repair-order-requests.index'))}
                            className="text-blue-600 hover:text-blue-800 underline"
                        >
                            {t('Add repair parts')}
                        </button>
                    </p>
                </div>
                
                <div>
                    <Label required>{t('Warranty Number')}</Label>
                    <Input
                        id="warranty_number"
                        type="text"
                        value={data.warranty_number}
                        onChange={(e) => setData('warranty_number', e.target.value)}
                        placeholder={t('Enter Warranty Number')}
                    />
                    <InputError message={errors.warranty_number} />
                </div>
                
                <div>
                    <Label required>{t('Warranty Period')}</Label>
                    <DateRangePicker
                        value={data.warranty_period}
                        onChange={(dateRange) => setData('warranty_period', dateRange)}
                        placeholder={t('Select Warranty Period')}
                    />
                    <InputError message={errors.warranty_period} />
                </div>
                
                <div>
                    <Label required>{t('Warranty Terms')}</Label>
                    <Textarea
                        id="warranty_terms"
                        value={data.warranty_terms}
                        onChange={(e) => setData('warranty_terms', e.target.value)}
                        placeholder={t('Enter Warranty Terms')}
                        rows={3}
                    />
                    <InputError message={errors.warranty_terms} />
                </div>
                
                <div>
                    <Label required>{t('Claim Status')}</Label>
                    <RadioGroup value={data.claim_status?.toString() || '0'} onValueChange={(value) => setData('claim_status', value)} className="flex gap-6 mt-2">
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="0" id="claim_status_0" />
                            <Label htmlFor="claim_status_0" className="cursor-pointer">{t('Active')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="1" id="claim_status_1" />
                            <Label htmlFor="claim_status_1" className="cursor-pointer">{t('Pending')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="2" id="claim_status_2" />
                            <Label htmlFor="claim_status_2" className="cursor-pointer">{t('Claimed')}</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <RadioGroupItem value="3" id="claim_status_3" />
                            <Label htmlFor="claim_status_3" className="cursor-pointer">{t('Expired')}</Label>
                        </div>
                    </RadioGroup>
                    <InputError message={errors.claim_status} />
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