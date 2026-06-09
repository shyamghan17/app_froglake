import { useState } from 'react';
import { Head, useForm, usePage } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { MultiSelectEnhanced } from "@/components/ui/multi-select-enhanced";
import { Card, CardContent } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Dialog } from "@/components/ui/dialog";
import MediaPicker from "@/components/MediaPicker";
import { TimePicker } from '@/components/ui/time-picker';
import InputError from "@/components/ui/input-error";

interface Item {
    id: number;
    name: string;
    sku?: string;
    tax_ids?: string;
    category_id?: number;
    description?: string;
    sale_price?: number;
    purchase_price?: number;
    unit?: number;
    image?: string;
    images?: string | string[];
    duration?: number;
    total_slots?: number;
    type?: string;
}

interface Category {
    id: number;
    name: string;
    type?: string;
}

interface Unit {
    id: number;
    unit_name: string;
}

interface Tax {
    id: number;
    tax_name: string;
    rate: number;
}

interface EditPageProps {
    item: Item;
    categories: Category[];
    units: Unit[];
    taxes: Tax[];
}

interface ItemFormData {
    name: string;
    sku: string;
    tax_ids: string[];
    category_id: string;
    description: string;
    sale_price: string;
    purchase_price: string;
    unit: string;
    image: string;
    images: string[];
    duration: string;
    total_slots: string;
    type: string;
}

