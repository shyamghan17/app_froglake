import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';
import { ChevronRight, ArrowRight } from 'lucide-react';
import Layout from '../../Components/Frontend/Layout';

interface CameraKitProps {
    userSlug?: string;
}

const CameraKit = ({ userSlug = '' }: CameraKitProps) => {
    const { photoStudioSettings, cameraKits, equipmentTypes } = usePage<{ photoStudioSettings?: any; cameraKits?: any[]; equipmentTypes?: any[] }>().props;
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState(() => {
        return equipmentTypes?.[0]?.name?.toLowerCase() || 'cameras';
    });

    const img = (name: string) => getImagePath(`packages/workdo/PhotoStudioManagement/src/Resources/assets/images/${name}`);
    
    const titleSection = photoStudioSettings?.title_section || {};
    
    // Get equipment data organized by type
    const equipmentByType = cameraKits?.reduce((acc: any, item: any) => {
        const typeName = item.equipment_type?.name?.toLowerCase() || 'other';
        if (!acc[typeName]) acc[typeName] = [];
        
        // Handle specifications properly
        let specs = [];
        if (item.specifications) {
            if (typeof item.specifications === 'string') {
                try {
                    specs = JSON.parse(item.specifications);
                } catch (e) {
                    specs = [];
                }
            } else if (Array.isArray(item.specifications)) {
                specs = item.specifications;
            }
        }
        
        acc[typeName].push({
            ...item,
            name: item.name,
            type: item.equipment_type?.name || 'Equipment',
            image: item.image ? getImagePath(item.image) : img(`${typeName}-1.png`),
            specs: specs,
            bestFor: item.description,
            features: item.description,
            idealFor: item.description,
            use: item.description
        });
        return acc;
    }, {}) || {};

    const cameras = equipmentByType.cameras || [];
    const lenses = equipmentByType.lenses || [];
    const lighting = equipmentByType.lighting || [];
    const accessories = equipmentByType.accessories || [];

    const EquipmentCard = ({ item, showUse = false, showFeatures = false, showIdealFor = false }: any) => (
        <div className="bg-gray-50 lg:p-6 p-4 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 hover:border-[#674B2F]/10">
            <div className="relative overflow-hidden mb-4">
                <img src={item.image} alt={item.name} className="w-full h-64 object-cover transition-transform duration-500 hover:scale-105" />
            </div>
            <h4 className="text-xl mb-4 flex items-center font-medium">
                {item.name}
                <span className="ms-3 text-xs py-1 px-2 bg-[#674B2F] text-[#ffffff] font-medium">{item.type}</span>
            </h4> 
            <div className="space-y-2 text-sm font-medium">
                {item.specs && item.specs.length > 0 ? (
                    item.specs.map((spec: any, idx: number) => (
                        <p key={idx}><strong>{spec.field_name}:</strong> {spec.description}</p>
                    ))
                ) : (
                    <p className="text-gray-500">{t('No specifications available')}</p>
                )}
            </div>
            <div className="mt-4 px-3 py-2 bg-[#674B2F]/10">
                <p className="text-sm font-medium">
                    <strong>{showUse ? t('Use') : showFeatures ? t('Features') : t('Ideal For')}:</strong> {showUse ? (item.tag_names || []).join(', ') || t('General Use') : (item.bestFor || item.features || item.idealFor || item.use)}
                </p>
            </div>
            <span className="text-sm text-gray-500 mt-4 block font-medium">{item.description || t('In studio & available for rental')}</span>
        </div>
    );

    return (
        <Layout title={titleSection?.camera_kit_page_title || t("Camera Equipment")} userSlug={userSlug}>
            {/* Banner Section */}
            <section className="banner-section relative z-[1] lg:py-24 sm:py-12 py-10">
                <img src={photoStudioSettings?.banner_image ? getImagePath(photoStudioSettings.banner_image) : img('common-banner.png')} className="absolute z-[-1] inset-0 w-full h-full object-cover lg:object-center object-left" alt="banner" />
                <div className="md:container w-full mx-auto px-4">
                    <div className="sm:text-start text-center">
                        <h2 className="text-3xl md:text-4xl lg:text-5xl mb-4 capitalize font-medium">{titleSection?.camera_kit_page_title || t("Camera Equipment")}</h2>
                        <ul className="flex flex-wrap items-center sm:justify-start justify-center capitalize">
                            <li className="flex items-center capitalize">
                                <Link href={route('photo-studio-management.frontend.index', { userSlug })}>{t('Home')}</Link>
                                <ChevronRight className="mx-2" size={12} />
                            </li>
                            <li className="font-bold capitalize">{t('Camera Kit')}</li>
                        </ul>
                    </div>
                </div>
            </section>

            {/* Equipment Overview */}
            <section className="lg:py-16 py-10">
                <div className="md:container w-full mx-auto px-4">
                    <div className="text-center mb-6">
                        <span className="inline-block capitalize mb-2 text-primary lg:text-lg font-medium text-[#674B2F]">{titleSection?.camera_kit_details_label || t("Professional Equipment")}</span>
                        <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{titleSection?.camera_kit_details_title || t("Our Equipment Arsenal")}</h2>
                    </div>

                    {/* Equipment Tabs */}
                    <div className="equipment-tabs">
                        {/* Tab Navigation */}
                        <div className="flex items-center justify-center sm:mb-8 mb-6">
                            <div className="flex overflow-x-auto text-nowrap sm:gap-0 gap-4">
                                {equipmentTypes?.map(type => (
                                    <button
                                        key={type.id}
                                        onClick={() => setActiveTab(type.name.toLowerCase())}
                                        className={`sm:px-4 sm:py-2 pb-2 text-base font-medium transition-all duration-300 ${
                                            activeTab === type.name.toLowerCase()
                                                ? 'sm:bg-[#674B2F] sm:text-[#ffffff] text-[#674B2F]'
                                                : 'sm:bg-gray-100 text-gray-600'
                                        }`}
                                    >
                                        {type.name}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Tab Content */}
                        <div className="tab-content">
                            {equipmentTypes?.map(type => {
                                const typeEquipment = equipmentByType[type.name.toLowerCase()] || [];
                                return activeTab === type.name.toLowerCase() && (
                                    <div key={type.id} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6 gap-5">
                                        {typeEquipment.length > 0 ? (
                                            typeEquipment.map((item: any, idx: number) => (
                                                <EquipmentCard key={idx} item={item} showUse={true} />
                                            ))
                                        ) : (
                                            <div className="col-span-full text-center py-12">
                                                <p className="text-gray-500">{t('No')} {type.name.toLowerCase()} {t('equipment available')}</p>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>

                        {/* Equipment Overview CTA */}
                        <div className="text-center lg:mt-8 mt-6">
                            <Link href={route('photo-studio-management.frontend.contact', { userSlug })} className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#111111] hover:bg-transparent text-[#ffffff] hover:text-[#111111] border border-[#111111] hover:border-[#111111] transition-all duration-300 capitalize font-medium">
                                <span>{t('Request Custom Equipment')}</span>
                                <ArrowRight className="rtl:scale-x-[-1]" size={16} />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Equipment Specs Table */}
            <section className="lg:py-16 py-10 bg-[#674B2F]/5">
                <div className="md:container w-full mx-auto px-4">
                    <div className="text-center lg:mb-8 mb-5">
                        <span className="inline-block capitalize mb-2 text-primary lg:text-lg text-[#674B2F] font-medium">{titleSection?.equipment_specs_label || t("Equipment Specs")}</span>
                        <h2 className="text-2xl sm:text-3xl md:text-4xl font-medium">{titleSection?.equipment_specs_title || t("Complete Equipment Specifications")}</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full bg-white shadow-lg overflow-hidden">
                            <thead className="bg-[#674B2F]">
                                <tr>
                                    <th className="lg:px-6 px-4 lg:py-4 py-3 text-start text-sm font-medium uppercase tracking-wider text-[#ffffff]">{t('Equipment')}</th>
                                    <th className="lg:px-6 px-4 lg:py-4 py-3 text-start text-sm font-medium uppercase tracking-wider text-[#ffffff]">{t('Model')}</th>
                                    <th className="lg:px-6 px-4 lg:py-4 py-3 text-start text-sm font-medium uppercase tracking-wider text-[#ffffff]">{t('Key Specs')}</th>
                                    <th className="lg:px-6 px-4 lg:py-4 py-3 text-start text-sm font-medium uppercase tracking-wider text-[#ffffff]">{t('Primary Use')}</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {cameraKits?.map((item: any, index: number) => (
                                    <tr key={index} className="hover:bg-gray-50 transition duration-150">
                                        <td className="lg:px-6 px-4 lg:py-4 py-3 whitespace-nowrap text-sm font-medium">{item.equipment_type?.name || t('Equipment')}</td>
                                        <td className="lg:px-6 px-4 lg:py-4 py-3 whitespace-nowrap text-sm font-medium">{item.name}</td>
                                        <td className="lg:px-6 px-4 lg:py-4 py-3 text-sm whitespace-nowrap font-medium">
                                            {(() => {
                                                let specs = [];
                                                if (item.specifications) {
                                                    if (typeof item.specifications === 'string') {
                                                        try {
                                                            specs = JSON.parse(item.specifications);
                                                        } catch (e) {
                                                            return item.specifications;
                                                        }
                                                    } else if (Array.isArray(item.specifications)) {
                                                        specs = item.specifications;
                                                    }
                                                }
                                                
                                                if (specs.length > 0) {
                                                    return specs.map((spec: any) => `${spec.description}`).join(', ');
                                                }
                                                
                                                return item.description || '';
                                            })()
                                            }
                                        </td>
                                        <td className="lg:px-6 px-4 lg:py-4 py-3 text-sm whitespace-nowrap font-medium">
                                            {(item.tag_names || []).join(', ') || t('General Use')}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </Layout>
    );
};

export default CameraKit;
