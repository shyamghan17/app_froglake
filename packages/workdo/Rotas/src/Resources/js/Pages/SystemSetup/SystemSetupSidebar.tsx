import { router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { cn } from '@/lib/utils';
import {   Building, Users, Award, Tag, FileText, Heart, Clock } from "lucide-react";

interface SidebarItem {
    key: string;
    label: string;
    icon: React.ComponentType<{ className?: string }>;
    route: string;
    permission: string;
}

interface SystemSetupSidebarProps {
    activeItem?: string;
    onSectionChange?: (section: string) => void;
}

export default function SystemSetupSidebar({ activeItem, onSectionChange }: SystemSetupSidebarProps) {
    const { t } = useTranslation();
    const { auth } = usePage().props as any;
    const currentRoute = route().current();

    const sidebarItems: SidebarItem[] = [
        {
            key: 'rotas-branches',
            label: t('Branches'),
            icon: Building,
            route: 'rotas.branches.index',
            permission: 'manage-rotas-branches'
        },
        {
            key: 'rotas-departments',
            label: t('Departments'),
            icon: Users,
            route: 'rotas.departments.index',
            permission: 'manage-rotas-departments'
        },
        {
            key: 'rotas-designations',
            label: t('Designations'),
            icon: Award,
            route: 'rotas.designations.index',
            permission: 'manage-rotas-designations'
        },
        {
            key: 'rotas-employee-document-types',
            label: t('Employee Document Types'),
            icon: FileText,
            route: 'rotas.employee.document.types.index',
            permission: 'manage-rotas-employee-document-types'
        },
        {
            key: 'rotas-shifts',
            label: t('Shifts'),
            icon: Clock,
            route: 'rotas.shifts.index',
            permission: 'manage-rotas-shifts'
        },
        {
            key: 'rotas-announcement-categories',
            label: t('Announcement Categories'),
            icon: Tag,
            route: 'rotas.announcement-categories.index',
            permission: 'manage-rotas-announcement-categories'
        }
    ];

    const filteredItems = sidebarItems.filter(item =>
        auth.user?.permissions?.includes(item.permission)
    );

    return (
        <div className="sticky top-4">
            <ScrollArea className="h-[calc(100vh-8rem)]">
                <div className="pr-4 space-y-1">
                    {filteredItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = activeItem === item.key || currentRoute === item.route;

                        return (
                            <Button
                                key={item.key}
                                variant="ghost"
                                className={cn('w-full justify-start', {
                                    'bg-muted font-medium': isActive,
                                })}
                                onClick={() => {
                                    router.get(route(item.route));
                                    onSectionChange?.(item.key);
                                }}
                            >
                                <Icon className="h-4 w-4 mr-2" />
                                {item.label}
                            </Button>
                        );
                    })}
                </div>
            </ScrollArea>
        </div>
    );
}