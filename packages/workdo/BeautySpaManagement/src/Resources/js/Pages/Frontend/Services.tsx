import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import Layout from './Layout';
import { ChevronRight, ChevronLeft, Image } from 'lucide-react';
import { formatCurrency, getImagePath } from '@/utils/helpers';

interface Service {
    id: number;
    name: string;
    description: string;
    service_image: string;
    price: number;
    type?: {
        service_type: string;
    };
}

interface PaginatedServices {
    data: Service[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    title?: string;
    services: PaginatedServices;
    search?: string;
    userSlug: string;
    serviceTypes?: string[];
    allServices?: Service[];
}

export default function Services({
    title = 'All Services',
    services,
    search,
    userSlug,
    serviceTypes = [],
    allServices = []
}: Props) {
    const { t } = useTranslation();
    const [searchTerm, setSearchTerm] = useState(search || '');

    const handleSearch = () => {
        router.visit(route('beauty-spa.services', { userSlug }), {
            method: 'get',
            data: { search: searchTerm },
            preserveState: true
        });
    };

    // Filter services based on search
    const filteredServices = services.data.filter((service) => {
        if (!searchTerm) return true;
        return service.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
               service.description.toLowerCase().includes(searchTerm.toLowerCase());
    });

    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* Services Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        {/* Breadcrumb */}
                        <div className="mb-8">
                            <nav className="flex" aria-label="Breadcrumb">
                                <ol className="inline-flex items-center space-x-1 md:space-x-3">
                                    <li className="inline-flex items-center">
                                        <Link
                                            href={route('beauty-spa.home', { userSlug })}
                                            className="text-gray-700 hover:text-[#df9896]"
                                        >
                                            {t('Home')}
                                        </Link>
                                    </li>
                                    <li aria-current="page">
                                        <div className="flex items-center">
                                            <ChevronRight className="text-gray-400 mx-2 w-4 h-4" />
                                            <span className="text-[#df9896] font-medium">
                                                {t('All Services')}
                                            </span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">
                                {t('Premium Services')}
                            </span>
                            <h1 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">
                                {t('All Our Premium Services')}
                            </h1>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">
                                {t(
                                    'Indulge in our complete range of rejuvenating treatments designed to restore your natural beauty and inner peace.'
                                )}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Services Section */}
                <section id="services" className="lg:py-16 py-10 bg-white">
                    <div className="container mx-auto px-4">
                        {/* Search Section */}
                        <div className="bg-gray-50 rounded-lg shadow-md border border-gray-100 lg:p-8 md:p-6 p-4 lg:mb-12 mb-8">
                            <div className="max-w-2xl mx-auto">
                                <div className="flex gap-3 flex-wrap">
                                    <input
                                        type="text"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        placeholder={t('Search for services...')}
                                        className="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#df9896] focus:border-transparent transition-all"
                                        onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                                    />
                                    <button
                                        onClick={handleSearch}
                                        className="bg-[#df9896] hover:bg-[#c88a88] text-white px-8 py-3 rounded-lg transition-colors font-medium flex items-center justify-center"
                                    >
                                        <ChevronRight className="w-4 h-4 me-2" />
                                        {t('Search')}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Services Grid */}
                        <div id="servicesGrid" className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {filteredServices.map((service) => (
                                <div
                                    key={service.id}
                                    className="service-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow h-full flex flex-col cursor-pointer"
                                    onClick={() =>
                                        router.visit(
                                            route('beauty-spa.service.detail', {
                                                userSlug,
                                                service: service.id
                                            })
                                        )
                                    }
                                >
                                    <img
                                        src={service.service_image ? getImagePath(service.service_image) : getImagePath('/packages/workdo/BeautySpaManagement/src/Resources/assets/images/defualt.png')}
                                        alt={service.name}
                                        loading="lazy"
                                        className="w-full h-64 object-cover"
                                    />

                                    <div className="sm:p-6 p-4 h-full flex flex-col justify-between">
                                        <div>
                                            <h3 className="text-xl font-bold mb-2 text-[#df9896]">
                                                {service.name}
                                            </h3>
                                            <p className="text-gray-800 mb-4">
                                                {service.description.length > 120 ? service.description.substring(0, 120) + '...' : service.description}
                                            </p>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-[#df9896] font-bold">
                                                {t('From')} {formatCurrency(service.price)}
                                            </span>
                                            <Link
                                                href={route('beauty-spa.booking', { userSlug, service: service.id })}
                                                className="text-[#c88a88] hover:underline"
                                                onClick={(e) => e.stopPropagation()}
                                            >
                                                {t('Book Now')}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Pagination */}
                        {services.last_page > 1 && (
                            <div className="flex items-center justify-between mt-8 px-2 py-4">
                                <div className="text-sm text-gray-600">
                                    {t('Showing')} {services.from} {t('to')} {services.to} {t('of')} {services.total} {t('results')}
                                </div>
                                <div className="flex items-center space-x-2">
                                    <button
                                        onClick={() => router.get(route('beauty-spa.services', { userSlug }), { page: services.current_page - 1 }, { preserveState: true })}
                                        disabled={services.current_page === 1}
                                        className="flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <ChevronLeft className="w-4 h-4 mr-1" />
                                        {t('Previous')}
                                    </button>

                                    {Array.from({ length: services.last_page }, (_, i) => i + 1).map((page) => (
                                        <button
                                            key={page}
                                            onClick={() => router.get(route('beauty-spa.services', { userSlug }), { page }, { preserveState: true })}
                                            className={`px-3 py-2 text-sm font-medium rounded-lg ${page === services.current_page
                                                ? 'text-white bg-[#df9896] border border-[#df9896]'
                                                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'
                                                }`}
                                        >
                                            {page}
                                        </button>
                                    ))}

                                    <button
                                        onClick={() => router.get(route('beauty-spa.services', { userSlug }), { page: services.current_page + 1 }, { preserveState: true })}
                                        disabled={services.current_page === services.last_page}
                                        className="flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {t('Next')}
                                        <ChevronRight className="w-4 h-4 ml-1" />
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </section>
            </main>
        </Layout>
    );
}
