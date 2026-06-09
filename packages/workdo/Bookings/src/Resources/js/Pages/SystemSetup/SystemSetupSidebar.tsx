import { router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { cn } from '@/lib/utils';
import { Tag, Image, Settings, FileText, Plus, Phone, Share2, MessageSquare, Clock, Calendar, Mail, Info } from "lucide-react";

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
            key: 'brand-settings',
            label: t('Brand Settings'),
            icon: Tag,
            route: 'bookings.brand-settings.index',
            permission: 'manage-booking-brand-settings'
        },
        {
            key: 'banner-settings',
            label: t('Banner Settings'),
            icon: Image,
            route: 'bookings.banner-settings.index',
            permission: 'manage-booking-banner-settings'
        },
        {
            key: 'appointment-settings',
            label: t('Appointment Setting'),
            icon: Calendar,
            route: 'bookings.appointment-settings.index',
            permission: 'manage-booking-appointment-settings'
        },
        {
            key: 'contact-settings',
            label: t('Contact Setting'),
            icon: Mail,
            route: 'bookings.contact-settings.index',
            permission: 'manage-booking-contact-settings'
        },
        {
            key: 'about-us-settings',
            label: t('About Us Setting'),
            icon: Info,
            route: 'bookings.about-us-settings.index',
            permission: 'manage-booking-about-us-settings'
        },
        {
            key: 'additional-settings',
            label: t('Additional Setting'),
            icon: Settings,
            route: 'bookings.additional-settings.index',
            permission: 'manage-booking-additional-settings'
        },
        {
            key: 'custom-pages',
            label: t('Custom Pages'),
            icon: FileText,
            route: 'bookings.custom-pages.index',
            permission: 'manage-booking-custom-pages'
        },
        {
            key: 'social-links',
            label: t('Social Links'),
            icon: Share2,
            route: 'bookings.social-links.index',
            permission: 'manage-booking-social-links'
        },
        {
            key: 'contacts',
            label: t('Contacts'),
            icon: Phone,
            route: 'bookings.contacts-settings.index',
            permission: 'manage-booking-contacts'
        },
        {
            key: 'reviews',
            label: t('Reviews'),
            icon: MessageSquare,
            route: 'bookings.reviews-settings.index',
            permission: 'manage-booking-reviews'
        },
        {
            key: 'extra-services',
            label: t('Extra Services'),
            icon: Plus,
            route: 'bookings.booking-extra-services.index',
            permission: 'manage-booking-extra-services'
        },
        {
            key: 'business-hours',
            label: t('Business Hours'),
            icon: Clock,
            route: 'bookings.business-hours.index',
            permission: 'manage-booking-business-hours'
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
