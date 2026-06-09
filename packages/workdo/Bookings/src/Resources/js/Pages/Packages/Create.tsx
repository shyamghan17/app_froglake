import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import InputError from '@/components/ui/input-error';
import { formatCurrency } from '@/utils/helpers';
import { Item, ExtraService, PackageFormData } from './types';

interface CreateProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    items: Item[];
    extraServices: ExtraService[];
}

export default function Create({ open, onOpenChange, items, extraServices }: CreateProps) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm<PackageFormData>({
        name: '',
        item_id: '',
        services: [],
        delivery_time: '',
        delivery_period: '',
        price: '',
    });

    const calculatePrice = (itemId: string, selectedServices: number[]) => {
        const item = items.find(i => i.id.toString() === itemId);
        if (!item) return '0.00';
        
        let price = Number(item.sale_price) || 0;
        if (item.tax_ids) {
            const taxRate = 0.1;
            price = price + (price * taxRate);
        }
        
        // Add extra services prices
        selectedServices.forEach(serviceId => {
            const service = extraServices.find(s => s.id === serviceId);
            if (service) {
                price += Number(service.amount) || 0;
            }
        });
        
        return price.toFixed(2);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.packages.store'), {
            onSuccess: () => {
                reset();
                onOpenChange(false);
            }
        });
    };

    return (
        <Dialog open={open} onOpenChange={(open) => {
            if (!open) reset();
            onOpenChange(open);
        }}>
            <DialogContent className="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{t('Create Package')}</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <Label htmlFor="name">{t('Package Name')}</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder={t('Enter package name')}
                            required
                        />
                        <InputError message={errors.name} />
                    </div>
                    <div>
                        <Label htmlFor="item_id" required>{t('Item')}</Label>
                        <Select value={data.item_id} onValueChange={(value) => {
                            setData('item_id', value);
                            setData('price', calculatePrice(value, data.services));
                        }}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('Select item')} />
                            </SelectTrigger>
                            <SelectContent>
                                {items.map((item) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>
                                        {item.name} ({formatCurrency(item.sale_price)})
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.item_id} />
                    </div>
                    <div>
                        <Label htmlFor="services">{t('Extra Services')}</Label>
                        <MultiSelectEnhanced
                            options={extraServices.map(service => ({
                                value: service.id.toString(),
                                label: `${service.name} (${formatCurrency(service.amount)})`
                            }))}
                            value={data.services.map(id => id.toString())}
                            onValueChange={(values) => {
                                const serviceIds = values.map(v => parseInt(v));
                                setData('services', serviceIds);
                                if (data.item_id) {
                                    setData('price', calculatePrice(data.item_id, serviceIds));
                                }
                            }}
                            placeholder={t('Select extra services')}
                            searchable={true}
                        />
                        <InputError message={errors.services} />
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="delivery_period" required>{t('Period')}</Label>
                            <Select value={data.delivery_period} onValueChange={(value) => setData('delivery_period', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select period')} />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="minutes">{t('Minutes')}</SelectItem>
                                    <SelectItem value="hours">{t('Hours')}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={errors.delivery_period} />
                        </div>
                        <div>
                            <Label htmlFor="delivery_time">{t('Delivery Time')} ({data.delivery_period || t('Select period first')})</Label>
                            <Input
                                id="delivery_time"
                                type="number"
                                min="1"
                                max={data.delivery_period === 'hours' ? "24" : undefined}
                                value={data.delivery_time}
                                onChange={(e) => setData('delivery_time', e.target.value)}
                                placeholder={data.delivery_period === 'minutes' ? t('Enter minutes') : data.delivery_period === 'hours' ? t('Enter hours') : t('Select period first')}
                                disabled={!data.delivery_period}
                                required
                            />
                            <InputError message={errors.delivery_time} />
                        </div>
                    </div>
                    <div>
                        <Label htmlFor="price">{t('Price (with tax)')}</Label>
                        <Input
                            id="price"
                            type="number"
                            step="0.01"
                            value={data.price}
                            onChange={(e) => setData('price', e.target.value)}
                            placeholder={t('Enter price')}
                            disabled
                            required
                        />
                        <InputError message={errors.price} />
                    </div>
                    <div className="flex justify-end gap-2 pt-4">
                        <Button type="button" variant="outline" onClick={() => {
                            reset();
                            onOpenChange(false);
                        }}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? t('Creating...') : t('Create')}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
