import { CalendarClock } from 'lucide-react';
import { isPackageActive } from '@/utils/helpers';

declare global {
    function route(name: string): string;
}

type MenuItem = {
    title: string;
    href?: string;
    permission: string;
    icon?: any;
    order?: number;
    children?: MenuItem[];
};

/**
 * Generates the rotas company menu structure
 * @param t - Translation function
 * @returns Array of menu items for rotas functionality
 */
export const rotasCompanyMenu = (t: (key: string) => string): MenuItem[] => {
    const rotasChildren: MenuItem[] = [];

    rotasChildren.push(
        {
            title: t('Work Schedule'),
            href: route('rotas.work-schedules.index'),
            permission: 'manage-rotas-work-schedules',
            order: 10,
        },
        {
            title: t('Availabilities'),
            href: route('rotas.availabilities.index'),
            permission: 'manage-rotas-availabilities',
            order: 12,
        },
        {
            title: t('Rotas'),
            href: route('rotas.index'),
            permission: 'manage-rotas',
            order: 15,
        }
    );

    // Show this menus if HRM package is in active
    if (!isPackageActive('Hrm')) {
        rotasChildren.push(
            {
                title: t('Employees'),
                href: route('rotas.employees.index'),
                permission: 'manage-rotas-employees',
                order: 20,
            },
            {
                title: t('Announcements'),
                href: route('rotas.announcements.index'),
                permission: 'manage-rotas-announcements',
                order: 30,
            },
            {
                title: t('Leave Management'),
                permission: 'manage-rotas-leave-applications',
                order: 40,
                children: [
                    {
                        title: t('Leave Types'),
                        href: route('rotas.leave-types.index'),
                        permission: 'manage-rotas-leave-types',
                    },
                    {
                        title: t('Leave Applications'),
                        href: route('rotas.leave-applications.index'),
                        permission: 'manage-rotas-leave-applications',
                    },
                    {
                        title: t('Leave Balance'),
                        href: route('rotas.leave-balance.index'),
                        permission: 'manage-rotas-leave-balance',
                    },
                ],
            },
            {
                title: t('System Setup'),
                permission: 'manage-rotas-system-setup',
                order: 50,
                href: route('rotas.branches.index')
            }
        );
    }

    return [
        {
            title: t('Rotas Dashboard'),
            href: route('rotas.dashboard.index'),
            permission: 'manage-rotas-dashboard',
            parent: 'dashboard',
            order: 100,
        },
        {
            title: t('Rotas'),
            icon: CalendarClock,
            permission: 'manage-rotas',
            order: 625,
            children: rotasChildren,
        },
    ];
};