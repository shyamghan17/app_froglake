import React, { useEffect, useState } from 'react';
import Layout from './Layout';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { usePage, useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { route } from 'ziggy-js';
import SocialLinks from '@/components/SocialLinks';
import { MapPin, Phone, Mail, Clock, User, Tag, MessageCircle, Send, Calendar } from 'lucide-react';
import * as LucideIcons from 'lucide-react';


interface Props {
    title?: string;
}

export default function Contact({ title = 'Contact Us | Serenity Spa' }: Props) {
    const { t } = useTranslation();
    const { beautySpaSettings, flash } = usePage().props as any;
    const { url } = usePage();


    const pathParts = url.split('/');
    const beautyIndex = pathParts.findIndex(part => part === 'beauty-spa');
    const userSlug = beautyIndex !== -1 && pathParts[beautyIndex - 1] ? pathParts[beautyIndex - 1] : null;

    const [errors, setErrors] = useState<{[key: string]: string}>({});

    const { data, setData, post, processing, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
    });



    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        let processedValue = value;
        
        // Phone number validation - ensure it starts with + and country code
        if (name === 'phone' && processedValue) {
            // Remove any non-digit characters except +
            let cleanPhone = processedValue.replace(/[^+\d]/g, '');
            // If it doesn't start with +, add +91 (India code)
            if (cleanPhone && !cleanPhone.startsWith('+')) {
                cleanPhone = '+91' + cleanPhone;
            }
            processedValue = cleanPhone;
        }
        
        setData(name as keyof typeof data, processedValue);
        
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

        if (name === 'phone' && value.trim()) {
            if (!/^\+\d{9,15}$/.test(value)) {
                newErrors.phone = t('Phone number must start with + followed by 9-15 digits (e.g., +945875565)');
            }
        }

        setErrors(newErrors);
    };
    
    const validateForm = () => {
        const newErrors: { [key: string]: string } = {};
        let firstErrorField = '';

        if (!data.name.trim()) {
            newErrors.name = t('Name is required');
            if (!firstErrorField) firstErrorField = 'name';
        }

        if (!data.email.trim()) {
            newErrors.email = t('Email is required');
            if (!firstErrorField) firstErrorField = 'email';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
            newErrors.email = t('Please enter a valid email format (e.g., test@example.com)');
            if (!firstErrorField) firstErrorField = 'email';
        }

        if (data.phone.trim() && !/^\+\d{9,15}$/.test(data.phone)) {
            newErrors.phone = t('Phone number must start with + followed by 9-15 digits (e.g., +945875565)');
            if (!firstErrorField) firstErrorField = 'phone';
        }
        
        if (!data.subject) {
            newErrors.subject = t('Please select a subject');
            if (!firstErrorField) firstErrorField = 'subject';
        }
        
        if (!data.message.trim()) {
            newErrors.message = t('Message is required');
            if (!firstErrorField) firstErrorField = 'message';
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

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        const routeParams = userSlug ? { userSlug } : {};
        post(route('beauty-spa.contact.store', routeParams), {
            onSuccess: () => reset()
        });
    };

    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* Contact Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">{beautySpaSettings?.contact_info?.header_title || 'Get in Touch'}</span>
                            <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{t('Contact Us')}</h2>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">{beautySpaSettings?.contact_info?.header_description || 'Have questions or need assistance? We are here to help. Reach out to our team using any of the methods below.'}</p>
                        </div>
                    </div>
                </section>

                {/* Contact Information & Form Section */}
                <section className="md:py-16 py-12 bg-white">
                    <div className="container mx-auto px-4">
                        <div className="grid grid-cols-1 lg:grid-cols-3 md:gap-12 gap-8">
                            <div className="bg-gradient-to-br from-[#df9896] to-[#df9896] text-white rounded-xl md:p-8 sm:p-6 p-4 lg:p-10 h-fit shadow-lg relative overflow-hidden">
                                <div className="absolute inset-0 opacity-10">
                                    <div className="absolute top-10 end-10 w-16 h-16 rounded-full border-4 border-white"></div>
                                    <div className="absolute bottom-10 start-10 w-32 h-32 rounded-full border-4 border-white"></div>
                                </div>

                                <div className="relative z-10">
                                    <h2 className="text-2xl font-bold md:mb-8 mb-4">{t('Contact Information')}</h2>

                                    <div className="space-y-6 md:mb-10 mb-6">
                                        {beautySpaSettings?.contact_info?.location && (
                                            <div className="flex items-start gap-4">
                                                <div className="bg-white/20 rounded-full mt-1 w-10 h-10 flex items-center justify-center">
                                                    <div className="text-white">
                                                        {beautySpaSettings.contact_info.location_icon ? (
                                                            <SocialLinks 
                                                                icon={beautySpaSettings.contact_info.location_icon}
                                                                className="w-5 h-5"
                                                                style={{ color: 'white' }}
                                                            />
                                                        ) : (
                                                            <MapPin size={16} />
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-semibold mb-1">{t('Our Location')}</h3>
                                                    <p className="text-white/80">{beautySpaSettings.contact_info.location}</p>
                                                </div>
                                            </div>
                                        )}

                                        {beautySpaSettings?.contact_info?.phone_number && (
                                            <div className="flex items-start gap-4">
                                                <div className="bg-white/20 rounded-full mt-1 w-10 h-10 flex items-center justify-center">
                                                    <div className="text-white">
                                                        {beautySpaSettings.contact_info.phone_icon ? (
                                                            <SocialLinks 
                                                                icon={beautySpaSettings.contact_info.phone_icon}
                                                                className="w-5 h-5"
                                                                style={{ color: 'white' }}
                                                            />
                                                        ) : (
                                                            <Phone size={16} />
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-semibold mb-1">{t('Phone Number')}</h3>
                                                    <a href={`tel:${beautySpaSettings.contact_info.phone_number}`}>{beautySpaSettings.contact_info.phone_number}</a>
                                                </div>
                                            </div>
                                        )}

                                        {beautySpaSettings?.contact_info?.email_address && (
                                            <div className="flex items-start gap-4">
                                                <div className="bg-white/20 rounded-full mt-1 w-10 h-10 flex items-center justify-center">
                                                    <div className="text-white">
                                                        {beautySpaSettings.contact_info.email_icon ? (
                                                            <SocialLinks 
                                                                icon={beautySpaSettings.contact_info.email_icon}
                                                                className="w-5 h-5"
                                                                style={{ color: 'white' }}
                                                            />
                                                        ) : (
                                                            <Mail size={16} />
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-semibold mb-1">{t('Email Address')}</h3>
                                                    <a href={`mailto:${beautySpaSettings.contact_info.email_address}`}>{beautySpaSettings.contact_info.email_address}</a>
                                                </div>
                                            </div>
                                        )}

                                        {beautySpaSettings?.working_hours && beautySpaSettings.working_hours.length > 0 && (
                                            <div className="flex items-start gap-4">
                                                <div className="bg-white/20 rounded-full mt-1 w-10 h-10 flex items-center justify-center">
                                                    <Clock className="w-4 h-4 text-white" />
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-semibold mb-1">{t('Working Hours')}</h3>
                                                    {(() => {
                                                        const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                        const workingDays = new Set();

                                                        beautySpaSettings.working_hours.forEach((hour: any) => {
                                                            hour.day_of_week.split(',').forEach((day: string) => {
                                                                workingDays.add(day.trim());
                                                            });
                                                        });

                                                        const formatTime = (time: string) => {
                                                            const [hours, minutes] = time.split(':');
                                                            const hour12 = parseInt(hours) % 12 || 12;
                                                            const ampm = parseInt(hours) >= 12 ? 'PM' : 'AM';
                                                            return `${hour12}:${minutes} ${ampm}`;
                                                        };

                                                        return allDays.map((day, index) => {
                                                            const workingHour = beautySpaSettings.working_hours.find((hour: any) =>
                                                                hour.day_of_week.split(',').map((d: string) => d.trim()).includes(day)
                                                            );

                                                            return (
                                                                <p key={index} className="text-white/80">
                                                                    {day}: {workingHour ?
                                                                        `${formatTime(workingHour.opening_time)} - ${formatTime(workingHour.closing_time)}` :
                                                                        'Closed'
                                                                    }
                                                                </p>
                                                            );
                                                        });
                                                    })()}
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <h3 className="font-semibold mb-3">{t('Connect With Us')}</h3>
                                            <SocialLinks socialLinks={beautySpaSettings?.social_links?.social_links || []} variant="light"
                                        style={{ backgroundColor: '#ffffff',color: '#df9896'  }}
                                         />
                                    </div>
                                </div>
                            </div>

                            {/* Contact Form */}
                            <div className="lg:col-span-2 bg-white border border-gray-100 rounded-xl md:p-8 sm:p-6 p-4 lg:p-10 shadow-lg">
                                <h2 className="text-2xl font-bold text-gray-800 mb-2">{t('Send Us a Message')}</h2>
                                <p className="text-gray-600 md:mb-8 mb-4">{t('Fill out the form below and we\'ll get back to you as soon as possible.')}</p>

                                <form onSubmit={handleSubmit} className="space-y-6" noValidate>
                                    <div>
                                        <div className="relative">
                                            <div className="absolute top-1/2 start-0 flex items-center ps-4 pointer-events-none transform -translate-y-1/2">
                                                <User className="w-4 h-4 text-[#df9896]" />
                                            </div>
                                            <Input
                                                type="text"
                                                id="name"
                                                name="name"
                                                value={data.name}
                                                onChange={handleInputChange}
                                                placeholder={t('Enter Your Full Name')}
                                                className={`pl-12 ${errors.name ? 'border-red-500' : ''}`}
                                                required
                                            />
                                        </div>
                                        {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <div className="relative">
                                                <div className="absolute top-1/2 start-0 flex items-center ps-4 pointer-events-none transform -translate-y-1/2">
                                                    <Mail className="w-4 h-4 text-[#df9896]" />
                                                </div>
                                                <Input
                                                    type="email"
                                                    id="email"
                                                    name="email"
                                                    value={data.email}
                                                    onChange={handleInputChange}
                                                    onBlur={handleBlur}
                                                    placeholder={t('Enter Email Address')}
                                                    className={`pl-12 ${errors.email ? 'border-red-500' : ''}`}
                                                    required
                                                />
                                            </div>
                                            {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                                        </div>
                                        <div>
                                            <div className="relative">
                                                <div className="absolute top-1/2 start-0 flex items-center ps-4 pointer-events-none transform -translate-y-1/2">
                                                    <Phone className="w-4 h-4 text-[#df9896]" />
                                                </div>
                                                <Input
                                                    type="tel"
                                                    id="phone"
                                                    name="phone"
                                                    value={data.phone}
                                                    onChange={handleInputChange}
                                                    onBlur={handleBlur}
                                                    placeholder={t('Enter phone number (e.g., +945875565)')}
                                                    className={`pl-12 ${errors.phone ? 'border-red-500' : ''}`}
                                                />
                                            </div>
                                            {errors.phone && <p className="text-red-500 text-sm mt-1">{errors.phone}</p>}
                                        </div>
                                    </div>

                                    <div>
                                        <div className="relative">
                                            <div className="absolute top-1/2 start-0 flex items-center ps-4 pointer-events-none transform -translate-y-1/2 z-10">
                                                <Tag className="w-4 h-4 text-[#df9896]" />
                                            </div>
                                            <Select name="subject" value={data.subject} onValueChange={(value) => setData('subject', value)} required>
                                                <SelectTrigger className={`pl-12 ${errors.subject ? 'border-red-500' : ''}`}>
                                                    <SelectValue placeholder="Select Subject" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="booking">{t('Booking Inquiry')}</SelectItem>
                                                    <SelectItem value="service">{t('Service Information')}</SelectItem>
                                                    <SelectItem value="feedback">{t('Feedback')}</SelectItem>
                                                    <SelectItem value="partnership">{t('Partnership Opportunity')}</SelectItem>
                                                    <SelectItem value="other">{t('Other')}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        {errors.subject && <p className="text-red-500 text-sm mt-1">{errors.subject}</p>}
                                    </div>

                                    <div>
                                        <div className="relative">
                                            <div className="absolute top-3 start-0 flex items-start ps-4 pointer-events-none">
                                                <MessageCircle className="w-4 h-4 text-[#df9896]" />
                                            </div>
                                            <Textarea
                                                id="message"
                                                name="message"
                                                value={data.message}
                                                onChange={handleInputChange}
                                                rows={5}
                                                placeholder={t('Enter Your Message')}
                                                className={`pl-12 ${errors.message ? 'border-red-500' : ''}`}
                                                required
                                            />
                                        </div>
                                        {errors.message && <p className="text-red-500 text-sm mt-1">{errors.message}</p>}
                                    </div>

                                    <div>
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className="w-full bg-[#df9896] hover:bg-white hover:text-[#df9896] border border-[#df9896] text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-2 transition-all disabled:opacity-50"
                                        >
                                            <Send className="w-4 h-4" />
                                            {processing ? 'Sending...' : 'Send Message'}
                                        </button>
                                        <p className="text-center text-gray-500 text-sm mt-4"> {t('We respect your privacy and will never share your information with third parties.')}</p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Map Section */}
                {beautySpaSettings?.contact_info?.map_iframe && (
                    <section className="md:py-16 py-12 bg-[#F5F5F5]">
                        <div className="container mx-auto px-4">
                            <div className="text-center md:mb-12 mb-6">
                                <span className="text-[#df9896] font-medium uppercase tracking-wider">{beautySpaSettings?.contact_info?.map_title || 'Find Us'}</span>
                                <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{t('Our Location')}</h2>
                                <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                                <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">{beautySpaSettings?.contact_info?.map_subtext || 'Visit us at our convenient location for the ultimate spa experience.'}</p>
                            </div>

                            <div className="bg-white rounded-xl overflow-hidden shadow-lg" style={{ height: '450px' }}>
                                <div
                                    dangerouslySetInnerHTML={{ __html: beautySpaSettings.contact_info.map_iframe }}
                                    style={{ width: '100%', height: '100%' }}
                                />
                            </div>
                        </div>
                    </section>
                )}

                {/* CTA Section */}
                <section className="md:py-20 py-12 bg-gradient-to-r from-[#df9896] to-[#df9896] text-white relative overflow-hidden">
                    <div className="absolute inset-0 opacity-10">
                        <div className="absolute top-0 start-1/4 md:w-64 md:h-64 w-48 h-48 rounded-full border-4 border-white"></div>
                        <div className="absolute bottom-0 end-1/4 md:w-96 md:h-96 w-64 h-64 rounded-full border-4 border-white"></div>
                    </div>

                    <div className="container mx-auto px-4 relative z-10 text-center">
                        <h2 className="text-3xl md:text-4xl font-bold md:mb-6 mb-4">
                            {beautySpaSettings?.contact_info?.cta_title}
                        </h2>
                        <p className="md:text-xl sm:text-[16px] text-[14px] md:mb-8 mb-4 max-w-2xl mx-auto">
                            {beautySpaSettings?.contact_info?.cta_description}
                        </p>
                        <a href={route('beauty-spa.booking', { userSlug })} className="inline-flex items-center bg-white border border-white text-[#df9896] hover:bg-transparent hover:text-white font-bold py-3 px-8 rounded-full transition-all">
                            <Calendar className="w-4 h-4 me-2" /> {t('Book Your Appointment')}
                        </a>
                    </div>
                </section>


            </main>
        </Layout>
    );
}