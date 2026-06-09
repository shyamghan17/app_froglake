import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { getImagePath } from '@/utils/helpers';
import Header from './Header';
import Footer from './Footer';

interface LayoutProps {
    title: string;
    children: React.ReactNode;
}

const Layout = ({ title, children }: LayoutProps) => {
    const { photoStudioSettings, userSlug } = usePage<{
        photoStudioSettings?: { brand_settings?: { favicon?: string; site_title?: string } };
        userSlug?: string;
    }>().props;

    const siteTitle = photoStudioSettings?.brand_settings?.site_title || 'Photo Studio';
    const pageTitle = `${title} | ${siteTitle}`;

    const faviconUrl = photoStudioSettings?.brand_settings?.favicon
        ? getImagePath(photoStudioSettings.brand_settings.favicon)
        : getImagePath('packages/workdo/PhotoStudioManagement/src/Resources/assets/images/favicon.png');

    return (
        <>
            <Head title={pageTitle}>
                <link rel="icon" type="image/png" href={faviconUrl} />
            </Head>
            <div className="overlay"></div>
            <Header userSlug={userSlug || ''} />
            {children}
            <Footer userSlug={userSlug || ''} />
        </>
    );
};

export default Layout;
