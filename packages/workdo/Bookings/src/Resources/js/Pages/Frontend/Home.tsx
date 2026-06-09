import React, { useState, useEffect } from 'react';
import PublicLayout from './components/PublicLayout';
import { Link, usePage } from '@inertiajs/react';
import { Calendar, Clock, Users, Star, CheckCircle, ArrowRight, Phone, MapPin, Mail, ArrowLeft, User } from 'lucide-react';
import { getImagePath, formatDate, formatTime } from '@/utils/helpers';
import { TimeSlotPicker } from '../../components/time-slot-picker';
import { usePageButtons } from '@/hooks/usePageButtons';
import { Input, Select, Label, Button, Textarea, Image, Card } from './components';
import { useTranslation } from 'react-i18next';
import axios from 'axios';
import SocialLinks from '@/components/SocialLinks';
import { RadioGroup } from '@/components/ui/radio-group';
import { useFormFields } from '@/hooks/useFormFields';

interface Item {
    id: number;
    name: string;
    description?: string;
    image?: string;
}

interface PackageItem {
    id: number;
    name: string;
    item_id: number;
}

interface User {
    id: number;
    name: string;
}

interface HomeProps {
    title: string;
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    bannerSettings?: any;
    socialLinks?: any[];
    customPages?: any[];
    footerServices?: any[];
    pageSettings?: any;
    items?: Item[];
    packageItems?: PackageItem[];
    users?: User[];
    closedDays?: string[];
}

