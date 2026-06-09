import React, { useState, useEffect } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { formatDate, formatTime } from '@/utils/helpers';
import Create from '../Create';
import Edit from '../Edit';
import { Appointment, Item, Package, User, Customer } from '../types';

interface AppointmentDialogProps {
    mode: 'create' | 'edit' | 'view';
    open: boolean;
    onOpenChange: (open: boolean) => void;
    appointment?: Appointment | null;
    items?: Item[];
    packages?: Package[];
    users?: User[];
    customers?: Customer[];
    onSuccess?: () => void;
}

export function AppointmentDialog({
    mode,
    open,
    onOpenChange,
    appointment,
    items = [],
    packages = [],
    users = [],
    customers = [],
    onSuccess
}: AppointmentDialogProps) {
    const { t } = useTranslation();
    const { props } = usePage();

    const handleClose = () => {
        onOpenChange(false);
    };

    const getTitle = () => {
        switch (mode) {
            case 'create': return t('Create Appointment');
            case 'edit': return t('Edit Appointment');
            case 'view': return t('View Appointment');
            default: return t('Appointment');
        }
    };

    return (
        <Dialog open={open} onOpenChange={mode === 'view' ? onOpenChange : handleClose}>
            <DialogContent className="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{getTitle()}</DialogTitle>
                </DialogHeader>
                
                {mode === 'view' && appointment && (
                    <div className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-700 block mb-1">{t('Appointment Number')}</label>
                            <p className="text-base font-semibold text-gray-900">{appointment.appointment_number || 'N/A'}</p>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-2">{t('Status')}</label>
                                <span className={`px-2 py-1 rounded-full text-sm font-normal ${
                                    appointment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    appointment.status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                    appointment.status === 'completed' ? 'bg-green-100 text-green-800' :
                                    'bg-gray-100 text-gray-800'
                                }`}>
                                    {appointment.status ? appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) : 'Pending'}
                                </span>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-2">{t('Payment Status')}</label>
                                <span className={`px-2 py-1 rounded-full text-sm font-normal ${
                                    appointment.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    appointment.payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                    appointment.payment_status === 'failed' ? 'bg-red-100 text-red-800' :
                                    appointment.payment_status === 'refunded' ? 'bg-purple-100 text-purple-800' :
                                    'bg-gray-100 text-gray-800'
                                }`}>
                                    {appointment.payment_status ? appointment.payment_status.charAt(0).toUpperCase() + appointment.payment_status.slice(1) : 'Pending'}
                                </span>
                            </div>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Date')}</label>
                                <p className="text-sm text-gray-900">{appointment.date ? formatDate(appointment.date, props) : 'N/A'}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Time')}</label>
                                <p className="text-sm text-gray-900">{appointment.start_time && appointment.end_time ? `${formatTime(appointment.start_time, props)} - ${formatTime(appointment.end_time, props)}` : 'N/A'}</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Customer')}</label>
                                <p className="text-sm text-gray-900">{(appointment.customer?.first_name || '') + ' ' + (appointment.customer?.last_name || '') || 'N/A'}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Staff')}</label>
                                <p className="text-sm text-gray-900">{appointment.staff?.name || 'N/A'}</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Item')}</label>
                                <p className="text-sm text-gray-900">{appointment.item?.name || 'N/A'}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 block mb-1">{t('Package')}</label>
                                <p className="text-sm text-gray-900">{appointment.package?.name || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                )}

                {mode === 'create' && (
                    <Create
                        items={items}
                        packages={packages}
                        users={users}
                        customers={customers}
                        onSuccess={() => {
                            onOpenChange(false);
                            onSuccess?.();
                        }}
                    />
                )}

                {mode === 'edit' && appointment && (
                    <Edit
                        appointment={appointment}
                        items={items}
                        packages={packages}
                        users={users}
                        customers={customers}
                        onSuccess={() => {
                            onOpenChange(false);
                            onSuccess?.();
                        }}
                    />
                )}
            </DialogContent>
        </Dialog>
    );
}