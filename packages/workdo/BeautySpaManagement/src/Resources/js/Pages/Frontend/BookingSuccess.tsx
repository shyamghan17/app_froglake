import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import Layout from './Layout';
import { CheckCircle, Calendar, Download, Phone } from 'lucide-react';
import { formatCurrency } from '@/utils/helpers';
import html2pdf from 'html2pdf.js';
import { route } from 'ziggy-js';

interface BookingData {
    id: number;
    name: string;
    phone_number: string;
    date: string;
    start_time: string;
    end_time: string;
    service?: {
        name: string;
    };
    beautyService?: {
        name: string;
    };
    service_name?: string;
    person: number;
    payment_option: string;
    price: number;
}

interface ContactInfo {
    beauty_spa_store_name?: string;
    phone_number?: string;
}

interface Props {
    title?: string;
    beautybooking: BookingData;
    contact_info: ContactInfo;
    userSlug: string;
}

export default function BookingSuccess({ title = 'Booking Success', beautybooking, contact_info, userSlug }: Props) {
    const { t } = useTranslation();

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-GB');
    };

    const formatTime = (timeString: string) => {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    };

    const saveAsPDF = async () => {
        const element = document.getElementById('printableArea');
        if (element) {
            const opt = {
                margin: 0.25,
                filename: 'Booking Receipt.pdf',
                image: { type: 'jpeg' as const, quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' as const }
            };

            try {
                await html2pdf().set(opt).from(element as HTMLElement).save();
            } catch (error) {
                console.error('PDF generation failed:', error);
            }
        }
    };

    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Get in Touch')}</span>
                            <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">
                                {t('Book Your Appointment')}
                            </h2>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-lg md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">
                                {t('Your comfort and care are our priority. Select your service and book a time that works best for you.')}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Success Section */}
                <section className="lg:py-16 py-10">
                    <div className="container mx-auto px-4">
                        <div id="successMessage" className="flex items-center justify-center">
                            <div className="bg-white rounded-2xl border border-gray-200 lg:p-6 p-4 max-w-xl w-full flex flex-col items-center">
                                {/* Printable Section */}
                                <div id="printableArea">
                                    <div className="flex flex-col items-center mb-6 text-center">
                                        <div className="bg-green-100 rounded-full flex items-center justify-center w-12 h-12 md:w-16 md:h-16">
                                            <CheckCircle className="text-2xl md:text-3xl text-green-500" />
                                        </div>
                                        <h3 className="text-2xl md:text-3xl font-extrabold text-[#df9896] my-2">{t('Booking Confirmed!')}</h3>
                                        <p className="text-gray-700 text-base sm:text-lg">
                                            {t('Thank you for booking your appointment with us. Your service has been successfully scheduled at')}
                                            <span className="font-semibold text-[#df9896]"> {contact_info?.beauty_spa_store_name || '-'}</span>!
                                        </p>
                                    </div>

                                    <div className="bg-gray-50 rounded-lg p-4 sm:p-6 w-full mb-6">
                                        <h2 className="text-[#df9896] font-bold text-xl md:text-2xl mb-3">
                                            {t('Appointment Details')}
                                        </h2>

                                        <ul className="text-gray-800 text-base sm:text-lg flex flex-col gap-3">
                                            <li><span className="font-semibold">{t('Name')} :</span> <span className="text-base">{beautybooking?.name || '-'}</span></li>
                                            <li><span className="font-semibold">{t('Phone')} :</span> <span className="text-base">{beautybooking?.phone_number || '-'}</span></li>
                                            <li><span className="font-semibold">{t('Date')} :</span> <span className="text-base">{beautybooking?.date ? formatDate(beautybooking.date) : '-'}</span></li>
                                            <li><span className="font-semibold">{t('Time')} :</span>
                                                <span className="text-base">{beautybooking?.start_time && beautybooking?.end_time ? `${formatTime(beautybooking.start_time)} - ${formatTime(beautybooking.end_time)}` : '-'}</span>
                                            </li>
                                            <li><span className="font-semibold">{t('Service')} :</span> <span className="text-base">{beautybooking?.service?.name || beautybooking?.beautyService?.name || beautybooking?.service_name || '-'}</span></li>
                                            <li><span className="font-semibold">{t('Persons')} :</span> <span className="text-base">{beautybooking?.person || '-'}</span></li>
                                            <li><span className="font-semibold">{t('Payment')} :</span> <span className="text-base">{beautybooking?.payment_option || '-'}</span></li>
                                            <li><span className="font-semibold">{t('Total Amount')} :</span>
                                                <span className="text-base">{beautybooking?.price ? formatCurrency(beautybooking.price) : '-'}</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div className="text-center mb-4">
                                        <p className="text-gray-800 text-xs sm:text-sm">
                                            {t('For any inquiries or changes to your booking, please contact us at')}
                                            <a href={`tel:${contact_info?.phone_number || ''}`} className="text-[#df9896] font-semibold underline">
                                                {contact_info?.phone_number || '-'}
                                            </a>.
                                        </p>
                                    </div>
                                </div>
                                {/* END Printable Section */}

                                <div className="booking-confirm-wrp flex gap-2 w-full mt-4">
                                    <Link
                                        href={route('beauty-spa.home', { userSlug })}
                                        className="inline-flex flex-1 justify-center w-full rounded-md border items-center gap-2 border-[#df9896] bg-[#df9896] shadow-sm px-4 py-2 font-medium text-white hover:bg-transparent hover:text-[#df9896] transition-colors"
                                    >
                                        {t('Back to Home')}
                                    </Link>
                                    <button
                                        onClick={saveAsPDF}
                                        className="inline-flex flex-1 justify-center w-full rounded-md border items-center gap-2 border-[#df9896] bg-[#df9896] shadow-sm px-4 py-2 font-medium text-white hover:bg-transparent hover:text-[#df9896] transition-colors"
                                    >
                                        <Download className="w-4 h-4" />
                                        {t('Download')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </Layout>
    );
}