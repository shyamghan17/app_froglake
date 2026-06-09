import { useState, useMemo, useCallback, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Copy, Send, Mail, Download, Share2, Upload, ChevronLeft, ChevronRight } from "lucide-react";
import ShareDialog from './ShareDialog';
import DownloadDialog from './DownloadDialog';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { RotasIndexProps, Employee, Shift } from './types';
import { formatDate } from '@/utils/helpers';
import ScheduleBuilder from './ScheduleBuilder';

interface ScheduleData {
    [employeeId: number]: Array<{
        id?: string;
        employeeId: number;
        shiftId?: number;
        date: string;
        startTime: string;
        endTime: string;
        type: 'shift' | 'dayoff' | 'leave';
        notes?: string;
    }>;
}

declare global {
    interface Window {
        scheduleTimeout: NodeJS.Timeout;
    }
}

export default function Index() {
    const { t } = useTranslation();
    const { pageProps } = usePage().props as any;
    const { branches, departments, designations, auth, employees, shifts, settings, leaveApplications, holidays, weekPublished, allRotasPublished } = usePage<RotasIndexProps & {
        employees?: Employee[];
        shifts?: Shift[];
        settings?: { rotas_week_starts?: number };
        leaveApplications?: any;
        holidays?: any[];
        weekPublished?: boolean;
        allRotasPublished?: boolean;
    }>().props;

    const [currentWeek, setCurrentWeek] = useState(0);

    const [shareDialog, setShareDialog] = useState(false);
    const [downloadDialog, setDownloadDialog] = useState(false);

    const urlParams = new URLSearchParams(window.location.search);
    const [filters, setFilters] = useState({
        branch_id: urlParams.get('branch_id') || 'all',
        department_id: urlParams.get('department_id') || 'all',
        designation_id: urlParams.get('designation_id') || 'all',
    });

    useFlashMessages();

    const handleFilter = () => {
        router.get(route('rotas.index'), {
            ...filters,
            week: currentWeek
        }, {
            preserveState: true,
            replace: true
        });
    };

    const clearFilters = () => {
        setFilters({
            branch_id: 'all',
            department_id: 'all',
            designation_id: 'all',
        });
        router.get(route('rotas.index'));
    };



    const handlePublishWeek = () => {
        router.post(route('rotas.publish-week'), {
            start_date: weekDates.start,
            end_date: weekDates.end
        }, {
            preserveState: true
        });
    };

    const handleCopyToNextWeek = () => {
        router.post(route('rotas.copy-week'), {
            start_date: weekDates.start,
            end_date: weekDates.end
        }, {
            preserveState: true
        });
    };

    const handleSendMail = () => {
        router.post(route('rotas.send-mail'), {
            start_date: weekDates.start,
            end_date: weekDates.end
        }, {
            preserveState: true
        });
    };

    // Use dates from URL params or calculate current week
    const weekDates = useMemo(() => {
        const urlStartDate = urlParams.get('start_date');
        const urlEndDate = urlParams.get('end_date');

        if (urlStartDate && urlEndDate) {
            return {
                start: urlStartDate,
                end: urlEndDate
            };
        }

        // Fallback to current week calculation
        const today = new Date();
        const weekStart = settings?.rotas_week_starts ?? 1;
        const currentDay = today.getDay();
        const diff = currentDay - weekStart;
        const adjustedDiff = diff < 0 ? diff + 7 : diff;
        const startDate = new Date(today);
        startDate.setDate(today.getDate() - adjustedDiff + (currentWeek * 7));

        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + 6);

        return {
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0]
        };
    }, [currentWeek, settings?.rotas_week_starts, urlParams]);

    const handleScheduleChange = useCallback(() => {
    }, []);

    const navigateWeek = (direction: 'prev' | 'next') => {
        // Get current dates from URL or calculate from current week
        const currentStartDate = new Date(weekDates.start);

        // Calculate new week dates by adding/subtracting 7 days
        const newStartDate = new Date(currentStartDate);
        newStartDate.setDate(currentStartDate.getDate() + (direction === 'next' ? 7 : -7));

        const newEndDate = new Date(newStartDate);
        newEndDate.setDate(newStartDate.getDate() + 6);

        // Update currentWeek state for consistency
        const newWeek = direction === 'next' ? currentWeek + 1 : currentWeek - 1;
        setCurrentWeek(newWeek);

        // Fetch new week data from backend
        router.get(route('rotas.index'), {
            ...filters,
            start_date: newStartDate.toISOString().split('T')[0],
            end_date: newEndDate.toISOString().split('T')[0]
        }, {
            preserveState: true,
            replace: true
        });
    };

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    { label: t('Rotas'), url: route('rotas.dashboard.index') },
                    { label: t('Manage Rotas') }
                ]}
                pageTitle={t('Manage Rotas')}
                pageActions={
                    <div className="flex gap-2">
                        {auth.user?.permissions?.includes('share-rotas') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="outline" size="sm" onClick={() => setShareDialog(true)}>
                                        <Share2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Share Schedule')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('publish-rotas') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="outline" size="sm" onClick={() => handlePublishWeek()}>
                                        <Upload className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Publish Week')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('download-rotas') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="outline" size="sm" onClick={() => setDownloadDialog(true)}>
                                        <Download className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Download Rotas')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('send-mail-rotas') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="outline" size="sm" onClick={() => handleSendMail()}>
                                        <Mail className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Send Mail')}</p></TooltipContent>
                            </Tooltip>
                        )}
                        {auth.user?.permissions?.includes('create-rotas') && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button variant="outline" size="sm" onClick={() => handleCopyToNextWeek()}>
                                        <Copy className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent><p>{t('Copy to Next Week')}</p></TooltipContent>
                            </Tooltip>
                        )}
                    </div>
                }
            >
                <Head title={t('Rotas')} />

                <Card className="shadow-sm">
                    <CardContent className="p-6 border-b bg-gray-50/50">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex items-center gap-3">
                                <div>
                                    <Select value={filters.branch_id} onValueChange={(value) => {
                                        const newFilters = { ...filters, branch_id: value };
                                        setFilters(newFilters);
                                        router.get(route('rotas.index'), {
                                            ...newFilters,
                                            start_date: weekDates.start,
                                            end_date: weekDates.end
                                        }, {
                                            preserveState: true,
                                            replace: true
                                        });
                                    }}>
                                        <SelectTrigger className="w-48">
                                            <SelectValue placeholder={t('All Branches')} />
                                        </SelectTrigger>
                                        <SelectContent searchable>
                                            <SelectItem value="all">{t('All Branches')}</SelectItem>
                                            {branches?.map((branch) => (
                                                <SelectItem key={branch.id} value={branch.id.toString()}>
                                                    {branch.branch_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Select value={filters.department_id} onValueChange={(value) => {
                                        const newFilters = { ...filters, department_id: value };
                                        setFilters(newFilters);
                                        router.get(route('rotas.index'), {
                                            ...newFilters,
                                            start_date: weekDates.start,
                                            end_date: weekDates.end
                                        }, {
                                            preserveState: true,
                                            replace: true
                                        });
                                    }}>
                                        <SelectTrigger className="w-48">
                                            <SelectValue placeholder={t('All Departments')} />
                                        </SelectTrigger>
                                        <SelectContent searchable>
                                            <SelectItem value="all">{t('All Departments')}</SelectItem>
                                            {departments?.map((department) => (
                                                <SelectItem key={department.id} value={department.id.toString()}>
                                                    {department.department_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Select value={filters.designation_id} onValueChange={(value) => {
                                        const newFilters = { ...filters, designation_id: value };
                                        setFilters(newFilters);
                                        router.get(route('rotas.index'), {
                                            ...newFilters,
                                            start_date: weekDates.start,
                                            end_date: weekDates.end
                                        }, {
                                            preserveState: true,
                                            replace: true
                                        });
                                    }}>
                                        <SelectTrigger className="w-48">
                                            <SelectValue placeholder={t('All Designations')} />
                                        </SelectTrigger>
                                        <SelectContent searchable>
                                            <SelectItem value="all">{t('All Designations')}</SelectItem>
                                            {designations?.map((designation) => (
                                                <SelectItem key={designation.id} value={designation.id.toString()}>
                                                    {designation.designation_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            {/* Week Navigation */}
                            <div className="flex items-center gap-3">
                                <div className="flex items-center gap-2">
                                    <Button size="sm" variant="outline" onClick={() => navigateWeek('prev')}>
                                        <ChevronLeft className="h-4 w-4" />
                                    </Button>
                                    <div className="flex flex-col items-center">
                                        <span className="text-sm font-medium px-3">
                                            {formatDate(weekDates.start, pageProps)} - {formatDate(weekDates.end, pageProps)}
                                        </span>
                                        {weekPublished && (
                                            <span className="text-xs text-green-600 font-medium">
                                                {allRotasPublished ? t('All Published') : t('Partially Published')}
                                            </span>
                                        )}
                                        {!weekPublished && (
                                            <span className="text-xs text-gray-500">
                                                {t('Unpublished')}
                                            </span>
                                        )}
                                    </div>
                                    <Button size="sm" variant="outline" onClick={() => navigateWeek('next')}>
                                        <ChevronRight className="h-4 w-4" />
                                    </Button>
                                </div>
                                <Button variant="outline" onClick={clearFilters} size="sm">{t('Clear Filters')}</Button>
                            </div>
                        </div>
                    </CardContent>

                    <CardContent className="p-0">


                        {/* Schedule Builder */}
                        {employees && shifts ? (
                            <ScheduleBuilder
                                employees={employees}
                                shifts={shifts}
                                onScheduleChange={handleScheduleChange}
                                startDate={weekDates.start}
                                endDate={weekDates.end}
                                currentWeek={currentWeek}
                                leaveApplications={leaveApplications}
                                holidays={holidays}
                            />
                        ) : (
                            <Card>
                                <CardContent className="p-8 text-center text-muted-foreground">
                                    {t('Loading schedule data...')}
                                </CardContent>
                            </Card>
                        )}
                    </CardContent>


                </Card>



                <ShareDialog
                    open={shareDialog}
                    onOpenChange={setShareDialog}
                    startDate={weekDates.start}
                    endDate={weekDates.end}
                />

                <DownloadDialog
                    open={downloadDialog}
                    onOpenChange={setDownloadDialog}
                    employees={employees || []}
                    startDate={weekDates.start}
                    endDate={weekDates.end}
                    leaveApplications={leaveApplications}
                />
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}