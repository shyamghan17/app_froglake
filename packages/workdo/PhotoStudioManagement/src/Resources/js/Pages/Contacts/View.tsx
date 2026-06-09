import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Mail, Phone, Calendar, User, MessageSquare } from 'lucide-react';
import { formatDate } from '@/utils/helpers';

interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone_number?: string;
    message: string;
    received_date: string;
    created_at: string;
}

interface ViewProps {
    contact: Contact;
}

export default function View({ contact }: ViewProps) {
    const { t } = useTranslation();
    const fullName = `${contact.first_name} ${contact.last_name}`.trim();
    const initials = `${contact.first_name?.[0] ?? ''}${contact.last_name?.[0] ?? ''}`.toUpperCase();

    return (
        <DialogContent className="max-w-lg p-0 gap-0 rounded-2xl overflow-hidden border-2 border-indigo-100">
           <DialogHeader className="mb-4">
                <DialogTitle className="flex items-center gap-2">
                    <User className="h-4 w-4" />
                    {t('Contact Details')}
                </DialogTitle>
            </DialogHeader>
            {/* Avatar + Name */}
            <div className="flex items-center gap-4 pt-6 pb-4 bg-white">
                <div className="w-16 h-16 rounded-2xl bg-primary/10 shadow-sm flex items-center justify-center text-2xl font-bold text-primary/50 border border-primary/20   shrink-0">
                    {initials || <User className="h-7 w-7" />}
                </div>
                <div>
                    <h2 className="text-xl font-bold text-gray-900">{fullName || '—'}</h2>
                    <p className="text-xs text-indigo-500 font-medium mt-0.5 uppercase tracking-widest">{t('Contact')}</p>
                </div>
            </div>

            {/* Divider */}
            <div className="h-px bg-gray-100 mx-6" />

            {/* Info rows */}
            <div className="bg-white py-4 space-y-3">
                <Row icon={<Mail className="h-4 w-4 text-indigo-500" />} label={t('Email')} value={contact.email} />
                <Row icon={<Phone className="h-4 w-4 text-purple-500" />} label={t('Phone')} value={contact.phone_number || '—'} />
                <Row icon={<Calendar className="h-4 w-4 text-pink-500" />} label={t('Received Date')} value={formatDate(contact.received_date)} />
            </div>

            {/* Message bubble */}
            <div className="mb-6 rounded-2xl bg-primary/5 from-primary/50 to-primary/100 border border-primary/20 p-4">
                <div className="flex items-center gap-2 mb-2">
                    <MessageSquare className="h-4 w-4 text-primary/50" />
                    <span className="text-xs font-semibold text-primary/50 uppercase tracking-wide">{t('Message')}</span>
                </div>
                <p className="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">
                    {contact.message || <span className="italic text-gray-400">{t('No message provided')}</span>}
                </p>
            </div>
        </DialogContent>
    );
}

function Row({ icon, label, value }: { icon: React.ReactNode; label: string; value: string }) {
    return (
        <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                {icon}
            </div>
            <div className="min-w-0">
                <p className="text-[11px] text-gray-400 font-medium uppercase tracking-wide">{label}</p>
                <p className="text-sm font-semibold text-gray-800 truncate">{value}</p>
            </div>
        </div>
    );
}
