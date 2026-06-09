import React from 'react';
import Layout from './Layout';
import { usePage } from '@inertiajs/react';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import * as LucideIcons from 'lucide-react';

interface Props {
    title?: string;
}

export default function About({ title = 'About Us | Beauty Spa' }: Props) {
    const { t } = useTranslation();
    const pageProps = usePage().props as any;
    const { beautySpaSettings } = pageProps;

    const renderIcon = (iconName: string, size: number = 24) => {
        const IconComponent = LucideIcons[iconName as keyof typeof LucideIcons] as React.ComponentType<{ size?: number, className?: string }>;
        if (IconComponent) {
            return <IconComponent size={size} className="text-white" />;
        }
        return <span className="text-white text-2xl font-bold">★</span>;
    };


    return (
        <Layout title={title}>
            <main className="pt-20 -mt-4">
                {/* About Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        <div className="text-center max-w-2xl mx-auto">
                            <span className="text-[#df9896] font-medium uppercase tracking-wider">
                                {beautySpaSettings?.about_section?.main_title}
                            </span>
                            <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{t('About Us')}</h2>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">
                                {beautySpaSettings?.about_section?.sub_text}
                            </p>
                        </div>
                    </div>
                </section>

                {/* About Content Section */}
                {beautySpaSettings?.about_section?.content || beautySpaSettings?.about_section?.about_image ? (
                    <section className="py-10 lg:py-16 bg-white overflow-hidden">
                        <div className="container mx-auto px-4">
                            <div className="grid grid-cols-1 lg:grid-cols-2 items-center gap-6 lg:gap-12">
                                {/* Left: Text Content */}
                                <div>
                                    {beautySpaSettings?.about_section?.content && (
                                        <div dangerouslySetInnerHTML={{ __html: beautySpaSettings.about_section.content }} />
                                    )}
                                </div>
                                {/* Right: Image */}
                                <div className="relative sm:pt-[50%] pt-[70%] h-full rounded-lg overflow-hidden">
                                    <img
                                        src={beautySpaSettings?.about_section?.about_image ? getImagePath(beautySpaSettings.about_section.about_image, pageProps) : getImagePath('packages/workdo/BeautySpaManagement/src/Resources/assets/images/defualt.png', pageProps)}
                                        alt="About Image"
                                        className="object-cover inset-0 size-full absolute"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>
                ) : (
                    <section className="py-10 lg:py-16">
                        <div className="container mx-auto flex flex-col items-center justify-center">
                            <svg className="w-64 h-64 text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 className="text-2xl font-bold text-gray-600 mb-4">{t('No About Content Available')}</h2>
                            <p className="text-gray-500 text-center max-w-md">{t('There is no about content configured for this page. Please contact the administrator to set up the about content.')}</p>
                        </div>
                    </section>
                )}

                {/* Statistics Section */}
                <section className="bg-[#df98962b] lg:py-16 py-10">
                    <div className="container mx-auto px-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                            <div className="text-black">
                                <p className="text-3xl lg:text-5xl font-bold">{pageProps.statistics?.services_count || 0}+</p>
                                <p className="mt-2 text-base md:text-lg font-bold">{t('Services')}</p>
                            </div>
                            <div className="text-black">
                                <p className="text-3xl lg:text-5xl font-bold">{pageProps.statistics?.total_bookings || 0}+</p>
                                <p className="mt-2 text-base md:text-lg font-bold">{t('Bookings')}</p>
                            </div>
                            <div className="text-black">
                                <p className="text-3xl lg:text-5xl font-bold">{pageProps.statistics?.pending_bookings || 0}+</p>
                                <p className="mt-2 text-base md:text-lg font-bold">{t('Pending Bookings')}</p>
                            </div>
                            <div className="text-black">
                                <p className="text-3xl lg:text-5xl font-bold">{pageProps.statistics?.completed_bookings || 0}+</p>
                                <p className="mt-2 text-base md:text-lg font-bold">{t('Completed Bookings')}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Our Purpose Section */}
                {beautySpaSettings?.about_section?.about_stats && beautySpaSettings.about_section.about_stats.length > 0 && (
                    <section className="lg:pt-16 pt-10 pb-0">
                        <div className="container mx-auto px-4">
                            <div className="text-center mb-16">
                                <h2 className="text-3xl md:text-4xl font-bold text-[#df9896] mb-2">
                                    {beautySpaSettings?.about_section?.purpose_title}
                                </h2>
                                <p className="md:text-lg text-[16px] text-gray-800">
                                    {beautySpaSettings?.about_section?.purpose_description}
                                </p>
                            </div>

                            <div className="relative max-w-6xl mx-auto grid md:grid-cols-2 md:gap-10 gap-14">
                                {beautySpaSettings.about_section.about_stats.map((stat: any, index: number) => (
                                    <div key={index} className="relative group bg-white border border-t-4 border-[#df9896] rounded-xl p-4 md:p-8 transition-all duration-500 hover:-translate-y-1">
                                        {/* Icon Circle */}
                                        <div className="absolute -top-10 start-1/2 transform -translate-x-1/2">
                                            <div className="w-16 h-16 md:w-20 md:h-20 bg-[#df9896] rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition duration-300">
                                                {renderIcon(stat.icon, 24)}
                                            </div>
                                        </div>
                                        {/* Title & Description */}
                                        <div className="pt-6 md:pt-10 text-center">
                                            <h3 className="text-2xl text-gray-800 mb-4 group-hover:text-[#df9896] transition-colors duration-300">
                                                {stat.title}
                                            </h3>
                                            <p className="text-gray-800 leading-relaxed text-sm md:text-lg">
                                                {stat.description}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                )}
            </main>
        </Layout>
    );
}