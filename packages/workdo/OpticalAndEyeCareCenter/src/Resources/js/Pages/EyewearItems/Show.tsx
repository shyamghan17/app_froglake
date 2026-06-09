import { Head, usePage } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Glasses, Image } from "lucide-react";
import { formatCurrency, getImagePath } from "@/utils/helpers";
import { ImageSlider } from "@/components/ui/image-slider";

interface ShowEyewearItemPageProps {
    item: {
        id: number;
        name: string;
        sku?: string;
        description?: string;
        sale_price?: number;
        purchase_price?: number;
        total_quantity?: number;
        type: string;
        image?: string;
        images?: string[] | string;
        warehouse_stocks?: Array<{
            warehouse_name: string;
            quantity: number;
        }>;
        category?: {
            id: number;
            name: string;
        };
        unit_relation?: {
            id: number;
            unit_name: string;
        };
        taxes?: Array<{
            id: number;
            tax_name: string;
            rate: number;
        }>;
        customization_details?: string;
    };
}

export default function Show() {
    const { t } = useTranslation();
    const { item } = usePage<ShowEyewearItemPageProps>().props;
    let imageUrl = getImagePath(item.image);

    console.log('Item data:', item);
    console.log('Warehouse stocks:', item.warehouse_stocks);
    if (item.warehouse_stocks && item.warehouse_stocks.length > 0) {
        console.log('First warehouse stock:', item.warehouse_stocks[0]);
        console.log('Warehouse name:', item.warehouse_stocks[0].warehouse_name);
    }

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Optical And Eye Care Center') },
                { label: t('Eyewear Items'), url: route('optical-and-eye-care-center.eyewear-items.index') },
                { label: t('Details') }
            ]}
            pageTitle={t('Eyewear Item Details')}
        >
            <Head title={t('Eyewear Item Details')} />

            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Glasses className="h-5 w-5" />
                        {item.name}
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-8">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div className="lg:col-span-2 space-y-6">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Basic Information')}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {item.sku && (
                                        <div className="bg-gray-50 p-4 rounded-lg">
                                            <label className="text-lg font-semibold text-gray-800 mb-3">{t('SKU')}</label>
                                            <p className="text-gray-700 leading-relaxed">{item.sku}</p>
                                        </div>
                                    )}
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Category')}</label>
                                        <p className="text-gray-700 leading-relaxed">{item.category?.name || '-'}</p>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Type')}</label>
                                        <p className="text-gray-700 leading-relaxed">{item.type || '-'}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Pricing & Inventory')}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {item.sale_price && (
                                        <div className="bg-green-50 p-4 rounded-lg border border-green-200">
                                            <label className="text-sm font-medium text-green-700">{t('Sale Price')}</label>
                                            <p className="text-xl font-bold text-green-800 mt-1">{formatCurrency(item.sale_price)}</p>
                                        </div>
                                    )}
                                    {item.purchase_price && (
                                        <div className="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                            <label className="text-sm font-medium text-blue-700">{t('Purchase Price')}</label>
                                            <p className="text-xl font-bold text-blue-800 mt-1">{formatCurrency(item.purchase_price)}</p>
                                        </div>
                                    )}
                                    <div className="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                        <label className="text-sm font-medium text-orange-700">{t('Total Quantity')}</label>
                                        <p className="text-xl font-bold text-orange-800 mt-1">{Math.floor(item.total_quantity) || 0}</p>
                                    </div>
                                </div>
                            </div>

                            {item.warehouse_stocks && item.warehouse_stocks.length > 0 && (
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Warehouse Stock')}</h3>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <div className="space-y-2">
                                            {item.warehouse_stocks.map((stock, index) => (
                                                <div key={index} className="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                                                    <span className="font-medium text-gray-700">{stock.warehouse_name}</span>
                                                    <span className="text-lg font-semibold text-gray-900">{Math.floor(stock.quantity)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div>
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Additional Details')}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Unit')}</label>
                                        <p className="text-gray-700 leading-relaxed">{item.unit_relation?.unit_name || '-'}</p>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Taxes')}</label>
                                        <div className="mt-2">
                                            {item.taxes && item.taxes.length > 0 ? (
                                                <div className="flex flex-wrap gap-2">
                                                    {item.taxes.map((tax) => (
                                                        <Badge key={tax.id} variant="outline" className="text-sm">
                                                            {tax.tax_name} ({tax.rate}%)
                                                        </Badge>
                                                    ))}
                                                </div>
                                            ) : (
                                                <p className="text-lg text-gray-900">-</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                {item.description && (
                                    <div className="bg-gray-50 p-4 rounded-lg mt-4">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Description')}</label>
                                        <p className="text-gray-700 leading-relaxed">{item.description}</p>
                                    </div>
                                )}
                                {item.customization_details && (
                                    <div className="bg-gray-50 p-4 rounded-lg mt-4">
                                        <label className="text-lg font-semibold text-gray-800 mb-3">{t('Customization Details')}</label>
                                        <p className="text-gray-700 leading-relaxed">{item.customization_details}</p>
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="lg:col-span-1 space-y-6">
                            <div className="bg-white border rounded-lg p-6 shadow-sm">
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Product Image')}</h3>
                                {item.image ? (
                                    <img
                                        src={imageUrl}
                                        alt={item.name}
                                        className="w-full h-48 object-cover rounded-lg border shadow-md cursor-pointer"
                                        onClick={() => window.open(imageUrl, '_blank')}
                                    />
                                ) : (
                                    <div className="w-full h-48 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                        <div className="text-center">
                                            <Image className="h-16 w-16 text-gray-400 mx-auto mb-2" />
                                            <p className="text-gray-500 text-sm">{t('No Image Available')}</p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {(() => {
                                const additionalImages = item.images ?
                                    (typeof item.images === 'string' ? JSON.parse(item.images) : item.images).filter(Boolean) : [];
                                const fullPathImages = additionalImages.map(img => getImagePath(img));

                                return additionalImages.length > 0 && (
                                    <div className="bg-white border rounded-lg p-6 shadow-sm">
                                        <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Additional Images')}</h3>
                                        <ImageSlider
                                            images={fullPathImages}
                                            className="w-full"
                                            aspectRatio="square"
                                            showZoom={true}
                                            showDownload={true}
                                            autoPlay={additionalImages.length > 1}
                                            autoPlayInterval={5000}
                                            onImageClick={(index) => {
                                                window.open(fullPathImages[index], '_blank');
                                            }}
                                        />
                                    </div>
                                );
                            })()}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
