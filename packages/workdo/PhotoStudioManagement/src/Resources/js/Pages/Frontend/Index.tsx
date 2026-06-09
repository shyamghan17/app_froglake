import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import Layout from '../../Components/Frontend/Layout';
import { getImagePath } from '@/utils/helpers';
import SocialLinks from '@/components/SocialLinks';

const Index = () => {
    const { photoStudioSettings, userSlug, services: servicesData, cameraKits: cameraKitsData } = usePage<{ photoStudioSettings?: any; userSlug?: string; services?: any[]; cameraKits?: any[] }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();

    const [heroSlide, setHeroSlide] = useState(0);
    const [serviceSlide, setServiceSlide] = useState(0);
    const [cameraSlide, setCameraSlide] = useState(0);
    const [testimonialSlide, setTestimonialSlide] = useState(0);
    const [gallerySlide, setGallerySlide] = useState(0);
    const [isLargeScreen, setIsLargeScreen] = useState(false);

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const bannerSection = photoStudioSettings?.banner_section || {};
    const aboutSection = photoStudioSettings?.about_section || {};
    const titleSection = photoStudioSettings?.title_section || {};
    const testimonialsData = photoStudioSettings?.testimonials || {};
    const gallerySection = photoStudioSettings?.gallery_section || {};

    const heroSlides = bannerSection.banners && bannerSection.banners.length > 0
        ? bannerSection.banners.map((banner: any) => ({
            title: banner.title || '',
            sub_title: banner.sub_title || '',
            description: banner.description || '',
            image: banner.image ? getImagePath(banner.image) : img('hero-banner1.png'),
            cta: banner.cta_text || 'view portfolio',
            ctaLink: banner.cta_link || route('photo-studio-management.frontend.portfolio', { userSlug: slug }),
        }))
        : [
            { title: t("Capturing Life's Perfect Moments"), sub_title: t('Your Story, Our Lens'), description: t('Professional photography studio specializing in portraits, weddings, commercial, and creative shoots.'), image: img('hero-banner1.png'), cta: t('view portfolio'), ctaLink: route('photo-studio-management.frontend.portfolio', { userSlug: slug }) },
            { title: t('Your Love Story, Beautifully Captured'), sub_title: t('Award-Winning Wedding Photography'), description: t('From engagement to "I do," our expert team preserves every magical moment with artistry and care.'), image: img('hero-banner2.png'), cta: t('wedding services'), ctaLink: route('photo-studio-management.frontend.services', { userSlug: slug }) },
            { title: t('Elevate Your Brand With Stunning Visuals'), sub_title: t('Creative Commercial Shoots'), description: t('We help businesses stand out with high-impact product, lifestyle, and branding photography.'), image: img('hero-banner3.png'), cta: t('commercial photography'), ctaLink: route('photo-studio-management.frontend.services', { userSlug: slug }) },
        ];

    const galleryImages = gallerySection.images && gallerySection.images.length > 0
        ? gallerySection.images.map((item: any) => item.image ? getImagePath(item.image) : '')
        : [];

    const testimonials = testimonialsData.testimonials && testimonialsData.testimonials.length > 0
        ? testimonialsData.testimonials
        : [];

    useEffect(() => {
        const handleResize = () => {
            setIsLargeScreen(window.innerWidth >= 1024);
        };
        
        handleResize();
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    useEffect(() => {
        const timer = setInterval(() => setHeroSlide((prev) => (prev + 1) % heroSlides.length), 5000);
        return () => clearInterval(timer);
    }, [heroSlides.length]);

    useEffect(() => {
        if (testimonials.length === 0) return;
        const timer = setInterval(() => setTestimonialSlide((prev) => (prev + 1) % testimonials.length), 5000);
        return () => clearInterval(timer);
    }, [testimonials.length]);

    const handleServiceNext = () => { const max = Math.max(0, defaultServices.length - 3); setServiceSlide((p) => (p === max ? 0 : p + 1)); };
    const handleServicePrev = () => { const max = Math.max(0, defaultServices.length - 3); setServiceSlide((p) => (p === 0 ? max : p - 1)); };
    const handleCameraNext = () => { const max = Math.max(0, defaultCameraKit.length - 4); setCameraSlide((p) => (p === max ? 0 : p + 1)); };
    const handleCameraPrev = () => { const max = Math.max(0, defaultCameraKit.length - 4); setCameraSlide((p) => (p === 0 ? max : p - 1)); };
    const handleGalleryNext = () => { const max = Math.max(0, galleryImages.length - 3); setGallerySlide((p) => (p === max ? 0 : p + 1)); };
    const handleGalleryPrev = () => { const max = Math.max(0, galleryImages.length - 3); setGallerySlide((p) => (p === 0 ? max : p - 1)); };

    const defaultServices = servicesData || [];
    const defaultCameraKit = cameraKitsData || [];

    const hasAnyContent =
        (bannerSection.banners && bannerSection.banners.length > 0) ||
        (aboutSection.title) ||
        (defaultServices.length > 0) ||
        (defaultCameraKit.length > 0) ||
        (testimonials.length > 0) ||
        (galleryImages.length > 0);

    return (
        <Layout title={t('Home')}>
            {/* Hero Banner */}
            <section className="hero-banner-section">
                <div className="relative w-full overflow-hidden" style={{ minHeight: '650px' }}>
                    {heroSlides.map((slide: any, index: number) => (
                        <div key={index} className={`absolute inset-0 w-full h-full transition-opacity duration-1000 ${index === heroSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'}`}>
                            <div className="hero-image h-full relative z-[1] xl:pt-32 xl:pb-40 lg:pt-24 lg:pb-26 pt-10 pb-12 flex items-center">
                                <img src={slide.image} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-[20%]" alt="hero banner" />
                                <div className="md:container w-full mx-auto px-4">
                                    <div className="lg:max-w-2xl max-w-md md:mx-0 mx-auto mb-8 lg:mb-0 md:text-start text-center">
                                        <span className="inline-block capitalize mb-2 text-primary lg:text-lg text-[#674B2F] font-medium">{slide.sub_title}</span>
                                        <h2 className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl lg:mb-6 mb-3 capitalize font-medium">{slide.title}</h2>
                                        <p className="max-w-2xl lg:text-lg lg:mb-8 mb-5 md:mx-0 mx-auto font-medium">{slide.description}</p>
                                        <Link href={route('photo-studio-management.frontend.portfolio', { userSlug: slug })} className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">
                                            {t('View Portfolio')}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                    {/* Pagination */}
                    <div className="absolute bottom-10 lg:bottom-14 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                        {heroSlides.map((_, index) => (
                            <button
                                key={index}
                                onClick={() => setHeroSlide(index)}
                                className={`transition-all ${index === heroSlide ? 'bg-primary w-8 h-3 rounded-full' : 'bg-[#674B2F]/50 w-3 h-3 rounded-full'
                                    }`}
                            />
                        ))}
                    </div>

                </div>
            </section>

            {!hasAnyContent ? (
                <div className="flex flex-col items-center justify-center min-h-screen py-16">
                    <svg className="w-24 h-24 text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 className="text-xl font-medium text-gray-500 mb-4">{t('No Content Available')}</h2>
                    <p className="text-gray-400 text-center max-w-md">{t('There is no content configured for this page. Please contact the administrator to set up the page content.')}</p>
                </div>
            ) : (
                <>
                    {/* About Section */}
                    {aboutSection.title && (
                        <section className="lg:py-16 py-10">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="grid grid-cols-1 lg:grid-cols-2 xl:gap-20 lg:gap-10 gap-6 items-center">
                                    <div>
                                        <span className="inline-block capitalize mb-2 text-primary lg:text-lg text-[#674B2F]">{aboutSection.sub_title}</span>
                                        <h2 className="text-2xl capitalize sm:text-3xl md:text-4xl mb-4 font-medium">{aboutSection.title}</h2>
                                        <p className="text-gray-700 mb-4 xl:text-lg font-medium" dangerouslySetInnerHTML={{ __html: aboutSection.content }} />
                                        <p className="text-gray-700 mb-5 text-sm max-w-lg">
                                            {aboutSection.description}
                                        </p>
                                        {aboutSection.tips && aboutSection.tips.length > 0 && (
                                            <ul className="list-disc ps-4 space-y-1 font-medium">
                                                {aboutSection.tips.map((tip: any, idx: number) => (
                                                    <li key={idx} dangerouslySetInnerHTML={{ __html: tip.description }} />
                                                ))}
                                            </ul>
                                        )}
                                    </div>
                                    <div className="xl:ms-16 lg:ms-10">
                                        <img
                                            src={aboutSection.about_us_image ? getImagePath(aboutSection.about_us_image) : img('about-image.png')}
                                            alt="about image"
                                            className="w-full object-cover"
                                        />
                                    </div>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Services Section */}
                    {defaultServices.length > 0 && (
                        <section className="lg:py-16 py-10 bg-[#674B2F]/5">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-primary lg:text-lg text-[#674B2F] font-medium">{titleSection.services_label || t('Our Services')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{titleSection.services_title || t('Professional Photography Services')}</h2>
                                </div>
                                <div className="relative lg:!pb-8 !pb-6">
                                    {defaultServices.length === 1 ? (
                                        <div className="flex justify-center">
                                            <div className="w-full max-w-md px-2">
                                                {(() => {
                                                    const service = defaultServices[0];
                                                    return (
                                                        <div className="bg-white flex flex-col h-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group relative border">
                                                            <div className="relative h-56 overflow-hidden">
                                                                <img src={service.image ? getImagePath(service.image) : ''} className="w-full h-full object-cover transition-all duration-500 group-hover:scale-110" alt="service" />
                                                                <span className="bg-white text-black absolute z-[1] top-4 end-4 px-3 py-1 text-sm font-bold shadow-lg">{t('From')} {service.price}</span>
                                                                <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60 group-hover:opacity-70 transition-opacity duration-300"></div>
                                                                <div className="absolute bottom-5 start-0 w-full lg:px-6 px-4"><h3 className="text-2xl font-bold text-white mb-0">{service.name}</h3></div>
                                                            </div>
                                                            <div className="flex-1 flex flex-col lg:p-6 p-4 relative">
                                                                <div className="flex-1">
                                                                    <div className="flex flex-wrap gap-2 mb-4">{(service.category_names || []).map((tag: any, idx: number) => <span key={idx} className="font-medium text-xs bg-gray-200 text-gray-800 px-2 py-1">{tag}</span>)}</div>
                                                                    <p className="text-gray-600 mb-5 font-medium line-clamp-3">{service.description}</p>
                                                                </div>
                                                                <Link href={route('photo-studio-management.frontend.appointment', { userSlug: slug })} className="inline-flex items-center justify-center w-full gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">{t('Book Now')}</Link>
                                                            </div>
                                                        </div>
                                                    );
                                                })()}
                                            </div>
                                        </div>
                                    ) : defaultServices.length === 2 ? (
                                        <div className="flex justify-center gap-4">
                                            {defaultServices.map((service: any, index: number) => (
                                                <div key={index} className="w-full max-w-md">
                                                    <div className="bg-white flex flex-col h-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group relative border">
                                                        <div className="relative h-56 overflow-hidden">
                                                            <img src={service.image ? getImagePath(service.image) : ''} className="w-full h-full object-cover transition-all duration-500 group-hover:scale-110" alt="service" />
                                                            <span className="bg-white text-black absolute z-[1] top-4 end-4 px-3 py-1 text-sm font-bold shadow-lg">{t('From')} {service.price}</span>
                                                            <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60 group-hover:opacity-70 transition-opacity duration-300"></div>
                                                            <div className="absolute bottom-5 start-0 w-full lg:px-6 px-4"><h3 className="text-2xl font-bold text-white mb-0">{service.name}</h3></div>
                                                        </div>
                                                        <div className="flex-1 flex flex-col lg:p-6 p-4 relative">
                                                            <div className="flex-1">
                                                                <div className="flex flex-wrap gap-2 mb-4">{(service.category_names || []).map((tag: any, idx: number) => <span key={idx} className="font-medium text-xs bg-gray-200 text-gray-800 px-2 py-1">{tag}</span>)}</div>
                                                                <p className="text-gray-600 mb-5 font-medium line-clamp-3">{service.description}</p>
                                                            </div>
                                                            <Link href={route('photo-studio-management.frontend.appointment', { userSlug: slug })} className="inline-flex items-center justify-center w-full gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">{t('Book Now')}</Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="overflow-hidden">
                                            <div className="flex transition-transform duration-500 ease-in-out lg:flex-nowrap flex-wrap lg:gap-0 gap-4" style={{ transform: isLargeScreen ? `translateX(-${serviceSlide * (100 / 3)}%)` : 'none' }}>
                                                {defaultServices.map((service: any, index: number) => (
                                                    <div key={index} className="lg:w-1/3 w-full lg:flex-shrink-0 px-2">
                                                        <div className="bg-white flex flex-col h-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group relative border">
                                                            <div className="relative h-56 overflow-hidden">
                                                                <img src={service.image ? getImagePath(service.image) : ''} className="w-full h-full object-cover transition-all duration-500 group-hover:scale-110" alt="service" />
                                                                <span className="bg-white text-black absolute z-[1] top-4 end-4 px-3 py-1 text-sm font-bold shadow-lg">{t('From')} {service.price}</span>
                                                                <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60 group-hover:opacity-70 transition-opacity duration-300"></div>
                                                                <div className="absolute bottom-5 start-0 w-full lg:px-6 px-4"><h3 className="text-2xl font-bold text-white mb-0">{service.name}</h3></div>
                                                            </div>
                                                            <div className="flex-1 flex flex-col lg:p-6 p-4 relative">
                                                                <div className="flex-1">
                                                                    <div className="flex flex-wrap gap-2 mb-4">{(service.category_names || []).map((tag: any, idx: number) => <span key={idx} className="font-medium text-xs bg-gray-200 text-gray-800 px-2 py-1">{tag}</span>)}</div>
                                                                    <p className="text-gray-600 mb-5 font-medium line-clamp-3">{service.description}</p>
                                                                </div>
                                                                <Link href={route('photo-studio-management.frontend.appointment', { userSlug: slug })} className="inline-flex items-center justify-center w-full gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">{t('Book Now')}</Link>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    {defaultServices.length > 3 && (
                                        <>
                                            <button onClick={handleServicePrev} className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">‹</button>
                                            <button onClick={handleServiceNext} className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">›</button>
                                        </>
                                    )}
                                </div>
                                <div className="text-center mt-8">
                                    <Link href={route('photo-studio-management.frontend.services', { userSlug: slug })} className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-black hover:bg-transparent text-white hover:text-black border border-black hover:border-black transition-all duration-300 capitalize font-medium">
                                        {t('All services')} <SocialLinks icon="ArrowRight" className="w-4 h-4 rtl:scale-x-[-1]" />
                                    </Link>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Camera Kit Section */}
                    {defaultCameraKit.length > 0 && (
                        <section className="lg:py-20 py-12">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-primary lg:text-lg font-medium text-[#674B2F]">{titleSection.equipment_label || t('Professional Equipment')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{titleSection.equipment_title || t('Our Camera Kit')}</h2>
                                </div>
                                <div className="relative lg:!pb-8 !pb-6">
                                    {defaultCameraKit.length === 1 ? (
                                        <div className="flex justify-center">
                                            <div className="w-full max-w-xs px-2">
                                                {(() => {
                                                    const kit = defaultCameraKit[0];
                                                    return (
                                                        <div className="bg-white flex flex-col h-full border shadow-md hover:shadow-lg transition-all duration-300 group">
                                                            <div className="relative h-48 overflow-hidden"><img src={kit.image ? getImagePath(kit.image) : ''} alt={kit.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                                            <div className="flex-1 flex flex-col lg:p-6 p-4 border-t">
                                                                <div className="flex-1"><h3 className="text-xl mb-3 font-medium">{kit.name}</h3><p className="text-gray-600 mb-4 line-clamp-2 font-medium">{kit.description}</p></div>
                                                                <div className="flex flex-wrap gap-2">{(kit.tag_names || []).map((tag: any, idx: number) => <span key={idx} className="text-xs bg-gray-200 px-2 py-1 font-medium">{tag}</span>)}</div>
                                                            </div>
                                                        </div>
                                                    );
                                                })()}
                                            </div>
                                        </div>
                                    ) : defaultCameraKit.length === 2 ? (
                                        <div className="flex justify-center gap-4">
                                            {defaultCameraKit.map((kit: any, index: number) => (
                                                <div key={index} className="w-full max-w-xs">
                                                    <div className="bg-white flex flex-col h-full border shadow-md hover:shadow-lg transition-all duration-300 group">
                                                        <div className="relative h-48 overflow-hidden"><img src={kit.image ? getImagePath(kit.image) : ''} alt={kit.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                                        <div className="flex-1 flex flex-col lg:p-6 p-4 border-t">
                                                            <div className="flex-1"><h3 className="text-xl mb-3 font-medium">{kit.name}</h3><p className="text-gray-600 mb-4 line-clamp-2 font-medium">{kit.description}</p></div>
                                                            <div className="flex flex-wrap gap-2">{(kit.tag_names || []).map((tag: any, idx: number) => <span key={idx} className="text-xs bg-gray-200 px-2 py-1 font-medium">{tag}</span>)}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : defaultCameraKit.length === 3 ? (
                                        <div className="flex justify-center gap-4 flex-wrap lg:flex-nowrap">
                                            {defaultCameraKit.map((kit: any, index: number) => (
                                                <div key={index} className="lg:w-1/3 w-full max-w-xs px-2">
                                                    <div className="bg-white flex flex-col h-full border shadow-md hover:shadow-lg transition-all duration-300 group">
                                                        <div className="relative h-48 overflow-hidden"><img src={kit.image ? getImagePath(kit.image) : ''} alt={kit.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                                        <div className="flex-1 flex flex-col lg:p-6 p-4 border-t">
                                                            <div className="flex-1"><h3 className="text-xl mb-3 font-medium">{kit.name}</h3><p className="text-gray-600 mb-4 line-clamp-2 font-medium">{kit.description}</p></div>
                                                            <div className="flex flex-wrap gap-2">{(kit.tag_names || []).map((tag: any, idx: number) => <span key={idx} className="text-xs bg-gray-200 px-2 py-1 font-medium">{tag}</span>)}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="overflow-hidden">
                                            <div className="flex transition-transform duration-500 ease-in-out lg:flex-nowrap flex-wrap lg:gap-0 gap-4" style={{ transform: isLargeScreen ? `translateX(-${cameraSlide * (100 / 4)}%)` : 'none' }}>
                                                {defaultCameraKit.map((kit: any, index: number) => (
                                                    <div key={index} className="lg:w-1/4 w-full lg:flex-shrink-0 px-2">
                                                        <div className="bg-white flex flex-col h-full border shadow-md hover:shadow-lg transition-all duration-300 group">
                                                            <div className="relative h-48 overflow-hidden"><img src={kit.image ? getImagePath(kit.image) : ''} alt={kit.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                                            <div className="flex-1 flex flex-col lg:p-6 p-4 border-t">
                                                                <div className="flex-1"><h3 className="text-xl mb-3 font-medium">{kit.name}</h3><p className="text-gray-600 mb-4 line-clamp-2 font-medium">{kit.description}</p></div>
                                                                <div className="flex flex-wrap gap-2">{(kit.tag_names || []).map((tag: any, idx: number) => <span key={idx} className="text-xs bg-gray-200 px-2 py-1 font-medium">{tag}</span>)}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    {defaultCameraKit.length > 4 && (
                                        <>
                                            <button onClick={handleCameraPrev} className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">‹</button>
                                            <button onClick={handleCameraNext} className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">›</button>
                                        </>
                                    )}
                                </div>
                                <div className="text-center mt-8">
                                    <Link href={route('photo-studio-management.frontend.camera-kit', { userSlug: slug })} className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-black hover:bg-transparent text-white hover:text-black border border-black hover:border-black transition-all duration-300 capitalize font-medium">
                                        {t('View All Equipment')} <SocialLinks icon="ArrowRight" className="w-4 h-4 rtl:scale-x-[-1]" />
                                    </Link>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Testimonials Section */}
                    {testimonials.length > 0 && (
                        <section className="lg:py-20 py-10 relative z-[1]">
                            <img src={testimonialsData.testimonial_image ? getImagePath(testimonialsData.testimonial_image) : img('testimonial-bg.png')} className="absolute z-[-1] inset-0 h-full w-full object-cover" alt="testimonial bg" />
                            <div className="absolute inset-0 h-full w-full bg-black/40"></div>
                            <div className="relative md:container w-full mx-auto px-4">
                                <div className="grid items-center lg:grid-cols-2 grid-cols-1 lg:gap-10 gap-6">
                                    <div className="text-white text-center lg:text-start">
                                        <h2 className="text-2xl sm:text-3xl md:text-4xl xl:text-5xl mb-6 font-medium">
                                            {testimonialsData.testimonial_title || t("Need help with professional photography? Let's work together!")}
                                        </h2>
                                        <Link href={route('photo-studio-management.frontend.appointment', { userSlug: slug })} className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-transparent hover:bg-[#ffffff] text-[#ffffff] hover:text-[#111111] border border-[#ffffff] hover:border-[#ffffff] transition-all duration-300 capitalize font-medium">
                                            {t('book appointment')}
                                        </Link>
                                    </div>
                                    <div className="xl:ms-[25%] lg:ms-[10%]">
                                        <div className="bg-white flex flex-col xl:p-10 md:p-8 sm:p-6 p-4">
                                            <div className="relative overflow-hidden">
                                                <div className="flex transition-transform duration-500 ease-in-out" style={{ transform: `translateX(-${testimonialSlide * 100}%)` }}>
                                                    {testimonials.map((testimonial: any, index: number) => (
                                                        <div key={index} className="w-full flex-shrink-0 flex flex-col">
                                                            <div className="flex-1">
                                                                <p className="sm:ps-5 ps-4 border-s border-black line-clamp-4 font-medium">{testimonial.comment}</p>
                                                            </div>

                                                            <div className="relative flex items-center sm:gap-5 gap-4 border-t border-gray-100 sm:mt-7 sm:pt-6 mt-5 pt-5">

                                                                <SocialLinks icon="Quote" className="absolute end-0 sm:w-16 sm:h-16 w-12 h-12 text-black/5" />
                                                                {testimonial.profile_image ? (
                                                                    <div className="w-14 h-14 rounded-full overflow-hidden border flex-shrink-0">
                                                                        <img src={getImagePath(testimonial.profile_image)} className="h-full w-full object-cover" alt="client" />
                                                                    </div>
                                                                ) : (
                                                                    <div className="w-14 h-14 rounded-full bg-[#674B2F]/10 flex items-center justify-center flex-shrink-0">
                                                                        <span className="text-[#674B2F] font-medium">{testimonial.customer_name?.charAt(0)}</span>
                                                                    </div>
                                                                )}

                                                                <div className="flex-1 min-w-0">

                                                                    <h3 className="text-xl mb-2 truncate font-medium">{testimonial.customer_name}</h3>
                                                                    <p className="text-sm text-gray-500 truncate font-medium">{testimonial.designation}</p>
                                                                    <div className="flex gap-1 mb-3">
                                                                        {[...Array(5)].map((_, i) => (
                                                                            <SocialLinks key={i} icon="Star" className={`w-4 h-4 ${i < Math.floor(testimonial.rating || 0) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}`} />
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Gallery Section */}
                    {galleryImages.length > 0 && (
                        <section className="lg:pt-16 pt-10">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-primary lg:text-lg font-medium text-[#674B2F]">{gallerySection.gallery_label || t('Featured Gallery')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{gallerySection.gallery_title || t('A Showcase of Our Finest Work')}</h2>
                                </div>
                            </div>
                            <div className="relative">
                                <div className="overflow-hidden">
                                    <div className="flex transition-transform duration-500 ease-in-out lg:flex-nowrap flex-wrap lg:gap-0 gap-4" style={{ transform: isLargeScreen ? `translateX(-${gallerySlide * (100 / Math.min(galleryImages.length, 3))}%)` : 'none' }}>
                                        {galleryImages.map((image: string, index: number) => (
                                            <div key={index} className={`lg:flex-shrink-0 px-2 w-full sm:w-1/2 ${galleryImages.length >= 3 ? 'lg:w-1/3' : galleryImages.length === 2 ? 'lg:w-1/2' : 'lg:w-full'}`}>
                                                <div className="md:h-80 h-60 w-full overflow-hidden">
                                                    <img src={image} className="h-full w-full object-cover hover:scale-110 transition duration-500" alt="gallery image" />
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                                {galleryImages.length > 3 && (
                                    <>
                                        <button onClick={handleGalleryPrev} className="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">‹</button>
                                        <button onClick={handleGalleryNext} className="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">›</button>
                                    </>
                                )}
                            </div>
                        </section>
                    )}
                </>
            )}
        </Layout>
    );
};

export default Index;
