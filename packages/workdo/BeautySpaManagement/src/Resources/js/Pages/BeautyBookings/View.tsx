import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Card, CardContent } from "@/components/ui/card";
import { Calendar } from "lucide-react";
import { formatDate, formatTime, formatCurrency } from '@/utils/helpers';
import { BeautyBooking } from './types';

interface ViewModalProps {
    booking: BeautyBooking;
    beautyservices: Array<{ id: number; name: string; price: number }>;
}

export default function ViewModal({ booking, beautyservices }: ViewModalProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader className="pb-4 border-b">
                <div className="flex items-center gap-3">
                    <div className="p-2 bg-primary/10 rounded-lg">
                        <Calendar className="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle className="text-xl font-semibold">{t('Booking Details')}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>

            <div className="overflow-y-auto flex-1 p-4 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Name')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.name}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Email')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.email}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Phone Number')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.phone_number}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Gender')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.gender === '0' ? t('Male') : booking.gender === '1' ? t('Female') : t('Other')}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Service')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{beautyservices?.find(s => s.id.toString() === booking.service?.toString())?.name || booking.service}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Date')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatDate(booking.date)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Time')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatTime(booking.start_time)} - {formatTime(booking.end_time)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Person')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.person}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Price')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{formatCurrency(booking.price)}</p>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Payment Option')}</label>
                        <div className="bg-gray-50 p-2 rounded">
                            <span className={`px-2 py-1 rounded-full text-xs ${booking.payment_option === 'paid' ? 'bg-green-100 text-green-800' :
                                booking.payment_option === 'Offline' ? 'bg-gray-100 text-gray-800' :
                                    'bg-blue-100 text-blue-800'
                                }`}>
                                {t(booking.payment_option?.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) || 'Pending')}
                            </span>
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Payment Status')}</label>
                        <div className="bg-gray-50 p-2 rounded">
                            <span className={`px-2 py-1 rounded-full text-xs ${booking.payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                    booking.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-red-100 text-red-800'
                                }`}>
                                {t(booking.payment_status?.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) || 'Pending')}
                            </span>
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Reference')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded">{booking.reference === '0' ? t('Google') : booking.reference === '1' ? t('Friend') : booking.reference === '2' ? t('Social Media') : t('Other')}</p>
                    </div>
                </div>

                {booking.notes && (
                    <div className="space-y-2">
                        <label className="text-sm font-medium text-gray-700">{t('Notes')}</label>
                        <p className="text-sm text-gray-900 bg-gray-50 p-2 rounded whitespace-pre-wrap">{booking.notes}</p>
                    </div>
                )}
            </div>
        </DialogContent>
    );
}