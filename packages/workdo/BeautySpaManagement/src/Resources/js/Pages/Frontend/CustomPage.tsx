import { Head } from '@inertiajs/react';
import Layout from './Layout';
import { useTranslation } from 'react-i18next';

interface CustomPage {
    id: number;
    title: string;
    slug: string;
    contents: string;
    description: string;
}

interface CustomPageProps {
    title: string;
    customPage: CustomPage;
    userSlug?: string;
    customPages?: CustomPage[];
    beautySpaSettings?: any;
}

export default function CustomPage({ title, customPage, userSlug = '', customPages = [], beautySpaSettings }: CustomPageProps) {
    const { t } = useTranslation();

    return (
        <Layout title={title} userSlug={userSlug} customPages={customPages} beautySpaSettings={beautySpaSettings}>
            <Head title={title} />

            <main className="pt-20 -mt-4">
                {/* Hero Section */}
                <section className="relative lg:py-16 py-10 bg-[#df98962b] overflow-hidden">
                    <div className="absolute top-0 end-0 md:w-64 md:h-64 w-48 h-48 bg-[#df9896] opacity-5 rounded-full translate-x-20 -translate-y-20"></div>
                    <div className="absolute bottom-0 start-0 md:w-96 md:h-96 w-64 h-64 bg-[#df9896] opacity-5 rounded-full -translate-x-40 translate-y-20"></div>

                    <div className="container mx-auto px-4 relative z-10">
                        <div className="text-center max-w-2xl mx-auto">
                            <h2 className="text-4xl md:text-5xl font-bold text-gray-800 mt-2 md:mb-4 mb-2">{customPage.title}</h2>
                            <div className="w-24 h-1 bg-[#df9896] mx-auto rounded-full md:mb-6 mb-4"></div>
                            <p className="md:text-lg sm:text-[16px] text-[14px] text-gray-600">{customPage.description}</p>
                        </div>
                    </div>
                </section>

                {/* Content Section */}
                <section className="md:py-16 py-12 bg-white">
                    <div className="container mx-auto px-4">
                        <div className="max-w-4xl mx-auto">
                            <div className="prose max-w-none">
                                <div className="lg:mb-12 mb-6" dangerouslySetInnerHTML={{ __html: customPage.contents }} />
                                <div className="border-t border-gray-200 pt-8 text-sm text-gray-500">
                                    <p>{t('Last Updated:')} {new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </Layout>
    );
}