import { router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { ShieldAlert, Bell, CheckCheck, FileText, Download, Eye } from 'lucide-react';
import { AlertDialog, AlertDialogContent } from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getImagePath } from '@/utils/helpers';
import { useCriticalNoticeAlert } from '../hooks/useCriticalNoticeAlert';

export default function CriticalNoticeAlert() {
    const { t } = useTranslation();
    const pageProps = usePage().props as any;
    const { alerts, dismiss, acknowledge } = useCriticalNoticeAlert();

    if (alerts.length === 0) return null;

    const current = alerts[0];

    const handleAcknowledge = () => acknowledge(current.id);
    const handleDismiss = () => dismiss(current.id);
    const handleView = () => router.visit(route('notice-board.notices.show', current.id));

    const isImage = (url: string) => /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(url);

    return (
        <AlertDialog open={!!current}>
            <AlertDialogContent className="max-w-md p-0 overflow-hidden gap-0 border-red-200 dark:border-red-900 shadow-xl">

                {/* Priority strip */}
                <div className="h-1.5 w-full bg-red-500" />

                {/* Header */}
                <div className="px-5 py-4 border-b dark:border-slate-700 bg-red-50/30 dark:bg-red-900/10 flex items-start gap-3">
                    <div className="h-10 w-10 rounded-xl bg-red-100 dark:bg-red-900/40 border border-red-200 dark:border-red-800 flex items-center justify-center shrink-0">
                        <Bell className="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div className="flex-1 min-w-0 text-start">
                        <div className="flex items-center gap-2 flex-wrap mb-1">
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                                <ShieldAlert className="h-3 w-3" /> {t('Critical Notice')}
                            </span>
                            {alerts.length > 1 && (
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    {alerts.length} {t('pending')}
                                </span>
                            )}
                        </div>
                        <p className="text-sm font-bold text-slate-900 dark:text-slate-50 leading-snug">{current.title}</p>
                    </div>
                </div>

                {/* Body */}
                <div className="max-h-[45vh] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-600">

                    {/* Description */}
                    {current.description && (
                        <div className="px-5 py-4">
                            <div
                                className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed prose prose-sm dark:prose-invert max-w-none"
                                dangerouslySetInnerHTML={{ __html: current.description }}
                            />
                        </div>
                    )}

                    {/* Attachments */}
                    {current.attachments?.length > 0 && (
                        <div className="px-5 pb-4 border-t dark:border-slate-700 pt-3 space-y-2">
                            <p className="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{t('Attachments')}</p>
                            <div className="grid grid-cols-2 gap-2">
                                {current.attachments.map((url, i) => {
                                    const fileName = url.split('/').pop() || url;
                                    const fileUrl = getImagePath(url, pageProps);
                                    const img = isImage(url);

                                    return (
                                        <div key={i} className="flex flex-col rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm transition-all">
                                            {img ? (
                                                <div className="h-16 overflow-hidden bg-slate-50 dark:bg-slate-700">
                                                    <img src={fileUrl} alt={fileName} className="w-full h-full object-cover" />
                                                </div>
                                            ) : (
                                                <div className="h-16 flex items-center justify-center bg-slate-50 dark:bg-slate-700 border-b dark:border-slate-600">
                                                    <FileText className="h-7 w-7 text-slate-400 dark:text-slate-500" />
                                                </div>
                                            )}
                                            <div className="flex items-center justify-center gap-1 py-1 border-t dark:border-slate-700 bg-slate-50/60 dark:bg-slate-700/60">
                                                <TooltipProvider>
                                                    <Tooltip delayDuration={0}>
                                                        <TooltipTrigger asChild>
                                                            <Button size="sm" variant="ghost" className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                                                onClick={() => { const a = document.createElement('a'); a.href = fileUrl; a.download = fileName; a.click(); }}>
                                                                <Download className="h-4 w-4" />
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent><p>{t('Download')}</p></TooltipContent>
                                                    </Tooltip>
                                                    <Tooltip delayDuration={0}>
                                                        <TooltipTrigger asChild>
                                                            <Button size="sm" variant="ghost" className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                                                onClick={() => window.open(fileUrl, '_blank')}>
                                                                <Eye className="h-4 w-4" />
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent><p>{t('View')}</p></TooltipContent>
                                                    </Tooltip>
                                                </TooltipProvider>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>

                {/* Footer */}
                <div className="px-5 py-3 border-t dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex items-center justify-end gap-2">
                    {current.require_acknowledgment ? (
                        <>
                            <Button variant="outline" size="sm" className="order-1 rtl:order-2" onClick={handleView}>
                                {t('Read More')}
                            </Button>
                            <Button size="sm" className="gap-1.5 bg-green-600 hover:bg-green-700 text-white order-2 rtl:order-1" onClick={handleAcknowledge}>
                                <CheckCheck className="h-3.5 w-3.5" />
                                {t('I Acknowledge')}
                            </Button>
                        </>
                    ) : (
                        <>
                            <Button variant="outline" size="sm" className="order-1 rtl:order-2" onClick={handleDismiss}>
                                {t('Dismiss')}
                            </Button>
                            <Button size="sm" className="order-2 rtl:order-1" onClick={handleView}>
                                {t('Read More')}
                            </Button>
                        </>
                    )}
                </div>

            </AlertDialogContent>
        </AlertDialog>
    );
}
