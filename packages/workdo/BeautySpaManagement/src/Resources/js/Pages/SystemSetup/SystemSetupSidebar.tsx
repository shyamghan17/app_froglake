import { router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { cn } from '@/lib/utils';
import {  Tag, Clock , FileText, Palette, Image, Zap, MessageSquare, Info, Phone, Share2, Home } from "lucide-react";

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
            key: 'service-types',
            label: t('Service Types'),
            icon: Tag,
            route: 'beauty-spa-management.service-types.index',
            permission: 'manage-beauty-service-types'
        },
        {
            key: 'working-hours',
            label: t('Working Hours'),
            icon: Clock,
            route: 'beauty-spa-management.working-hours.index',
            permission: 'manage-beauty-spa-management'
        },
        {
            key: 'custom-pages',
            label: t('Custom Pages'),
            icon: FileText,
            route: 'beauty-spa-management.custom-pages.index',
            permission: 'manage-beauty-custom-pages'
        },
        {
            key: 'brand-settings',
            label: t('Brand Settings'),
            icon: Palette,
            route: 'beauty-spa-management.brand-settings.index',
            permission: 'manage-beauty-brand-settings'
        },
        {
            key: 'banner-section',
            label: t('Banner Section'),
            icon: Image,
            route: 'beauty-spa-management.banner-section.index',
            permission: 'manage-beauty-banner-section'
        },
        {
            key: 'home-section',
            label: t('Home Section'),
            icon: Home,
            route: 'beauty-spa-management.home-section.index',
            permission: 'manage-beauty-home-section'
        },
        {
            key: 'feature-section',
            label: t('Feature Section'),
            icon: Zap,
            route: 'beauty-spa-management.feature-section.index',
            permission: 'manage-beauty-feature-section'
        },
        {
            key: 'about-section',
            label: t('About Section'),
            icon: Info,
            route: 'beauty-spa-management.about-section.index',
            permission: 'manage-beauty-about-section'
        },
        {
            key: 'contact-info',
            label: t('Contact Info'),
            icon: Phone,
            route: 'beauty-spa-management.contact-info.index',
            permission: 'manage-beauty-contact-info'
        },
        {
            key: 'social-links',
            label: t('Social Links'),
            icon: Share2,
            route: 'beauty-spa-management.social-links.index',
            permission: 'manage-beauty-social-links'
        },
        {
            key: 'testimonials',
            label: t('Testimonials'),
            icon: MessageSquare,
            route: 'beauty-spa-management.testimonials.index',
            permission: 'manage-beauty-testimonials'
        },
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