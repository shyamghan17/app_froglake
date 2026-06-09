import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { Badge } from '@/components/ui/badge';
import { formatCurrency, getImagePath } from '@/utils/helpers';
import { Scissors, Image } from 'lucide-react';

interface ViewServiceProps {
    service: any;
}

export default function ViewService({ service }: ViewServiceProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Scissors className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Service Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>
            
            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Service Type')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.service_type?.name || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Price')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.price ? formatCurrency(service.price) : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Max Bookable Persons')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.max_bookable_persons || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Time')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.time ? `${service.time}` : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Staff')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.staff?.name || t('No Staff Assigned')}</p>
                    </div>
                </div>

                {service.included_services && service.included_services.length > 0 && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Included Services')}</label>
                        <div className="bg-gray-50 p-2 rounded">
                            {service.included_services.map((includedService: string, index: number) => (
                                includedService && (
                                    <Badge key={index} variant="secondary" className="mr-2">
                                        {includedService}
                                    </Badge>
                                )
                            ))}
                        </div>
                    </div>
                )}

                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Description')}</label>
                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{service.description || '-'}</p>
                </div>
            </div>
        </DialogContent>
    );
}