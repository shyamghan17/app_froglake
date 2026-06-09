import React, { useState, useMemo } from 'react';
import PublicLayout from './components/PublicLayout';
import PageHeader from '../../components/PageHeader';
import { Input, Button, Image } from './components';
import { Link, router } from '@inertiajs/react';
import { Search, Star } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';

interface Item {
    id: number;
    name: string;
    description?: string;
    image?: string;
}

interface ServicesProps {
    title: string;
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
    items?: Item[];
    pagination?: {
        current_page: number;
        total_pages: number;
        total_items: number;
        per_page: number;
    };
    search?: string;
}

export default function Services({ title, brandSettings, colorSettings, socialLinks, customPages, footerServices, pageSettings, userSlug, items = [], pagination, search }: ServicesProps) {
    const { t } = useTranslation();
    const pageSettingsData = pageSettings?.services || {};
    const headerSection = pageSettingsData?.header || {};
    const searchSection = pageSettingsData?.search || {};
    const emptyStateSection = pageSettingsData?.empty_state || {};
    const paginationSection = pageSettingsData?.pagination || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';
    const [searchTerm, setSearchTerm] = useState(search || '');

    const services = items.map((item, i) => ({
        ...item,
        rating: '4.8',
        price: 'Contact',
        badge: i === 0 ? t('Popular') : i === 1 ? t('New') : null,
        tags: item.name.split(' ').slice(0, 2)
    }));

    const handleSearch = () => {
        router.get(userSlug ? route('booking.services', { userSlug }) : '#', { search: searchTerm });
    };

    const handlePageChange = (page: number) => {
        const params = search ? { search, page } : { page };
        router.get(userSlug ? route('booking.services', { userSlug }) : '#', params);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return (
        <PublicLayout title={title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            <PageHeader 
                title={headerSection.title || t('Our Services')} 
                description={headerSection.description || t('Discover our comprehensive range of professional services designed to meet your needs')} 
                bgColor={ primaryColor}
            />

            {/* Search Section */}
            <section className="py-10 bg-gray-50">
                <div className="container mx-auto px-4">
                    <div className="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
                        <div className="flex gap-3 flex-wrap">
                            <Input
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                placeholder={searchSection.search_placeholder || t('Search for services...')}
                                className="flex-1"
                            />
                            <Button 
                                onClick={handleSearch}
                                variant="primary"
                                className="flex items-center gap-2"
                                primaryColor={primaryColor}
                            >
                                <Search className="w-4 h-4" />
                                {searchSection.search_button_text || t('Search')}
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            {/* Services Grid */}
            <section className="lg:py-16 py-10">
                <div className="container mx-auto px-4">
                    {services.length === 0 ? (
                        <div className="text-center py-16">
                            <div className="text-gray-400 mb-4">
                                <Search className="w-16 h-16 mx-auto mb-4" />
                            </div>
                            <h3 className="text-xl font-semibold text-gray-600 mb-2">{emptyStateSection.title || t('No services found')}</h3>
                            <p className="text-gray-500">{emptyStateSection.description || t('Try adjusting your search terms or browse all services.')}</p>
                        </div>
                    ) : (
                        <>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                {services.map((service, i) => (
                            <div key={service.id} className="service-card flex flex-col bg-white shadow-lg rounded-lg overflow-hidden h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                                <div className="relative">
                                    <Image
                                        src={service.image ? getImagePath(service.image) : getImagePath(`packages/workdo/Bookings/src/assets/images/service-${(i % 6) + 1}.png`)}
                                        alt={service.name}
                                        className="w-full h-64 object-cover"
                                    />
                                    <div className="absolute top-4 right-4 text-white text-sm px-3 py-1 rounded-full" style={{ backgroundColor: primaryColor }}>
                                        {service.name}
                                    </div>
                                </div>
                                <div className="xl:p-6 p-4 h-full flex flex-col">
                                    <h3 className="text-xl font-bold mb-3 service-title" style={{ color: primaryColor }}>
                                        {service.name}
                                    </h3>
                                    <div className="h-full flex flex-col justify-end">
                                        <p className="text-gray-600 mb-4 service-description">
                                            {service.description || ''}
                                        </p>
                                        <div className="text-center">
                                            <Link 
                                                href={userSlug ? route('booking.services.detail', { userSlug: userSlug, id: service.id }) : undefined}
                                                className="service-detail-btn text-white px-6 py-3 rounded-md hover:bg-white border-2 hover:text-current transition w-full inline-block text-center"
                                                style={{ 
                                                    backgroundColor: primaryColor, 
                                                    borderColor: primaryColor,
                                                    '--hover-color': primaryColor
                                                } as React.CSSProperties}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.backgroundColor = 'white';
                                                    e.currentTarget.style.color = primaryColor;
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.backgroundColor = primaryColor;
                                                    e.currentTarget.style.color = 'white';
                                                }}
                                            >
                                                {t('View Details')}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ))}
                        </div>
                        
                        {/* Pagination */}
                        {pagination && pagination.total_pages > 1 && (
                            <div className="flex justify-center mt-12">
                                <div className="flex items-center space-x-2">
                                    <Button
                                        onClick={() => handlePageChange(pagination.current_page - 1)}
                                        disabled={pagination.current_page === 1}
                                        variant="outline"
                                        size="sm"
                                    >
                                        {paginationSection.previous_text || t('Previous')}
                                    </Button>
                                    
                                    {Array.from({ length: pagination.total_pages }, (_, i) => i + 1).map(page => (
                                        <Button
                                            key={page}
                                            onClick={() => handlePageChange(page)}
                                            variant={pagination.current_page === page ? 'primary' : 'outline'}
                                            size="sm"
                                        >
                                            {page}
                                        </Button>
                                    ))}
                                    
                                    <Button
                                        onClick={() => handlePageChange(pagination.current_page + 1)}
                                        disabled={pagination.current_page === pagination.total_pages}
                                        variant="outline"
                                        size="sm"
                                    >
                                        {paginationSection.next_text || t('Next')}
                                    </Button>
                                </div>
                            </div>
                        )}
                    </>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}