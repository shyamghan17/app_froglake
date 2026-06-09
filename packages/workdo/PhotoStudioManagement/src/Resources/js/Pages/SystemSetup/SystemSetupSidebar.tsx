import { router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { cn } from '@/lib/utils';
import { Palette, Image, Info, Type, MessageSquare, GalleryHorizontal, LayoutGrid, Tag, Trophy, Newspaper, HelpCircle, Phone, LayoutTemplate, Layers, FileText, Boxes, Camera } from 'lucide-react';

interface SidebarItem {
    key: string;
    label: string;
    icon: React.ComponentType<{ className?: string }>;
    route: string;
    permission: string;
}

interface SystemSetupSidebarProps {
    activeItem?: string;
}

export default function SystemSetupSidebar({ activeItem }: SystemSetupSidebarProps) {
    const { t } = useTranslation();
    const { auth } = usePage().props as any;

    const sidebarItems: SidebarItem[] = [
        {
            key: 'brand-settings',
            label: t('Brand Settings'),
            icon: Palette,
            route: 'photo-studio-management.brand-settings.index',
            permission: 'manage-photo-studio-brand-settings',
        },
        {
            key: 'banner-section',
            label: t('Banner Section'),
            icon: Image,
            route: 'photo-studio-management.banner-section.index',
            permission: 'manage-photo-studio-banner-section',
        },
        {
            key: 'about-section',
            label: t('About Section'),
            icon: Info,
            route: 'photo-studio-management.about-section.index',
            permission: 'manage-photo-studio-about-section',
        },
        {
            key: 'title-section',
            label: t('Title Section'),
            icon: Type,
            route: 'photo-studio-management.title-section.index',
            permission: 'manage-photo-studio-title-section',
        },
        {
            key: 'testimonials',
            label: t('Testimonials'),
            icon: MessageSquare,
            route: 'photo-studio-management.testimonials.index',
            permission: 'manage-photo-studio-testimonials',
        },
        {
            key: 'gallery-section',
            label: t('Gallery Section'),
            icon: GalleryHorizontal,
            route: 'photo-studio-management.gallery-section.index',
            permission: 'manage-photo-studio-gallery-section',
        },
        {
            key: 'award-section',
            label: t('Award Section'),
            icon: Trophy,
            route: 'photo-studio-management.award-section.index',
            permission: 'manage-photo-studio-award-section',
        },
        {
            key: 'media-section',
            label: t('Media Section'),
            icon: Newspaper,
            route: 'photo-studio-management.media-section.index',
            permission: 'manage-photo-studio-media-section',
        },
        {
            key: 'faqs',
            label: t('FAQ'),
            icon: HelpCircle,
            route: 'photo-studio-management.faqs.index',
            permission: 'manage-photo-studio-faqs',
        },
        {
            key: 'contact-section',
            label: t('Contact Section'),
            icon: Phone,
            route: 'photo-studio-management.contact-section.index',
            permission: 'manage-photo-studio-contact-section',
        },
        {
            key: 'footer-section',
            label: t('Footer Section'),
            icon: LayoutTemplate,
            route: 'photo-studio-management.footer-section.index',
            permission: 'manage-photo-studio-footer-section',
        },
        {
            key: 'custom-pages',
            label: t('Custom Pages'),
            icon: FileText,
            route: 'photo-studio-management.custom-pages.index',
            permission: 'manage-photo-studio-custom-pages',
        },
        {
            key: 'service-categories',
            label: t('Service Categories'),
            icon: Layers,
            route: 'photo-studio-management.service-categories.index',
            permission: 'manage-photo-studio-service-category',
        },
        {
            key: 'equipment-tags',
            label: t('Equipment Tags'),
            icon: Tag,
            route: 'photo-studio-management.equipment-tags.index',
            permission: 'manage-photo-studio-equipment-tag',
        },
        {
            key: 'equipment-types-list',
            label: t('Equipment Types'),
            icon: Boxes,
            route: 'photo-studio-management.equipment-types.index',
            permission: 'manage-photo-studio-equipment-type',
        },
        {
            key: 'gallery-types',
            label: t('Gallery Types'),
            icon: LayoutGrid,
            route: 'photo-studio-management.gallery-types.index',
            permission: 'manage-photo-studio-gallery-type',
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
                        const isActive = activeItem === item.key;

                        return (
                            <Button
                                key={item.key}
                                variant="ghost"
                                className={cn('w-full justify-start', {
                                    'bg-muted font-medium': isActive,
                                })}
                                onClick={() => router.get(route(item.route))}
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
