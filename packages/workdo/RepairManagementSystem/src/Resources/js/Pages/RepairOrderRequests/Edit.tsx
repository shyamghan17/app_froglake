import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DatePicker } from '@/components/ui/date-picker';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { EditRepairOrderRequestProps, EditRepairOrderRequestFormData } from './types';
import { usePage } from '@inertiajs/react';

export default function EditRepairOrderRequest({ repairorderrequest, onSuccess }: EditRepairOrderRequestProps) {
    const { repairtechnicians, repairstatuses } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditRepairOrderRequestFormData>({
        product_name: repairorderrequest.product_name ?? '',
        product_quantity: repairorderrequest.product_quantity ?? '',
        customer_name: repairorderrequest.customer_name ?? '',
        customer_email: repairorderrequest.customer_email ?? '',
        customer_mobile_no: repairorderrequest.customer_mobile_no ?? '',
        date: repairorderrequest.date || '',
        expiry_date: repairorderrequest.expiry_date || '',
        repair_technician: repairorderrequest.repair_technician ?? '',


    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('repair-management-system.repair-order-requests.update', repairorderrequest.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Order Request')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Product Name')}</Label>
                        <Input
                            id="product_name"
                            type="text"
                            value={data.product_name}
                            onChange={(e) => setData('product_name', e.target.value)}
                            placeholder={t('Enter Product Name')}
                        />
                        <InputError message={errors.product_name} />
                    </div>
                    
                    <div>
                        <Label htmlFor="product_quantity">{t('Product Quantity')}</Label>
                        <Input
                            id="product_quantity"
                            type="number"
                            step="1"
                            min="0"
                            value={data.product_quantity}
                            onChange={(e) => setData('product_quantity', e.target.value)}
                            placeholder="0"
                        />
                        <InputError message={errors.product_quantity} />
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Customer Name')}</Label>
                        <Input
                            id="customer_name"
                            type="text"
                            value={data.customer_name}
                            onChange={(e) => setData('customer_name', e.target.value)}
                            placeholder={t('Enter Customer Name')}
                        />
                        <InputError message={errors.customer_name} />
                    </div>
                    
                    <div>
                        <Label required>{t('Customer Email')}</Label>
                        <Input
                            id="customer_email"
                            type="email"
                            value={data.customer_email}
                            onChange={(e) => setData('customer_email', e.target.value)}
                            placeholder={t('Enter Customer Email')}
                        />
                        <InputError message={errors.customer_email} />
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <PhoneInputComponent required
                            label={t('Mobile No')}
                            value={data.customer_mobile_no}
                            onChange={(value) => setData('customer_mobile_no', value || '')}
                            error={errors.customer_mobile_no}
                        />
                    </div>
                    
                    <div>
                        <Label required>{t('Date')}</Label>
                        <DatePicker
                            value={data.date}
                            onChange={(date) => setData('date', date)}
                            placeholder={t('Select Date')}
                        />
                        <InputError message={errors.date} />
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label required>{t('Expiry Date')}</Label>
                        <DatePicker
                            value={data.expiry_date}
                            onChange={(date) => setData('expiry_date', date)}
                            placeholder={t('Select Expiry Date')}
                        />
                        <InputError message={errors.expiry_date} />
                    </div>
                    
                    <div>
                        <Label required>{t('Repair Technician')}</Label>
                        <Select value={data.repair_technician?.toString() || ''} onValueChange={(value) => setData('repair_technician', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select Repair Technician')} />
                            </SelectTrigger>
                            <SelectContent>
                                {repairtechnicians?.map((item: any) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>
                                        {item.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.repair_technician} />
                    </div>
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