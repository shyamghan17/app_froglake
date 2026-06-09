import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useTranslation } from 'react-i18next';
import { Badge } from '@/components/ui/badge';
import { ViewServiceProps } from './types';
import { getImagePath, formatCurrency } from '@/utils/helpers';
import { Briefcase, Tag, Camera, DollarSign, FileText } from 'lucide-react';

export default function View({ service, serviceCategories, cameraKits }: ViewServiceProps) {
    const { t } = useTranslation();

    const imagePreview = service.image
        ? (service.image.startsWith('http') ? service.image : getImagePath(service.image))
        : null;

    const resolveCategories = () =>
        service.service_category_ids?.map((id) => serviceCategories.find((c) => c.id.toString() === id)?.name).filter(Boolean) || [];

    const resolveCameraKits = () =>
        service.camera_kit_ids?.map((id) => cameraKits.find((k) => k.id.toString() === id)?.name).filter(Boolean) || [];

    const isActive = service.status === 'active';

    return (
        <DialogContent className="max-w-2xl p-0 overflow-hidden rounded-2xl max-h-[90vh] flex flex-col">

            {/* Dialog header */}
           <DialogHeader className="mb-4">
                <DialogTitle className="flex items-center gap-2">
                    <Briefcase className="h-5 w-5" />
                    {t('Service Details')}
                </DialogTitle>
            </DialogHeader>

            {/* Hero Banner */}
            <div className="relative h-48 shrink-0 bg-gradient-to-br from-slate-800 to-slate-900 overflow-hidden mt-3 rounded-xl">
                {imagePreview ? (
                    <img src={imagePreview} alt={service.name} className="w-full h-full object-cover rounded-lg opacity-80" />
                ) : (
                    <div className="w-full h-full flex items-center justify-center">
                        <Briefcase className="w-16 h-16 text-white/20" />
                    </div>
                )}
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent rounded-lg" />

                {/* Status badge — matches Index list view exactly */}
                <div className="absolute top-3 right-3">
                    <span className={`px-2 py-1 rounded-full text-sm ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                        {isActive ? t('Active') : t('Inactive')}
                    </span>
                </div>

                {/* Title + price overlay */}
                <div className="absolute bottom-0 left-0 right-0 px-5 pb-4">
                    <h2 className="text-xl font-bold text-white drop-shadow-md truncate">{service.name}</h2>
                    <div className="flex items-center gap-1.5 mt-1">
                        <DollarSign className="w-4 h-4 text-emerald-400" />
                        <span className="text-emerald-300 font-semibold text-sm">{formatCurrency(service.price)}</span>
                    </div>
                </div>
            </div>

            {/* Body */}
            <div className="overflow-y-auto flex-1 py-5 space-y-5">

                {/* Categories */}
                <Section icon={<Tag className="w-4 h-4 text-violet-500" />} label={t('Service Categories')}>
                    <TagList items={resolveCategories()} variant="secondary" emptyText="-" />
                </Section>

                {/* Camera Kits */}
                <Section icon={<Camera className="w-4 h-4 text-amber-500" />} label={t('Camera Kits')}>
                    <TagList items={resolveCameraKits()} variant="outline" emptyText="-" />
                </Section>

                {/* Description */}
                <Section icon={<FileText className="w-4 h-4 text-slate-500" />} label={t('Description')}>
                    <p className="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {service.description || <span className="text-gray-400">-</span>}
                    </p>
                </Section>
            </div>
        </DialogContent>
    );
}

function Section({ icon, label, children }: { icon: React.ReactNode; label: string; children: React.ReactNode }) {
    return (
        <div>
            <div className="flex items-center gap-2 mb-2">
                {icon}
                <span className="text-xs font-semibold uppercase tracking-wide text-gray-500">{label}</span>
            </div>
            <div className="pl-6">{children}</div>
        </div>
    );
}

function TagList({ items, variant, emptyText }: { items: (string | undefined)[]; variant: 'secondary' | 'outline'; emptyText: string }) {
    const filtered = items.filter(Boolean) as string[];
    if (!filtered.length) return <span className="text-sm text-gray-400">{emptyText}</span>;
    return (
        <div className="flex flex-wrap gap-1.5">
            {filtered.map((name, i) => (
                <Badge key={i} variant={variant} className="text-xs rounded-full px-2.5 py-0.5">{name}</Badge>
            ))}
        </div>
    );
}
