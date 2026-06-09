import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { Badge } from '@/components/ui/badge';
import { Tag } from 'lucide-react';
import { BeautyServiceOffer } from './types';
import { formatCurrency, formatDate } from '@/utils/helpers';

interface ViewProps {
    beautyserviceoffer: BeautyServiceOffer;
}

export default function View({ beautyserviceoffer }: ViewProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Tag className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Service Offer Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Title')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.title || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Service')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.service?.name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Discount (%)')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.discount ? `${beautyserviceoffer.discount}%` : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Price')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.price ? formatCurrency(beautyserviceoffer.price) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Offer Price')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.offer_price ? formatCurrency(beautyserviceoffer.offer_price) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Start Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.start_date ? formatDate(beautyserviceoffer.start_date) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('End Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.end_date ? formatDate(beautyserviceoffer.end_date) : '-'}</p>
                    </div>
                </div>

                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Description')}</label>
                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyserviceoffer.description || '-'}</p>
                </div>
            </div>
        </DialogContent>
    );
}