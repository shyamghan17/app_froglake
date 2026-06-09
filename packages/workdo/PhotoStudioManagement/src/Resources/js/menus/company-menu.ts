import { Camera } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const photostudiomanagementCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Photo Studio Dashboard'),
        href: route('photo-studio-management.index'),
        permission: 'manage-photo-studio-management-dashboard',
        parent: 'dashboard',
        order: 403,
    },
    {
        title: t('Photo Studio'),
        icon: Camera,
        permission: 'manage-photo-studio-management',
        order: 1180,
        children: [
            {
                title: t('Team Members'),
                href: route('photo-studio-management.team-members.index'),
                permission: 'manage-photo-studio-team-members',
            },
            {
                title: t('Camera Kits'),
                href: route('photo-studio-management.camera-kits.index'),
                permission: 'manage-photo-studio-camera-kit',
            },
            {
                title: t('Services'),
                href: route('photo-studio-management.services.index'),
                permission: 'manage-photo-studio-service',
            },
            {
                title: t('Appointments'),
                href: route('photo-studio-management.appointments.index'),
                permission: 'manage-photo-studio-appointments',
            },
            {
                title: t('Payments'),
                href: route('photo-studio-management.appointment-payments.index'),
                permission: 'manage-photo-studio-appointment-payments',
            },
            {
                title: t('Contacts'),
                href: route('photo-studio-management.contacts.index'),
                permission: 'manage-photo-studio-contacts',
            },
            {
                title: t('Subscribers'),
                href: route('photo-studio-management.subscribers.index'),
                permission: 'manage-photo-studio-subscribers',
            },
            {
                title: t('System Setup'),
                href: route('photo-studio-management.brand-settings.index'),
                permission: 'manage-photo-studio-brand-settings',
            },
        ],
    },
];