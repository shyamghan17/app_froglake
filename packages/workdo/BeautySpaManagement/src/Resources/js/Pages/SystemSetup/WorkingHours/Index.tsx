import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useFlashMessages } from '@/hooks/useFlashMessages';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from '@/components/ui/button';
import { Card, CardContent } from "@/components/ui/card";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { TimePicker } from '@/components/ui/time-picker';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/ui/input-error';
import SystemSetupSidebar from "../SystemSetupSidebar";

interface BusinessHour {
    day: string;
    is_open: boolean;
}

interface WorkingHoursProps {
    work: {
        opening_time: string;
        closing_time: string;
        day_of_week: string;
        business_hours?: string;
        holiday_setting: string;
    } | null;
    week_days: string[];
    business_hours: BusinessHour[];
    auth: any;
    isHrmActive: boolean;
}

export default function Index() {
    const { t } = useTranslation();
    const { work, week_days, business_hours, auth, isHrmActive } = usePage<WorkingHoursProps>().props;

    const [businessHoursState, setBusinessHoursState] = useState<BusinessHour[]>(business_hours || []);

    const { data, setData, post, processing, errors } = useForm({
        opening_time: work?.opening_time || '09:00',
        closing_time: work?.closing_time || '17:00',
        business_hours: businessHoursState,
        holiday_setting: work?.holiday_setting || 'off'
    });

    // Sync business hours state with form data
    React.useEffect(() => {
        setData('business_hours', businessHoursState);
    }, [businessHoursState]);

    useFlashMessages();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('beauty-spa-management.working-hours.store'));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Beauty Spa Management'), url: route('beauty-spa-management.index') },
                { label: t('System Setup'), url: route('beauty-spa-management.service-types.index') },
                { label: t('Working Hours') }
            ]}
            pageTitle={t('System Setup')}
        >
            <Head title={t('Working Hours')} />

            <div className="flex flex-col md:flex-row gap-8">
                <div className="md:w-64 flex-shrink-0">
                    <SystemSetupSidebar activeItem="working-hours" />
                </div>

                <div className="flex-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <h3 className="text-lg font-medium mb-6">{t('Working Hours')}</h3>

                            <form onSubmit={submit} className="space-y-6">
                                <div className="grid grid-cols-2 gap-6">
                                    <div>
                                        <Label className="text-base font-medium">{t('Working days of the week:')}</Label>
                                        <div className="space-y-0.5 mt-3">
                                            {week_days.map((day) => (
                                                <div key={day} className="flex items-center justify-between py-2 px-3">
                                                    <Label className="font-medium">{t(day)}</Label>
                                                    <Switch
                                                        checked={businessHoursState.find(h => h.day === day)?.is_open ?? true}
                                                        onCheckedChange={(checked) => {
                                                            const newHours = [...businessHoursState];
                                                            const existingIndex = newHours.findIndex(h => h.day === day);
                                                            if (existingIndex >= 0) {
                                                                newHours[existingIndex].is_open = checked;
                                                            } else {
                                                                newHours.push({ day, is_open: checked });
                                                            }
                                                            setBusinessHoursState(newHours);
                                                            setData('business_hours', newHours);
                                                        }}
                                                    />
                                                </div>
                                            ))}
                                        </div>
                                        <InputError message={errors.business_hours} />
                                    </div>

                                    <div className="space-y-4">
                                        <Label className="text-base font-medium">{t('Working Time:')}</Label>
                                        <div>
                                            <Label htmlFor="opening_time">{t('Opening Time')}</Label>
                                            <TimePicker
                                                id="opening_time"
                                                value={data.opening_time}
                                                onChange={(value) => setData('opening_time', value)}
                                                required
                                            />
                                            <InputError message={errors.opening_time} />
                                        </div>

                                        <div>
                                            <Label htmlFor="closing_time">{t('Closing Time')}</Label>
                                            <TimePicker
                                                id="closing_time"
                                                value={data.closing_time}
                                                onChange={(value) => setData('closing_time', value)}
                                                required
                                            />
                                            <InputError message={errors.closing_time} />
                                        </div>

                                        {/* Holiday Setting - Only show if Hrm addon is active */}
                                        {isHrmActive && (
                                            <div className="flex items-center justify-between">
                                                <Label className="text-base font-medium">{t('Holiday')}</Label>
                                                <Switch
                                                    checked={data.holiday_setting === 'on'}
                                                    onCheckedChange={(checked) => {
                                                        setData('holiday_setting', checked ? 'on' : 'off');
                                                    }}
                                                />
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div className="flex justify-end">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? t('Saving...') : t('Save Changes')}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}