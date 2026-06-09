import { Package } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const ebillingCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('EBilling Dashboard'),
        href: route('ebilling.index'),
        permission: 'manage-ebilling',
        parent: 'dashboard',
        order: 10,
    },
    {
        title: t('EBilling'),
        icon: Package,
        permission: 'manage-ebilling',
        order: 10,
        children: [
            {
                title: t('Items'),
                href: route('ebilling.items.index'),
                permission: 'manage-ebilling',
            },
        ],
    },
];