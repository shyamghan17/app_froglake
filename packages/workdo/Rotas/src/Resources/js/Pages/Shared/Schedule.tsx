import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { BrandProvider } from '@/contexts/brand-context';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Download, Calendar, Clock, UserIcon } from 'lucide-react';
import { formatDate, formatTime, getImagePath, formatCurrency } from '@/utils/helpers';
import ScheduleViewer from './ScheduleViewer';

interface SharedScheduleProps {
    userSlug: string;
    employees: any[];
    shifts: any[];
    scheduleData: any;
    shareData: any;
    startDate: string;
    endDate: string;
    leaveApplications?: any;
}

function SharedScheduleContent({
    employees,
    shifts,
    scheduleData,
    shareData,
    startDate,
    endDate,
    leaveApplications
}: SharedScheduleProps) {
    const { t } = useTranslation();
    const { pageProps, companyAllSetting } = usePage().props as any;
    const companyName = companyAllSetting?.titleText || 'Company Name';
    const logoDark = companyAllSetting?.logo_dark;
    const logoLight = companyAllSetting?.logo_light;
    const logoUrl = logoDark ? getImagePath(logoDark, pageProps) : (logoLight ? getImagePath(logoLight, pageProps) : '');
    const themeColor = companyAllSetting?.themeColor || 'blue';
    const customColor = companyAllSetting?.customColor || '#3b82f6';

    const colorMap: Record<string, string> = {
        blue: '#3b82f6',
        green: '#10b981',
        purple: '#8b5cf6',
        orange: '#f97316',
        red: '#ef4444'
    };
    const primaryColor = themeColor === 'custom' ? customColor : (colorMap[themeColor] || '#3b82f6');

    return (
        <div className="min-h-screen bg-transparent p-2 md:p-6">
            <Head title={t('Shared Schedule')} />

            <div className="w-full space-y-6">
                {/* Header Section */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center gap-4">
                        {logoUrl ? (
                            <img src={logoUrl} alt={companyName} className="h-12 w-auto object-contain" />
                        ) : (
                            <div className="h-12 w-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary font-bold text-xl">
                                {companyName.charAt(0)}
                            </div>
                        )}
                        <div>
                            <h1 className="text-xl font-bold text-gray-900">{companyName}</h1>
                            <div className="flex items-center gap-2 text-sm text-muted-foreground mt-1">
                                <Calendar className="h-4 w-4 text-primary" />
                                <span>{formatDate(startDate, pageProps)} - {formatDate(endDate, pageProps)}</span>
                            </div>
                        </div>
                    </div>

                    {shareData?.description && (
                        <div className="max-w-md text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 italic">
                            {shareData.description}
                        </div>
                    )}

                    <div id="ignore-pdf">
                        <Button
                            className="bg-primary hover:bg-primary/90 text-white shadow-sm transition-all active:scale-95"
                            onClick={() => {
                                if (typeof (window as any).downloadSchedulePDF === 'function') {
                                    (window as any).downloadSchedulePDF();
                                }
                            }}
                        >
                            <Download className="h-4 w-4 mr-2" />
                            {t('Download PDF')}
                        </Button>
                    </div>
                </div>

                {/* Main Content */}
                <Card className="shadow-lg border-none overflow-hidden rounded-xl">
                    <div className="bg-primary/5 p-4 border-b flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <Clock className="h-5 w-5 text-primary" />
                            <h2 className="font-semibold text-gray-800">{t('Weekly Schedule')}</h2>
                        </div>
                    </div>
                    <CardContent className="p-0">
                        <div className="overflow-x-hidden">
                            <div className="w-full">
                                <ScheduleViewer
                                    employees={employees}
                                    shifts={shifts}
                                    scheduleData={scheduleData}
                                    startDate={startDate}
                                    endDate={endDate}
                                    leaveApplications={leaveApplications}
                                    showHeader={false}
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Footer Info */}
                <div className="text-center text-xs text-muted-foreground pt-4 pb-8">
                    {t('Generated by')} {companyName} • {new Date().toLocaleDateString()}
                </div>
            </div>
            <style dangerouslySetInnerHTML={{
                __html: `
                .scrollbar-hide::-webkit-scrollbar {
                    display: none;
                }
                .scrollbar-hide {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            ` }} />
        </div>
    );
}

export default function Schedule(props: SharedScheduleProps) {
    return (
        <BrandProvider>
            <SharedScheduleContent {...props} />
        </BrandProvider>
    );
}