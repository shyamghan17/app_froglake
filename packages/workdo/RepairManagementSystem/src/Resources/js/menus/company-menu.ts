import { Hammer } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const repairmanagementsystemCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Repair'),
        icon: Hammer,
        permission: 'manage-repair-management-system',
        order: 714,
        children: [
            {
                title: t('Order Requests'),
                href: route('repair-management-system.repair-order-requests.index'),
                permission: 'manage-repair-order-requests',
            },
            {
                title: t('Repair Invoices'),
                href: route('repair-management-system.repair-invoices.index'),
                permission: 'manage-repair-invoices',
            },
            {
                title: t('Technicians'),
                href: route('repair-management-system.repair-technicians.index'),
                permission: 'manage-repair-technicians',
            },
            {
                title: t('Warranties'),
                href: route('repair-management-system.repair-warranties.index'),
                permission: 'manage-repair-warranties',
            },
        ],
    },
];