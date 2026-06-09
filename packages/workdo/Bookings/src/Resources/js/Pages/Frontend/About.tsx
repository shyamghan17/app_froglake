import React from 'react';
import PublicLayout from './components/PublicLayout';
import PageHeader from '../../components/PageHeader';
import { Image } from './components';
import { Users, Lightbulb } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import SocialLinks from '@/components/SocialLinks';

interface AboutProps {
    title: string;
    userSlug?: string;
    brandSettings?: any;
    colorSettings?: any;
    socialLinks?: any;
    customPages?: any;
    footerServices?: any[];
    pageSettings?: any;
}

export default function About({ title, userSlug, brandSettings, colorSettings, socialLinks, customPages, footerServices, pageSettings }: AboutProps) {
    const { t } = useTranslation();
    const aboutPage = pageSettings?.about || {};
    const headerSection = aboutPage?.header || {};
    const storySection = aboutPage?.story || {};
    const missionSection = aboutPage?.mission || {};
    const teamSection = aboutPage?.team || {};
    const colors = colorSettings || {};
    const primaryColor = colors.primary_color || '#52816D';
    const secondaryColor = colors.secondary_color || '#ffffff';

    return (
        <PublicLayout 
            title={title} 
            userSlug={userSlug} 
            brandSettings={brandSettings}
            colorSettings={colorSettings}
            socialLinks={socialLinks}
            customPages={customPages}
            footerServices={footerServices}
        >
            <PageHeader 
                title={headerSection.title || t('About Us')} 
                description={headerSection.description || t('Learn more about our story, mission, and the team behind our success')} 
                bgColor={primaryColor}
            />

            <section className="lg:py-16 py-10 relative">
                <div className="container mx-auto px-4 relative z-10">
                    {/* Our Story */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 xl:gap-16 gap-8 items-center">
                        <div>
                            <h2 className="text-3xl md:text-4xl xl:text-5xl font-bold lg:mb-6 mb-4 leading-tight">
                                {storySection.title || t('Revolutionizing Bookings')}
                            </h2>
                            {(storySection.content || []).map((item, i) => (
                                <p key={i} className="text-gray-600 xl:text-lg md:text-base text-sm xl:mb-6 mb-4 leading-relaxed">
                                    {item.content}
                                </p>
                            ))}
                            <div className="grid sm:grid-cols-3 grid-cols-1 lg:gap-6 gap-4">
                                {(storySection.stats || []).map((stat, i) => (
                                    <div key={i} className="text-center p-4 bg-white rounded-lg border border-gray-200">
                                        <div className="md:text-3xl text-2xl font-bold mb-1" style={{ color: primaryColor }}>{stat.number}</div>
                                        <div className="text-sm text-gray-600 font-medium">{stat.label}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="about-img relative sm:pt-[60%] pt-[75%] rounded-lg overflow-hidden h-full">
                            <Image src={storySection.image ? getImagePath(storySection.image) : getImagePath('packages/workdo/Bookings/src/assets/images/about-img.png')} alt="about-img" className="absolute object-cover w-full h-full inset-0" />
                        </div>
                    </div>
                </div>
            </section>

            {/* Our Mission Section */}
            <section className="lg:py-16 py-10 relative overflow-hidden bg-gray-50">
                <div className="absolute top-0 left-0 w-full h-full">
                    <div className="absolute top-20 left-20 w-40 h-40 rounded-full blur-3xl animate-pulse" style={{ backgroundColor: `${primaryColor}1a` }}></div>
                    <div className="absolute bottom-20 right-20 w-60 h-60 bg-green-500/10 rounded-full blur-3xl animate-pulse" style={{animationDelay: '1s'}}></div>
                    <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
                </div>
                
                <div className="container mx-auto px-4 relative z-10">
                    <div className="flex flex-col items-center text-center xl:mb-12 md:mb-10 mb-6">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4 text-gray-900">{missionSection.title || t('Our mission')}</h2>
                        <p className="text-gray-800 max-w-xl mx-auto">{missionSection.subtitle || t('Empowering people through simple, effective service experiences.')}</p>
                    </div>
                    
                    <div className="max-w-6xl mx-auto">
                        <div className="grid grid-cols-1 lg:grid-cols-3 md:gap-8 gap-6">
                            <div className="lg:col-span-2">
                                <div className="bg-white rounded-xl border p-4 lg:p-8 h-full relative overflow-hidden" style={{ borderColor: primaryColor }}>
                                    <div className="absolute -top-10 -right-10 w-32 h-32 opacity-10 rounded-full" style={{ background: `linear-gradient(to bottom right, ${primaryColor}, #10b981)` }}></div>
                                    <div className="relative z-10">
                                        <h3 className="md:text-3xl text-2xl font-bold lg:mb-6 mb-4 text-gray-800">{missionSection.content_title || 'Revolutionizing Service Connections'}</h3>
                                        <p className="text-gray-800 lg:text-xl text-sm md:text-base leading-relaxed">
                                            {missionSection.content_description || 'To create seamless connections between service seekers and providers, making quality services accessible to everyone while empowering professionals to grow their businesses.'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="space-y-6">
                                {(missionSection.features || []).map((feature, i) => {
                                    return (
                                        <div key={i} className="border rounded-xl lg:p-6 p-4 text-gray-900" style={{ borderColor: primaryColor }}>
                                            <div className="flex items-center mb-4">
                                                <div className="w-12 h-12 rounded-lg text-white flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                                                    <SocialLinks 
                                                        icon={feature.icon}
                                                        className="w-5 h-5" 
                                                    />
                                                </div>
                                                <h4 className="text-lg font-bold">{feature.title}</h4>
                                            </div>
                                            <p>{feature.description}</p>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Team Section */}
            <section className="lg:py-16 py-10">
                <div className="container mx-auto px-4">
                    <div className="flex flex-col items-center text-center xl:mb-12 md:mb-10 mb-6">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4 text-gray-900">{teamSection.title || t('Meet Our Team')}</h2>
                        <p className="text-gray-800 max-w-xl mx-auto">{teamSection.subtitle || t('The passionate professionals behind your exceptional service experience')}</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {(teamSection.members || []).map((member, i) => (
                            <div key={i} className="h-full group bg-gray-50 border rounded-xl lg:p-6 p-4 text-center">
                                <div className="w-24 h-24 mb-5 mx-auto rounded-full overflow-hidden border-4 border-gray-200 transition duration-300" 
                                     style={{ '--hover-border-color': `${primaryColor}66` } as React.CSSProperties}
                                     onMouseEnter={(e) => e.currentTarget.style.borderColor = `${primaryColor}66`}
                                     onMouseLeave={(e) => e.currentTarget.style.borderColor = '#e5e7eb'}>
                                    <Image src={member.image ? getImagePath(member.image) : getImagePath(`packages/workdo/Bookings/src/assets/images/team-${i + 1}.png`)} alt="team-image" className="w-full h-full object-cover" />
                                </div>
                                <h3 className="text-lg mb-1">{member.name}</h3>
                                <p className="font-semibold mb-3" style={{ color: primaryColor }}>{member.position}</p>
                                <p className="text-gray-600 mb-4">{member.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}