import React from 'react';
import { Link } from '@inertiajs/react';
import SocialLinks from '@/components/SocialLinks';
import { MapPin, Phone, Mail } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';
import { Image } from './Image';

interface FooterProps {
    brandSettings: {
        footer_logo?: string;
        site_title?: string;
        footer_description?: string;
        footer_copyright?: string;
        footer_contact_title?: string;
        footer_address?: string;
        footer_phone?: string;
        footer_email?: string;
        footer_hours?: string;
        userSlug?: string;
    };
    primaryColor: string;
    secondaryColor: string;
    userSlug?: string;
    socialLinks?: Array<{
        name: string;
        icon: string;
        link?: string;
    }>;
    footerServices?: Array<{
        id: number;
        name: string;
    }>;
}

export default function Footer({ 
    brandSettings, 
    primaryColor, 
    secondaryColor, 
    userSlug, 
    socialLinks = [],
    footerServices = []
}: FooterProps) {
    return (
        <footer className="text-white" style={{ backgroundColor: primaryColor }}>
            <div className="container mx-auto px-4">
                <div className="sm:py-12 py-6 grid grid-cols-1 lg:grid-cols-4 md:grid-cols-2 lg:gap-8 gap-6">
                    <div>
                        <Link href={userSlug ? route('booking.home', { userSlug }) : '#'} className="inline-block md:mb-5 mb-4">
                            <Image
                                src={brandSettings?.footer_logo ? getImagePath(brandSettings.footer_logo) : getImagePath('packages/workdo/Bookings/src/assets/images/footer-logo.png')}
                                alt={brandSettings.site_title || 'Service Bookings Addon'}
                                className="h-10 rounded"
                            />
                        </Link>
                        <p className="text-white mb-6">
                            {brandSettings?.footer_description || 'Streamline your service booking process with our powerful, customizable booking addon solution. Perfect for businesses of all sizes.'}
                        </p>
                        <SocialLinks socialLinks={socialLinks} variant="light" style={{ color: primaryColor, backgroundColor: secondaryColor }} />
                    </div>
                    
                    <div>
                        <h3 className="text-xl font-bold sm:mb-6 mb-4">Quick Links</h3>
                        <ul className="space-y-3">
                            <li>
                                <Link href={userSlug ? route('booking.home', { userSlug }) : '#'} className="text-white border-b-2 border-transparent hover:border-white transition inline-flex items-center">
                                    <svg className="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" /></svg>
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link href={userSlug ? route('booking.services', { userSlug }) : '#'} className="text-white border-b-2 border-transparent hover:border-white transition inline-flex items-center">
                                    <svg className="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" /></svg>
                                    Services
                                </Link>
                            </li>
                            <li>
                                <Link href={userSlug ? route('booking.contact', { userSlug }) : '#'} className="text-white border-b-2 border-transparent hover:border-white transition inline-flex items-center">
                                    <svg className="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" /></svg>
                                    Contact Us
                                </Link>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 className="text-xl font-bold sm:mb-6 mb-4">Our Services</h3>
                        <ul className="space-y-3">
                            {footerServices.length > 0 ? (
                                footerServices.map((service) => (
                                    <li key={service.id}>
                                        <Link 
                                            href={userSlug ? route('booking.services.detail', { userSlug, id: service.id }) : '#'}
                                            className="text-white border-b-2 border-transparent hover:border-white transition inline-flex items-center"
                                        >
                                            <svg className="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" /></svg>
                                            {service.name}
                                        </Link>
                                    </li>
                                ))
                            ) : (
                                <li className="text-white">No services available</li>
                            )}
                        </ul>
                    </div>
                    
                    <div>
                        <h3 className="text-xl font-bold sm:mb-6 mb-4">{brandSettings?.footer_contact_title || 'Contact Information'}</h3>
                        <ul className="space-y-4">
                            {brandSettings?.footer_address && (
                                <li className="flex items-start">
                                    <MapPin className="w-4 h-4 mt-1 mr-3 text-white flex-shrink-0" />
                                    <span>{brandSettings.footer_address}</span>
                                </li>
                            )}
                            {brandSettings?.footer_phone && (
                                <li className="flex items-center">
                                    <Phone className="w-4 h-4 mr-3 text-white flex-shrink-0" />
                                    <a href={`tel:${brandSettings.footer_phone}`}>{brandSettings.footer_phone}</a>
                                </li>
                            )}
                            {brandSettings?.footer_email && (
                                <li className="flex items-center">
                                    <Mail className="w-4 h-4 mr-3 text-white flex-shrink-0" />
                                    <a href={`mailto:${brandSettings.footer_email}`}>{brandSettings.footer_email}</a>
                                </li>
                            )}
                            {brandSettings?.footer_hours && (
                                <li className="flex items-center">
                                    <svg className="w-4 h-4 mr-3 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" /></svg>
                                    <span>{brandSettings.footer_hours}</span>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
                <div className="md:py-6 py-4 border-t border-white text-center">
                    <p className="text-white"> &copy; {new Date().getFullYear()} {brandSettings?.footer_copyright || '© 2025 Booking Addon. All rights reserved.'}</p>
                </div>
            </div>
        </footer>
    );
}
