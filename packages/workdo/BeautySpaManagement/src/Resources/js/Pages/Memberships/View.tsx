import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { CreditCard } from 'lucide-react';
import { BeautyMembership } from './types';

interface ViewProps {
    beautymembership: BeautyMembership;
}

export default function View({ beautymembership }: ViewProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <CreditCard className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Membership Details')}</DialogTitle>
                        <p className="text-sm text-muted-foreground">{beautymembership.name}</p>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.name}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Duration')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.duration || '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Price')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.price ? `$${beautymembership.price}` : '-'}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Included Services')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.included_services?.name || '-'}</p>
                    </div>
                </div>

                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Benefits')}</label>
                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.benefits || '-'}</p>
                </div>

                <div className="space-y-2">
                    <label className="text-sm font-medium text-gray-700">{t('Description')}</label>
                    <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautymembership.description || '-'}</p>
                </div>
            </div>
        </DialogContent>
    );
}