export default function Edit() {
    const { t } = useTranslation();
    const { item, categories, units, taxes } = usePage<EditPageProps>().props;
    const [activeTab, setActiveTab] = useState('details');

    useFlashMessages();

    const { data, setData, put, processing, errors } = useForm<ItemFormData>({
        name: item.name || '',
        sku: item.sku || '',
        tax_ids: item.tax_ids || [],
        category_id: item.category_id?.toString() || '',
        description: item.description || '',
        sale_price: item.sale_price?.toString() || '',
        purchase_price: item.purchase_price?.toString() || '',
        unit: item.unit?.toString() || '',
        image: item.image || '',
        images: item.images ? (typeof item.images === 'string' ? JSON.parse(item.images) : item.images) : [],
        duration: item.duration?.toString() || '',
        total_slots: item.total_slots?.toString() || '1',
        type: item.type || 'bookings',
    });

    const validateDetailsTab = () => {
        return data.name.trim() !== '' && 
               data.sku.trim() !== '' &&
               data.tax_ids.length > 0 &&
               data.category_id !== '';
    };

    const validatePricingTab = () => {
        return data.sale_price.trim() !== '' &&
               data.purchase_price.trim() !== '' &&
               data.unit !== '';
    };

    const nextTab = () => {
        if (activeTab === 'details') {
            if (!validateDetailsTab()) {
                return;
            }
            setActiveTab('pricing');
        }
        else if (activeTab === 'pricing') {
            if (!validatePricingTab()) {
                return;
            }
            setActiveTab('media');
        }
        else if (activeTab === 'media') setActiveTab('duration');
    };

    const prevTab = () => {
        if (activeTab === 'pricing') setActiveTab('details');
        else if (activeTab === 'media') setActiveTab('pricing');
        else if (activeTab === 'duration') setActiveTab('media');
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('bookings.items.update', item.id), {
            onSuccess: () => {
                // Success message will be shown via flash messages
            },
            onError: (errors) => {
                console.error('Update errors:', errors);
            }
        });
    };

    return (
        <Dialog>
            <AuthenticatedLayout
                breadcrumbs={[
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('Items'), url: route('bookings.items.index')},
                    {label: t('Edit')}
                ]}
                pageTitle={
                    <div className="flex items-center justify-between">
                        <span>{t('Edit Item')}</span>
                    </div>
                }
            >
                <Head title={t('Edit Item')} />

                <Card>
                    <CardContent>
                        <form onSubmit={submit} className="pt-5">
                            <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">

                                <TabsContent value="details" className="space-y-6 mt-6">
                                    <input type="hidden" name="type" value="bookings" />
                                    <div>
                                        <Label htmlFor="name">{t('Name')}</Label>
                                        <Input
                                            id="name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder={t('Enter Name')}
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                                        <div>
                                            <Label htmlFor="sku">{t('SKU')}</Label>
                                            <div className="flex gap-2">
                                                <Input
                                                    id="sku"
                                                    value={data.sku}
                                                    onChange={(e) => setData('sku', e.target.value)}
                                                    placeholder={t('Enter SKU')}
                                                    required
                                                />
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() => setData('sku', 'SKU-' + Date.now())}
                                                >
                                                    {t('Generate')}
                                                </Button>
                                            </div>
                                            <InputError message={errors.sku} />
                                        </div>
                                        <div>
                                            <Label htmlFor="tax_ids" required>{t('Tax')}</Label>
                                            <MultiSelectEnhanced
                                                options={taxes?.map(tax => ({
                                                    value: tax.id.toString(),
                                                    label: `${tax.tax_name} (${tax.rate}%)`
                                                })) || []}
                                                value={data.tax_ids ? data.tax_ids.map(String) : []}
                                                onValueChange={(value) => setData('tax_ids', value)}
                                                placeholder={t('Select Taxes')}
                                            />
                                            <InputError message={errors.tax_ids} />
                                        </div>
                                        <div>
                                            <Label htmlFor="category_id" required>{t('Category')}</Label>
                                            <Select value={data.category_id} onValueChange={(value) => setData('category_id', value)} required>
                                                <SelectTrigger>
                                                    <SelectValue placeholder={t('Select Category')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {categories.map((category) => (
                                                        <SelectItem key={category.id} value={category.id.toString()}>
                                                            {category.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors.category_id} />
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

                                    <div className="flex justify-end">
                                        <Button
                                            type="button"
                                            onClick={nextTab}
                                            disabled={!validateDetailsTab()}
                                        >
                                            {t('Next')}
                                        </Button>
                                    </div>
                                </TabsContent>

                                <TabsContent value="pricing" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="sale_price">{t('Sale Price')}</Label>
                                            <Input
                                                id="sale_price"
                                                type="number"
                                                step="0.01"
                                                value={data.sale_price}
                                                onChange={(e) => setData('sale_price', e.target.value)}
                                                placeholder={t('Enter Sale Price')}
                                                required
                                            />
                                            <InputError message={errors.sale_price} />
                                        </div>
                                        <div>
                                            <Label htmlFor="purchase_price">{t('Purchase Price')}</Label>
                                            <Input
                                                id="purchase_price"
                                                type="number"
                                                step="0.01"
                                                value={data.purchase_price}
                                                onChange={(e) => setData('purchase_price', e.target.value)}
                                                placeholder={t('Enter Purchase Price')}
                                                required
                                            />
                                            <InputError message={errors.purchase_price} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="unit" required>{t('Unit')}</Label>
                                            <Select value={data.unit} onValueChange={(value) => setData('unit', value)} required>
                                                <SelectTrigger>
                                                    <SelectValue placeholder={t('Select Unit')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {units.map((unit) => (
                                                        <SelectItem key={unit.id} value={unit.id.toString()}>
                                                            {unit.unit_name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors.unit} />
                                        </div>

                                    </div>

                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={prevTab}>
                                            {t('Previous')}
                                        </Button>
                                        <Button type="button" onClick={nextTab}>
                                            {t('Next')}
                                        </Button>
                                    </div>
                                </TabsContent>

                                <TabsContent value="media" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <MediaPicker
                                                label={t('Product Image')}
                                                value={data.image}
                                                onChange={(value) => setData('image', value)}
                                                placeholder={t('Select image...')}
                                                showPreview={true}
                                            />
                                            <InputError message={errors.image} />
                                        </div>
                                        <div>
                                            <MediaPicker
                                                label={t('Additional Images')}
                                                value={data.images}
                                                onChange={(value) => setData('images', Array.isArray(value) ? value : [value].filter(Boolean))}
                                                multiple={true}
                                                placeholder={t('Select multiple images')}
                                                showPreview={true}
                                            />
                                            <InputError message={errors.images} />
                                        </div>
                                    </div>
                                    
                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={prevTab}>
                                            {t('Previous')}
                                        </Button>
                                        <Button type="button" onClick={nextTab}>
                                            {t('Next')}
                                        </Button>
                                    </div>
                                </TabsContent>

                                <TabsContent value="duration" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="duration">{t('Duration')}</Label>
                                            <TimePicker
                                                id="duration"
                                                value={data.duration}
                                                onChange={(value) => setData('duration', value)}
                                                placeholder={t('Select duration')}
                                                required
                                            />
                                            <InputError message={errors.duration} />
                                        </div>
                                        <div>
                                            <Label htmlFor="total_slots">{t('Total Slots')}</Label>
                                            <Input
                                                id="total_slots"
                                                type="number"
                                                min="1"
                                                value={data.total_slots}
                                                onChange={(e) => setData('total_slots', e.target.value)}
                                                placeholder={t('Enter total slots')}
                                                required
                                            />
                                            <InputError message={errors.total_slots} />
                                        </div>
                                    </div>
                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={prevTab}>
                                            {t('Previous')}
                                        </Button>
                                        <div className="flex gap-2">
                                            <Button type="button" variant="outline" onClick={() => window.history.back()}>
                                                {t('Cancel')}
                                            </Button>
                                            <Button type="submit" disabled={processing}>
                                                {processing ? t('Updating...') : t('Update')}
                                            </Button>
                                        </div>
                                    </div>
                                </TabsContent>
                            </Tabs>
                        </form>
                    </CardContent>
                </Card>
            </AuthenticatedLayout>
        </Dialog>
    );
}