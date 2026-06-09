import React, { useState } from 'react';
import { Link, usePage, useForm, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';
import SocialLinks from '@/components/SocialLinks';

interface FooterProps {
    userSlug?: string;
}

const Footer = ({ userSlug = '' }: FooterProps) => {
    const { photoStudioSettings, customPages } = usePage<{ photoStudioSettings?: any; customPages?: any[] }>().props;

    const brandSettings = photoStudioSettings?.brand_settings || {};
    const footerSection = photoStudioSettings?.footer_section || {};

    const logoUrl = brandSettings.footer_logo
        ? getImagePath(brandSettings.footer_logo)
        : getImagePath('packages/workdo/PhotoStudioManagement/src/Resources/assets/images/footer-logo.png');

    const { t } = useTranslation();
    const [email, setEmail] = useState('');
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');

    const handleNewsletterSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post(route('photo-studio-management.frontend.newsletter.store', { userSlug }), { email }, {
            onSuccess: (page) => {
                setEmail('');
                const message = page.props.flash?.success || t('Successfully subscribed to newsletter!');
                setToastMessage(message);
                setShowToast(true);
                setTimeout(() => setShowToast(false), 4000);
            },
            onError: (errors) => {
                const message = errors.email || t('Failed to subscribe. Please try again.');
                setToastMessage(message);
                setShowToast(true);
                setTimeout(() => setShowToast(false), 4000);
            }
        });
    };

    const quickLinks = [
        { label: t('Services'), route: 'photo-studio-management.frontend.services' },
        { label: t('Portfolio'), route: 'photo-studio-management.frontend.portfolio' },
        { label: t('Camera Kit'), route: 'photo-studio-management.frontend.camera-kit' },
        { label: t('Awards'), route: 'photo-studio-management.frontend.media-awards' },
        { label: t('FAQ'), route: 'photo-studio-management.frontend.faq' },
        { label: t('Contact'), route: 'photo-studio-management.frontend.contact' },
    ];

    return (
        <footer className="lg:pt-16 pt-10 bg-black text-white">
            {/* Toast Notification */}
            {showToast && (
                <div className="fixed top-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 bg-[#674B2F] text-white">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{toastMessage}</span>
                </div>
            )}
            <div className="md:container w-full mx-auto px-4">
                {/* Newsletter Section */}
                <div className="flex flex-col md:flex-row items-center justify-between gap-5 pb-8 border-b border-gray-600">
                    <div className="text-center md:text-start">
                        <h2 className="text-2xl sm:text-3xl capitalize mb-3">
                            {footerSection.newsletter_label || t('Sign up to get latest update')}
                        </h2>
                        <p>{footerSection.newsletter_title || t('Sign up for our monthly newsletter for the latest news & articles')}</p>
                    </div>
                    <div className="relative lg:max-w-md max-w-full w-full">
                        <form onSubmit={handleNewsletterSubmit} className="flex flex-col sm:flex-row gap-3 sm:gap-0">
                            <input
                                type="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                className="flex-1 py-3 px-4 border border-white bg-transparent text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder={t('Enter your email')}
                                required
                            />
                            <button
                                type="submit"
                                className="btn py-2.5 px-6 bg-white text-black border-white hover:bg-transparent focus:bg-transparent hover:text-white focus:text-white sm:absolute sm:top-1/2 sm:end-1 sm:-translate-y-1/2 transition-all duration-300"
                            >
                                {t('get started')}
                            </button>
                        </form>
                    </div>
                </div>

                {/* Footer Links Section */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 py-6 border-b border-gray-600">
                    {/* Quick Links */}
                    <div>
                        <h3 className="text-lg font-semibold mb-4 text-white">{t('Quick Links')}</h3>
                        <ul className="space-y-2">
                            {quickLinks.map((link) => (
                                <li key={link.route}>
                                    <Link href={route(link.route, { userSlug })} className="text-white hover:text-[#674B2F] transition duration-300">
                                        {link.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Pages */}
                    <div>
                        <h3 className="text-lg font-semibold mb-4 text-white">{t('Pages')}</h3>
                        <ul className="space-y-2">
                            <li>
                                <Link href={route('photo-studio-management.frontend.index', { userSlug })} className="text-white hover:text-[#674B2F] transition duration-300">
                                    {t('Home')}
                                </Link>
                            </li>
                            <li>
                                <Link href={route('photo-studio-management.frontend.appointment', { userSlug })} className="text-white hover:text-[#674B2F] transition duration-300">
                                    {t('Appointment')}
                                </Link>
                            </li>
                            {customPages && customPages.map((page) => (
                                <li key={page.id}>
                                    <Link
                                        href={route('photo-studio-management.frontend.custom-page', { userSlug, slug: page.slug })}
                                        className="text-white hover:text-[#674B2F] transition duration-300"
                                    >
                                        {page.title}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Contact Info */}
                    {(footerSection.phone_no || footerSection.email || footerSection.location) && (
                        <div>
                            <h3 className="text-lg font-semibold mb-4 text-white">{t('Contact Info')}</h3>
                            <ul className="space-y-2">
                                {footerSection.phone_no && (
                                    <li className="flex items-start gap-3">
                                        <SocialLinks icon={footerSection.phone_icon || 'Phone'} className="w-4 h-4 mt-1 shrink-0" />
                                        <a href={`tel:${footerSection.phone_no}`} className="text-white hover:text-[#674B2F] transition duration-300">
                                            {footerSection.phone_no}
                                        </a>
                                    </li>
                                )}
                                {footerSection.email && (
                                    <li className="flex items-start gap-3">
                                        <SocialLinks icon={footerSection.email_icon || 'Mail'} className="w-4 h-4 mt-1 shrink-0" />
                                        <a href={`mailto:${footerSection.email}`} className="text-white hover:text-[#674B2F] transition duration-300">
                                            {footerSection.email}
                                        </a>
                                    </li>
                                )}
                                {footerSection.location && (
                                    <li className="flex items-start gap-3">
                                        <SocialLinks icon={footerSection.location_icon || 'MapPin'} className="w-4 h-4 mt-1 shrink-0" />
                                        <span className="text-white">{footerSection.location}</span>
                                    </li>
                                )}
                            </ul>
                        </div>
                    )}
                </div>

                {/* Footer Bottom */}
                <div className="md:py-8 py-6 flex md:flex-row flex-col items-center justify-between gap-3">
                    {/* Social Links */}
                    {footerSection.social_links && footerSection.social_links.length > 0 && (
                        <div className="md:flex-1">
                            <div className="flex md:justify-start justify-center">
                                <SocialLinks
                                    socialLinks={footerSection.social_links.map((link: any) => ({
                                        platform: link.platform || 'social',
                                        icon: link.social_icon || link.icon || 'Globe',
                                        url: link.social_link || link.url || '#',
                                        enabled: link.enabled !== false,
                                    }))}
                                    variant="light"
                                    size="sm"
                                    className="text-white hover:text-[#674B2F] transition duration-300"
                                />
                            </div>
                        </div>
                    )}

                    {/* Footer Logo */}
                    <div className="footer-logo lg:max-w-[150px] max-w-[120px] mx-auto w-full">
                        <Link href={route('photo-studio-management.frontend.index', { userSlug })}>
                            <img src={logoUrl} alt={t('photo studio logo')} loading="lazy" />
                        </Link>
                    </div>

                    {/* Copyright */}
                    {brandSettings.footer_text && (
                        <div className="md:flex-1 md:text-end text-center">
                            <p>{new Date().getFullYear()} {brandSettings.footer_text}</p>
                        </div>
                    )}
                </div>
            </div>
        </footer>
    );
};

export default Footer;
