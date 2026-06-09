import React, { useState, useEffect } from 'react';
import Layout from './Layout';
import { usePage, useForm } from '@inertiajs/react';
import { getImagePath, formatCurrency } from '@/utils/helpers';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { ChevronRight, Calendar, Check, User, Mail, MessageCircle, Send, Star } from 'lucide-react';
import { Label } from '@/components/ui/label';

interface Service {
    id: number;
    name: string;
    description: string;
    price: number;
    time: string;
    service_image?: string;
    included_services?: string;
}

interface Review {
    id: number;
    name: string;
    email: string;
    rating: number;
    review: string;
}

interface Props {
    title?: string;
    service: Service;
    reviews: Review[];
    averageRating: number;
    reviewCount: number;
    related_services: Service[];
    slug: string;
}

export default function ServiceDetail({
    title = 'Service Details',
    service,
    reviews = [],
    averageRating = 0,
    reviewCount = 0,
    related_services = [],
    slug
}: Props) {
    const { t } = useTranslation();
    const pageProps = usePage().props as any;
    const [selectedRating, setSelectedRating] = useState(0);
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        email: '',
        rating: 0,
        review: ''
    });

    const includedServices = service.included_services ?
        (typeof service.included_services === 'string' ? JSON.parse(service.included_services) : service.included_services) : [];

    useEffect(() => {
        // Component mounted
    }, []);

    useEffect(() => {
        setData('rating', selectedRating);
    }, [selectedRating]);

    const handleStarClick = (rating: number) => {
        setSelectedRating(rating);
    };

    const handleSubmitReview = async (e: React.FormEvent) => {
        e.preventDefault();

        if (selectedRating === 0) {
            return;
        }

        post(route('beauty-spa.service.review.store', { userSlug: slug, service: service.id }), {
            onSuccess: () => {
                reset();
                setSelectedRating(0);
            },
            onError: (errors) => {
                console.error('Error submitting review:', errors);
            }
        });
    };

    const renderStars = (rating: number, size = 'h-5 w-5') => {
        return (
            <div className="flex">
                {[1, 2, 3, 4, 5].map((star) => (
                    <Star
                        key={star}
                        className={`${size} ${star <= rating ? 'text-yellow-400 fill-current' : 'text-gray-300'}`}
                    />
                ))}
            </div>
        );
    };

    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* Service Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        {/* Breadcrumb */}
                        <div className="mb-8">
                            <nav className="flex" aria-label="Breadcrumb">
                                <ol className="inline-flex items-center space-x-1 md:space-x-3">
                                    <li className="inline-flex items-center">
                                        <a href={route('beauty-spa.booking', { userSlug: slug })} className="text-gray-700 hover:text-[#df9896]">{t('Home')}</a>
                                    </li>
                                    <li>
                                        <div className="flex items-center">
                                            <ChevronRight className="text-gray-400 mx-2 w-4 h-4" />
                                            <a href={route('beauty-spa.services', { userSlug: slug })} className="text-gray-700 hover:text-[#df9896]">{t('All Services')}</a>
                                        </div>
                                    </li>
                                    <li aria-current="page">
                                        <div className="flex items-center">
                                            <ChevronRight className="text-gray-400 mx-2 w-4 h-4" />
                                            <span className="text-[#df9896] font-medium">{service.name}</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Service Detail')}</span>
                            <h1 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{service.name}</h1>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">
                                {service.description}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Service Detail Section */}
                <section className="lg:py-16 py-10 bg-white">
                    <div className="container mx-auto px-4">
                        <div className="grid grid-cols-1 lg:grid-cols-2 items-center gap-12">
                            <div className="order-2 lg:order-1">
                                <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Premium Service')}</span>
                                <h2 className="text-2xl font-bold text-gray-800 mt-2 mb-4">{t('Service Details')}</h2>
                                <div className="w-16 h-1 bg-[#df9896] rounded-full mb-6"></div>

                                {/* Rating & Book Button */}
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center">
                                        <div className="flex items-center">
                                            {renderStars(averageRating)}
                                        </div>
                                        <span className="ml-2 text-gray-600">{averageRating} ({reviewCount} {t('reviews')})</span>
                                    </div>
                                    <a href={route('beauty-spa.booking', { userSlug: slug, service: service.id })} className="bg-[#df9896] hover:bg-[#c88684] text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center">
                                        <Calendar className="w-4 h-4 me-2" />{t('Book This Service')}
                                    </a>
                                </div>

                                {/* Price & Duration */}
                                <div className="bg-[#F5F5F5] rounded-lg p-4 mb-6">
                                    <div className="flex justify-between items-center mb-3">
                                        <span className="text-2xl font-bold text-[#df9896]">{formatCurrency(service.price, pageProps)}</span>
                                        <span className="text-gray-600 font-medium">{service.time}</span>
                                    </div>
                                    <p className="text-gray-700 text-sm">
                                        {service.description}
                                    </p>
                                </div>

                                {/* Service Features */}
                                <div className="space-y-4">
                                    <div className="bg-white rounded-lg p-4 border border-gray-200">
                                        <h3 className="text-lg font-bold text-[#df9896] mb-3 flex items-center">
                                            {t("What's Included")}
                                        </h3>
                                        {includedServices.length > 0 ? (
                                            <ul className="text-gray-800 space-y-2">
                                                {includedServices.map((item: string, index: number) => (
                                                    <li key={index} className="flex items-center">
                                                        <Check className="w-4 h-4 text-[#df9896] me-2" /> {item}
                                                    </li>
                                                ))}
                                            </ul>
                                        ) : (
                                            <p className="text-gray-500">{t('No service features listed.')}</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                            <div className="order-1 lg:order-2 relative">
                                <img
                                    src={service.service_image ? getImagePath(service.service_image, pageProps) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/defualt.png', pageProps)}
                                    alt="Service Image"
                                    className="w-full h-auto rounded-xl shadow-lg"
                                />
                                <div className="absolute -bottom-4 -end-4 w-24 h-24 bg-[#df9896] rounded-full opacity-20"></div>
                                <div className="absolute -top-4 -start-4 w-32 h-32 border-4 border-[#df9896] rounded-full opacity-20"></div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Reviews Section */}
                <section className="lg:py-16 py-10 bg-[#F5F5F5]">
                    <div className="container mx-auto px-4">
                        <div className="grid grid-cols-1 lg:grid-cols-2 items-center gap-12">
                            {/* Add Review Form */}
                            <div>
                                <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Share Your Experience')}</span>
                                <h2 className="text-3xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{t('Write a Review')}</h2>
                                <div className="w-16 h-1 bg-[#df9896] rounded-full mb-4"></div>

                                <form onSubmit={handleSubmitReview} className="bg-white rounded-lg p-6 shadow-md">
                                    <div className="mb-4">
                                        <Label required>{t('Your Name')}</Label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#df9896] focus:border-transparent"
                                            placeholder={t('Enter Your Name')}
                                            required
                                        />
                                    </div>
                                    <div className="mb-4">
                                        <Label required>{t('Your Email')}</Label>
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#df9896] focus:border-transparent"
                                            placeholder={t('Enter Your Email')}
                                            required
                                        />
                                    </div>

                                    <div className="mb-4">
                                        <Label required>{t('Rating')}</Label>
                                        <div className="flex cursor-pointer">
                                            {[1, 2, 3, 4, 5].map(star => (
                                                <Star
                                                    key={star}
                                                    onClick={() => handleStarClick(star)}
                                                    className={`h-8 w-8 mr-1 ${star <= selectedRating ? 'text-yellow-400 fill-current' : 'text-gray-300 hover:text-yellow-400 fill-current'}`}
                                                />
                                            ))}
                                        </div>
                                    </div>

                                    <div className="mb-6">
                                        <Label required>{t('Your Review')}</Label>
                                        <textarea
                                            rows={4}
                                            value={data.review}
                                            onChange={(e) => setData('review', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#df9896] focus:border-transparent"
                                            placeholder={t('Enter Your Review')}
                                            required
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="bg-[#df9896] hover:bg-[#c88684] text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center disabled:opacity-50"
                                    >
                                        <Send className="w-4 h-4 me-2" />
                                        {processing ? 'Submitting...' : t('Submit Review')}
                                    </button>
                                </form>
                            </div>

                            {/* All Reviews */}
                            <div>
                                <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Customer Feedback')}</span>
                                <h2 className="text-3xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{t('All Reviews')}</h2>
                                <div className="w-16 h-1 bg-[#df9896] rounded-full mb-4"></div>

                                <div className="bg-white rounded-lg p-6 shadow-md max-h-[500px] overflow-auto space-y-4">
                                    {reviews.length > 0 ? (
                                        reviews.map((review) => (
                                            <div key={review.id} className="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                                <div className="flex items-center justify-between mb-3">
                                                    <h4 className="font-bold text-gray-800">{review.name}</h4>
                                                    <div className="flex items-center">
                                                        {renderStars(review.rating, 'h-4 w-4')}
                                                    </div>
                                                </div>
                                                <p className="text-gray-800 text-sm">{review.review}</p>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="bg-gray-50 rounded-lg p-4 border border-gray-100 text-center">
                                            <p className="text-gray-600">{t('No reviews yet. Be the first to review!')}</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Related Services Section */}
                {related_services.length > 0 && (
                    <section className="lg:pt-12 pt-8">
                        <div className="container mx-auto px-4">
                            <div className="text-center lg:mb-12 sm:mb-8 mb-6">
                                <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">{t('Related Services')}</h2>
                                <p className="md:text-lg text-[16px] text-gray-800">{t('Explore our other premium treatments')}</p>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                {related_services.slice(0, 3).map((relatedService) => (
                                    <div key={relatedService.id} className="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow h-full flex flex-col min-h-[400px]">
                                        <a href={route('beauty-spa.service.detail', { userSlug: slug, service: relatedService.id })}>
                                            <img
                                                src={relatedService.service_image ? getImagePath(relatedService.service_image, pageProps) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/default.jpg', pageProps)}
                                                alt={relatedService.name}
                                                className="w-full h-64 object-cover"
                                            />
                                        </a>
                                        <div className="sm:p-6 p-4 h-full flex flex-col justify-between">
                                            <div>
                                                <h3 className="text-xl font-bold mb-2 text-[#df9896]">{relatedService.name}</h3>
                                                <p className="text-gray-800 mb-4">{relatedService.description.substring(0, 120)}...</p>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-[#df9896] font-bold">{t('From')} {formatCurrency(relatedService.price, pageProps)}</span>
                                                <a href={route('beauty-spa.booking', { userSlug: slug })} className="text-[#c88684] hover:underline">{t('Book Now')}</a>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {related_services.length > 3 && (
                                <div className="text-center mt-8">
                                    <a
                                        href={route('beauty-spa.services', { userSlug: slug })}
                                        className="bg-[#df9896] hover:bg-[#c88684] text-white font-bold py-3 px-8 rounded-lg transition-colors inline-flex items-center"
                                    >
                                        {t('View All Services')}
                                    </a>
                                </div>
                            )}
                        </div>
                    </section>
                )}
            </main>
        </Layout>
    );
}