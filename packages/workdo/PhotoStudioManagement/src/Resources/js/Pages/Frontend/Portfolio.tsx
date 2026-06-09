import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { ChevronRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';

const Portfolio = () => {
    const { userSlug, photoStudioSettings, galleryTypes } = usePage<{ userSlug?: string; photoStudioSettings?: any; galleryTypes?: any[] }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();
    const [activeFilter, setActiveFilter] = useState('all');

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const gallerySection = photoStudioSettings?.gallery_section || {};
    
    // Map gallery type IDs to names and create portfolio items
    const portfolioItems: { id: number; gallery_type: string; image: string }[] =
        gallerySection.images && gallerySection.images.length > 0
            ? gallerySection.images.map((item: any, idx: number) => {
                // Handle both string and number IDs
                const galleryType = galleryTypes?.find(type => 
                    type.id == item.gallery_type_id || type.id === parseInt(item.gallery_type_id)
                );
                return {
                    id: idx + 1,
                    gallery_type: galleryType?.name || t('General'),
                    image: item.image ? getImagePath(item.image) : '',
                };
            })
            : [];

    const categories = ['all', ...Array.from(new Set(portfolioItems.map(item => item.gallery_type)))] as string[];

    const filteredItems = activeFilter === 'all'
        ? portfolioItems
        : portfolioItems.filter(item => item.gallery_type === activeFilter);

    return (
        <Layout title={t('Portfolio')}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div className="sm:text-start text-center">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{gallerySection.gallery_page_title || t('Portfolio')}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <ChevronRight className="mx-2" size={12} />
                            </li>
                            <li className="font-bold capitalize">{t('Portfolio')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Portfolio Grid */}
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    {portfolioItems.length > 0 ? (
                        <>
                            <div className="text-center mb-6">
                                <span className="inline-block capitalize mb-2 text-primary lg:text-lg font-medium text-[#674B2F]">{gallerySection.gallery_category_label || t('Browse By Category')}</span>
                                <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{gallerySection.gallery_category_title || t('Our Photography Work')}</h2>
                            </div>
                            {categories.length > 1 && (
                                <div className="flex items-center justify-center sm:mb-8 mb-6">
                                    <div className="flex overflow-x-auto gap-2 sm:gap-0 px-4 sm:px-0 w-full sm:w-auto" style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
                                        {categories.map((category) => (
                                            <button
                                                key={category}
                                                onClick={() => setActiveFilter(category)}
                                                className={`filter-btn whitespace-nowrap sm:px-4 sm:py-2 px-3 py-2 text-sm sm:text-base font-medium transition-all flex-shrink-0 ${
                                                    activeFilter === category
                                                        ? 'sm:bg-[#674B2F] sm:text-[#ffffff] bg-[#674B2F] text-white'
                                                        : 'sm:bg-gray-100 bg-gray-100 text-gray-600'
                                                }`}
                                            >
                                                {category === 'all' ? t('All Work') : category}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            )}
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-8">
                                {filteredItems.map((item) => (
                                    <div key={item.id} className="portfolio-item relative group overflow-hidden shadow-lg">
                                        <img src={item.image} alt="portfolio" className="w-full md:h-80 h-64 object-cover group-hover:scale-110 transition duration-500" />
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                                    </div>
                                ))}
                            </div>
                        </>
                    ) : (
                        <div className="flex flex-col items-center justify-center py-24">
                            <svg className="w-24 h-24 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 className="text-xl font-medium text-gray-500 mb-2">{t('No Portfolio Items Available')}</h3>
                            <p className="text-gray-400 text-center max-w-md">{t('No portfolio images have been added yet. Please check back later.')}</p>
                        </div>
                    )}
                </div>
            </section>
        </Layout>
    );
};

export default Portfolio;
