import React, { useState, useEffect } from 'react';
import Layout from './Layout';
import { useForm } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { User, Wrench, Calendar, Clock, Users, AlertTriangle, Phone, ChevronDown, CheckCircle } from 'lucide-react';
import { usePageButtons } from '@/hooks/usePageButtons';
import { RadioGroup } from '@/components/ui/radio-group';
import { formatCurrency } from '@/utils/helpers';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';


interface Service {
    id: number;
    name: string;
    price: number;
    offer_note?: string;
    time: string;
}

interface TimeSlot {
    start_time: string;
    end_time: string;
    display: string;
    available_seats: number;
}

interface Props {
    title?: string;
    services: Service[];
    userSlug: string;
    service_id?: number;
}

export default function Booking({ title = 'Booking', services = [], userSlug, service_id }: Props) {
    const { t } = useTranslation();
    const { props: pageProps } = usePage();
    const csrfToken = (pageProps as any).csrf_token || (pageProps as any)._token;

    const [showDetails, setShowDetails] = useState(false);
    const [timeSlots, setTimeSlots] = useState<TimeSlot[]>([]);
    const [serviceDetails, setServiceDetails] = useState<any>(null);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const [errors, setErrors] = useState<{[key: string]: string}>({});

    const { data: basicData, setData: setBasicData } = useForm({
        serviceDetail: service_id?.toString() || '',
        dateDetail: '',
        genderDetail: '',
        timeSlot: '',
        persons: ''
    });

    const { data: detailsData, setData: setDetailsData, post, processing } = useForm({
        name: '',
        email: '',
        phone_number: '',
        service: '',
        date: '',
        time_slot: '',
        person: '',
        gender: '',
        reference: '',
        additional_notes: '',
        payment_option: ''
    });

    const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<string>('Offline');

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        let processedValue = value;

        // Phone number validation - ensure it starts with + and country code
        if (name === 'phone_number' && processedValue) {
            // Remove any non-digit characters except +
            let cleanPhone = processedValue.replace(/[^+\d]/g, '');
            // If it doesn't start with +, add +91 (India code)
            if (cleanPhone && !cleanPhone.startsWith('+')) {
                cleanPhone = '+91' + cleanPhone;
            }
            processedValue = cleanPhone;
        }

        setDetailsData(name as keyof typeof detailsData, processedValue);
        
        // Clear error when user starts typing
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };
    
    const handleBlur = (e: React.FocusEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        const newErrors = { ...errors };

        if (name === 'email' && value.trim()) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                newErrors.email = t('Please enter a valid email format (e.g., test@example.com)');
            }
        }

        if (name === 'phone_number' && value.trim()) {
            if (!/^\+\d{9,15}$/.test(value)) {
                newErrors.phone_number = t('Phone number must start with + followed by 9-15 digits (e.g., +945875565)');
            }
        }

        setErrors(newErrors);
    };
    
    const validateDetailsForm = () => {
        const newErrors: { [key: string]: string } = {};
        let firstErrorField = '';

        if (!detailsData.name.trim()) {
            newErrors.name = t('Name is required');
            if (!firstErrorField) firstErrorField = 'name';
        }

        if (!detailsData.email.trim()) {
            newErrors.email = t('Email is required');
            if (!firstErrorField) firstErrorField = 'email';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(detailsData.email)) {
            newErrors.email = t('Please enter a valid email format (e.g., test@example.com)');
            if (!firstErrorField) firstErrorField = 'email';
        }

        if (!detailsData.phone_number.trim()) {
            newErrors.phone_number = t('Phone number is required');
            if (!firstErrorField) firstErrorField = 'phone_number';
        } else if (!/^\+\d{9,15}$/.test(detailsData.phone_number)) {
            newErrors.phone_number = t('Phone number must start with + followed by 9-15 digits (e.g., +945875565)');
            if (!firstErrorField) firstErrorField = 'phone_number';
        }

        setErrors(newErrors);

        // Focus on first error field
        if (firstErrorField) {
            setTimeout(() => {
                const element = document.getElementById(firstErrorField);
                if (element) {
                    element.focus();
                }
            }, 100);
        }

        return Object.keys(newErrors).length === 0;
    };

    const paymentButtons = usePageButtons('beautySpaPayment', {
        selectedMethod: selectedPaymentMethod,
        onMethodChange: (method: string) => {
            setSelectedPaymentMethod(method);
            setDetailsData('payment_option', method);
        }
    }, false);

    useEffect(() => {
        if (service_id) {
            setBasicData('serviceDetail', service_id.toString());
            loadServiceDetails(service_id.toString(), basicData.dateDetail);
        }
    }, [service_id]);

    useEffect(() => {
        if (basicData.serviceDetail && basicData.dateDetail) {
            loadTimeSlots();
            loadServiceDetails(basicData.serviceDetail, basicData.dateDetail);
        }
    }, [basicData.serviceDetail, basicData.dateDetail]);

    const loadServiceDetails = async (serviceId: string, date: string) => {
        if (!serviceId) {
            setServiceDetails(null);
            return;
        }

        const selectedService = services.find(s => s.id.toString() === serviceId);
        if (selectedService) {
            setServiceDetails({
                service_time: selectedService.time,
                formatted_price: selectedService.price,
                offer_note: selectedService.offer_note || '',
            });
        }
    };

    const loadTimeSlots = async () => {
        const { serviceDetail, dateDetail } = basicData;
        setError('');

        if (!serviceDetail || !dateDetail) {
            setTimeSlots([]);
            return;
        }

        setLoading(true);
        try {
            const serviceId = parseInt(serviceDetail);
            if (isNaN(serviceId)) {
                throw new Error('Invalid service ID');
            }

            const response = await fetch(route('beauty-spa.check-holiday', { userSlug }), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ date: dateDetail, service: serviceId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }

            const result = await response.json();
            if (result.is_success && result.slots) {
                setTimeSlots(result.slots);
            } else {
                setTimeSlots([]);
                setError(result.message || t('No slots available'));
            }
        } catch (error) {
            setTimeSlots([]);
            setError(t('Failed to load time slots. Please try again.'));
        } finally {
            setLoading(false);
        }
    };

    const handleBasicSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        if (!basicData.dateDetail || !basicData.serviceDetail || !basicData.genderDetail || !basicData.persons) {
            setError(t('Please fill all required fields'));
            return;
        }

        if (!basicData.timeSlot) {
            setError(t('Please select a time slot'));
            return;
        }

        try {
            const response = await fetch(route('beauty-spa.validate-slot-capacity', { userSlug }), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    date: basicData.dateDetail,
                    service: basicData.serviceDetail,
                    time_slot: basicData.timeSlot,
                    persons: basicData.persons
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (!result.is_success) {
                setError(result.message);
                return;
            }

            // Populate details form
            const selectedService = services.find(s => s.id.toString() === basicData.serviceDetail);
            const [startTime, endTime] = basicData.timeSlot.split('-');
            const selectedTimeSlot = timeSlots.find(slot => slot.start_time === startTime && slot.end_time === endTime);

            setDetailsData({
                ...detailsData,
                service: basicData.serviceDetail,
                date: basicData.dateDetail,
                time_slot: basicData.timeSlot,
                gender: basicData.genderDetail,
                person: basicData.persons
            });

            setShowDetails(true);
        } catch (error) {
            setError(t('An error occurred. Please try again.'));
        }
    };

    const handleDetailsSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!validateDetailsForm()) {
            return;
        }

        // Handle online payment - redirect to payment gateway
        if (selectedPaymentMethod !== 'Offline') {
            // Find the selected payment button by matching the selectedPaymentMethod
            const selectedButton = paymentButtons.find(button => {
                const expectedButtonId = `${selectedPaymentMethod.toLowerCase()}-beauty-spa-payment`;
                return button.id === expectedButtonId;
            });

            if (selectedButton) {

                // Create payment form and submit to payment gateway with booking data
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = selectedButton.dataUrl;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }

                // Add booking data to payment form
                const bookingData = {
                    service: detailsData.service,
                    date: detailsData.date,
                    time_slot: detailsData.time_slot,
                    person: detailsData.person,
                    gender: detailsData.gender,
                    name: detailsData.name,
                    email: detailsData.email,
                    phone_number: detailsData.phone_number,
                    reference: detailsData.reference,
                    additional_notes: detailsData.additional_notes,
                    payment_option: selectedPaymentMethod
                };

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
            } else {
                setError(t('Payment method not found. Please try again.'));
                return;
            }
        }
        // Handle offline payment - create booking directly
        post(route('beauty-spa.booking.store', { userSlug }));
    };

    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">{t('Get in Touch')}</span>
                            <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">
                                {t('Book Your Appointment')}
                            </h2>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-lg md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">
                                {t('Your comfort and care are our priority. Select your service and book a time that works best for you.')}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Basic Form Section */}
                <section className="lg:py-16 py-10">
                    <div className="container px-4 mx-auto">
                        <div className="flex justify-center items-center">
                            <div className="w-full bg-white rounded-2xl p-4 sm:p-6 border border-gray-200">
                                {error && (
                                    <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                                        <AlertTriangle className="w-4 h-4 me-2 flex-shrink-0" />
                                        <span>{error}</span>
                                    </div>
                                )}

                                <form onSubmit={handleBasicSubmit} className="flex xl:flex-row flex-col gap-4 xl:items-end">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 md:gap-6 flex-1">
                                        {/* Service */}
                                        <div className="flex flex-col flex-1">
                                            <Label className="text-[#df9896] text-sm font-semibold mb-2 flex items-center">
                                                <Wrench className="w-4 h-4 me-2 text-[#df9896]" /> {t('Service')}
                                            </Label>
                                            <Select value={basicData.serviceDetail} onValueChange={(value) => setBasicData('serviceDetail', value)} required>
                                                <SelectTrigger className="rounded-lg px-4 py-2 border border-gray-200 text-gray-700 bg-gray-100 focus:border-[#df9896] focus:outline-none">
                                                    <SelectValue placeholder={t('Choose service')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {services.map(service => (
                                                        <SelectItem key={service.id} value={service.id.toString()}>
                                                            {service.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        {/* Date */}
                                        <div className="flex flex-col flex-1 relative">
                                            <Label className="text-[#df9896] text-sm font-semibold mb-2 flex items-center">
                                                <Calendar className="w-4 h-4 me-2 text-[#df9896]" /> {t('Date')}
                                            </Label>
                                            <div className="relative">
                                                <DatePicker
                                                    value={basicData.dateDetail}
                                                    onChange={(value) => setBasicData('dateDetail', value)}
                                                    required
                                                    className="rounded-lg text-gray-700 bg-gray-100  w-full [&>button]:bg-gray-100"
                                                />
                                                <div className="absolute inset-y-0 end-0 flex items-center pe-4 pointer-events-none">
                                                    <ChevronDown className="w-4 h-4 text-black" />
                                                </div>
                                            </div>
                                        </div>

                                        {/* Gender */}
                                        <div className="flex flex-col flex-1">
                                            <Label className="text-[#df9896] text-sm font-semibold mb-2 flex items-center">
                                                <User className="w-4 h-4 me-2 text-[#df9896]" /> {t('Gender')}
                                            </Label>
                                            <Select value={basicData.genderDetail} onValueChange={(value) => setBasicData('genderDetail', value)} required>
                                                <SelectTrigger className="rounded-lg px-4 py-2 text-gray-700 bg-gray-100 focus:border-[#df9896] focus:outline-none">
                                                    <SelectValue placeholder={t('Select Gender')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="male">{t('Male')}</SelectItem>
                                                    <SelectItem value="female">{t('Female')}</SelectItem>
                                                    <SelectItem value="other">{t('Other')}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        {/* Time Slot */}
                                        <div className="flex flex-col flex-1">
                                            <Label className="text-[#df9896] text-sm font-semibold mb-2 flex items-center">
                                                <Clock className="w-4 h-4 me-2 text-[#df9896]" /> {t('Time Slot')}
                                            </Label>
                                            <Select value={basicData.timeSlot} onValueChange={(value) => setBasicData('timeSlot', value)} required disabled={loading || timeSlots.length === 0}>
                                                <SelectTrigger className="rounded-lg px-4 py-2 border border-gray-200 text-gray-700 bg-gray-100 focus:border-[#df9896] focus:outline-none">
                                                    <SelectValue placeholder={loading ? t('Loading...') : t('Select time slot')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {timeSlots.map((slot, index) => (
                                                        <SelectItem key={`${slot.start_time}-${slot.end_time}-${index}`} value={`${slot.start_time}-${slot.end_time}`}>
                                                            {slot.display} ({slot.available_seats} seats)
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        {/* Persons */}
                                        <div className="flex flex-col flex-1">
                                            <Label className="text-[#df9896] text-sm font-semibold mb-2 flex items-center">
                                                <Users className="w-4 h-4 me-2 text-[#df9896]" /> {t('Persons')}
                                            </Label>
                                            <Input
                                                type="number"
                                                value={basicData.persons}
                                                onChange={(e) => setBasicData('persons', e.target.value)}
                                                min="1"
                                                required
                                                placeholder={t('Number of persons')}
                                                className="rounded-lg px-4 py-2 border border-gray-200 text-gray-700 bg-gray-100 focus:border-[#df9896] focus:outline-none"
                                            />
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        className="inline-flex justify-center rounded-md border items-center gap-2 border-[#df9896] bg-[#df9896] shadow-sm px-4 py-2 font-medium text-white hover:bg-transparent hover:text-[#df9896] transition-colors"
                                    >
                                        {t('Next')}
                                    </button>
                                </form>
                            </div>
                        </div>

                        {/* Details Section */}
                        {showDetails && (
                            <div className="flex justify-center items-center mt-10">
                                <div className="w-full max-w-4xl bg-white rounded-2xl p-4 sm:p-6 border border-gray-200">
                                    <h2 className="text-base md:text-xl font-semibold text-[#df9896] mb-6 text-center md:text-left">
                                        {t('Confirm Your Details')}
                                    </h2>

                                    {serviceDetails && (
                                        <div className="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                            <div className="flex items-center gap-6">
                                                <div>
                                                    <h6 className="text-sm font-semibold text-gray-800 mb-1">{t('Estimate time')}</h6>
                                                    <p className="text-gray-600 m-0">{serviceDetails.service_time}</p>
                                                </div>
                                                <div>
                                                    <h6 className="text-sm font-semibold text-gray-800 mb-1">{t('Service Price')}</h6>
                                                    <div className="flex items-center gap-2">
                                                        <p className="text-[#df9896] font-medium m-0">
                                                            {formatCurrency(serviceDetails.formatted_price)}
                                                        </p>
                                                        {serviceDetails.offer_note && (
                                                            <span className="text-green-600 text-sm">
                                                                ( {serviceDetails.offer_note} )
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    <form onSubmit={handleDetailsSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6" noValidate>
                                        {/* Name */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block" required>{t('Name')}</Label>
                                            <Input
                                                type="text"
                                                id="name"
                                                name="name"
                                                value={detailsData.name}
                                                onChange={handleInputChange}
                                                placeholder={t('Enter Name')}
                                                required
                                                className={`w-full px-4 py-2 rounded-lg border bg-gray-50 focus:ring-[#df9896] focus:outline-none ${errors.name ? 'border-red-500' : 'border-gray-200'}`}
                                            />
                                            {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                                        </div>

                                        {/* Email */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block" required>{t('Email')}</Label>
                                            <Input
                                                type="email"
                                                id="email"
                                                name="email"
                                                value={detailsData.email}
                                                onChange={handleInputChange}
                                                onBlur={handleBlur}
                                                placeholder={t('Enter Email')}
                                                required
                                                className={`w-full px-4 py-2 rounded-lg border bg-gray-50 focus:ring-[#df9896] focus:outline-none ${errors.email ? 'border-red-500' : 'border-gray-200'}`}
                                            />
                                            {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                                        </div>

                                        {/* Phone */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block" required>{t('Phone Number')}</Label>
                                            <Input
                                                type="tel"
                                                id="phone_number"
                                                name="phone_number"
                                                value={detailsData.phone_number}
                                                onChange={handleInputChange}
                                                onBlur={handleBlur}
                                                placeholder={t('Enter phone number (e.g., +945875565)')}
                                                required
                                                className={`w-full px-4 py-2 rounded-lg border bg-gray-50 focus:ring-[#df9896] focus:outline-none ${errors.phone_number ? 'border-red-500' : 'border-gray-200'}`}
                                            />
                                            {errors.phone_number && <p className="text-red-500 text-sm mt-1">{errors.phone_number}</p>}
                                        </div>

                                        {/* Service */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block">{t('Service')}</Label>
                                            <Input
                                                type="text"
                                                value={services.find(s => s.id.toString() === detailsData.service)?.name || ''}
                                                readOnly
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Date */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block">{t('Date')}</Label>
                                            <input
                                                type="date"
                                                value={detailsData.date}
                                                readOnly
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Time */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block">{t('Appointment Time')}</Label>
                                            <input
                                                type="text"
                                                value={timeSlots.find(slot => `${slot.start_time}-${slot.end_time}` === detailsData.time_slot)?.display || ''}
                                                readOnly
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Gender */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block">{t('Gender')}</Label>
                                            <input
                                                type="text"
                                                value={detailsData.gender}
                                                readOnly
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Persons */}
                                        <div>
                                            <Label className="text-sm text-black mb-1 block">{t('Person')}</Label>
                                            <input
                                                type="number"
                                                value={detailsData.person}
                                                readOnly
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Notes */}
                                        <div className="md:col-span-2">
                                            <Label className="text-sm text-black mb-1 block">{t('Additional Notes')}</Label>
                                            <textarea
                                                value={detailsData.additional_notes}
                                                onChange={(e) => setDetailsData('additional_notes', e.target.value)}
                                                rows={3}
                                                placeholder={t('Any special requests?')}
                                                className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-[#df9896] focus:outline-none"
                                            />
                                        </div>

                                        {/* Reference */}
                                        <div className="md:col-span-2">
                                            <Label className="text-sm text-gray-600 mb-1 block">{t('How did you hear about us?')}</Label>
                                            <Select value={detailsData.reference} onValueChange={(value) => setDetailsData('reference', value)}>
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder={t('Select an option')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="Google">{t('Google')}</SelectItem>
                                                    <SelectItem value="Friend">{t('Friend')}</SelectItem>
                                                    <SelectItem value="Social Media">{t('Social Media')}</SelectItem>
                                                    <SelectItem value="Other">{t('Other')}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        {/* Payment Option */}
                                        <div className="md:col-span-2">
                                            <Label className="text-sm text-black mb-2 block font-medium required">{t('Select Payment Option')}</Label>
                                            <div className="flex flex-col sm:flex-row gap-4">
                                                <div className="flex items-center">
                                                    <input
                                                        type="radio"
                                                        name="payment_option"
                                                        id="online_payment"
                                                        value="Online"
                                                        checked={selectedPaymentMethod !== 'Offline'}
                                                        onChange={(e) => {
                                                            setSelectedPaymentMethod('Online');
                                                            setDetailsData('payment_option', 'Online');
                                                        }}
                                                        required
                                                        className="form-check-input me-2"
                                                    />
                                                    <Label className="form-check-label text-gray-800" htmlFor="online_payment">{t('Online')}</Label>
                                                </div>
                                                <div className="flex items-center">
                                                    <input
                                                        type="radio"
                                                        name="payment_option"
                                                        id="offline_payment"
                                                        value="Offline"
                                                        checked={selectedPaymentMethod === 'Offline'}
                                                        onChange={(e) => {
                                                            setSelectedPaymentMethod(e.target.value);
                                                            setDetailsData('payment_option', e.target.value);
                                                        }}
                                                        required
                                                        className="form-check-input me-2"
                                                    />
                                                    <Label className="form-check-label text-gray-800" htmlFor="offline_payment">{t('Offline')}</Label>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Offline Section (visible only if Offline is selected) */}
                                        {selectedPaymentMethod !== 'Offline' && (
                                            <div className="md:col-span-2" id="offlineSection">
                                                <div className="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                                                    <h5 className="text-lg font-semibold text-[#df9896] mb-2">{t('Payment Method')}</h5>
                                                    <RadioGroup
                                                        value={selectedPaymentMethod !== 'Offline' && selectedPaymentMethod !== 'Online' ? selectedPaymentMethod : ''}
                                                        onValueChange={(value) => {
                                                            setSelectedPaymentMethod(value);
                                                            setDetailsData('payment_option', value);
                                                            // Ensure Online radio stays selected
                                                            const onlineRadio = document.getElementById('online_payment') as HTMLInputElement;
                                                            if (onlineRadio) onlineRadio.checked = true;
                                                        }}
                                                        className="grid md:grid-cols-2 grid-cols-1 gap-4 [&_[data-state=checked]]:text-[#df9896] [&_[data-state=checked]]:border-[#df9896]"
                                                    >
                                                        {paymentButtons.map((button) => (
                                                            <div key={button.id} className="w-full">{button.component}</div>
                                                        ))}
                                                    </RadioGroup>
                                                </div>
                                            </div>
                                        )}

                                        {/* Submit Button */}
                                        <div className="md:col-span-2">
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                className="mt-4 inline-flex justify-center w-full rounded-md border items-center gap-2 border-[#df9896] bg-[#df9896] shadow-sm px-4 py-2 font-medium text-white hover:bg-transparent hover:text-[#df9896] transition-colors disabled:opacity-50"
                                            >
                                                {processing ? t('Processing...') : t('Confirm & Book Appointment')}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        )}
                    </div>
                </section>
            </main>
        </Layout>
    );
}