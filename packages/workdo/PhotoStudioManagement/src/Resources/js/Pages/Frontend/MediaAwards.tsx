import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import SocialLinks from '@/components/SocialLinks';

const MediaAwards = () => {
    const { userSlug, photoStudioSettings } = usePage<{ userSlug?: string; photoStudioSettings?: any }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();
    const [testimonialSlide, setTestimonialSlide] = useState(0);
    const [mediaSlide, setMediaSlide] = useState(0);
    const [isLargeScreen, setIsLargeScreen] = useState(false);

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const formatDate = (dateString: string) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        const options: Intl.DateTimeFormatOptions = {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-GB', options);
    };

    const awardSection = photoStudioSettings?.award_section || {};
    const mediaSection = photoStudioSettings?.media_section || {};
    const testimonialsData = photoStudioSettings?.testimonials || {};

    const awards = awardSection.awards && awardSection.awards.length > 0 ? awardSection.awards : [];

    const mediaFeatures = mediaSection.media_items && mediaSection.media_items.length > 0
        ? mediaSection.media_items.map((item: any) => ({
            title: item.media_heading,
            description: item.content,
            date: item.date,
            type: item.content_type,
            image: item.media_image ? getImagePath(item.media_image) : '',
        }))
        : [];

    const testimonials = testimonialsData.testimonials && testimonialsData.testimonials.length > 0
        ? testimonialsData.testimonials.map((t: any) => ({
            text: t.comment,
            name: t.customer_name,
            role: t.designation,
            initials: t.customer_name?.charAt(0) || '',
            profile_image: t.profile_image,
            rating: t.rating
        }))
        : [];

    useEffect(() => {
        const handleResize = () => {
            setIsLargeScreen(window.innerWidth >= 1024);
        };

        handleResize();
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const handleTestimonialNext = () => { const max = Math.max(0, testimonials.length - 2); setTestimonialSlide((p) => (p === max ? 0 : p + 1)); };
    const handleTestimonialPrev = () => { const max = Math.max(0, testimonials.length - 2); setTestimonialSlide((p) => (p === 0 ? max : p - 1)); };
    const handleMediaNext = () => { const max = Math.max(0, mediaFeatures.length - 4); setMediaSlide((p) => (p === max ? 0 : p + 1)); };
    const handleMediaPrev = () => { const max = Math.max(0, mediaFeatures.length - 4); setMediaSlide((p) => (p === 0 ? max : p - 1)); };

    const hasAnyContent = awards.length > 0 || mediaFeatures.length > 0 || testimonials.length > 0;

    return (
        <Layout title={t('Media & Awards')}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div className="sm:text-start text-center">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{awardSection.award_page_title || t('Awards & Media')}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <SocialLinks icon="ChevronRight" className="mx-2 w-3 h-3" />
                            </li>
                            <li className="font-bold capitalize">{t('Awards')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {!hasAnyContent ? (
                <div className="flex flex-col items-center justify-center min-h-[50vh] py-16">
                    <svg className="w-24 h-24 text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 className="text-xl font-medium text-gray-500 mb-4">{t('No Content Available')}</h2>
                    <p className="text-gray-400 text-center max-w-md">{t('There is no content configured for this page. Please contact the administrator to set up the page content.')}</p>
                </div>
            ) : (
                <>
                    {/* Awards Section */}
                    {awards.length > 0 && (
                        <section className="lg:py-16 py-10">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-[#674B2F] lg:text-lg font-medium">{awardSection.label || t('Excellence Recognized')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{awardSection.title || t('Awards & Recognition')}</h2>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-8">
                                    {awards.map((award: any, index: number) => (
                                        <div key={index} className="bg-white lg:p-6 p-4 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 hover:border-[#674B2F]/10 relative overflow-hidden">
                                            <div className="flex sm:flex-row flex-col sm:items-center sm:mb-5 mb-4 gap-4">
                                                <div className="lg:w-16 lg:h-16 w-12 h-12 bg-[#674B2F]/10 flex items-center justify-center flex-shrink-0">
                                                    <SocialLinks icon={award.award_icon} className="text-[#674B2F] lg:w-6 lg:h-6 w-5 h-5" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h3 className="text-xl mb-2 font-medium">{award.award_title}</h3>
                                                    <p className="text-[#674B2F] font-medium">{award.award_name}</p>
                                                </div>
                                            </div>
                                            <p className="text-gray-600 mb-4 font-medium">{award.description}</p>
                                            <div className="flex items-center text-[#674B2F]">
                                                <SocialLinks icon={award.achievement_icon} className="me-2 w-4 h-4" />
                                                <span className="font-semibold">{award.achievement_name}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Media Coverage */}
                    {mediaFeatures.length > 0 && (
                        <section className="lg:py-16 py-10 bg-[#674B2F]/5">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-[#674B2F] lg:text-lg font-medium">{mediaSection.label || t('In The Press')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{mediaSection.title || t('Media Coverage')}</h2>
                                </div>
                                <div className="relative lg:!pb-8 !pb-6">
                                    <div className="overflow-hidden">
                                        <div className="flex transition-transform duration-500 ease-in-out lg:flex-nowrap flex-wrap lg:gap-0 gap-4" style={{ transform: isLargeScreen ? `translateX(-${mediaSlide * (100 / Math.min(mediaFeatures.length, 4))}%)` : 'none' }}>
                                            {mediaFeatures.map((media: any, index: number) => (
                                                <div key={index} className={`lg:flex-shrink-0 px-2 w-full sm:w-1/2 ${mediaFeatures.length >= 4 ? 'lg:w-1/4' : mediaFeatures.length === 3 ? 'lg:w-1/3' : mediaFeatures.length === 2 ? 'lg:w-1/2' : 'lg:w-full'}`}>
                                                    <div className="bg-white flex flex-col h-full overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 group relative border">
                                                        <div className="relative h-48 overflow-hidden">
                                                            <img src={media.image} className="w-full h-full object-cover" alt="media coverage image" />
                                                        </div>
                                                        <div className="flex-1 flex flex-col lg:p-6 p-4 relative">
                                                            <div className="flex-1">
                                                                <h4 className="text-xl mb-3 font-medium">{media.title}</h4>
                                                                <p className="text-gray-600 mb-4 line-clamp-3 font-medium">{media.description}</p>
                                                            </div>
                                                            <div className="flex items-center justify-between">
                                                                <span className="text-sm font-medium">{formatDate(media.date)}</span>
                                                                <span className="text-[#674B2F] font-medium">{media.type}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                    {mediaFeatures.length > 4 && (
                                        <>
                                            <button onClick={handleMediaPrev} className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">‹</button>
                                            <button onClick={handleMediaNext} className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">›</button>
                                        </>
                                    )}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Client Testimonials Section */}
                    {testimonials.length > 0 && (
                        <section className="lg:py-16 py-10">
                            <div className="md:container w-full mx-auto px-4">
                                <div className="text-center lg:mb-8 mb-5">
                                    <span className="inline-block capitalize mb-2 text-[#674B2F] lg:text-lg font-medium">{testimonialsData.client_feedback_label || t('Client Feedback')}</span>
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{testimonialsData.client_feedback_title || t('Award-Winning Results')}</h2>
                                </div>
                                <div className="relative lg:!pb-8 !pb-6">
                                    <div className="overflow-hidden">
                                        <div className="flex transition-transform duration-500 ease-in-out lg:flex-nowrap flex-wrap lg:gap-0 gap-4" style={{ transform: isLargeScreen ? `translateX(-${testimonialSlide * (100 / Math.min(testimonials.length, 2))}%)` : 'none' }}>
                                            {testimonials.map((testimonial: any, index: number) => (
                                                <div key={index} className={`lg:flex-shrink-0 px-2 w-full ${testimonials.length >= 2 ? 'lg:w-1/2' : 'lg:w-full'}`}>
                                                    <div className="h-full bg-white lg:p-6 p-4 shadow-md border border-gray-200">
                                                        <div className="flex items-start">
                                                            <div className="text-[#674B2F] text-4xl me-4 flex-shrink-0"><SocialLinks icon="Quote" className="w-10 h-10" /></div>
                                                            <div className="min-w-0">
                                                                <p className="text-gray-600 italic line-clamp-4 font-medium mb-4">"{testimonial.text}"</p>
                                                                <div className="flex items-center">
                                                                    {testimonial.profile_image ? (
                                                                        <div className="w-12 h-12 rounded-full overflow-hidden border flex-shrink-0 me-3">
                                                                            <img src={getImagePath(testimonial.profile_image)} className="h-full w-full object-cover" alt="client" />
                                                                        </div>
                                                                    ) : (
                                                                        <div className="w-12 h-12 bg-[#674B2F]/10 flex items-center justify-center me-3 flex-shrink-0 rounded-full">
                                                                            <span className="text-[#674B2F] font-medium capitalize">{testimonial.initials}</span>
                                                                        </div>
                                                                    )}
                                                                    <div className="flex-1 min-w-0">
                                                                        <h4 className="mb-1 font-medium truncate">{testimonial.name}</h4>
                                                                        <p className="text-sm text-[#674B2F] font-medium truncate">{testimonial.role}</p>
                                                                        <div className="flex gap-1 mt-2">
                                                                            {[...Array(5)].map((_, i) => (
                                                                                <SocialLinks key={i} icon="Star" className={`w-4 h-4 ${i < Math.floor(testimonial.rating || 0) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}`} />
                                                                            ))}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                    {testimonials.length > 2 && (
                                        <>
                                            <button onClick={handleTestimonialPrev} className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">‹</button>
                                            <button onClick={handleTestimonialNext} className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-10 w-10 h-10 bg-[#674B2F] hover:bg-[#674B2F]/80 text-white transition-all flex items-center justify-center hidden lg:flex">›</button>
                                        </>
                                    )}
                                </div>
                            </div>
                        </section>
                    )}
                </>
            )}
        </Layout>
    );
};

export default MediaAwards;
