import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { getImagePath, formatCurrency } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import { ChevronRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const Services = () => {
    const { userSlug, services, photoStudioSettings } = usePage<{ userSlug?: string; services?: any[]; photoStudioSettings?: any }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const serviceList = services || [];
    const titleSection = photoStudioSettings?.title_section || {};

    return (
        <Layout title={t('Services')}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div>
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{titleSection?.service_page_title || t('Our Services')}</h2>
                        <ul className="flex flex-wrap items-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <ChevronRight className="w-3 h-3 mx-2" />
                            </li>
                            <li className="font-bold capitalize">{t('Services')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Services Grid */}
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    {serviceList.length > 0 ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8 gap-5">
                            {serviceList.map((service: any, index: number) => (
                                <div key={index} className="bg-white flex flex-col h-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group relative border">
                                    <div className="relative h-56 overflow-hidden">
                                        <img src={service.image ? getImagePath(service.image) : ''} className="w-full h-full object-cover transition-all duration-500 group-hover:scale-110" alt="service" />
                                        <span className="bg-white text-black absolute z-[1] top-4 end-4 px-3 py-1 text-sm font-bold shadow-lg">
                                            {t('From')} {formatCurrency(service.price)}
                                        </span>
                                        <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60 group-hover:opacity-70 transition-opacity duration-300"></div>
                                        <div className="absolute bottom-5 start-0 w-full lg:px-6 px-4">
                                            <h3 className="text-2xl font-bold text-white mb-0">{service.name}</h3>
                                        </div>
                                    </div>
                                    <div className="flex-1 flex flex-col lg:p-6 p-4 relative">
                                        <div className="flex-1">
                                            <div className="flex flex-wrap gap-2 mb-4">
                                                {(service.category_names || []).map((tag: string, idx: number) => (
                                                    <span key={idx} className="font-medium text-xs bg-gray-200 text-gray-800 px-2 py-1">
                                                        {tag}
                                                    </span>
                                                ))}
                                            </div>
                                            <p className="text-gray-600 mb-5 line-clamp-3 font-medium">{service.description}</p>
                                        </div>
                                        <Link href={route('photo-studio-management.frontend.appointment', { userSlug: slug })} className="inline-flex items-center justify-center w-full gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">
                                            {t('Book Now')}
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center py-24">
                            <svg className="w-24 h-24 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 className="text-xl font-medium text-gray-500 mb-2">{t('No Services Available')}</h3>
                            <p className="text-gray-400 text-center max-w-md">{t('No services have been configured yet. Please check back later.')}</p>
                        </div>
                    )}
                </div>
            </section>
        </Layout>
    );
};

export default Services;
