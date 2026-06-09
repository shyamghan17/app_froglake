import React from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from './components/PublicLayout';
import PageHeader from '../../components/PageHeader';
import { useTranslation } from 'react-i18next';
import { useFormFields } from '@/hooks/useFormFields';

interface CustomPageProps {
    page: {
        id: number;
        title: string;
        slug: string;
        page_header?: string;
        page_header_description?: string;
        content: string;
        is_active: boolean;
    };
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
}

export default function CustomPage({ page, userSlug, settings, bookingSettings }: CustomPageProps) {
    const colors = bookingSettings?.color_settings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';

    return (
        <PublicLayout title={page.page_header || page.title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            <Head title={page.page_header || page.title} />

            <PageHeader
                title={page.page_header || page.title}
                description={page.page_header_description || ''}
                bgColor={primaryColor}
            />

            {/* Page Content */}
            <section className="py-16">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        <div
                            className="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900"
                            style={{ '--link-color': primaryColor } as React.CSSProperties}
                            dangerouslySetInnerHTML={{ __html: page.content }}
                        />
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}