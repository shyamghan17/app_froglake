import React, { useState, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Edit, Copy } from 'lucide-react';
import { TimePicker } from '@/components/ui/time-picker';
import { toast } from 'sonner';
import SystemSetupSidebar from '../SystemSetupSidebar';

interface TimeSlot {
    open: string;
    close: string;
}

interface DayHours {
    is_closed: boolean;
    time_slots: TimeSlot[];
}

interface BusinessHoursData {
    [key: string]: DayHours;
}

interface BusinessHoursProps {
    businessHours: BusinessHoursData;
    auth: {
        user: {
            permissions?: string[];
        };
    };
}

export default function Index() {
    const { t } = useTranslation();
    const { businessHours: initialBusinessHours, auth } = usePage<BusinessHoursProps>().props;
    const [businessHours, setBusinessHours] = useState<BusinessHoursData>(initialBusinessHours || {});
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingDay, setEditingDay] = useState<string | null>(null);
    const [formData, setFormData] = useState({
        is_closed: false,
        time_slots: [{ open: '09:00', close: '17:00' }]
    });

    useFlashMessages();

    const daysOfWeek = [
        { key: 'monday', label: t('Monday') },
        { key: 'tuesday', label: t('Tuesday') },
        { key: 'wednesday', label: t('Wednesday') },
        { key: 'thursday', label: t('Thursday') },
        { key: 'friday', label: t('Friday') },
        { key: 'saturday', label: t('Saturday') },
        { key: 'sunday', label: t('Sunday') }
    ];

    useEffect(() => {
        if (initialBusinessHours) {
            setBusinessHours(initialBusinessHours);
        }
    }, [initialBusinessHours]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!editingDay) return;

        router.put(route('bookings.business-hours.update', editingDay), formData, {
            onSuccess: (page: any) => {
                if (page.props.businessHours) {
                    setBusinessHours(page.props.businessHours);
                }
                resetForm();
                toast.success(t('Business hours updated successfully'));
            }
        });
    };

    const resetForm = () => {
        setFormData({ is_closed: false, time_slots: [{ open: '09:00', close: '17:00' }] });
        setEditingDay(null);
        setIsModalOpen(false);
    };

    const openEditModal = (dayKey: string) => {
        const dayData = businessHours[dayKey];
        setEditingDay(dayKey);
        setFormData({
            is_closed: dayData?.is_closed || false,
            time_slots: dayData?.time_slots?.length ? dayData.time_slots : [{ open: '09:00', close: '17:00' }]
        });
        setIsModalOpen(true);
    };

    const addTimeSlot = () => {
        setFormData(prev => ({
            ...prev,
            time_slots: [...prev.time_slots, { open: '09:00', close: '17:00' }]
        }));
    };

    const removeTimeSlot = (index: number) => {
        setFormData(prev => ({
            ...prev,
            time_slots: prev.time_slots.filter((_, i) => i !== index)
        }));
    };

    const updateTimeSlot = (index: number, field: 'open' | 'close', value: string) => {
        setFormData(prev => ({
            ...prev,
            time_slots: prev.time_slots.map((slot, i) =>
                i === index ? { ...slot, [field]: value } : slot
            )
        }));
    };

    const copyFromPreviousDay = (currentDay: string) => {
        const dayIndex = daysOfWeek.findIndex(d => d.key === currentDay);
        const previousDay = dayIndex > 0 ? daysOfWeek[dayIndex - 1].key : daysOfWeek[6].key;
        
        if (businessHours[previousDay]) {
            const previousData = businessHours[previousDay];
            setEditingDay(currentDay);
            setFormData({
                is_closed: previousData.is_closed,
                time_slots: previousData.time_slots?.length ? [...previousData.time_slots] : [{ open: '09:00', close: '17:00' }]
            });
            setIsModalOpen(true);
            toast.success(t('Copied hours from previous day'));
        }
    };

    return (
        <TooltipProvider>
            <AuthenticatedLayout
                breadcrumbs={[
                    {label: t('Bookings'), url: route('bookings.dashboard')},
                    {label: t('System Setup'), url: route('bookings.brand-settings.index')},
                    {label: t('Business Hours')}
                ]}
                pageTitle={t('System Setup')}
            >
                <Head title={t('Business Hours')} />

                <div className="flex flex-col md:flex-row gap-8">
                    <div className="md:w-64 flex-shrink-0">
                        <SystemSetupSidebar activeItem="business-hours" />
                    </div>

                    <div className="flex-1">
                        <Card className="shadow-sm">
                            <CardContent className="p-6">
                                <h3 className="text-lg font-medium mb-6">{t('Business Hours')}</h3>
                                
                                <div className="divide-y">
                                    {daysOfWeek.map(({ key, label }) => {
                                        const dayData = businessHours[key];
                                        const isClosed = dayData?.is_closed || false;
                                        const timeSlots = dayData?.time_slots || [];
                                        
                                        return (
                                            <div key={key} className="py-4 first:pt-0">
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-4 flex-1">
                                                        <div className="w-24">
                                                            <span className="font-medium">{label}</span>
                                                        </div>
                                                        <div className="flex-1">
                                                            {isClosed ? (
                                                                <span className="text-gray-500">{t('Closed')}</span>
                                                            ) : timeSlots.length > 0 ? (
                                                                <div className="flex flex-wrap gap-2">
                                                                    {timeSlots.map((slot, index) => (
                                                                        <span key={index} className="text-sm px-2 py-1 bg-primary text-primary-foreground rounded">
                                                                            {slot.open} - {slot.close}
                                                                        </span>
                                                                    ))}
                                                                </div>
                                                            ) : (
                                                                <span className="text-gray-400">{t('No hours set')}</span>
                                                            )}
                                                        </div>
                                                    </div>
                                                    <div className="flex items-center gap-3">
                                                        <Switch
                                                            checked={!isClosed}
                                                            onCheckedChange={(checked) => {
                                                                router.put(route('bookings.business-hours.update', key), {
                                                                    is_closed: !checked,
                                                                    time_slots: checked ? (timeSlots.length ? timeSlots : [{ open: '09:00', close: '17:00' }]) : []
                                                                }, {
                                                                    onSuccess: (page: any) => {
                                                                        if (page.props.businessHours) {
                                                                            setBusinessHours(page.props.businessHours);
                                                                        }
                                                                    }
                                                                });
                                                            }}
                                                        />
                                                        {auth.user?.permissions?.includes('edit-booking-business-hours') && (
                                                            <div className="flex gap-1">
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button
                                                                            variant="ghost"
                                                                            size="sm"
                                                                            onClick={() => copyFromPreviousDay(key)}
                                                                            className="h-8 w-8 p-0"
                                                                        >
                                                                            <Copy className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Copy from previous day')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                                <Tooltip delayDuration={0}>
                                                                    <TooltipTrigger asChild>
                                                                        <Button
                                                                            variant="ghost"
                                                                            size="sm"
                                                                            onClick={() => openEditModal(key)}
                                                                            className="h-8 w-8 p-0 text-blue-600"
                                                                        >
                                                                            <Edit className="h-4 w-4" />
                                                                        </Button>
                                                                    </TooltipTrigger>
                                                                    <TooltipContent>
                                                                        <p>{t('Edit')}</p>
                                                                    </TooltipContent>
                                                                </Tooltip>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>
                                {t('Edit Hours')} - {editingDay ? daysOfWeek.find(d => d.key === editingDay)?.label : ''}
                            </DialogTitle>
                            <DialogDescription>
                                {t('Configure the operating hours for this day.')}
                            </DialogDescription>
                        </DialogHeader>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="flex items-center space-x-2">
                                <Switch
                                    checked={!formData.is_closed}
                                    onCheckedChange={(checked) => setFormData({ ...formData, is_closed: !checked })}
                                />
                                <Label>{formData.is_closed ? t('Closed') : t('Open')}</Label>
                            </div>

                            {!formData.is_closed && (
                                <div className="space-y-4">
                                    <div className="flex justify-between items-center">
                                        <Label>{t('Time Slots')}</Label>
                                        <Button type="button" variant="outline" size="sm" onClick={addTimeSlot}>
                                            {t('Add Slot')}
                                        </Button>
                                    </div>
                                    
                                    {formData.time_slots.map((slot, index) => (
                                        <div key={index} className="flex items-center gap-3">
                                            <TimePicker
                                                value={slot.open}
                                                onChange={(value) => updateTimeSlot(index, 'open', value)}
                                                required
                                            />
                                            <span>{t('to')}</span>
                                            <TimePicker
                                                value={slot.close}
                                                onChange={(value) => updateTimeSlot(index, 'close', value)}
                                                required
                                            />
                                            {formData.time_slots.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => removeTimeSlot(index)}
                                                >
                                                    {t('Remove')}
                                                </Button>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}

                            <div className="flex justify-end gap-2">
                                <Button type="button" variant="outline" onClick={resetForm}>
                                    {t('Cancel')}
                                </Button>
                                <Button type="submit">
                                    {t('Update')}
                                </Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>
            </AuthenticatedLayout>
        </TooltipProvider>
    );
}
