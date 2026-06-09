import { Eye, User, FileText, Calendar, ShoppingBag, Glasses } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const opticalandeyecarecenterCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Optical & Eye Care Center Dashboard'),
        href: route('optical-and-eye-care-center.dashboard'),
        permission: 'manage-optical-dashboard',
        parent: 'dashboard',
        order: 189,
    },
    {
        title: t('Optical & Eye Care'),
        icon: Eye,
        permission: 'manage-optical-and-eye-care-center',
        order: 691,
        children: [
            {
                title: t('Doctor'),
                href: route('optical-and-eye-care-center.optical-doctors.index'),
                permission: 'manage-optical-doctors',
            },
            {
                title: t('Eye Patients'),
                href: route('optical-and-eye-care-center.eye-patients.index'),
                permission: 'manage-eye-patients',
            },
            {
                title: t('Eye Test Prescriptions'),
                href: route('optical-and-eye-care-center.eye-test-prescriptions.index'),
                permission: 'manage-eye-test-prescriptions',
            },
            {
                title: t('Eye Care Appointments'),
                href: route('optical-and-eye-care-center.eye-care-appoinments.index'),
                permission: 'manage-eye-care-appoinments',
            },
               {
                title: t('Eyewear Items'),
                href: route('optical-and-eye-care-center.eyewear-items.index'),
                permission: 'manage-eyewear-items',
            },
              {
                title: t('Eyewear Orders'),
                href: route('optical-and-eye-care-center.eyewear-orders.index'),
                permission: 'manage-eyewear-orders',
            },
        ],
    },
];
