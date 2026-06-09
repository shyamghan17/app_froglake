import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { CreditCard, User, Briefcase, CalendarDays, DollarSign, FileText, Hash } from 'lucide-react';
import { PhotoStudioAppointmentPayment } from './types';
import { formatDate, formatCurrency } from '@/utils/helpers';

const statusConfig: Record<string, { dot: string; badge: string }> = {
    pending: { dot: 'bg-yellow-400', badge: 'bg-yellow-100 text-yellow-800' },
    cleared: { dot: 'bg-green-400',  badge: 'bg-green-100 text-green-800' },
};

interface ViewProps {
    payment: PhotoStudioAppointmentPayment;
}

export default function View({ payment }: ViewProps) {
    const { t } = useTranslation();
    const status = statusConfig[payment.payment_status] ?? { dot: 'bg-gray-400', badge: 'bg-gray-50 text-gray-700 ring-1 ring-gray-200' };
    const statusLabel = payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1);

    return (
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto p-0 gap-0 rounded-2xl overflow-hidden">
           <DialogHeader className="mb-4">
<DialogTitle className="flex items-center gap-2">
<CreditCard className="h-4 w-4" />
{t('Payment Details')}
</DialogTitle>
</DialogHeader>

            {/* Hero Banner */}
            <div className="relative bg-white border rounded-lg border-gray-200 px-6 pt-5 pb-8">
                <div className="relative flex items-start justify-between">
                    <div className="flex items-center gap-3">
                        <div className="p-2.5 bg-blue-100 rounded-xl">
                            <CreditCard className="h-5 w-5 text-blue-600" />
                        </div>
                        <div>
                            <p className="text-slate-500 text-xs font-medium">{t('Appointment No.')}</p>
                            <p className="text-gray-900 font-semibold text-md leading-tight">{payment.appointment_number || '—'}</p>
                        </div>
                    </div>
                    <span className={`px-2 py-1 rounded-full text-sm ${status.badge}`}>
                        {t(statusLabel)}
                    </span>
                </div>

                <div className="relative mt-5 text-center">
                    <p className="text-slate-500 text-xs font-medium uppercase tracking-widest">{t('Total Amount')}</p>
                    <p className="text-emerald-600 text-xl font-extrabold mt-1 tracking-tight">{formatCurrency(payment.amount)}</p>
                </div>
            </div>

            {/* Body */}
            <div className="py-5 space-y-5 bg-gray-50">

                {/* Customer & Service */}
                <Section label={t('Customer & Service')} color="blue">
                    <div className="grid grid-cols-2 gap-3">
                        <InfoTile icon={<User className="h-4 w-4 text-blue-600" />} iconBg="from-blue-100 to-blue-50" value={payment.customer_name} label={t('Customer Name')} />
                        <InfoTile icon={<Briefcase className="h-4 w-4 text-violet-600" />} iconBg="from-violet-100 to-violet-50" value={payment.service_name} label={t('Service')} />
                    </div>
                </Section>

                {/* Payment Info */}
                <Section label={t('Payment Info')} color="emerald">
                    <div className="grid grid-cols-2 gap-3">
                        <InfoTile icon={<CalendarDays className="h-4 w-4 text-sky-600" />} iconBg="from-sky-100 to-sky-50" value={formatDate(payment.payment_date)} label={t('Payment Date')} />
                        <InfoTile
                            icon={<Hash className="h-4 w-4 text-indigo-600" />}
                            iconBg="from-indigo-100 to-indigo-50"
                            value={payment.payment_type ? payment.payment_type.charAt(0).toUpperCase() + payment.payment_type.slice(1) : '—'}
                            label={t('Payment Type')}
                        />
                    </div>
                </Section>

                {/* Description */}
                <Section label={t('Description')} color="gray">
                    <div className="flex items-start gap-3 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div className="p-1.5 bg-gradient-to-br from-gray-100 to-gray-50 rounded-lg mt-0.5 shrink-0">
                            <FileText className="h-4 w-4 text-gray-500" />
                        </div>
                        <p className="text-sm text-gray-600 leading-relaxed">{payment.description || <span className="italic text-gray-400">{t('No description provided')}</span>}</p>
                    </div>
                </Section>

            </div>
        </DialogContent>
    );
}

function Section({ label, color, children }: { label: string; color: string; children: React.ReactNode }) {
    const accent: Record<string, string> = {
        blue: 'border-blue-500',
        emerald: 'border-emerald-500',
        gray: 'border-gray-400',
    };
    return (
        <div>
            <div className={`flex items-center gap-2 mb-3 pl-2 border-l-2 ${accent[color] ?? 'border-gray-400'}`}>
                <span className="text-xs font-semibold text-gray-600 uppercase tracking-wide">{label}</span>
            </div>
            {children}
        </div>
    );
}

function InfoTile({ icon, iconBg, value, label }: {
    icon: React.ReactNode;
    iconBg: string;
    value: string;
    label: string;
}) {
    return (
        <div className="group bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 text-center">
            <div className={`p-2 bg-gradient-to-br ${iconBg} rounded-lg mb-2 mx-auto w-fit group-hover:scale-110 transition-transform duration-200`}>
                {icon}
            </div>
            <div className="font-semibold text-sm text-gray-800 truncate px-1">{value || '—'}</div>
            <div className="text-[11px] text-slate-500 mt-0.5">{label}</div>
        </div>
    );
}
