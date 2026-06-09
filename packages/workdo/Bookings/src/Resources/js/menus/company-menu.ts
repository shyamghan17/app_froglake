import {  List , Settings, CreditCard } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const bookingsCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Booking Dashboard'),
        href: route('bookings.dashboard'),
        permission: 'manage-bookings-dashboard',
        parent: 'dashboard',
        order: 175,
    },
    {
        title: t('Bookings'),
        icon: List,
        permission: 'manage-bookings',
        order: 696,
        children: [
            {
                title: t('Items'),
                href: route('bookings.items.index'),
                permission: 'manage-booking-items',
            },
            {
                title: t('Packages'),
                href: route('bookings.packages.index'),
                permission: 'manage-booking-packages',
            },
            {
                title: t('Staff'),
                href: route('bookings.staff.index'),
                permission: 'manage-booking-staff',
            },
            {
                title: t('Customers'),
                href: route('bookings.customers.index'),
                permission: 'manage-booking-customers',
            },
            {
                title: t('Appointments'),
                href: route('bookings.appointments.index'),
                permission: 'manage-booking-appointments',
            },
            {
                title: t('Payments'),
                href: route('bookings.payments.index'),
                permission: 'manage-booking-payments',
            },
            {
                title: t('System Setup'),
                href: route('bookings.brand-settings.index'),
                permission: 'manage-booking-brand-settings',
            },
        ],
    },
];