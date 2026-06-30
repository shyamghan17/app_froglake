import * as React from 'react';
import { useTranslation } from 'react-i18next';

import { cn } from '@/lib/utils';

import InputError from './input-error';
import { Input } from './input';
import { Label } from './label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './select';

type CountryOption = {
    code: string;
    name: string;
    dialCode: string;
    timezones?: string[];
};

const COUNTRY_OPTIONS: CountryOption[] = [
    { code: 'US', name: 'United States', dialCode: '1', timezones: ['America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'America/Phoenix', 'America/Anchorage', 'Pacific/Honolulu'] },
    { code: 'CA', name: 'Canada', dialCode: '1', timezones: ['America/Toronto', 'America/Vancouver', 'America/Edmonton', 'America/Halifax', 'America/St_Johns'] },
    { code: 'MX', name: 'Mexico', dialCode: '52', timezones: ['America/Mexico_City', 'America/Cancun', 'America/Tijuana'] },
    { code: 'BR', name: 'Brazil', dialCode: '55', timezones: ['America/Sao_Paulo', 'America/Manaus', 'America/Recife'] },
    { code: 'AR', name: 'Argentina', dialCode: '54', timezones: ['America/Argentina/Buenos_Aires'] },
    { code: 'CL', name: 'Chile', dialCode: '56', timezones: ['America/Santiago'] },
    { code: 'CO', name: 'Colombia', dialCode: '57', timezones: ['America/Bogota'] },
    { code: 'PE', name: 'Peru', dialCode: '51', timezones: ['America/Lima'] },
    { code: 'GB', name: 'United Kingdom', dialCode: '44', timezones: ['Europe/London'] },
    { code: 'IE', name: 'Ireland', dialCode: '353', timezones: ['Europe/Dublin'] },
    { code: 'FR', name: 'France', dialCode: '33', timezones: ['Europe/Paris'] },
    { code: 'DE', name: 'Germany', dialCode: '49', timezones: ['Europe/Berlin'] },
    { code: 'ES', name: 'Spain', dialCode: '34', timezones: ['Europe/Madrid'] },
    { code: 'IT', name: 'Italy', dialCode: '39', timezones: ['Europe/Rome'] },
    { code: 'NL', name: 'Netherlands', dialCode: '31', timezones: ['Europe/Amsterdam'] },
    { code: 'BE', name: 'Belgium', dialCode: '32', timezones: ['Europe/Brussels'] },
    { code: 'PT', name: 'Portugal', dialCode: '351', timezones: ['Europe/Lisbon'] },
    { code: 'CH', name: 'Switzerland', dialCode: '41', timezones: ['Europe/Zurich'] },
    { code: 'AT', name: 'Austria', dialCode: '43', timezones: ['Europe/Vienna'] },
    { code: 'SE', name: 'Sweden', dialCode: '46', timezones: ['Europe/Stockholm'] },
    { code: 'NO', name: 'Norway', dialCode: '47', timezones: ['Europe/Oslo'] },
    { code: 'DK', name: 'Denmark', dialCode: '45', timezones: ['Europe/Copenhagen'] },
    { code: 'FI', name: 'Finland', dialCode: '358', timezones: ['Europe/Helsinki'] },
    { code: 'PL', name: 'Poland', dialCode: '48', timezones: ['Europe/Warsaw'] },
    { code: 'CZ', name: 'Czech Republic', dialCode: '420', timezones: ['Europe/Prague'] },
    { code: 'HU', name: 'Hungary', dialCode: '36', timezones: ['Europe/Budapest'] },
    { code: 'RO', name: 'Romania', dialCode: '40', timezones: ['Europe/Bucharest'] },
    { code: 'GR', name: 'Greece', dialCode: '30', timezones: ['Europe/Athens'] },
    { code: 'TR', name: 'Turkey', dialCode: '90', timezones: ['Europe/Istanbul'] },
    { code: 'UA', name: 'Ukraine', dialCode: '380', timezones: ['Europe/Kyiv'] },
    { code: 'AE', name: 'United Arab Emirates', dialCode: '971', timezones: ['Asia/Dubai'] },
    { code: 'SA', name: 'Saudi Arabia', dialCode: '966', timezones: ['Asia/Riyadh'] },
    { code: 'QA', name: 'Qatar', dialCode: '974', timezones: ['Asia/Qatar'] },
    { code: 'KW', name: 'Kuwait', dialCode: '965', timezones: ['Asia/Kuwait'] },
    { code: 'OM', name: 'Oman', dialCode: '968', timezones: ['Asia/Muscat'] },
    { code: 'BH', name: 'Bahrain', dialCode: '973', timezones: ['Asia/Bahrain'] },
    { code: 'IL', name: 'Israel', dialCode: '972', timezones: ['Asia/Jerusalem'] },
    { code: 'JO', name: 'Jordan', dialCode: '962', timezones: ['Asia/Amman'] },
    { code: 'EG', name: 'Egypt', dialCode: '20', timezones: ['Africa/Cairo'] },
    { code: 'MA', name: 'Morocco', dialCode: '212', timezones: ['Africa/Casablanca'] },
    { code: 'ZA', name: 'South Africa', dialCode: '27', timezones: ['Africa/Johannesburg'] },
    { code: 'NG', name: 'Nigeria', dialCode: '234', timezones: ['Africa/Lagos'] },
    { code: 'KE', name: 'Kenya', dialCode: '254', timezones: ['Africa/Nairobi'] },
    { code: 'GH', name: 'Ghana', dialCode: '233', timezones: ['Africa/Accra'] },
    { code: 'IN', name: 'India', dialCode: '91', timezones: ['Asia/Kolkata', 'Asia/Calcutta'] },
    { code: 'PK', name: 'Pakistan', dialCode: '92', timezones: ['Asia/Karachi'] },
    { code: 'BD', name: 'Bangladesh', dialCode: '880', timezones: ['Asia/Dhaka'] },
    { code: 'NP', name: 'Nepal', dialCode: '977', timezones: ['Asia/Kathmandu', 'Asia/Katmandu'] },
    { code: 'LK', name: 'Sri Lanka', dialCode: '94', timezones: ['Asia/Colombo'] },
    { code: 'CN', name: 'China', dialCode: '86', timezones: ['Asia/Shanghai', 'Asia/Urumqi'] },
    { code: 'HK', name: 'Hong Kong', dialCode: '852', timezones: ['Asia/Hong_Kong'] },
    { code: 'TW', name: 'Taiwan', dialCode: '886', timezones: ['Asia/Taipei'] },
    { code: 'JP', name: 'Japan', dialCode: '81', timezones: ['Asia/Tokyo'] },
    { code: 'KR', name: 'South Korea', dialCode: '82', timezones: ['Asia/Seoul'] },
    { code: 'SG', name: 'Singapore', dialCode: '65', timezones: ['Asia/Singapore'] },
    { code: 'MY', name: 'Malaysia', dialCode: '60', timezones: ['Asia/Kuala_Lumpur'] },
    { code: 'ID', name: 'Indonesia', dialCode: '62', timezones: ['Asia/Jakarta', 'Asia/Makassar'] },
    { code: 'TH', name: 'Thailand', dialCode: '66', timezones: ['Asia/Bangkok'] },
    { code: 'VN', name: 'Vietnam', dialCode: '84', timezones: ['Asia/Ho_Chi_Minh'] },
    { code: 'PH', name: 'Philippines', dialCode: '63', timezones: ['Asia/Manila'] },
    { code: 'AU', name: 'Australia', dialCode: '61', timezones: ['Australia/Sydney', 'Australia/Melbourne', 'Australia/Brisbane', 'Australia/Perth', 'Australia/Adelaide'] },
    { code: 'NZ', name: 'New Zealand', dialCode: '64', timezones: ['Pacific/Auckland'] },
];

const COUNTRY_BY_CODE = new Map(COUNTRY_OPTIONS.map((country) => [country.code, country]));
const COUNTRIES_BY_DIAL_CODE = [...COUNTRY_OPTIONS].sort((left, right) => right.dialCode.length - left.dialCode.length);
const DEFAULT_COUNTRY_CODE = 'US';
const DEFAULT_NATIONAL_PLACEHOLDER = '9876543210';

interface PhoneInputProps {
    label?: string;
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    error?: string;
    className?: string;
    id?: string;
    required?: boolean;
    readOnly?: boolean;
    style?: React.CSSProperties;
    timezone?: string;
}

function sanitizePhoneDigits(value: string) {
    return value.replace(/\D/g, '');
}

function getBrowserTimezone() {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch {
        return undefined;
    }
}

function resolveCountryCodeFromTimezone(timezone?: string) {
    if (!timezone) {
        return DEFAULT_COUNTRY_CODE;
    }

    const exactMatch = COUNTRY_OPTIONS.find((country) => country.timezones?.includes(timezone));
    if (exactMatch) {
        return exactMatch.code;
    }

    if (timezone.startsWith('Europe/')) {
        return 'GB';
    }

    if (timezone.startsWith('Australia/')) {
        return 'AU';
    }

    if (timezone.startsWith('Pacific/')) {
        return 'NZ';
    }

    if (timezone.startsWith('Africa/')) {
        return 'ZA';
    }

    if (timezone.startsWith('Asia/')) {
        return 'IN';
    }

    if (timezone.startsWith('America/')) {
        return 'US';
    }

    return DEFAULT_COUNTRY_CODE;
}

function parseFormattedPhone(value: string) {
    const trimmedValue = value.trim();
    if (!trimmedValue) {
        return {
            countryCode: undefined,
            nationalNumber: '',
        };
    }

    const normalizedValue = trimmedValue.replace(/[^\d+]/g, '');
    const digits = sanitizePhoneDigits(normalizedValue);

    if (!normalizedValue.startsWith('+')) {
        return {
            countryCode: undefined,
            nationalNumber: digits,
        };
    }

    const matchedCountry = COUNTRIES_BY_DIAL_CODE.find((country) => digits.startsWith(country.dialCode));
    if (!matchedCountry) {
        return {
            countryCode: undefined,
            nationalNumber: digits,
        };
    }

    return {
        countryCode: matchedCountry.code,
        nationalNumber: digits.slice(matchedCountry.dialCode.length),
    };
}

function formatPhoneValue(countryCode: string, nationalNumber: string) {
    const sanitizedNationalNumber = sanitizePhoneDigits(nationalNumber);
    if (!sanitizedNationalNumber) {
        return '';
    }

    const country = COUNTRY_BY_CODE.get(countryCode) ?? COUNTRY_BY_CODE.get(DEFAULT_COUNTRY_CODE);
    return `+${country?.dialCode ?? COUNTRY_BY_CODE.get(DEFAULT_COUNTRY_CODE)?.dialCode}${sanitizedNationalNumber}`;
}

export function PhoneInputComponent({
    label,
    value,
    onChange,
    placeholder,
    error,
    className,
    id,
    required,
    readOnly,
    style,
    timezone,
}: PhoneInputProps) {
    const { t } = useTranslation();
    const inputId = React.useId();
    const resolvedId = id ?? inputId;
    const fallbackTimezone = React.useMemo(() => timezone ?? getBrowserTimezone(), [timezone]);
    const defaultCountryCode = React.useMemo(
        () => resolveCountryCodeFromTimezone(fallbackTimezone),
        [fallbackTimezone],
    );
    const parsedValue = React.useMemo(() => parseFormattedPhone(value), [value]);

    const [selectedCountryCode, setSelectedCountryCode] = React.useState(
        parsedValue.countryCode ?? defaultCountryCode,
    );
    const [nationalNumber, setNationalNumber] = React.useState(parsedValue.nationalNumber);

    React.useEffect(() => {
        setNationalNumber(parsedValue.nationalNumber);

        if (parsedValue.countryCode) {
            setSelectedCountryCode(parsedValue.countryCode);
            return;
        }

        if (!value) {
            setSelectedCountryCode(defaultCountryCode);
        }
    }, [defaultCountryCode, parsedValue.countryCode, parsedValue.nationalNumber, value]);

    const handleCountryChange = React.useCallback(
        (countryCode: string) => {
            setSelectedCountryCode(countryCode);
            onChange(formatPhoneValue(countryCode, nationalNumber));
        },
        [nationalNumber, onChange],
    );

    const handleNumberChange = React.useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const sanitizedNationalNumber = sanitizePhoneDigits(event.target.value);
            setNationalNumber(sanitizedNationalNumber);
            onChange(formatPhoneValue(selectedCountryCode, sanitizedNationalNumber));
        },
        [onChange, selectedCountryCode],
    );

    return (
        <div>
            {label && <Label htmlFor={resolvedId} required={required}>{label}</Label>}
            <div className="grid grid-cols-1 gap-3 sm:grid-cols-[220px_minmax(0,1fr)]">
                <Select
                    value={selectedCountryCode}
                    onValueChange={handleCountryChange}
                    disabled={readOnly}
                >
                    <SelectTrigger className={cn(error && 'border-destructive')}>
                        <SelectValue placeholder={t('Select country')} />
                    </SelectTrigger>
                    <SelectContent searchable className="max-h-80">
                        {COUNTRY_OPTIONS.map((country) => (
                            <SelectItem key={country.code} value={country.code}>
                                {country.name} (+{country.dialCode})
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>

                <Input
                    id={resolvedId}
                    type="tel"
                    value={nationalNumber}
                    onChange={handleNumberChange}
                    placeholder={placeholder || DEFAULT_NATIONAL_PLACEHOLDER}
                    className={cn(className, readOnly && 'bg-gray-50')}
                    required={required}
                    readOnly={readOnly}
                    style={style}
                    inputMode="tel"
                    autoComplete="tel-national"
                    error={error}
                />
            </div>
            <InputError message={error} />
        </div>
    );
}
