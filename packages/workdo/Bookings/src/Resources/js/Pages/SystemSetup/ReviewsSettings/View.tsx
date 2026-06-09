import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useTranslation } from 'react-i18next';
import { formatDate } from '@/utils/helpers';
import { Star } from 'lucide-react';

interface Review {
    id: number;
    name: string;
    email: string;
    comment: string;
    rating: number;
    item_id?: number;
    item?: {
        id: number;
        name: string;
    };
    created_at?: string;
}

interface ViewProps {
    review: Review;
}

export default function View({ review }: ViewProps) {
    const { t } = useTranslation();

    const renderStars = (rating: number) => {
        return (
            <div className="flex gap-1">
                {[1, 2, 3, 4, 5].map((star) => (
                    <Star
                        key={star}
                        className={`h-5 w-5 ${star <= rating ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300'}`}
                    />
                ))}
            </div>
        );
    };

    return (
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-hidden">
            <DialogHeader className="pb-4">
                <DialogTitle>{t('Review Details')}</DialogTitle>
            </DialogHeader>

            <div className="overflow-y-auto max-h-[calc(90vh-140px)] p-2">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    {/* Customer Information Card */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                        <h4 className="text-lg font-semibold text-gray-900 mb-4">
                            {t('Customer Information')}
                        </h4>
                        <div className="space-y-3">
                            <div>
                                <span className="text-gray-600">{t('Name')}:</span>
                                <p className="font-medium">{review.name}</p>
                            </div>
                            <div>
                                <span className="text-gray-600">{t('Email')}:</span>
                                <p className="font-medium text-sm break-all">{review.email}</p>
                            </div>
                            {review.item && (
                                <div>
                                    <span className="text-gray-600">{t('Service')}:</span>
                                    <p className="font-medium">{review.item.name}</p>
                                </div>
                            )}
                            {review.created_at && (
                                <div>
                                    <span className="text-gray-600">{t('Date')}:</span>
                                    <p className="font-medium">{formatDate(review.created_at)}</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Review Information Card */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 md:p-6">
                        <h4 className="text-lg font-semibold text-gray-900 mb-4">
                            {t('Review Details')}
                        </h4>
                        <div className="space-y-3">
                            <div>
                                <span className="text-gray-600">{t('Rating')}:</span>
                                <div className="mt-1">{renderStars(review.rating)}</div>
                            </div>
                            <div>
                                <span className="text-gray-600">{t('Comment')}:</span>
                                <div className="mt-2 p-3 bg-gray-50 rounded-md border">
                                    <p className="text-sm leading-relaxed whitespace-pre-wrap">{review.comment}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    );
}
