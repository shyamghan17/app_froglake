import { useState, useEffect, useRef } from 'react';
import { Globe } from 'lucide-react';
import { useTranslation } from 'react-i18next';

const languagesData = [
    { "code": "en", "name": "English", "countryCode": "GB" },
    { "code": "es", "name": "Español", "countryCode": "ES", "enabled": true },
    { "code": "ar", "name": "العربية", "countryCode": "SA", "enabled": true },
    { "code": "fr", "name": "Français", "countryCode": "FR" },
    { "code": "de", "name": "Deutsch", "countryCode": "DE" },
    { "code": "it", "name": "Italiano", "countryCode": "IT" },
    { "code": "pt", "name": "Português", "countryCode": "PT" },
    { "code": "ru", "name": "Русский", "countryCode": "RU" },
    { "code": "zh", "name": "中文", "countryCode": "CN" }
];

const getCountryFlag = (countryCode: string): string => {
    const codePoints = countryCode
        .toUpperCase()
        .split('')
        .map(char => 127397 + char.charCodeAt(0));
    return String.fromCodePoint(...codePoints);
};

const languages = languagesData
    .filter((lang: any) => lang.enabled !== false)
    .map((lang: any) => ({
        ...lang,
        flag: getCountryFlag(lang.countryCode)
    }));

interface BookingLanguageSwitcherProps {
    primaryColor: string;
    currentLanguage?: string;
    onLanguageChange?: (languageCode: string) => void;
}

export function BookingLanguageSwitcher({ 
    primaryColor, 
    currentLanguage = 'en',
    onLanguageChange 
}: BookingLanguageSwitcherProps) {
    const { i18n } = useTranslation();
    const [selectedLanguage, setSelectedLanguage] = useState(currentLanguage);
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        setSelectedLanguage(currentLanguage);
    }, [currentLanguage]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
            document.addEventListener('keydown', handleKeyDown);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen]);

    const handleLanguageChange = (languageCode: string) => {
        setSelectedLanguage(languageCode);
        setIsOpen(false);
        i18n.changeLanguage(languageCode);
        onLanguageChange?.(languageCode);
    };

    const currentLang = languages.find(lang => lang.code === selectedLanguage) || languages[0];

    return (
        <div ref={dropdownRef} className="relative inline-block text-start">
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                onKeyDown={(e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        setIsOpen(!isOpen);
                    }
                }}
                className="inline-flex items-center justify-center w-full rounded-md border px-4 py-2 bg-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                style={{ 
                    color: primaryColor, 
                    borderColor: primaryColor, 
                    '--hover-bg': `${primaryColor}0d`,
                    '--focus-ring-color': `${primaryColor}40`
                } as React.CSSProperties}
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = `${primaryColor}0d`}
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = 'white'}
                onFocus={(e) => e.currentTarget.style.boxShadow = `0 0 0 2px ${primaryColor}40`}
                onBlur={(e) => e.currentTarget.style.boxShadow = 'none'}
                aria-expanded={isOpen}
                aria-haspopup="listbox"
                aria-label="Select language"
            >
                <Globe className="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" />
                <span className="w-[60px] text-left rtl:text-right">{currentLang.name}</span>
                <svg className="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
            </button>

            {isOpen && (
                <div className="absolute right-0 rtl:right-auto rtl:left-0 z-20 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                        <div className="py-1 max-h-60 overflow-y-auto">
                            {languages.map((language) => (
                                <button
                                    key={language.code}
                                    onClick={() => handleLanguageChange(language.code)}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter' || e.key === ' ') {
                                            e.preventDefault();
                                            handleLanguageChange(language.code);
                                        }
                                    }}
                                    className={`w-full text-left rtl:text-right px-4 py-2 text-sm hover:bg-gray-100 flex items-center transition-colors focus:outline-none focus:bg-gray-100 ${
                                        selectedLanguage === language.code ? 'bg-gray-50' : ''
                                    }`}
                                    style={{
                                        '--hover-bg': `${primaryColor}0d`
                                    } as React.CSSProperties}
                                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = `${primaryColor}0d`}
                                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = selectedLanguage === language.code ? '#f9fafb' : 'white'}
                                    onFocus={(e) => e.currentTarget.style.backgroundColor = `${primaryColor}0d`}
                                    onBlur={(e) => e.currentTarget.style.backgroundColor = selectedLanguage === language.code ? '#f9fafb' : 'white'}
                                    role="option"
                                    aria-selected={selectedLanguage === language.code}
                                >
                                    <span>{language.name}</span>
                                    {selectedLanguage === language.code && (
                                        <svg className="w-4 h-4 ml-auto rtl:ml-0 rtl:mr-auto" style={{ color: primaryColor }} fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                        </svg>
                                    )}
                                </button>
                            ))}
                        </div>
                </div>
            )}
        </div>
    );
}