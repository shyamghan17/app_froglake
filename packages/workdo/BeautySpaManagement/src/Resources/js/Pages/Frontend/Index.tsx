import React, { useEffect } from 'react';
import Layout from './Layout';
import { usePage, Link } from '@inertiajs/react';
import { getImagePath, formatCurrency } from '@/utils/helpers';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import * as LucideIcons from 'lucide-react';
import { Star, Quote } from 'lucide-react';
import SocialLinks from '@/components/SocialLinks';

interface Props {
    title?: string;
    services?: any[];
    totalBookings?: number;
    totalClients?: number;
    features?: any[];
    offers?: any[];
    testimonials?: any[];
}

export default function Index({ title = 'Home', services = [], totalBookings = 0, totalClients = 0, offers = [] }: Props) {
    const { t } = useTranslation();
    const pageProps = usePage().props as any;
    const { beautySpaSettings } = pageProps;
    const { url } = usePage();

    const bannerSection = beautySpaSettings?.banner_section || {};
    const featureSection = beautySpaSettings?.feature_section || {};
    const testimonialsSection = beautySpaSettings?.testimonials || {};
    const pathParts = url.split('/');
    const beautyIndex = pathParts.findIndex(part => part === 'beauty-spa');
    const userSlug = beautyIndex !== -1 && pathParts[beautyIndex - 1] ? pathParts[beautyIndex - 1] : null;


    const servicesCount = services.length;
    const bookingsCount = totalBookings;
    const clientsCount = totalClients;


    const hasAnyContent = (bannerSection.heading || bannerSection.description || bannerSection.image) || (services && services.length > 0) || (featureSection.features && featureSection.features.length > 0) || (offers && offers.length > 0) || (testimonialsSection?.testimonials && testimonialsSection?.testimonials.length > 0);
    return (
        <Layout title={title}>
            <main className="pt-20">
                {!hasAnyContent ? (
                    <div className="flex flex-col items-center justify-center">
                        <svg className="w-64 h-64 text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M6 7l8.5 8.5M6 17l8.5-8.5" />
                            <circle cx="4" cy="7" r="2" strokeWidth={1} />
                            <circle cx="4" cy="17" r="2" strokeWidth={1} />
                        </svg>
                        <h2 className="text-2xl font-bold text-gray-600 mb-4">{t('No Content Available')}</h2>
                        <p className="text-gray-500 text-center max-w-md mb-5">{t('There is no content configured for this page. Please contact the administrator to set up the page content.')}</p>
                    </div>
                ) : (
                    <>
                        {/* Hero Section */}
                        {(bannerSection.heading || bannerSection.description || bannerSection.image) && (
                            <section id="home" className="relative overflow-hidden bg-gradient-to-r from-[#df98962b] to-white py-16 lg:py-20 -mt-4">
                                <div className="absolute top-0 start-0 w-48 h-48 bg-[#df9896] opacity-10 rounded-full -translate-x-24 -translate-y-12"></div>
                                <div className="absolute top-1/3 end-0 w-64 h-64 bg-[#df9896] opacity-10 rounded-full translate-x-20"></div>
                                <div className="absolute bottom-0 start-1/4 w-96 h-96 bg-[#df9896] opacity-10 rounded-full translate-y-40"></div>

                                <div className="container mx-auto px-4 h-full flex items-center">
                                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                                        <div className="order-1 text-center lg:text-left z-10">
                                            <p className="text-[#df9896] font-medium mb-4 uppercase tracking-wider">{bannerSection.heading}</p>
                                            <h2 className="text-4xl md:text-5xl lg:text-6xl font-bold md:mb-6 mb-4 leading-tight">
                                                <span className="text-gray-800">{bannerSection.title}</span><br className="hidden md:block" />
                                            </h2>
                                            <p className="text-gray-600 md:text-lg text-[16px] sm:mb-8 mb-6 max-w-xl mx-auto lg:mx-0">
                                                {bannerSection.description}
                                            </p>
                                            <div className="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                                                <a href={route('beauty-spa.booking', { userSlug })} className="bg-[#df9896] border border-[#df9896] hover:bg-transparent hover:text-[#df9896] text-white font-medium py-3 px-8 rounded-full inline-block transition-colors">
                                                    {'Book Appointment'}
                                                </a>
                                                <a href="#services" className="border border-[#df9896] text-[#df9896] hover:bg-[#df9896] hover:text-white font-medium py-3 px-8 rounded-full inline-block transition-colors">
                                                    {'Explore Services'}
                                                </a>
                                            </div>

                                            <div className="grid grid-cols-3 gap-4 md:mt-12 mt-8 max-w-md mx-auto lg:mx-0">
                                                <div className="text-center">
                                                    <p className="sm:text-3xl text-2xl font-bold text-[#df9896]">{bannerSection.stat_1_number || (bookingsCount > 0 ? `${bookingsCount}+` : '0')}</p>
                                                    <p className="text-gray-600 text-sm">{'Total Bookings'}</p>
                                                </div>
                                                <div className="text-center">
                                                    <p className="sm:text-3xl text-2xl font-bold text-[#df9896]">{bannerSection.stat_2_number || (clientsCount > 0 ? `${clientsCount}+` : '0')}</p>
                                                    <p className="text-gray-600 text-sm">{'Happy Clients'}</p>
                                                </div>
                                                <div className="text-center">
                                                    <p className="sm:text-3xl text-2xl font-bold text-[#df9896]">{bannerSection.stat_3_number || (servicesCount > 0 ? `${servicesCount}+` : '0')}</p>
                                                    <p className="text-gray-600 text-sm">{'Premium Services'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="order-1 lg:order-2 relative z-10">
                                            <div className="relative">
                                                <img
                                                    src={bannerSection.image ? getImagePath(bannerSection.image, pageProps) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/banner-image.png', pageProps)}
                                                    alt={bannerSection.image_alt || "Beauty Treatment"}
                                                    className="w-full h-auto rounded-lg shadow-xl object-cover max-h-[500px]"
                                                />
                                                <div className="absolute -top-4 -end-4 sm:w-64 sm:h-64 w-40 h-40 border-4 border-[#df9896] rounded-lg opacity-30 z-0"></div>
                                                <div className="absolute -bottom-6 -start-6 w-20 h-20 bg-[#df9896] rounded-full opacity-20"></div>
                                                <div className="absolute top-1/4 -end-3 w-10 h-10 bg-[#df9896] rounded-full opacity-40"></div>
                                                <div className="absolute bottom-6 start-1/2 transform -translate-x-1/2 bg-white bg-opacity-90 py-3 px-6 rounded-full shadow-md text-center">
                                                    <p className="text-[#df9896] font-medium">{bannerSection.badge_text || 'Experience Luxury Beauty'}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        )}
                        {/* Services Section */}
                        {(services && services.length > 0) && (
                            <section id="services" className="lg:py-20 py-12">
                                <div className="container mx-auto px-4">
                                    <div className="text-center lg:mb-12 sm:mb-8 mb-6">
                                        <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">
                                            {beautySpaSettings?.home_section?.services_title}
                                        </h2>
                                        <p className="md:text-lg text-[16px] text-gray-800">
                                            {beautySpaSettings?.home_section?.services_description}
                                        </p>
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        {services.length > 0 && services.slice(0, 6).map(service => (
                                            <div key={service.id} className="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow h-full flex flex-col">
                                                <Link href={route('beauty-spa.service.detail', { userSlug, service: service.id })}>
                                                    <img
                                                        src={service.service_image ? getImagePath(service.service_image, pageProps) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/default.png', pageProps)}
                                                        alt={service.name}
                                                        className="w-full h-64 object-cover"
                                                    />
                                                </Link>
                                                <div className="sm:p-6 p-4 h-full flex flex-col justify-between">
                                                    <div>
                                                        <h3 className="text-xl font-bold mb-2 text-[#df9896]">{service.name}</h3>
                                                        <p className="text-gray-800 mb-4">{service.description.length > 120 ? service.description.substring(0, 120) + '...' : service.description}</p>
                                                

                                                    </div>
                                                    <div className="flex justify-between items-center">
                                                        <span className="text-[#df9896] font-bold">{t('From')} {formatCurrency(service.price, pageProps)}</span>
                                                        <a href={route('beauty-spa.booking', { userSlug, service: service.id })} className="text-[#df9896]">{t('Book Now')}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </section>
                        )}

                        {/* Why Choose Us Section */}
                        {(featureSection.features && featureSection.features.length > 0) && (
                            <section className="lg:py-20 py-12 bg-[#F5F5F5] relative overflow-hidden">
                                <div className="container mx-auto px-4 relative z-10">
                                    <div className="text-center lg:mb-12 sm:mb-8 mb-6">
                                        <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">
                                            {beautySpaSettings?.feature_section?.why_choose_us_title}
                                        </h2>
                                        <p className="md:text-lg text-[16px] text-gray-800">
                                            {beautySpaSettings?.feature_section?.why_choose_us_description}
                                        </p>
                                    </div>

                                    <div className={`grid grid-cols-1 md:grid-cols-2 ${featureSection.features && featureSection.features.length > 0 ? (featureSection.features.length >= 4 ? 'lg:grid-cols-4' : featureSection.features.length === 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-2') : 'lg:grid-cols-4'} lg:gap-8 gap-6`}>
                                        {(featureSection.features && featureSection.features.length > 0) && featureSection.features.map((feature, index) => (
                                            <div key={index} className="group text-center bg-white rounded-lg lg:p-6 p-4 border border-gray-200">
                                                <div className="bg-gradient-to-br from-[#df9896] to-[#df9896] rounded-full md:w-20 md:h-20 w-16 h-16 flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                                    {feature.icon ? (
                                                        <SocialLinks 
                                                            icon={feature.icon}
                                                            className="w-6 h-6"
                                                            style={{ color: 'white' }}
                                                        />
                                                    ) : (
                                                        <span className="text-white text-2xl">★</span>
                                                    )}
                                                </div>
                                                <h3 className="text-xl font-bold mb-3 text-gray-800 group-hover:text-[#df9896] transition-colors">{feature.title}</h3>
                                                <p className="text-gray-800 leading-relaxed">{feature.description}</p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </section>
                        )}
                        {/* Promotional Offers - Only show if offers exist */}
                        {(offers && offers.length > 0) && (
                            <section id="offers" className="relative overflow-hidden py-12 lg:py-20">
                                <div className="container mx-auto px-4 relative z-10">
                                    <div className="text-center lg:mb-12 sm:mb-8 mb-6">
                                        <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">
                                            {beautySpaSettings?.home_section?.offers_title || t('Special Promotional Offers')}
                                        </h2>
                                        <p className="md:text-lg text-[16px] text-gray-800">
                                            {beautySpaSettings?.home_section?.offers_description || t('Limited-time deals you don\'t want to miss')}
                                        </p>
                                    </div>

                                    <div className={`grid grid-cols-1 ${offers.length === 1 ? '' : 'md:grid-cols-2'} gap-8`}>
                                        {offers.map((offer, index) => (
                                            <div key={index} className="bg-white rounded-lg overflow-hidden shadow-md border border-[#df9896] h-full flex flex-col">
                                                <div className="bg-[#df9896] text-white py-2 px-4 text-center font-bold">{('SPECIAL OFFER')}</div>
                                                <div className="sm:p-6 p-4 h-full flex flex-col justify-between">
                                                    <div className="flex flex-col flex-1 h-full">
                                                        <h3 className="text-2xl font-bold mb-2 text-[#df9896]">{offer.title}</h3>
                                                        <p className="text-gray-800 mb-4">{offer.description}</p>
                                                        <div className="flex items-center mb-4">
                                                            <span className="text-3xl font-bold text-[#df9896] me-3">{formatCurrency(offer.offer_price, pageProps)}</span>
                                                            <span className="text-gray-500 line-through">{formatCurrency(offer.price, pageProps)}</span>
                                                            <span className="ms-auto bg-[#df9896] text-white py-1 px-3 rounded-full text-sm">{offer.discount}% OFF</span>
                                                        </div>
                                                    </div>
                                                    <a href={route('beauty-spa.booking', { userSlug, service: offer.beauty_service_id })} className="block w-full bg-[#df9896] border border-[#df9896] hover:bg-transparent hover:text-[#df9896] text-white text-center font-bold py-3 px-4 rounded-full transition-colors">
                                                        {t('Book This Offer')}
                                                    </a>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </section>
                        )}
                        {/* Testimonials Section */}
                        {(testimonialsSection?.testimonials && testimonialsSection?.testimonials.length > 0) && (
                            <section className="lg:py-20 py-12 bg-[#F5F5F5] relative overflow-hidden">
                                <div className="container mx-auto px-4 relative z-10">
                                    <div className="text-center lg:mb-12 sm:mb-8 mb-6">
                                        <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">
                                            {testimonialsSection?.title || t('What Our Clients Say')}
                                        </h2>
                                        <p className="md:text-lg text-[16px] text-gray-800">
                                            {testimonialsSection?.description || t('Hear from our satisfied customers about their experience')}
                                        </p>
                                    </div>

                                    <div className={`grid grid-cols-1 ${testimonialsSection.testimonials?.length === 1 ? '' : 'md:grid-cols-2'} ${testimonialsSection.testimonials?.length >= 3 ? 'lg:grid-cols-3' : ''} gap-6`}>
                                        {testimonialsSection.testimonials?.map((testimonial, index) => (
                                            <div key={index} className="group text-center bg-white rounded-lg lg:p-6 p-4 border border-gray-200 h-full flex flex-col">
                                                <div className="bg-gradient-to-br from-[#df9896] to-[#df9896] rounded-full md:w-20 md:h-20 w-16 h-16 flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                                    <Quote size={24} className="text-white" />
                                                </div>
                                                <div className="flex-1">
                                                    <p className="text-gray-800 leading-relaxed mb-4 italic">
                                                        {testimonial.comment || ''}
                                                    </p>
                                                    <div className="flex justify-center mb-3">
                                                        {[1, 2, 3, 4, 5].map((star) => (
                                                            <Star key={star} size={16} className={`${star <= (testimonial.rating || 0) ? 'text-yellow-400 fill-current' : 'text-gray-300'
                                                                }`} />
                                                        ))}
                                                    </div>
                                                    <h4 className="font-bold text-[#df9896] group-hover:text-[#df9896] transition-colors">
                                                        {testimonial.customer_name || ''}
                                                    </h4>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </section>
                        )}
                    </>
                )}
            </main>
        </Layout>
    );
}