export default function Home({ title, brandSettings, colorSettings, bannerSettings, socialLinks, customPages, footerServices, pageSettings, userSlug, items = [], packageItems = [], users = [], closedDays = [] }: HomeProps) {
    const [selectedDate, setSelectedDate] = useState('');
    const { t } = useTranslation();
    const [selectedStaff, setSelectedStaff] = useState('');
    const [selectedItem, setSelectedItem] = useState('');
    const [selectedPackageItem, setSelectedPackageItem] = useState('');
    const [selectedTimeSlot, setSelectedTimeSlot] = useState<{start_time: string, end_time: string, label: string} | null>(null);
    const [filteredPackageItems, setFilteredPackageItems] = useState<PackageItem[]>([]);
    const [currentMonth, setCurrentMonth] = useState(new Date());
    const [currentStep, setCurrentStep] = useState(1);
    const [errors, setErrors] = useState<{[key: string]: string[]}>({});
    const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<string>('online');
    const [formData, setFormData] = useState({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        description: '',
        paymentOption: 'online'
    });
    
    const pageProps = usePage().props;
    
    const paymentButtons = usePageButtons('bookingPayment', {
        selectedMethod: selectedPaymentMethod,
        onMethodChange: (method: string) => {
            setSelectedPaymentMethod(method);
            handleFormChange('paymentOption', method);
        }
    }, false);
    
    const heroSection = pageSettings?.home?.hero || {};
    const bookingSection = pageSettings?.home?.booking || {};
    const bookingFormSection = bookingSection?.form || {};
    const statsSection = pageSettings?.home?.stats || {};
    const servicesSection = pageSettings?.home?.services || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';

    const integrationFields = useFormFields('getIntegrationFields', {}, () => { }, {}, 'create', t, 'Bookings');

    useEffect(() => {
        if (selectedItem) {
            const itemPackages = packageItems.filter(pkg => pkg.item_id.toString() === selectedItem);
            setFilteredPackageItems(itemPackages);
            setSelectedPackageItem('');
            setSelectedTimeSlot(null);
        } else {
            setFilteredPackageItems([]);
            setSelectedPackageItem('');
            setSelectedTimeSlot(null);
        }
    }, [selectedItem, packageItems]);

    const handleDateChange = (date: string) => {
        setSelectedDate(date);
        setSelectedTimeSlot(null);
    };

    const handleContinueToDetails = () => {
        if (selectedDate && selectedItem && selectedPackageItem && selectedTimeSlot) {
            setCurrentStep(2);
        }
    };

    const handleBackToCalendar = () => {
        setCurrentStep(1);
    };

    const handleFormChange = (field: string, value: string) => {
        setFormData(prev => ({ ...prev, [field]: value }));
    };

    const handleBookingSubmit = async () => {
        setErrors({});
        
        // Validate required fields
        const validationErrors: {[key: string]: string[]} = {};
        if (!formData.firstName) validationErrors['formData.firstName'] = ['First name is required'];
        if (!formData.lastName) validationErrors['formData.lastName'] = ['Last name is required'];
        if (!formData.email) validationErrors['formData.email'] = ['Email is required'];
        if (!formData.phone) validationErrors['formData.phone'] = ['Phone is required'];
        
        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            return;
        }
        
        // Handle online payment - go to payment first
        if (paymentButtons.length > 0) {
            const selectedButton = paymentButtons.find(btn => btn.id.includes(selectedPaymentMethod)) || paymentButtons[0];
            const dataUrl = (selectedButton as any)?.dataUrl;
            
            if (dataUrl) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = dataUrl;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }
                
                const bookingData = {
                    selectedDate,
                    selectedStaff: selectedStaff || '',
                    selectedItem,
                    selectedPackage: selectedPackageItem,
                    selectedPackageItem: selectedPackageItem,
                    'selectedTimeSlot[start_time]': selectedTimeSlot?.start_time,
                    'selectedTimeSlot[end_time]': selectedTimeSlot?.end_time,
                    'selectedTimeSlot[label]': selectedTimeSlot?.label,
                    'formData[firstName]': formData.firstName,
                    'formData[lastName]': formData.lastName,
                    'formData[email]': formData.email,
                    'formData[phone]': formData.phone,
                    'formData[description]': formData.description,
                    'formData[paymentOption]': selectedPaymentMethod
                };
                
                console.log('Booking Data being sent:', bookingData);
                console.log('Selected Package Item:', selectedPackageItem);
                
                Object.entries(bookingData).forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value?.toString() || '';
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
                return;
            }
        }
    };

    const navigateMonth = (direction: 'prev' | 'next') => {
        const newMonth = new Date(currentMonth);
        if (direction === 'prev') {
            newMonth.setMonth(currentMonth.getMonth() - 1);
        } else {
            newMonth.setMonth(currentMonth.getMonth() + 1);
        }
        setCurrentMonth(newMonth);
    };

    const getLocalDateString = (date: Date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    return (
        <PublicLayout title={title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            {/* Hero Section */}
            <section className="relative pt-32 pb-16 overflow-hidden" style={{ backgroundColor: primaryColor }}>
                <div className="absolute -bottom-14 -left-14 w-48 h-48 bg-white rounded-full opacity-10"></div>
                <div className="absolute -top-4 -right-14 w-48 h-48 bg-white rounded-full opacity-10"></div>
                <div className="container relative mx-auto px-4 z-10">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="text-center lg:text-left">
                            <h2 className="text-4xl md:text-5xl xl:text-6xl font-bold text-white mb-6 leading-tight">
                                <span className="relative">{bannerSettings?.title || heroSection.title || 'Modern Service Booking'}</span>
                            </h2>
                            <p className="text-green-50 text-lg xl:text-xl mb-10 max-w-xl mx-auto lg:mx-0">
                                {bannerSettings?.description || heroSection.description || 'Connect with expert professionals and book appointments seamlessly. Your perfect service experience starts here.'}
                            </p>
                            <div className="flex flex-wrap gap-4 justify-center lg:justify-start">
                                <Link href={heroSection.button_url_two || '#services'} className="inline-block bg-transparent text-white font-semibold px-6 py-3 rounded-lg border-2 border-white hover:bg-white transition-all" style={{ '--hover-color': primaryColor } as React.CSSProperties} onMouseEnter={(e) => e.currentTarget.style.color = primaryColor} onMouseLeave={(e) => e.currentTarget.style.color = 'white'}>
                                    {heroSection.button_text_two || 'Explore Services'}
                                </Link>
                            </div>
                        </div>
                        <div className="relative mx-auto max-w-md lg:max-w-full">
                            <div className="relative rounded-2xl overflow-hidden transform hover:rotate-1 transition-transform duration-500">
                                <Image src={bannerSettings?.image ? getImagePath(bannerSettings.image, pageProps) : (heroSection.image ? getImagePath(heroSection.image, pageProps) : getImagePath('packages/workdo/Bookings/src/assets/images/banner-img.png', pageProps))} alt="Service Booking Platform" className="w-full h-auto" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Booking Section */}
            <section id="booking" className="py-16 relative bg-gray-50">
                <div className="absolute inset-0 bg-gray-50"></div>
                <div className="absolute top-0 right-0 w-1/3 h-full opacity-5 rounded-l-full" style={{ backgroundColor: primaryColor }}></div>
                <div className="container mx-auto px-4 relative z-10">
                    <div className="flex flex-col items-center text-center mb-16">
                        <div className="inline-flex items-center justify-center w-20 h-20 text-white rounded-full mb-6 transform transition-transform hover:scale-110" style={{ backgroundColor: primaryColor }}>
                            <SocialLinks 
                                icon={bookingSection?.icon || 'Calendar'}
                                className="h-8 w-8" 
                            />
                        </div>
                        <h2 className="text-3xl md:text-4xl font-bold mb-4">{bookingSection.title || 'Book Your Service Online'}</h2>
                        <p className="text-gray-600 max-w-xl mx-auto">
                            {bookingSection.description || 'Schedule your appointment in minutes with our easy-to-use booking system. Select your preferred service, date, and time for a seamless booking experience.'}
                        </p>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div>
                            {/* Booking Step 1 - Date & Time Selection */}
                            {currentStep === 1 && (
                                <div className="bg-white rounded-2xl shadow-lg overflow-hidden relative">
                                    <div className="h-1.5 w-full" style={{ backgroundColor: primaryColor }}></div>
                                    <div className="p-6">
                                        <h3 className="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                            <span className="w-10 h-10 rounded-full flex items-center justify-center mr-3" style={{ backgroundColor: primaryColor }}>
                                                <Calendar className="h-5 w-5 text-white" />
                                            </span>
                                            {bookingSection.form_title || 'Pick Your Date & Time'}
                                        </h3>

                                    <div className="bg-gray-50 p-4 rounded-xl mb-6">
                                        <div className="flex justify-between items-center mb-4">
                                            <Button 
                                                onClick={() => navigateMonth('prev')}
                                                variant="outline"
                                                size="sm"
                                                primaryColor={primaryColor}
                                            >
                                                <ArrowRight className="h-4 w-4 rotate-180" />
                                            </Button>
                                            <h4 className="text-lg font-semibold">{currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</h4>
                                            <Button 
                                                onClick={() => navigateMonth('next')}
                                                variant="outline"
                                                size="sm"
                                                primaryColor={primaryColor}
                                            >
                                                <ArrowRight className="h-4 w-4" />
                                            </Button>
                                        </div>

                                        <div className="grid grid-cols-7 gap-1 text-center mb-2">
                                            {(bookingSection.calendar_navigation?.days || ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']).map(day => (
                                                <Label key={day} className="text-xs font-medium text-gray-500">{day}</Label>
                                            ))}
                                        </div>

                                        <div className="grid grid-cols-7 gap-1">
                                            {Array.from({ length: 35 }, (_, i) => {
                                                const today = new Date();
                                                today.setHours(0, 0, 0, 0);
                                                const firstDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
                                                const startDate = new Date(firstDay);
                                                startDate.setDate(startDate.getDate() - firstDay.getDay());
                                                
                                                const currentDate = new Date(startDate);
                                                currentDate.setDate(startDate.getDate() + i);
                                                currentDate.setHours(0, 0, 0, 0);
                                                
                                                const isCurrentMonth = currentDate.getMonth() === currentMonth.getMonth();
                                                const isToday = currentDate.getTime() === today.getTime();
                                                const dateStr = getLocalDateString(currentDate);
                                                const isSelected = selectedDate === dateStr;
                                                const isPast = currentDate.getTime() < today.getTime();
                                                const dayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                                                const isClosedDay = closedDays.includes(dayName);
                                                
                                                return (
                                                    <div 
                                                        key={i} 
                                                        className={`text-center p-2 rounded cursor-pointer ${
                                                            isCurrentMonth && !isPast && !isClosedDay
                                                                ? isSelected
                                                                    ? 'text-white font-medium'
                                                                    : isToday
                                                                        ? 'bg-gray-200 font-medium'
                                                                        : 'text-gray-600 hover:bg-gray-100'
                                                                : 'text-gray-400 cursor-not-allowed'
                                                        }`}
                                                        style={isSelected ? { backgroundColor: primaryColor } : {}}
                                                        onClick={() => {
                                                            if (isCurrentMonth && !isPast && !isClosedDay) {
                                                                handleDateChange(getLocalDateString(currentDate));
                                                            }
                                                        }}
                                                    >
                                                        {currentDate.getDate()}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    <div className="space-y-4 mb-6">
                                        <div>
                                            <Label>{t('Service Type')}</Label>
                                            <Select 
                                                value={selectedItem}
                                                onChange={(e) => setSelectedItem(e.target.value)}
                                                options={items.map(item => ({ value: item.id, label: item.name }))}
                                                placeholder={t('Select Service')}
                                            />
                                        </div>
                                        <div>
                                            <Label>{t('Service Package')}</Label>
                                            <Select 
                                                value={selectedPackageItem}
                                                onChange={(e) => setSelectedPackageItem(e.target.value)}
                                                disabled={!selectedItem}
                                                options={filteredPackageItems.map(pkg => ({ value: pkg.id, label: pkg.name }))}
                                                placeholder={t('Select Package')}
                                            />
                                        </div>
                                    </div>

                                    {selectedDate && selectedItem && selectedPackageItem && (
                                        <div className="mb-6">
                                            <TimeSlotPicker
                                                date={selectedDate}
                                                staffId={selectedStaff}
                                                itemId={selectedItem}
                                                packageId={selectedPackageItem}
                                                selectedSlot={selectedTimeSlot}
                                                onSlotSelect={setSelectedTimeSlot}
                                                slotDuration={30}
                                                autoLoad={true}
                                                primaryColor={primaryColor}
                                            />
                                        </div>
                                    )}

                                    {currentStep === 1 && (
                                        <Button 
                                            onClick={handleContinueToDetails}
                                            disabled={!selectedDate || !selectedItem || !selectedPackageItem || !selectedTimeSlot}
                                            className="w-full flex items-center justify-center"
                                            variant="primary"
                                            primaryColor={primaryColor}
                                        >
                                            {bookingSection.button_text || t('Continue to Details')}
                                            <ArrowRight className="ml-2 h-4 w-4" />
                                        </Button>
                                    )}
                                    </div>
                                </div>
                            )}

                            {/* Booking Step 2 - User Details Form */}
                            {currentStep === 2 && (
                                <div className="bg-white rounded-2xl shadow-lg overflow-hidden relative">
                                    <div className="h-1.5 w-full" style={{ backgroundColor: primaryColor }}></div>
                                    <div className="sm:p-8 p-4">
                                    <div className="flex items-center mb-6">
                                        <Button
                                            onClick={handleBackToCalendar}
                                            variant="outline"
                                            size="sm"
                                            className="mr-4"
                                        >
                                            <ArrowLeft className="h-5 w-5" />
                                        </Button>
                                        <h3 className="text-2xl font-bold text-gray-800 flex items-center">
                                            <span className="w-10 h-10 rounded-full flex items-center justify-center mr-3" style={{ backgroundColor: primaryColor }}>
                                                <User className="h-5 w-5 text-white" />
                                            </span>
                                            {t('Your Details')}
                                        </h3>
                                    </div>

                                    {/* Selected Appointment Summary */}
                                    <div className="bg-blue-50 p-4 rounded-xl mb-6">
                                        <h4 className="font-semibold mb-2" style={{ color: primaryColor }}>{bookingFormSection.appointment_summary_title || 'Your Appointment'}</h4>
                                        <div className="grid sm:grid-cols-2 grid-cols-1 gap-2 text-sm">
                                            <div>
                                                <p className="text-gray-600">{t('Date:')}</p>
                                                <p className="font-medium text-gray-800">{formatDate(selectedDate, pageProps)}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-600">{t('Time:')}</p>
                                                <p className="font-medium text-gray-800">{formatTime(selectedTimeSlot?.start_time, pageProps)} - {formatTime(selectedTimeSlot?.end_time, pageProps)}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-600">{t('Service:')}</p>
                                                <p className="font-medium text-gray-800">{items.find(item => item.id.toString() === selectedItem)?.name}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-600">{t('Package:')}</p>
                                                <p className="font-medium text-gray-800">{filteredPackageItems.find(pkg => pkg.id.toString() === selectedPackageItem)?.name}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Personal Information Form */}
                                    <div className="space-y-4 mb-6">
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <Label required>{t('First Name')}</Label>
                                                <Input
                                                    value={formData.firstName}
                                                    onChange={(e) => handleFormChange('firstName', e.target.value)}
                                                    placeholder={t('First Name')}
                                                    className={errors['formData.firstName'] ? 'border-red-500' : ''}
                                                    required
                                                />
                                                {errors['formData.firstName'] && (
                                                    <p className="text-red-500 text-sm mt-1">{errors['formData.firstName'][0]}</p>
                                                )}
                                            </div>
                                            <div>
                                                <Label required>{t('Last Name')}</Label>
                                                <Input
                                                    value={formData.lastName}
                                                    onChange={(e) => handleFormChange('lastName', e.target.value)}
                                                    placeholder={t('Last Name')}
                                                    className={errors['formData.lastName'] ? 'border-red-500' : ''}
                                                    required
                                                />
                                                {errors['formData.lastName'] && (
                                                    <p className="text-red-500 text-sm mt-1">{errors['formData.lastName'][0]}</p>
                                                )}
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <Label required>{t('Email Address')}</Label>
                                                <Input
                                                    type="email"
                                                    value={formData.email}
                                                    onChange={(e) => handleFormChange('email', e.target.value)}
                                                    placeholder={t('Email Address')}
                                                    className={errors['formData.email'] ? 'border-red-500' : ''}
                                                    required
                                                />
                                                {errors['formData.email'] && (
                                                    <p className="text-red-500 text-sm mt-1">{errors['formData.email'][0]}</p>
                                                )}
                                            </div>
                                            <div>
                                                <Label required>{t('Phone Number')}</Label>
                                                <Input
                                                    type="tel"
                                                    value={formData.phone}
                                                    onChange={(e) => handleFormChange('phone', e.target.value)}
                                                    placeholder={t('Phone Number')}
                                                    className={errors['formData.phone'] ? 'border-red-500' : ''}
                                                    required
                                                />
                                                {errors['formData.phone'] && (
                                                    <p className="text-red-500 text-sm mt-1">{errors['formData.phone'][0]}</p>
                                                )}
                                            </div>
                                        </div>

                                        <div>
                                            <Label>{t('Special Requests (Optional)')}</Label>
                                            <Textarea
                                                value={formData.description}
                                                onChange={(e) => handleFormChange('description', e.target.value)}
                                                rows={2}
                                                placeholder={t('Special Requests')}
                                            />
                                        </div>

                                        <div>
                                            <Label className="mb-3">{t('Payment Type')}</Label>
                                            <div className="grid grid-cols-1 gap-4">
                                                <div className="relative">
                                                    <Input
                                                        type="radio"
                                                        name="payment_option"
                                                        id="payment-online"
                                                        value="online"
                                                        checked={true}
                                                        className="peer sr-only"
                                                    />
                                                    <Label
                                                        htmlFor="payment-online"
                                                        className="flex items-center justify-center p-4 bg-white border-2 ring-2 ring-opacity-20 rounded-lg cursor-pointer transition-all"
                                                        style={{ borderColor: primaryColor, ringColor: primaryColor }}
                                                    >
                                                        <CheckCircle className="mr-2 h-5 w-5" style={{ color: primaryColor }} />
                                                        <span className="font-medium">{bookingFormSection.online_payment_text || t('Online Payment')}</span>
                                                        <CheckCircle className="ml-auto h-5 w-5" style={{ color: primaryColor }} />
                                                    </Label>
                                                </div>
                                                
                                                <div className="mt-4 p-4 bg-gray-50 rounded-lg border">
                                                    <h4 className="text-sm font-medium text-gray-700 mb-3">
                                                        {paymentButtons.length > 0 ? 'Select Payment Method:' : 'Online Payment Selected'}
                                                    </h4>
                                                    {paymentButtons.length > 0 ? (
                                                        <>
                                                            <style>{`
                                                                .payment-radio [data-state="checked"] {
                                                                    border-color: ${primaryColor} !important;
                                                                    color: ${primaryColor} !important;
                                                                }
                                                                .payment-radio [data-state="checked"] svg {
                                                                    color: ${primaryColor} !important;
                                                                }
                                                            `}</style>
                                                            <RadioGroup 
                                                                value={selectedPaymentMethod} 
                                                                onValueChange={(value) => {
                                                                    setSelectedPaymentMethod(value);
                                                                    handleFormChange('paymentOption', value);
                                                                }}
                                                                className="payment-radio"
                                                            >
                                                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                                {paymentButtons.map((button) => (
                                                                    <div key={button.id} className="w-full">{button.component}</div>
                                                                ))}
                                                            </div>
                                                            </RadioGroup>
                                                        </>
                                                    ) : (
                                                        <div>
                                                            <div className={`flex items-center p-3 bg-white border-2 rounded-lg`} style={{ borderColor: primaryColor }}>
                                                                <CheckCircle className="h-5 w-5 mr-3" style={{ color: primaryColor }} />
                                                                <span className="font-medium">{t("Online Payment Selected")}</span>
                                                                <CheckCircle className="ml-auto h-5 w-5" style={{ color: primaryColor }} />
                                                            </div>
                                                            <p className="text-sm text-gray-600 mt-2">
                                                                {t("You will be redirected to the payment gateway after confirming your booking.")}
                                                            </p>
                                                        </div>
                                                    )}
                                                    {errors['paymentMethod'] && (
                                                        <p className="text-red-500 text-sm mt-2">{errors['paymentMethod'][0]}</p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <Button
                                        onClick={handleBookingSubmit}
                                        className="w-full flex items-center justify-center"
                                        variant="primary"
                                    >
                                        <CheckCircle className="mr-2 h-5 w-5" />
                                        {bookingFormSection.confirm_button_text || 'Confirm Booking'}
                                    </Button>
                                    </div>
                                </div>
                            )}

                            {/* Booking Step 3 - Confirmation */}
                            {currentStep === 3 && (
                                <div className="bg-white rounded-2xl shadow-lg overflow-hidden relative">
                                    <div className="h-1.5 w-full" style={{ backgroundColor: primaryColor }}></div>
                                    <div className="sm:p-8 p-4 text-center">
                                        <div className="mb-6 flex justify-center">
                                            <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                                                <CheckCircle className="text-green-500 h-12 w-12" />
                                            </div>
                                        </div>
                                        <h3 className="text-2xl font-bold text-gray-800 mb-2">
                                            {bookingFormSection.confirmation_title || 'Booking Confirmed!'}
                                        </h3>
                                        <p className="text-gray-600 mb-6">
                                            {bookingFormSection.confirmation_message || 'Your appointment has been successfully scheduled.'}
                                        </p>

                                        {/* Booking Details Summary */}
                                        <div className="bg-gray-50 p-6 rounded-xl mb-6 text-left">
                                            <h4 className="font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                                {bookingFormSection.confirmation_details_title || 'Appointment Details'}
                                            </h4>
                                            <div className="space-y-3">
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Date:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{selectedDate}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Time:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{selectedTimeSlot?.start_time} - {selectedTimeSlot?.end_time}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Service:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{items.find(item => item.id.toString() === selectedItem)?.name}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Package:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{filteredPackageItems.find(pkg => pkg.id.toString() === selectedPackageItem)?.name}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Name:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{formData.firstName} {formData.lastName}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Contact:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{formData.email} / {formData.phone}</div>
                                                </div>
                                                <div className="flex">
                                                    <div className="w-1/3 text-gray-600">{t('Payment:')}</div>
                                                    <div className="w-2/3 font-medium text-gray-800">{formData.paymentOption === 'online' ? t('Online Payment') : t('Pay on Service')}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <p className="text-gray-600 mb-6">
                                            {bookingFormSection.confirmation_email_message || 'A confirmation has been sent to your email with all the details.'}
                                        </p>

                                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                            <Button
                                                onClick={() => {
                                                    setCurrentStep(1);
                                                    setSelectedDate('');
                                                    setSelectedStaff('');
                                                    setSelectedItem('');
                                                    setSelectedPackageItem('');
                                                    setSelectedTimeSlot(null);
                                                    setFormData({
                                                        firstName: '',
                                                        lastName: '',
                                                        email: '',
                                                        phone: '',
                                                        description: '',
                                                        paymentOption: 'online'
                                                    });
                                                }}
                                                variant="secondary"
                                                className="flex items-center justify-center"
                                            >
                                                <ArrowLeft className="mr-2 h-5 w-5" />
                                                {bookingFormSection.return_home_button || 'Return to Home'}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="space-y-8">
                            <div className="bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
                                <div className="absolute -top-10 -right-10 w-40 h-40 bg-gray-100 rounded-full opacity-50"></div>
                                <h3 className="text-2xl font-bold text-gray-800 mb-6 relative z-10">{bookingSection.steps_title || 'Easy Booking Process'}</h3>
                                <div className="space-y-6 relative z-10">
                                    {(bookingSection.steps || [
                                        { title: 'Select Service & Time', description: 'Choose from our range of services and pick your preferred time slot' },
                                        { title: 'Provide Details', description: 'Fill in your contact information and any special requirements' },
                                        { title: 'Confirm Booking', description: 'Review your booking details and confirm your appointment' }
                                    ]).map((step, i) => (
                                        <div key={i} className="flex items-start">
                                            <div className="flex-shrink-0 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-4" style={{ backgroundColor: primaryColor }}>
                                                {i + 1}
                                            </div>
                                            <div>
                                                <h4 className="text-lg font-semibold mb-1" style={{ color: primaryColor }}>{step.title}</h4>
                                                <p className="text-gray-600">{step.description}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="bg-white rounded-xl shadow-lg p-6">
                                <h4 className="text-2xl font-bold text-gray-800 mb-6">{bookingSection.features_title || 'Why Book With Us?'}</h4>
                                <ul className="space-y-3 text-gray-600">
                                    {(bookingSection.features || [
                                        { text: 'Certified professionals for every service' },
                                        { text: 'Flexible scheduling to fit your busy lifestyle' },
                                        { text: 'Secure payment processing and data protection' }
                                    ]).map((feature, i) => (
                                        <li key={i} className="flex items-start">
                                            <CheckCircle className="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" style={{ color: primaryColor }} />
                                            {feature.text}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Stats Section */}
            <section className="py-16 bg-white relative overflow-hidden">
                <div className="absolute top-10 left-10 w-64 h-64 bg-gray-400 rounded-full opacity-20 filter blur-3xl"></div>
                <div className="absolute bottom-10 right-10 w-72 h-72 bg-gray-500 rounded-full opacity-10 filter blur-3xl"></div>
                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-bold mb-2">{statsSection.title || 'Trusted by Thousands Worldwide'}</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">
                            {statsSection.description || 'See why our booking solution has become the industry standard for service businesses'}
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {(statsSection.stats || []).map((stat, i) => {
                            return (
                            <div key={i} className="rounded-xl p-8 text-center transform transition duration-500 hover:scale-105" style={{ backgroundColor: primaryColor }}>
                                <div className="w-20 h-20 bg-white rounded-full flex items-center justify-center text-3xl mx-auto mb-6" style={{ color: primaryColor }}>
                                    <SocialLinks 
                                        icon={stat.icon}
                                        className="w-8 h-8" 
                                    />
                                </div>
                                <div className="text-5xl font-bold text-white mb-2">{stat.number}</div>
                                <p className="text-xl text-white">{stat.label}</p>
                                <div className="mt-4 h-1 w-16 bg-white mx-auto rounded-full"></div>
                                <p className="mt-4 text-white">{stat.description}</p>
                            </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Services Section */}
            <section id="services" className="py-16 bg-gray-50">
                <div className="container mx-auto px-4">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4">{servicesSection.title || 'Explore Our Premium Services'}</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">
                            {servicesSection.description || 'Discover the wide range of services that can be booked using our powerful addon solution'}
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                        {items.length > 0 ? items.slice(0, 6).map((item, i) => (
                            <div key={item.id} className="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                                <div className="relative">
                                    <Image 
                                        src={item.image ? getImagePath(item.image, pageProps) : getImagePath('packages/workdo/Bookings/src/assets/images/service-1.png', pageProps)} 
                                        alt={item.name} 
                                        className="w-full h-56 object-cover" 
                                    />
                                    {i === 0 && (
                                        <div className="absolute top-4 right-4 text-white text-sm px-3 py-1 rounded-full" style={{ backgroundColor: primaryColor }}>
                                            {servicesSection.card_popular_badge || 'Most Popular'}
                                        </div>
                                    )}
                                </div>
                                <div className="p-6">
                                    <div className="flex items-center mb-3">
                                        <div className="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <Star className="h-5 w-5" style={{ color: primaryColor }} />
                                        </div>
                                        <h3 className="text-xl font-bold" style={{ color: primaryColor }}>{item.name}</h3>
                                    </div>
                                    <p className="text-gray-600 mb-4">{item.description || 'Professional service with expert care and attention to detail.'}</p>
                                    <Link href={ userSlug ? route('booking.services.detail', { userSlug: userSlug, id: item.id }) : undefined} className="inline-flex items-center font-medium" style={{ color: primaryColor }}>
                                        {servicesSection.card_explore_text || 'Explore Services'}
                                        <ArrowRight className="ml-2 h-4 w-4" />
                                    </Link>
                                </div>
                            </div>
                        )) : null}
                    </div>
                    <div className="text-center">
                        <Link href={servicesSection.button_url || (userSlug ? route('booking.services', { userSlug }) : undefined)} className="inline-block px-6 py-2 border-2 font-semibold rounded-lg hover:text-white transition-colors duration-300" style={{ borderColor: primaryColor, color: primaryColor }} onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = primaryColor; e.currentTarget.style.color = 'white'; }} onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = primaryColor; }}>
                            {servicesSection.button_text || 'View All Services'}
                        </Link>
                    </div>
                </div>
            </section>

            {/* Integration Widgets (Tawk.to, WhatsApp, etc.) */}
            {integrationFields.map((field) => (
                <div key={field.id}>
                    {field.component}
                </div>
            ))}
        </PublicLayout>
    );
}