import React, { useState } from 'react';
import { Link, usePage, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import SocialLinks from '@/components/SocialLinks';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { InputError } from '@/components/ui/input-error';
import { PhoneInputComponent } from '@/components/ui/phone-input';

const Contact = () => {
    const { userSlug, photoStudioSettings } = usePage<{ userSlug?: string; photoStudioSettings?: any }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();
    const contactSection = photoStudioSettings?.contact_section || {};
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');

    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        phone_number: '',
        message: ''
    });

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setData(name as keyof typeof data, value);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.frontend.contact.store', { userSlug: slug }), {
            onSuccess: (page) => {
                const message = page.props.flash?.success || t('Message sent successfully!');
                setToastMessage(message);
                setShowToast(true);
                setTimeout(() => setShowToast(false), 4000);
                setData({
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone_number: '',
                    message: ''
                });
            },
            onError: (errors) => {
                const message = t('Please check the form and try again.');
                setToastMessage(message);
                setShowToast(true);
                setTimeout(() => setShowToast(false), 4000);
            }
        });
    };

    return (
        <Layout title={t('Contact')}>
            {/* Toast Notification */}
            {showToast && (
                <div className="fixed top-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 bg-[#674B2F] text-white">
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
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{contactSection.contact_page_title || 'Contact Us'}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>Home</Link>
                                <SocialLinks icon="ChevronRight" className="mx-2 w-3 h-3" />
                            </li>
                            <li className="font-bold capitalize">Contact</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Contact Section */}
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    {/* Contact Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-5 lg:mb-16 mb-10">
                        {contactSection.visit_address && (
                            <div className="bg-white lg:p-6 p-4 shadow-lg text-center hover:shadow-md transition-all duration-300 border border-gray-200 border-b-4 border-b-[#674B2F]">
                                <div className="lg:w-16 lg:h-16 w-12 h-12 bg-[#674B2F]/10 flex items-center justify-center mx-auto mb-4">
                                    <SocialLinks icon={contactSection.location_icon || 'MapPin'} className="text-[#674B2F] lg:w-8 lg:h-8 w-6 h-6" />
                                </div>
                                <h3 className="text-lg mb-2 font-medium">{contactSection.location_title || 'Our Location'}</h3>
                                <p className="text-gray-600 font-medium">{contactSection.visit_address}</p>
                            </div>
                        )}
                        {contactSection.call_details && (
                            <div className="bg-white lg:p-6 p-4 shadow-lg text-center hover:shadow-md transition-all duration-300 border border-gray-200 border-b-4 border-b-[#674B2F]">
                                <div className="lg:w-16 lg:h-16 w-12 h-12 bg-[#674B2F]/10 flex items-center justify-center mx-auto mb-4">
                                    <SocialLinks icon={contactSection.contact_icon || 'Phone'} className="text-[#674B2F] lg:w-8 lg:h-8 w-6 h-6" />
                                </div>
                                <h3 className="text-lg mb-2 font-medium">{contactSection.contact_title || 'Call Us'}</h3>
                                <a href={`tel:${contactSection.call_details}`} className="text-gray-600 hover:text-[#674B2F] transition-all duration-300 font-medium">{contactSection.call_details}</a>
                            </div>
                        )}
                        {contactSection.support_email && (
                            <div className="bg-white lg:p-6 p-4 shadow-lg text-center hover:shadow-md transition-all duration-300 border border-gray-200 border-b-4 border-b-[#674B2F]">
                                <div className="lg:w-16 lg:h-16 w-12 h-12 bg-[#674B2F]/10 flex items-center justify-center mx-auto mb-4">
                                    <SocialLinks icon={contactSection.email_icon || 'Mail'} className="text-[#674B2F] lg:w-8 lg:h-8 w-6 h-6" />
                                </div>
                                <h3 className="text-lg mb-2 font-medium">{contactSection.email_title || 'Email Us'}</h3>
                                <a href={`mailto:${contactSection.support_email}`} className="text-gray-600 hover:text-[#674B2F] transition-all duration-300 font-medium">{contactSection.support_email}</a>
                            </div>
                        )}
                    </div>

                            {/* Map and Form */}
                    <div className="bg-white border border-gray-200 shadow-md overflow-hidden">
                        <div className="grid grid-cols-1 lg:grid-cols-2">
                            {/* Map */}
                            <div className="h-80 lg:h-auto min-h-[400px]">
                                {contactSection.google_map_iframe ? (
                                    <div 
                                        dangerouslySetInnerHTML={{ __html: contactSection.google_map_iframe }}
                                        className="w-full h-full [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:min-h-[400px]"
                                    />
                                ) : (
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.2414523021944!2d-73.99051872417936!3d40.75986713434854!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9f775f259%3A0xe3b156c36b1428a1!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1683121234567!5m2!1sen!2sus"
                                        width="100%"
                                        height="100%"
                                        style={{ border: 0 }}
                                        allowFullScreen={true}
                                        loading="lazy"
                                        referrerPolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                )}
                            </div>

                            {/* Form */}
                            <div className="xl:p-8 lg:p-6 p-4">
                                <h3 className="text-2xl mb-5 font-medium">{t('Send a Message')}</h3>
                                <form onSubmit={handleSubmit} className="space-y-5">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <Input
                                                type="text"
                                                name="first_name"
                                                placeholder={t('First Name')}
                                                value={data.first_name}
                                                onChange={handleInputChange}
                                                className="w-full bg-transparent border-0 border-b-2 border-gray-300 focus:border-[#674B2F] focus:outline-none pb-2 placeholder:font-bold"
                                                required
                                            />
                                            <InputError message={errors.first_name} />
                                        </div>
                                        <div>
                                            <Input
                                                type="text"
                                                name="last_name"
                                                placeholder={t('Last Name')}
                                                value={data.last_name}
                                                onChange={handleInputChange}
                                                className="w-full bg-transparent border-0 border-b-2 border-gray-300 focus:border-[#674B2F] focus:outline-none pb-2 placeholder:font-bold"
                                                required
                                            />
                                            <InputError message={errors.last_name} />
                                        </div>
                                    </div>

                                    <div>
                                        <Input
                                            type="email"
                                            name="email"
                                            placeholder={t('Email Address')}
                                            value={data.email}
                                            onChange={handleInputChange}
                                            className="w-full bg-transparent border-0 border-b-2 border-gray-300 focus:border-[#674B2F] focus:outline-none pb-2 placeholder:font-bold"
                                            required
                                        />
                                        <InputError message={errors.email} />
                                    </div>

                                    <div>
                                        <PhoneInputComponent
                                            label={t('Phone Number (Optional)')}
                                            value={data.phone_number}
                                            onChange={(value) => setData('phone_number', value || '')}
                                            placeholder={t('Phone Number (Optional)')}
                                            error={errors.phone_number}
                                            className="w-full bg-transparent border-0 border-b-2 border-gray-300 focus:border-[#674B2F] focus:outline-none pb-2 placeholder:font-bold"
                                        />
                                    </div>

                                    <div>
                                        <Textarea
                                            name="message"
                                            placeholder={t('Your Message')}
                                            rows={3}
                                            value={data.message}
                                            onChange={handleInputChange}
                                            className="w-full bg-transparent border-0 border-b-2 border-gray-300 focus:border-[#674B2F] focus:outline-none pb-2 placeholder:font-bold resize-none"
                                            required
                                        />
                                        <InputError message={errors.message} />
                                    </div>

                                    <Button 
                                        type="submit" 
                                        disabled={processing}
                                        className="inline-flex items-center justify-center w-full gap-2 px-5 py-3 bg-[#674B2F] hover:bg-[#111111] text-[#ffffff] border border-[#674B2F] hover:border-[#111111] transition-all duration-300 capitalize font-medium"
                                    >
                                        {processing ? t('Sending...') : t('Send Message')}
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
};

export default Contact;
