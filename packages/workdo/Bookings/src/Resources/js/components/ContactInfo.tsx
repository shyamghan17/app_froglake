import React from 'react';
import { MapPin, Clock, Phone, Mail } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface ContactInfoProps {
    showTitle?: boolean;
    className?: string;
    primaryColor?: string;
    contactData?: any;
}

export default function ContactInfo({ showTitle = true, className = '', primaryColor = '#52816D', contactData }: ContactInfoProps) {
    const { t } = useTranslation();

    return (
        <div className={className}>
            {showTitle && (
                <h3 className="sm:text-2xl text-xl font-bold mb-6" style={{ color: primaryColor }}>{t('Our Information')}</h3>
            )}
            
            <div className="space-y-5">
                <div className="flex items-start">
                    <div className="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                        <MapPin className="w-4 h-4" />
                    </div>
                    <div>
                        <h4 className="font-semibold text-gray-800 mb-1">{t('Office Location')}</h4>
                        <p className="text-gray-600 whitespace-pre-line">{contactData.address}</p>
                    </div>
                </div>
                
                <div className="flex items-start">
                    <div className="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                        <Clock className="w-4 h-4" />
                    </div>
                    <div>
                        <h4 className="font-semibold text-gray-800 mb-1">{t('Business Hours')}</h4>
                        <p className="text-gray-600 whitespace-pre-line">{contactData.hours}</p>
                    </div>
                </div>
                
                <div className="flex items-start">
                    <div className="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                        <Phone className="w-4 h-4" />
                    </div>
                    <div>
                        <h4 className="font-semibold text-gray-800 mb-1">{t('Phone Numbers')}</h4>
                        {contactData.phone}
                    </div>
                </div>
                
                <div className="flex items-start">
                    <div className="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center mr-4" style={{ backgroundColor: primaryColor }}>
                        <Mail className="w-4 h-4" />
                    </div>
                    <div>
                        <h4 className="font-semibold text-gray-800 mb-1">{t('Email Addresses')}</h4>
                        {contactData.email}
                    </div>
                </div>
            </div>
        </div>
    );
}