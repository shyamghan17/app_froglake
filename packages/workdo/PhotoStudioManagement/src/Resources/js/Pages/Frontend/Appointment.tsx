import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import { ChevronRight, CalendarCheck } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Input } from '@/components/ui/input';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DateTimeRangePicker } from '@/components/ui/datetime-range-picker';
import { usePageButtons } from '@/hooks/usePageButtons';
import InputError from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';

interface Service {
    id: number;
    name: string;
    price: string;
}

const Appointment = () => {
    const { userSlug, photoStudioSettings, services } = usePage<{
        userSlug?: string;
        photoStudioSettings?: any;
        services: Service[];
    }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();
    const titleSection = photoStudioSettings?.title_section || {};

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        mobile_no: '',
        service_id: '',
        price: '',
        booking_start_date: '',
        booking_end_date: '',
        paymentOption: 'online',
        paymentMethod: ''
    });

    const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<string>('');
    const [errors, setErrors] = useState<{ [key: string]: string }>({});
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState<'success' | 'error'>('success');

    const { props } = usePage();

    useEffect(() => {
        if (props.flash?.success) {
            setToastMessage(props.flash.success);
            setToastType('success');
            setShowToast(true);
            setTimeout(() => setShowToast(false), 4000);
        }
        if (props.flash?.error) {
            setToastMessage(props.flash.error);
            setToastType('error');
            setShowToast(true);
            setTimeout(() => setShowToast(false), 4000);
        }
    }, [props.flash]);

    const paymentButtons = usePageButtons('photoStudioPayment', {
        selectedMethod: selectedPaymentMethod,
        onMethodChange: (method: string) => {
            setSelectedPaymentMethod(method);
            setFormData(prev => ({ ...prev, paymentMethod: method }));
        },
    }, false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));

        if (errors[name]) {
            setErrors(prev => ({ ...prev, [name]: '' }));
        }

        if (name === 'service_id') {
            const selected = services.find(s => s.id.toString() === value);
            if (selected) {
                setFormData(prev => ({ ...prev, service_id: value, price: selected.price }));
            }
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

        setErrors(newErrors);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const newErrors: { [key: string]: string } = {};
        if (!formData.name.trim()) newErrors.name = t('Name is required.');
        if (!formData.email.trim()) newErrors.email = t('Email is required.');
        if (!formData.mobile_no.trim()) newErrors.mobile_no = t('Mobile number is required.');
        if (!formData.service_id) newErrors.service_id = t('Service is required.');
        if (!formData.booking_start_date) newErrors.booking_start_date = t('Booking start date is required.');
        if (!formData.booking_end_date) newErrors.booking_end_date = t('Booking end date is required.');
        if (!selectedPaymentMethod) newErrors.paymentMethod = t('Please select a payment method.');

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        if (formData.paymentOption === 'online' && selectedPaymentMethod) {
            const selectedButton = paymentButtons.find(button =>
                button.id === `${selectedPaymentMethod}-photo-studio-payment`
            );

            if (selectedButton) {
                const paymentForm = document.createElement('form');
                paymentForm.method = 'POST';
                paymentForm.action = selectedButton.dataUrl;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    paymentForm.appendChild(csrfInput);
                }

                const bookingData = {
                    name: formData.name,
                    email: formData.email,
                    mobile_no: formData.mobile_no,
                    service_id: formData.service_id,
                    price: formData.price,
                    booking_start_date: formData.booking_start_date,
                    booking_end_date: formData.booking_end_date,
                    paymentMethod: selectedPaymentMethod,
                };

                Object.entries(bookingData).forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value?.toString() || '';
                    paymentForm.appendChild(input);
                });

                document.body.appendChild(paymentForm);
                paymentForm.submit();
                return;
            }
        }
    };

    return (
        <Layout title={t('Book Appointment')}>
            {/* Toast Notification */}
            {showToast && (
                <div className={`fixed top-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${toastType === 'success' ? 'bg-[#674B2F] text-white' : 'bg-red-500 text-white'
                    }`}>
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{toastMessage}</span>
                </div>
            )}
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div className="sm:text-start text-center">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{titleSection.booking_page_title || t('Book Appointment')}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <ChevronRight className="w-3 h-3 mx-2" />
                            </li>
                            <li className="font-bold capitalize">{t('appointment')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Booking Form */}
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    <div className="max-w-4xl mx-auto border shadow-lg xl:p-8 lg:p-6 p-4">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Personal Information */}
                            <div>
                                <h3 className="text-2xl mb-4 font-medium text-[#674B2F]">{t('Book Appointment')}</h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <Label htmlFor="name">{t('Name')}</Label>
                                        <Input
                                            type="text"
                                            id="name"
                                            name="name"
                                            value={formData.name}
                                            onChange={handleChange}
                                            placeholder={t('Enter Name')}
                                            className={errors.name ? 'border-red-500' : ''}
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div>
                                        <Label htmlFor="email">{t('Email')}</Label>
                                        <Input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleChange}
                                            onBlur={handleBlur}
                                            placeholder={t('Enter Email')}
                                            className={errors.email ? 'border-red-500' : ''}
                                            required
                                        />
                                        <InputError message={errors.email} />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-1 gap-5 mt-5">
                                    <div>
                                        <PhoneInputComponent
                                            label={t('Mobile No.')}
                                            value={formData.mobile_no}
                                            onChange={value => {
                                                setFormData(prev => ({ ...prev, mobile_no: value || '' }));
                                                if (errors.mobile_no) setErrors(prev => ({ ...prev, mobile_no: '' }));
                                            }}
                                            error={errors.mobile_no}
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Service Selection */}
                            <div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <Label htmlFor="service_id" required>{t('Service')}</Label>
                                        <select
                                            name="service_id"
                                            value={formData.service_id}
                                            onChange={handleChange}
                                            className={`w-full px-4 py-2 border focus:border-primary focus:outline-none appearance-none bg-white cursor-pointer ${errors.service_id ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                            style={{
                                                backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512'%3E%3Cpath fill='%23001d23' d='M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z'/%3E%3C/svg%3E")`,
                                                backgroundRepeat: 'no-repeat',
                                                backgroundSize: '12px',
                                                backgroundPosition: 'calc(100% - 15px) 50%',
                                                paddingRight: '35px'
                                            }}
                                            required
                                        >
                                            <option value="">{t('Select Service')}</option>
                                            {services.map(service => (
                                                <option key={service.id} value={service.id.toString()}>{service.name}</option>
                                            ))}
                                        </select>
                                        <InputError message={errors.service_id} />
                                    </div>
                                    <div>
                                        <Label htmlFor="price">{t('Price')}</Label>
                                        <Input
                                            type="number"
                                            id="price"
                                            name="price"
                                            value={formData.price}
                                            onChange={handleChange}
                                            placeholder="0.00"
                                            className="bg-gray-50"
                                            readOnly
                                            required
                                        />
                                    </div>
                                </div>

                                {/* Date and Time */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                                    <div>
                                        <Label htmlFor="booking_start_date" required>{t('Booking Start Date')}</Label>
                                        <DateTimeRangePicker
                                            id="booking_start_date"
                                            mode="single"
                                            value={formData.booking_start_date}
                                            onChange={value => {
                                                setFormData(prev => ({ ...prev, booking_start_date: value }));
                                                if (errors.booking_start_date) setErrors(prev => ({ ...prev, booking_start_date: '' }));
                                            }}
                                            placeholder={t('Select start date & time')}
                                            required
                                        />
                                        <InputError message={errors.booking_start_date} />
                                    </div>
                                    <div>
                                        <Label htmlFor="booking_end_date" required>{t('Booking End Date')}</Label>
                                        <DateTimeRangePicker
                                            id="booking_end_date"
                                            mode="single"
                                            value={formData.booking_end_date}
                                            onChange={value => {
                                                setFormData(prev => ({ ...prev, booking_end_date: value }));
                                                if (errors.booking_end_date) setErrors(prev => ({ ...prev, booking_end_date: '' }));
                                            }}
                                            placeholder={t('Select end date & time')}
                                            required
                                        />
                                        <InputError message={errors.booking_end_date} />
                                    </div>
                                </div>
                            </div>

                      

                            {/* Payment Method */}
                            <div>
                                <Label className="block text-sm font-medium text-gray-700 mb-2">{t('Payment Method')}</Label>
                                {paymentButtons.length === 0 ? (
                                    <div className="flex items-start gap-3 border border-yellow-200 bg-yellow-50 rounded-lg px-4 py-3 mt-2">
                                        <svg className="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>
                                        <div>
                                            <p className="text-sm font-semibold text-yellow-800">{t('Payment Method Not Available')}</p>
                                            <p className="text-xs text-yellow-700 mt-0.5">{t('No payment methods are currently configured. Please contact the admin to enable a payment option.')}</p>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                                        {paymentButtons.map((button) => (
                                            <div key={button.id} className="w-full">{button.component}</div>
                                        ))}
                                    </div>
                                )}
                                <InputError message={errors.paymentMethod} />
                            </div>

                            {/* Submit Button */}
                            <div className="text-center">
                                <button type="submit" className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium">
                                    <CalendarCheck className="w-4 h-4" /> {t('Book Appointment')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </Layout>
    );
};

export default Appointment;
