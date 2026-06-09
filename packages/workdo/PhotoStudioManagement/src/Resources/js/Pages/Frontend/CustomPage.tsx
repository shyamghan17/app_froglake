import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { getImagePath } from '@/utils/helpers';
import Layout from '../../Components/Frontend/Layout';
import { ChevronRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface CustomPageProps {
    page: {
        id: number;
        title: string;
        description: string;
        contents: string;
        slug: string;
    };
}

const CustomPage = () => {
    const { userSlug, page } = usePage<{ userSlug?: string; page?: CustomPageProps['page'] }>().props;
    const slug = userSlug || '';
    const { t } = useTranslation();

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);

    if (!page) {
        return <Layout title={t('Page Not Found')}><div>{t('Page not found')}</div></Layout>;
    }

    return (
        <Layout title={page.title}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div>
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{page.title}</h2>
                        <ul className="flex flex-wrap items-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug: slug })}>{t('Home')}</Link>
                                <ChevronRight className="w-3 h-3 mx-2" />
                            </li>
                            <li className="font-bold capitalize">{page.title}</li>
                        </ul>
                       
                    </div>
                </div>
            </section>

            {/* Page Content */}
            <section className="lg:py-16 py-10 bg-gray-50">
                <div className="md:container w-full mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        
                        <div className="bg-[#674B2F]/10 rounded-lg shadow-sm p-8">
                        {page.description && (
                            <div className="mb-6">
                                <p className="text-gray-700 text-lg leading-relaxed">
                                    {page.description}
                                </p>
                            </div>
                        )}
                            {page.contents ? (
                                <div
                                    className="prose prose-lg max-w-none text-gray-800 [&>*]:text-gray-800 [&>p]:text-gray-800 [&>h1]:text-gray-900 [&>h2]:text-gray-900 [&>h3]:text-gray-900"
                                    dangerouslySetInnerHTML={{ __html: page.contents }}
                                />
                            ) : (
                                <div className="text-center py-12">
                                    <p className="text-gray-500 text-lg">{t('No content available for this page.')}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
};

export default CustomPage;