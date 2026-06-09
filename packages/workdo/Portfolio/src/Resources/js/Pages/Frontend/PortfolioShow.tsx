import { useState } from "react";
import { Head } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ExternalLink, Copy, Mail } from "lucide-react";
import { getImagePath, formatDate } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import { toast } from 'sonner';

interface Portfolio {
    id: number;
    title: string;
    description: string;
    slug: string;
    overview: string;
    photo: string;
    images: string[];
    video_link: string;
    live_url: string;
    repository_url: string;
    client: string;
    start_date: string;
    end_date: string;
    duration: string;
    skills: string[];
    team_size: number;
    role: string;
    name: string;
    experience_years: string;
    education: string;
    budget: string;
    industry: string;
    email: string;
    contact_heading: string;
    contact_message: string;
    show_contact: boolean;
    show_gallery: boolean;
    show_overview: boolean;
    custom_sections: Array<{
        id: number;
        title: string;
        content: string;
        sort_order: number;
    }>;
    category_name?: string;
}

interface Props {
    portfolio: Portfolio;
    companyAllSetting: any;
}

export default function PortfolioShow({ portfolio, companyAllSetting }: Props) {
    const { t } = useTranslation();

    const tabs = [
        ...(portfolio.show_overview ? [{ id: "overview", label: t('Overview') }] : []),
        ...(portfolio.show_gallery && portfolio.images?.length > 0 ? [{ id: "gallery", label: t('Gallery') }] : []),
        ...(portfolio.custom_sections?.map(section => ({ id: `custom-${section.id}`, label: section.title })) || []),
    ];

    const [activeTab, setActiveTab] = useState(portfolio.show_overview ? "overview" : tabs[0]?.id || "overview");
    const [showEmailOptions, setShowEmailOptions] = useState(false);

    const handleEmailContact = () => {
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

        if (isLocalhost) {
            // On localhost, show email options
            setShowEmailOptions(true);
        } else {
            // On live server, try mailto first
            const mailtoLink = `mailto:${portfolio.email}?subject=${encodeURIComponent(`Inquiry: ${portfolio.title}`)}`;
            window.location.href = mailtoLink;
        }
    };

    const copyEmailToClipboard = () => {
        navigator.clipboard.writeText(portfolio.email).then(() => {
            toast.success('Email copied to clipboard!', {
                description: portfolio.email
            });
            setShowEmailOptions(false);
        }).catch(() => {
            toast.error('Failed to copy email', {
                description: `Please copy manually: ${portfolio.email}`
            });
        });
    };

    const getEmbedUrl = (url: string) => {
        if (url.includes('youtube.com/watch?v=')) {
            const videoId = url.split('v=')[1]?.split('&')[0];
            return `https://www.youtube.com/embed/${videoId}`;
        }
        if (url.includes('youtu.be/')) {
            const videoId = url.split('youtu.be/')[1]?.split('?')[0];
            return `https://www.youtube.com/embed/${videoId}`;
        }
        return url;
    };

    return (
        <>
            <Head title={portfolio.title}>
                <meta name="description" content={portfolio.description} />
                {companyAllSetting?.favicon && (
                    <link rel="icon" type="image/x-icon" href={getImagePath(companyAllSetting.favicon)} />
                )}
            </Head>

            {/* Hero Section */}
            <div className="bg-white border-b border-gray-200">
                <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
                        {/* Left Content */}
                        <div className="lg:col-span-2">
                            {/* Project Type Badge */}
                            <div className="mb-6">
                                <Badge className="bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-800 px-3 py-1 text-sm font-medium transition-colors duration-200">
                                    {portfolio.category_name || t('Project')}
                                </Badge>
                            </div>

                            {/* Project Title */}
                            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 mb-4 leading-tight">
                                {portfolio.title}
                            </h1>

                            {/* Project Subtitle */}
                            <p className="text-lg sm:text-xl text-slate-600 mb-8 leading-relaxed">
                                {portfolio.description}
                            </p>

                            {/* Action Buttons */}
                            <div className="flex flex-col sm:flex-row gap-4 mb-8">
                                {portfolio.live_url && (
                                    <Button
                                        size="lg"
                                        className="bg-slate-900 text-white hover:bg-slate-800 px-6 py-3 font-semibold transition-all duration-200"
                                        onClick={() => window.open(portfolio.live_url, "_blank")}
                                    >
                                        <ExternalLink className="w-4 h-4 mr-2" />
                                        {t('View Live Work')}
                                    </Button>
                                )}
                                {portfolio.repository_url && (
                                    <Button
                                        size="lg"
                                        variant="outline"
                                        className="border-slate-300 text-slate-700 hover:bg-slate-50 px-6 py-3 font-semibold transition-all duration-200"
                                        onClick={() => window.open(portfolio.repository_url, "_blank")}
                                    >
                                        <ExternalLink className="w-4 h-4 mr-2" />
                                        {t('View Details')}
                                    </Button>
                                )}
                            </div>

                            {/* Key Technologies */}
                            {portfolio.skills && portfolio.skills.length > 0 && (
                                <div>
                                    <p className="text-sm font-medium text-slate-500 mb-3">{t('Skills & Tools')}</p>
                                    <div className="flex flex-wrap gap-2">
                                        {portfolio.skills.slice(0, 5).map((tech, index) => (
                                            <Badge key={index} className="bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-800 px-3 py-1 text-sm transition-colors duration-200" title={tech}>
                                                {tech}
                                            </Badge>
                                        ))}
                                        {portfolio.skills.length > 5 && (
                                            <Badge className="bg-slate-200 text-slate-600 hover:bg-slate-300 hover:text-slate-700 px-3 py-1 text-sm transition-colors duration-200" title={portfolio.skills.slice(5).join(', ')}>
                                                +{portfolio.skills.length - 5} {t('more')}
                                            </Badge>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right Profile */}
                        <div className="lg:col-span-1">
                            <div className="text-center">
                                {/* Profile Image */}
                                <div className="mb-6">
                                    <div className="w-64 h-64 rounded-full overflow-hidden shadow-lg border-4 border-gray-200 mx-auto">
                                        <img
                                            src={portfolio.photo ? getImagePath(portfolio.photo) : getImagePath('avatar.png')}
                                            alt="Profile"
                                            className="w-full h-full object-cover"
                                        />
                                    </div>
                                </div>

                                {/* Developer Info */}
                                <div>
                                    <h3 className="text-2xl font-bold text-slate-900 mb-2">
                                        {portfolio.name || t('Professional')}
                                    </h3>
                                    <p className="text-lg text-slate-600 mb-2">
                                        {portfolio.role || t('Professional')}
                                    </p>
                                    <p className="text-slate-500">
                                        {portfolio.experience_years ? `${portfolio.experience_years}+ ${t('years experience')}` : t('Experienced Professional')}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Navigation Tabs */}
            <div className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm">
                <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex space-x-1 sm:space-x-8 overflow-x-auto">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`py-4 px-3 sm:px-4 border-b-3 font-medium text-sm whitespace-nowrap transition-all duration-200 ${activeTab === tab.id
                                    ? "border-slate-600 text-slate-700 bg-slate-50/50"
                                    : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                            >
                                {tab.label}
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-8">
                        {activeTab === "overview" && portfolio.show_overview && (
                            <div className="space-y-8">
                                <div>
                                    <h2 className="text-2xl font-bold mb-4">{t('Overview')}</h2>
                                    <div className="space-y-4">
                                        <div
                                            className="text-gray-600 leading-relaxed prose max-w-none"
                                            dangerouslySetInnerHTML={{ __html: portfolio.overview || portfolio.description }}
                                        />
                                    </div>
                                </div>

                                {portfolio.skills && portfolio.skills.length > 0 && (
                                    <div>
                                        <h3 className="text-xl font-semibold mb-4">{t('Skills & Tools')}</h3>
                                        <div className="flex flex-wrap gap-2">
                                            {portfolio.skills.map((tech, index) => (
                                                <Badge key={index} variant="outline" className="hover:bg-slate-100 hover:text-slate-800 px-3 py-1 transition-colors duration-200" title={tech}>
                                                    {tech}
                                                </Badge>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {activeTab === "gallery" && portfolio.show_gallery && portfolio.images && (
                            <div className="space-y-6">
                                <h2 className="text-2xl font-bold">{t('Media Gallery')}</h2>

                                {/* Video Section */}
                                {portfolio.video_link && (
                                    <div className="mb-8">
                                        <h3 className="text-lg font-semibold mb-4">{t('Video')}</h3>
                                        <div className="aspect-video rounded-lg overflow-hidden">
                                            <iframe
                                                src={getEmbedUrl(portfolio.video_link)}
                                                className="w-full h-full"
                                                allowFullScreen
                                                title="Project Demo"
                                            />
                                        </div>
                                    </div>
                                )}

                                {/* Image Gallery */}
                                <div>
                                    <h3 className="text-lg font-semibold mb-4">{t('Screenshots')}</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {portfolio.images.map((image, index) => (
                                            <div key={index} className="relative group cursor-pointer rounded-lg overflow-hidden">
                                                <img
                                                    src={getImagePath(image)}
                                                    alt={`Gallery ${index + 1}`}
                                                    className="w-full h-64 object-cover transition-transform group-hover:scale-105"
                                                    loading="lazy"
                                                />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {portfolio.custom_sections?.map((section) => (
                            activeTab === `custom-${section.id}` && (
                                <div key={section.id} className="space-y-6">
                                    <h2 className="text-2xl font-bold">{section.title}</h2>
                                    <div
                                        className="text-gray-600 leading-relaxed prose max-w-none"
                                        dangerouslySetInnerHTML={{ __html: section.content }}
                                    />
                                </div>
                            )
                        ))}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
                        {/* Project Details */}
                        <Card className="shadow-lg border border-gray-200 bg-white">
                            <CardHeader className="pb-4">
                                <CardTitle className="text-xl font-bold text-gray-900">{t('Details')}</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-5">
                                {portfolio.client && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Client/Company')}</label>
                                        <p className="font-medium">{portfolio.client}</p>
                                    </div>
                                )}
                                {portfolio.duration && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Duration')}</label>
                                        <p className="font-medium">{portfolio.duration}</p>
                                    </div>
                                )}
                                {portfolio.team_size && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Team Size')}</label>
                                        <p className="font-medium">{portfolio.team_size} {t('members')}</p>
                                    </div>
                                )}
                                {portfolio.role && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('My Role')}</label>
                                        <p className="font-medium">{portfolio.role}</p>
                                    </div>
                                )}
                                {portfolio.start_date && portfolio.end_date && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Timeline')}</label>
                                        <p className="font-medium text-sm">
                                            {formatDate(portfolio.start_date)} - {formatDate(portfolio.end_date)}
                                        </p>
                                    </div>
                                )}
                                {portfolio.budget && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Budget')}</label>
                                        <p className="font-medium">{portfolio.budget}</p>
                                    </div>
                                )}
                                {portfolio.industry && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Industry')}</label>
                                        <p className="font-medium">{portfolio.industry}</p>
                                    </div>
                                )}
                                {portfolio.education && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">{t('Education')}</label>
                                        <p className="font-medium">{portfolio.education}</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Contact CTA */}
                        {portfolio.show_contact && portfolio.email && (
                            <Card className="bg-slate-800 border-0 text-white shadow-xl">
                                <CardContent className="pt-8 pb-8 text-center">
                                    <div className="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span className="text-2xl">💼</span>
                                    </div>
                                    <h3 className="font-bold text-lg mb-2">
                                        {portfolio.contact_heading || t('Interested in working together?')}
                                    </h3>
                                    <p className="text-slate-200 mb-6 leading-relaxed">
                                        {portfolio.contact_message || t("Let's discuss your next project and bring your ideas to life")}
                                    </p>
                                    {!showEmailOptions ? (
                                        <Button
                                            className="w-full bg-white text-slate-800 hover:bg-gray-100 font-semibold py-3 shadow-lg hover:shadow-xl transition-all duration-200"
                                            onClick={handleEmailContact}
                                        >
                                            <Mail className="w-4 h-4 mr-2" />
                                            {t('Get In Touch')}
                                        </Button>
                                    ) : (
                                        <div className="space-y-3">
                                            <div className="text-center text-sm text-slate-200 mb-3">
                                                {t('Choose contact method:')}
                                            </div>
                                            <Button
                                                className="w-full bg-white text-slate-800 hover:bg-gray-100 font-semibold py-2 shadow-lg transition-all duration-200"
                                                onClick={() => {
                                                    const mailtoLink = `mailto:${portfolio.email}?subject=${encodeURIComponent(`Project Inquiry: ${portfolio.title}`)}`;
                                                    window.location.href = mailtoLink;
                                                }}
                                            >
                                                <Mail className="w-4 h-4 mr-2" />
                                                {t('Open Email App')}
                                            </Button>
                                            <Button
                                                variant="outline"
                                                className="w-full bg-slate-700 text-white border-slate-600 hover:bg-slate-600 hover:text-white hover:border-slate-500 font-semibold py-2 transition-all duration-200"
                                                onClick={copyEmailToClipboard}
                                            >
                                                <Copy className="w-4 h-4 mr-2" />
                                                {t('Copy Email')}
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="w-full text-slate-300 hover:text-white hover:bg-slate-700/50 py-1 transition-all duration-200"
                                                onClick={() => setShowEmailOptions(false)}
                                            >
                                                {t('Cancel')}
                                            </Button>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
