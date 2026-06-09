import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useTranslation } from 'react-i18next';
import { ChevronDown } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import SocialLinks from '@/components/SocialLinks';

const FAQ = () => {
    const { userSlug, photoStudioSettings } = usePage<{ userSlug?: string; photoStudioSettings?: any }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();
    const [openIndex, setOpenIndex] = useState<number | null>(null);

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    const faqSection = photoStudioSettings?.faq_section || {};
    const faqs = faqSection.faqs && faqSection.faqs.length > 0 ? faqSection.faqs : [];

    return (
        <Layout title={t('FAQ')}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div className="sm:text-start text-center">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{faqSection.faq_page_title || t('Frequently Asked Questions')}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <SocialLinks icon="ChevronRight" className="mx-2 w-3 h-3" />
                            </li>
                            <li className="font-bold capitalize">{t('FAQ')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* FAQ Section */}
            {faqs.length > 0 ? (
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    <div className="text-center lg:mb-8 mb-5">
                        <span className="inline-block capitalize mb-2 text-[#674B2F] lg:text-lg font-medium">{faqSection.faq_label || 'Get Answers'}</span>
                        <h2 className="text-2xl sm:text-3xl md:text-4xl mb-4 font-medium">{faqSection.faq_title || 'Photography Services FAQ'}</h2>
                    </div>
                    <div className="grid grid-cols-1 gap-5">
                        {faqs.map((faq: any, index: number) => (
                            <div key={index} className="bg-white shadow-sm border border-gray-200">
                                <button
                                    onClick={() => setOpenIndex(openIndex === index ? null : index)}
                                    className="faq-question w-full text-start lg:px-6 px-4 lg:py-4 py-3 font-semibold hover:text-[#674B2F] focus:outline-none flex items-center justify-between gap-3 transition duration-300"
                                >
                                    <span className="text-left">{faq.question}</span>
                                    <ChevronDown className={`transition-transform flex-shrink-0 w-4 h-4 ${openIndex === index ? 'rotate-180' : ''}`} />
                                </button>
                                {openIndex === index && (
                                    <div className="lg:px-6 px-4 lg:py-4 py-3 border-t border-gray-200 text-gray-600 whitespace-pre-line font-medium">
                                        {faq.answer}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            ) : (
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    <div className="flex flex-col items-center justify-center py-24">
                        <svg className="w-24 h-24 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 className="text-xl font-medium text-gray-500 mb-2">No FAQs Available</h3>
                        <p className="text-gray-400 text-center max-w-md">No frequently asked questions have been configured yet. Please check back later.</p>
                    </div>
                </div>
            </section>
            )}
        </Layout>
    );
};

export default FAQ;
