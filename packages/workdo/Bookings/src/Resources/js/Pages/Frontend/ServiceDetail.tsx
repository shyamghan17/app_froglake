import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import PublicLayout from './components/PublicLayout';
import PageHeader from '../../components/PageHeader';
import { Input, Button, Label, Textarea, Image } from './components';
import { formatDate, getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import { X, Star } from 'lucide-react';

interface Item {
    id: number;
    name: string;
    description?: string;
    image?: string;
}

interface ServiceDetailProps {
    title: string;
    userSlug?: string;
    serviceId: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
    item?: Item;
    duration?: any;
    reviews?: any[];
    averageRating?: number;
    totalReviews?: number;
}

const ServiceDetail: React.FC<ServiceDetailProps> = ({ title, userSlug, serviceId, brandSettings, colorSettings, socialLinks, customPages, footerServices, pageSettings, item, duration, reviews = [], averageRating = 0, totalReviews = 0 }) => {
    const { t } = useTranslation();
    const pageSettingsData = pageSettings?.service_detail || {};
    const headerSection = pageSettingsData?.header || {};
    const infoCardsSection = pageSettingsData?.info_cards || {};
    const reviewsSection = pageSettingsData?.reviews || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';
    const [selectedRating, setSelectedRating] = useState(0);
    const [showReviewModal, setShowReviewModal] = useState(false);
    const [reviewForm, setReviewForm] = useState({
        name: '',
        email: '',
        comment: ''
    });
    const [errors, setErrors] = useState({
        name: '',
        email: '',
        comment: '',
        rating: ''
    });

    const handleStarClick = (rating: number) => {
        setSelectedRating(rating);
    };

    const validateForm = () => {
        const newErrors = {
            name: '',
            email: '',
            comment: '',
            rating: ''
        };
        
        if (!reviewForm.name.trim()) {
            newErrors.name = t('Name is required');
        }
        
        if (!reviewForm.email.trim()) {
            newErrors.email = t('Email is required');
        } else if (!/\S+@\S+\.\S+/.test(reviewForm.email)) {
            newErrors.email = t('Please enter a valid email');
        }
        
        if (!reviewForm.comment.trim()) {
            newErrors.comment = t('Review comment is required');
        }
        
        if (selectedRating === 0) {
            newErrors.rating = t('Please select a rating');
        }
        
        setErrors(newErrors);
        return !Object.values(newErrors).some(error => error !== '');
    };

    const handleReviewSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        router.post(userSlug ? route('booking.reviews.submit', {userSlug: userSlug}) : '#', {
            name: reviewForm.name,
            email: reviewForm.email,
            rating: selectedRating,
            comment: reviewForm.comment,
            item_id: serviceId
        }, {
            onSuccess: () => {
                setShowReviewModal(false);
                setReviewForm({ name: '', email: '', comment: '' });
                setSelectedRating(0);
                setErrors({ name: '', email: '', comment: '', rating: '' });
            },
            onError: (errors) => {
                console.error('Error submitting review:', errors);
            }
        });
    };

    return (
        <PublicLayout title={title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            <PageHeader 
                title={headerSection.title || 'Service Details'} 
                description={headerSection.description || 'Complete information about our professional services'} 
                bgColor={primaryColor}
            />

            {/* Main Content */}
            <section className="lg:py-16 py-10">
                <div className="container mx-auto px-4">
                    <div className="w-full">
                        <div>
                            <div className="bg-white border border-gray-200 rounded-lg overflow-hidden lg:mb-8 mb-5 shadow-lg">
                                <div className="grid lg:grid-cols-3 gap-0">
                                    <div className="lg:col-span-2 relative pt-[45%]">
                                        <Image 
                                            src={item?.image ? getImagePath(item.image) : getImagePath('packages/workdo/Bookings/src/assets/images/default.jpg')} 
                                            alt={item?.name || 'Service Details'}
                                            className="absolute inset-0 w-full h-full object-cover"
                                        />
                                    </div>
                                    <div className="lg:p-8 md:p-6 p-4 flex flex-col justify-center bg-gradient-to-br from-gray-50 to-white">
                                        <h2 className="text-2xl lg:text-4xl font-bold md:mb-6 mb-4" style={{ color: primaryColor }}>
                                            {item?.name || 'Professional Service'}
                                        </h2>
                                        <p className="text-gray-700 md:text-lg text-sm leading-relaxed mb-4">
                                            {item?.description || 'Transform your experience with our professional service that combines years of expertise with the latest techniques.'}
                                        </p>
                                        <div className="space-y-4">
                                            <div className="flex items-center bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                                <div className="md:w-12 md:h-12 w-10 h-10 rounded-full flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                                                    <i className="fas fa-clock text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <span className="font-semibold text-gray-800 block">{t('Duration')}</span>
                                                    <span className="text-gray-600">{duration?.duration || ''}</span>
                                                </div>
                                            </div>
                                            <div className="flex items-center bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                                <div className="md:w-12 md:h-12 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-4">
                                                    <i className="fas fa-star text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <span className="font-semibold text-gray-800 block">{t('Rating')}</span>
                                                    <span className="text-gray-600">{averageRating}/5 ({totalReviews} {totalReviews === 1 ? t('review') : t('reviews')})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            {/* Customer Reviews */}
                            <div className="bg-white border-gray-200 border rounded-lg lg:p-6 p-4">
                                <div className="flex items-center justify-between md:mb-6 mb-4 flex-wrap gap-3">
                                    <h3 className="md:text-2xl text-xl font-bold" style={{ color: primaryColor }}>{reviewsSection.section_title || 'Customer Reviews'}</h3>
                                    <Button 
                                        onClick={() => setShowReviewModal(true)}
                                        variant="primary"
                                        className="flex items-center gap-2"
                                        primaryColor={primaryColor}
                                    >
                                        <i className="fas fa-edit"></i>{reviewsSection.button_text || 'Write Review'}
                                    </Button>
                                </div>
                                <div className="lg:space-y-6 space-y-4 max-h-[400px] overflow-auto">
                                    {reviews.length > 0 ? reviews.map((review, index) => (
                                        <div key={index} className="bg-gray-50 lg:p-6 p-4 rounded-lg border border-gray-200">
                                            <div className="flex items-start justify-between mb-3">
                                                <div>
                                                    <h4 className="font-semibold text-gray-800">{review.name}</h4>
                                                    <div className="flex items-center mt-1">
                                                        {[1,2,3,4,5].map(star => (
                                                            <Star key={star} className={`w-4 h-4 ${star <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}`} />
                                                        ))}
                                                        <span className="text-gray-500 text-sm ml-2">{formatDate(review.created_at)}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p className="text-gray-800 leading-relaxed">{review.comment}</p>
                                        </div>
                                    )) : (
                                        <div className="text-center py-8 text-gray-500">
                                            <p>{reviewsSection.empty_message || t('No reviews yet. Be the first to write a review!')}</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            {/* Review Modal */}
            {showReviewModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div className="bg-white rounded-lg shadow-2xl max-w-md lg:p-6 p-4 w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div className="flex justify-between items-center mb-3">
                            <h3 className="md:text-2xl text-xl font-bold">{reviewsSection.modal_title || 'Write a Review'}</h3>
                            <Button 
                                onClick={() => setShowReviewModal(false)}
                                variant="outline"
                                size="sm"
                                primaryColor={primaryColor}
                            >
                                <X className="h-5 w-5" />
                            </Button>
                        </div>
                        <form onSubmit={handleReviewSubmit}>
                            <div className="mb-4">
                                <Label required>{t('Your Name')}</Label>
                                <Input 
                                    value={reviewForm.name}
                                    onChange={(e) => {
                                        setReviewForm({...reviewForm, name: e.target.value});
                                        if (errors.name) setErrors({...errors, name: ''});
                                    }}
                                    placeholder={reviewsSection.form_placeholders?.name || t('Enter your name')}
                                    className={errors.name ? 'border-red-500' : ''}
                                    required 
                                />
                                {errors.name && <span className="text-red-500 text-sm mt-1">{errors.name}</span>}
                            </div>
                            <div className="mb-4">
                                <Label required>{t('Your Email')}</Label>
                                <Input 
                                    type="email"
                                    value={reviewForm.email}
                                    onChange={(e) => {
                                        setReviewForm({...reviewForm, email: e.target.value});
                                        if (errors.email) setErrors({...errors, email: ''});
                                    }}
                                    placeholder={reviewsSection.form_placeholders?.email || t('Enter your email')}
                                    className={errors.email ? 'border-red-500' : ''}
                                    required 
                                />
                                {errors.email && <span className="text-red-500 text-sm mt-1">{errors.email}</span>}
                            </div>
                            <div className="mb-4">
                                <Label required>{t('Rating')}</Label>
                                <div className="flex cursor-pointer">
                                    {[1,2,3,4,5].map(star => (
                                        <span 
                                            key={star}
                                            onClick={() => {
                                                handleStarClick(star);
                                                if (errors.rating) setErrors({...errors, rating: ''});
                                            }}
                                            className={`text-3xl mr-1 cursor-pointer hover:scale-110 transition-transform ${star <= selectedRating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-400'}`}
                                        >
                                            ★
                                        </span>
                                    ))}
                                </div>
                                {errors.rating && <span className="text-red-500 text-sm mt-1">{errors.rating}</span>}
                            </div>
                            <div className="md:mb-6 mb-4">
                                <Label required>{t('Your Review')}</Label>
                                <Textarea 
                                    value={reviewForm.comment}
                                    onChange={(e) => {
                                        setReviewForm({...reviewForm, comment: e.target.value});
                                        if (errors.comment) setErrors({...errors, comment: ''});
                                    }}
                                    placeholder={reviewsSection.form_placeholders?.comment || t('Tell us about your experience...')} 
                                    rows={4}
                                    className={errors.comment ? 'border-red-500' : ''}
                                    required
                                />
                                {errors.comment && <span className="text-red-500 text-sm mt-1">{errors.comment}</span>}
                            </div>
                            <div className="flex gap-3 sm:flex-row flex-col">
                                <Button 
                                    type="button" 
                                    onClick={() => setShowReviewModal(false)}
                                    variant="secondary"
                                    className="flex-1"
                                >
                                    {t('Cancel')}
                                </Button>
                                <Button 
                                    type="submit"
                                    variant="primary"
                                    className="flex-1"
                                    primaryColor={primaryColor}
                                >
                                    {reviewsSection.submit_button || t('Submit Review')}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </PublicLayout>
    );
};

export default ServiceDetail;