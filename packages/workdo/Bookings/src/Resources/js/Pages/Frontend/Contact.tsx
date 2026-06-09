import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import PublicLayout from './components/PublicLayout';
import PageHeader from '../../components/PageHeader';
import ContactInfo from '../../components/ContactInfo';
import SocialLinks from '@/components/SocialLinks';
import { Input, Button, Label, Textarea } from './components';
import { useTranslation } from 'react-i18next';
import { User, Tag, MessageCircle, Send, Phone, Mail, MapPin } from 'lucide-react';

interface ContactProps {
    title: string;
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
}

export default function Contact({ title, userSlug, brandSettings, colorSettings, socialLinks, customPages, footerServices, pageSettings }: ContactProps) {
    const { t } = useTranslation();
    const pageSettingsData = pageSettings?.contact || {};
    const headerSection = pageSettingsData?.header || {};
    const formSection = pageSettingsData?.form || {};
    const infoSection = pageSettingsData?.info || {};
    const mapSection = pageSettingsData?.map || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
    });

    const extractIframeSrc = (iframeHtml?: string): string => {
        if (!iframeHtml) return '';
        const match = iframeHtml.match(/src=["']([^"']+)["']/);
        return match?.[1] || '';
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        router.post(route('booking.contact.store', { userSlug }), formData, {
            onSuccess: () => {
                setFormData({ name: '', email: '', phone: '', subject: '', message: '' });
            },
            onError: (errors) => {
                console.error('Error submitting form:', errors);
            }
        });
    };

    return (
        <PublicLayout title={title} userSlug={userSlug} brandSettings={brandSettings} colorSettings={colorSettings} socialLinks={socialLinks} customPages={customPages} footerServices={footerServices}>
            <PageHeader 
                title={headerSection.title || 'Contact Us'} 
                description={headerSection.description || 'Get in touch with us for any questions, support, or booking assistance'}
                bgColor={primaryColor} 
            />

            {/* Contact Form and Information Section */}
            <section className="lg:py-16 py-10 relative">
                <div className="container mx-auto px-4">
                    <div className="flex flex-col lg:flex-row md:gap-16 gap-12">
                        {/* Left Column - Contact Form */}
                        <div className="lg:w-7/12">
                            <div className="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:shadow-2xl transition-all duration-300">
                                <div className="h-2" style={{ backgroundColor: primaryColor }}></div>
                                <div className="sm:p-8 p-4">
                                    <div className="flex items-center sm:mb-8 mb-6">
                                        <span className="w-10 h-10 rounded-full flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                                            <Send className="w-5 h-5 text-white" />
                                        </span>
                                        <h2 className="sm:text-3xl text-xl font-bold text-gray-800">{formSection.form_title || 'Send Us a Message'}</h2>
                                    </div>
                                    
                                    <p className="text-gray-600 mb-8">{formSection.form_description || 'Fill out the form below and our team will get back to you within 24 hours.'}</p>
                                    
                                    <form onSubmit={handleSubmit} className="space-y-6">
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <Label required>{t('Name')}</Label>
                                                <Input
                                                    name="name"
                                                    value={formData.name}
                                                    onChange={handleInputChange}
                                                    placeholder={formSection.name_placeholder || t('Your Name')}
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <Label required>{t('Email')}</Label>
                                                <Input
                                                    type="email"
                                                    name="email"
                                                    value={formData.email}
                                                    onChange={handleInputChange}
                                                    placeholder={formSection.email_placeholder || t('Your Email')}
                                                    required
                                                />
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <Label>{t('Phone')}</Label>
                                            <Input
                                                type="tel"
                                                name="phone"
                                                value={formData.phone}
                                                onChange={handleInputChange}
                                                placeholder={formSection.phone_placeholder || t('Your Phone')}
                                            />
                                        </div>
                                        <div>
                                            <Label>{t('Subject')}</Label>
                                            <Input
                                                name="subject"
                                                value={formData.subject}
                                                onChange={handleInputChange}
                                                placeholder={formSection.subject_placeholder || t('Subject')}
                                            />
                                        </div>
                                        <div>
                                            <Label>{t('Message')}</Label>
                                            <Textarea
                                                name="message"
                                                value={formData.message}
                                                onChange={handleInputChange}
                                                placeholder={formSection.message_placeholder || t('Your Message')}
                                                rows={5}
                                            />
                                        </div>
                                        
                                        <Button
                                            type="submit"
                                            variant="primary"
                                            className="w-full flex items-center justify-center"
                                            primaryColor={primaryColor}
                                        >
                                            <Send className="mr-2 h-5 w-5" />
                                            {formSection.button_text || t('Send Message')}
                                        </Button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        {/* Right Column - Information */}
                        <div className="lg:w-5/12 space-y-8">
                            {/* Company Information Card */}
                            <div className="rounded-xl shadow-lg sm:p-8 p-4 relative overflow-hidden" style={{ backgroundColor: `${primaryColor}0d` }}>
                                <div className="absolute -top-10 -right-10 w-40 h-40 rounded-full" style={{ backgroundColor: `${primaryColor}0d` }}></div>
                                
                                <div className="relative z-10">
                                    <h3 className="sm:text-2xl text-xl font-bold mb-6" style={{ color: primaryColor }}>{brandSettings?.footer_contact_title || 'Contact Information'}</h3>
                                    <ul className="space-y-6">
                                        {brandSettings?.footer_address && (
                                            <li>
                                                <div className="flex items-start">
                                                    <MapPin className="w-5 h-5 mt-1 mr-3 flex-shrink-0" style={{ color: primaryColor }} />
                                                    <div>
                                                        <h4 className="font-semibold text-gray-800 mb-1">Office Location</h4>
                                                        <span className="text-gray-700">{brandSettings.footer_address}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        )}
                                        {brandSettings?.footer_phone && (
                                            <li>
                                                <div className="flex items-start">
                                                    <Phone className="w-5 h-5 mt-1 mr-3 flex-shrink-0" style={{ color: primaryColor }} />
                                                    <div>
                                                        <h4 className="font-semibold text-gray-800 mb-1">Contact Number</h4>
                                                        <a href={`tel:${brandSettings.footer_phone}`} className="text-gray-700 hover:underline">{brandSettings.footer_phone}</a>
                                                    </div>
                                                </div>
                                            </li>
                                        )}
                                        {brandSettings?.footer_email && (
                                            <li>
                                                <div className="flex items-start">
                                                    <Mail className="w-5 h-5 mt-1 mr-3 flex-shrink-0" style={{ color: primaryColor }} />
                                                    <div>
                                                        <h4 className="font-semibold text-gray-800 mb-1">Email Address</h4>
                                                        <a href={`mailto:${brandSettings.footer_email}`} className="text-gray-700 hover:underline">{brandSettings.footer_email}</a>
                                                    </div>
                                                </div>
                                            </li>
                                        )}
                                        {brandSettings?.footer_hours && (
                                            <li>
                                                <div className="flex items-start">
                                                    <svg className="w-5 h-5 mt-1 mr-3 flex-shrink-0" style={{ color: primaryColor }} fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" /></svg>
                                                    <div>
                                                        <h4 className="font-semibold text-gray-800 mb-1">Business Hours</h4>
                                                        <span className="text-gray-700">{brandSettings.footer_hours}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        )}
                                    </ul>
                                </div>
                            </div>

                            {/* Social Media & Support */}
                            <div className="rounded-xl shadow-lg sm:p-8 p-4 text-white" style={{ backgroundColor: primaryColor }}>
                                <h3 className="sm:text-2xl text-xl font-bold mb-6">Connect With Us</h3>
                                
                                <SocialLinks socialLinks={socialLinks} variant="light" style={{ color: primaryColor, backgroundColor: secondaryColor }} />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Map Section */}
            {mapSection.embed_code && (
                <section className="md:pb-16 pb-12">
                    <div className="container mx-auto px-4">
                        <div className="bg-white shadow-lg rounded-2xl overflow-hidden" style={{ height: `${mapSection.height || '400'}px` }}>
                            <iframe
                                src={extractIframeSrc(mapSection.embed_code)}
                                className="w-full h-full border-0"
                                allowFullScreen
                                loading="lazy"
                            />
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}