import { useState } from 'react';
import { Head, useForm, usePage } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import { useFormFields } from '@/hooks/useFormFields';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { MultiSelectEnhanced } from "@/components/ui/multi-select-enhanced";
import { Card, CardContent } from "@/components/ui/card";
import { Tabs, TabsContent } from "@/components/ui/tabs";
import { Dialog } from "@/components/ui/dialog";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import MediaPicker from "@/components/MediaPicker";
import InputError from "@/components/ui/input-error";
import { RichTextEditor } from "@/components/ui/rich-text-editor";

export default function Create() {
    const { t } = useTranslation();
    const { taxes, categories, units, warehouses } = usePage<any>().props;
    const [activeTab, setActiveTab] = useState('details');

    useFlashMessages();

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        sku: '',
        tax_ids: [],
        category_id: '',
        description: '',
        long_description: '',
        sale_price: '',
        purchase_price: '',
        unit: '',
        quantity: '',
        image: '',
        images: [],
        warehouse_id: '',
        type: 'eyewear',
        product_type: '',
        brand_name: '',
        prescription_detail: '',
        numbering_status: 'numbering',
        customization_details: ''
    });

    const descriptionAI = useFormFields('aiField', data, setData, errors, 'create', 'description', 'Short Description', 'opticalandeyecarecenter', 'eyewearitem');

    const validateDetailsTab = () => {
        return data.name.trim() !== '' &&
            data.sku.trim() !== '' &&
            data.tax_ids.length > 0 &&
            data.category_id !== '';
    };

    const validatePricingTab = () => {
        return data.sale_price.trim() !== '' &&
            data.purchase_price.trim() !== '' &&
            data.unit !== '' &&
            data.quantity.trim() !== '';
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
        else if (activeTab === 'media') setActiveTab('warehouse');
        else if (activeTab === 'media') setActiveTab('warehouse');
    };

    const prevTab = () => {
        if (activeTab === 'pricing') setActiveTab('details');
        else if (activeTab === 'media') setActiveTab('pricing');
        else if (activeTab === 'warehouse') setActiveTab('media');
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('optical-and-eye-care-center.eyewear-items.store'));
    };

    return (
        <Dialog>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Optical And Eye Care Center') },
                    { label: t('Eyewear Items'), url: route('optical-and-eye-care-center.eyewear-items.index') },
                    { label: t('Create') }
                ]}
                pageTitle={
                    <div className="flex items-center justify-between">
                        <span>{t('Create Eyewear Item')}</span>
                    </div>
                }
            >
                <Head title={t('Create Eyewear Item')} />

                <Card>
                    <CardContent>
                        <form onSubmit={submit} className="pt-5">
                            <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                                <TabsContent value="details" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 gap-6">
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
                                                    onClick={() => setData('sku', 'EYE-' + Date.now())}
                                                >
                                                    {t('Generate')}
                                                </Button>
                                            </div>
                                            <InputError message={errors.sku} />
                                        </div>
                                        <div>
                                            <Label htmlFor="tax_ids" required>{t('Tax')}</Label>
                                            <MultiSelectEnhanced
                                                options={taxes.map(tax => ({
                                                    value: tax.id.toString(),
                                                    label: `${tax.tax_name} (${tax.rate}%)`
                                                }))}
                                                value={data.tax_ids}
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
                                        <div className="flex items-center justify-between mb-2">
                                            <Label htmlFor="description">{t('Short Description')}</Label>
                                            <div className="flex gap-2">
                                                {descriptionAI.map(field => <div key={field.id}>{field.component}</div>)}
                                            </div>
                                        </div>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder={t('Enter Short Description')}
                                            rows={3}
                                        />
                                        <InputError message={errors.description} />
                                    </div>

                                    <div>
                                        <Label htmlFor="long_description">{t('Description')}</Label>
                                        <RichTextEditor
                                            content={data.long_description || ''}
                                            onChange={(value) => setData('long_description', value)}
                                            placeholder={t('Enter Description')}
                                        />
                                        <InputError message={errors.long_description} />
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
                                        <div>
                                            <Label htmlFor="quantity">{t('Quantity')}</Label>
                                            <Input
                                                id="quantity"
                                                type="number"
                                                value={data.quantity}
                                                onChange={(e) => setData('quantity', e.target.value)}
                                                placeholder={t('Enter Quantity')}
                                                required
                                            />
                                            <InputError message={errors.quantity} />
                                        </div>
                                    </div>

                                    <div className="flex justify-between">
                                        <Button type="button" variant="outline" onClick={prevTab}>
                                            {t('Previous')}
                                        </Button>
                                        <Button
                                            type="button"
                                            onClick={nextTab}
                                            disabled={!validatePricingTab()}
                                        >
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
                                                showPreview={false}
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

                                <TabsContent value="warehouse" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="warehouse_id" required>{t('Warehouse')}</Label>
                                            <Select value={data.warehouse_id} onValueChange={(value) => setData('warehouse_id', value)} required>
                                                <SelectTrigger>
                                                    <SelectValue placeholder={t('Select Warehouse')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {warehouses.map((warehouse) => (
                                                        <SelectItem key={warehouse.id} value={warehouse.id.toString()}>
                                                            {warehouse.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors.warehouse_id} />
                                        </div>
                                        <div>
                                            <Label htmlFor="product_type">{t('Product Type')}</Label>
                                            <Input
                                                id="product_type"
                                                value={data.product_type}
                                                onChange={(e) => setData('product_type', e.target.value)}
                                                placeholder={t('Enter Product Type')}
                                            />
                                            <InputError message={errors.product_type} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="brand_name">{t('Brand Name')}</Label>
                                            <Input
                                                id="brand_name"
                                                value={data.brand_name}
                                                onChange={(e) => setData('brand_name', e.target.value)}
                                                placeholder={t('Enter Brand Name')}
                                            />
                                            <InputError message={errors.brand_name} />
                                        </div>
                                        <div>
                                            <Label htmlFor="prescription_detail">{t('Prescription Detail')}</Label>
                                            <Input
                                                id="prescription_detail"
                                                value={data.prescription_detail}
                                                onChange={(e) => setData('prescription_detail', e.target.value)}
                                                placeholder={t('Enter Prescription Detail')}
                                            />
                                            <InputError message={errors.prescription_detail} />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <Label htmlFor="numbering_status">{t('Numbering Status')}</Label>
                                            <RadioGroup value={data.numbering_status} onValueChange={(value) => setData('numbering_status', value)}>
                                                <div className="flex items-center space-x-2">
                                                    <RadioGroupItem value="numbering" id="numbering" />
                                                    <Label htmlFor="numbering">{t('Numbering')}</Label>
                                                </div>
                                                <div className="flex items-center space-x-2">
                                                    <RadioGroupItem value="non-numbering" id="non-numbering" />
                                                    <Label htmlFor="non-numbering">{t('Non-Numbering')}</Label>
                                                </div>
                                            </RadioGroup>
                                            <InputError message={errors.numbering_status} />
                                        </div>
                                        <div>
                                            <Label htmlFor="customization_details">{t('Customization Details')}</Label>
                                            <Textarea
                                                id="customization_details"
                                                value={data.customization_details}
                                                onChange={(e) => setData('customization_details', e.target.value)}
                                                placeholder={t('Enter Customization Details')}
                                                rows={3}
                                            />
                                            <InputError message={errors.customization_details} />
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
                                                {processing ? t('Creating...') : t('Create')}
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
