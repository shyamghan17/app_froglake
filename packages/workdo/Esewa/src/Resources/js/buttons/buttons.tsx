import { RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { useTranslation } from 'react-i18next';
import { usePage } from '@inertiajs/react';
import { getAdminSetting, getCompanySetting, getPackageFavicon } from '@/utils/helpers';

export const paymentMethodBtn = (data?: any) => {
    const { t } = useTranslation();
    const esewaEnabled = getAdminSetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-payment',
            dataUrl: route('esewa.plan.pay'),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg w-full">
                    <RadioGroupItem value="esewa" id="esewa" />
                    <Label htmlFor="esewa" className="cursor-pointer flex items-center space-x-2">
                        <div>
                            <div className="font-medium text-gray-900 dark:text-white">{t('Esewa')}</div>
                        </div>
                        <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-10 w-10" />
                    </Label>
                </div>
            )
        }];
    }
    return [];
};

export const bookingPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-booking-payment',
            dataUrl: route('esewa.booking.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg w-full">
                    <Label htmlFor="esewa-booking" className="cursor-pointer flex items-center space-x-2">
                        <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-10 w-10" />
                        <div>
                            <div className="font-medium text-gray-900 dark:text-white">{t('Esewa')}</div>
                        </div>
                    </Label>
                    <RadioGroupItem value="esewa" id="esewa-booking" />
                </div>
            )
        }];
    }
    return [];
};

export const beautySpaPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-beauty-spa-payment',
            dataUrl: route('esewa.beauty-spa.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <Label htmlFor="esewa-beauty-payment"
                    className="block border border-gray-200 rounded-lg p-4 hover:border-green-500 cursor-pointer transition-all duration-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 rounded-full overflow-hidden bg-white border">
                                <img src={getPackageFavicon('Esewa')} alt="Esewa Logo" className="object-contain w-full h-full" />
                            </div>
                            <div>
                                <h5 className="text-base font-medium text-gray-800">{t('Esewa')}</h5>
                            </div>
                        </div>
                        <RadioGroupItem value="esewa" id="esewa-beauty-payment" />
                    </div>
                </Label>
            )
        }];
    }
    return [];
};

export const lmsPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-lms-payment',
            dataUrl: route('esewa.lms.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg w-full hover:border-green-500 transition-colors cursor-pointer">
                    <RadioGroupItem value="esewa" id="esewa-lms" />
                    <Label htmlFor="esewa-lms" className="cursor-pointer flex items-center space-x-3 flex-1">
                        <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-8 w-8" />
                        <div>
                            <div className="font-medium text-gray-900">{t('Esewa')}</div>
                            <div className="text-sm text-gray-500">{t('Pay securely with Esewa')}</div>
                        </div>
                    </Label>
                </div>
            )
        }];
    }
    return [];
};

export const parkingPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-parking-payment',
            dataUrl: route('esewa.parking.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors">
                    <RadioGroupItem value="esewa" id="esewa-parking" />
                    <Label htmlFor="esewa-parking" className="cursor-pointer flex items-center space-x-3 flex-1">
                        <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-8 w-8" />
                        <div>
                            <div className="font-medium text-gray-900">{t('Esewa')}</div>
                        </div>
                    </Label>
                </div>
            )
        }];
    }
    return [];
};

export const laundryPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-laundry-payment',
            dataUrl: route('esewa.laundry.payment.pay', { userSlug: userSlug }),
            component: (
                <Label htmlFor="esewa-laundry-payment"
                    className="block border border-gray-200 rounded-lg p-4 hover:border-green-500 cursor-pointer transition-all duration-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 rounded-full overflow-hidden bg-white border">
                                <img src={getPackageFavicon('Esewa')} alt="Esewa Logo" className="object-contain w-full h-full" />
                            </div>
                            <div>
                                <h5 className="text-base font-medium text-gray-800">{t('Esewa')}</h5>
                            </div>
                        </div>
                        <RadioGroupItem value="esewa" id="esewa-laundry-payment" />
                    </div>
                </Label>
            )
        }];
    }
    return [];
};

export const eventsPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');
    const isSelected = data?.selectedMethod === 'esewa';

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa-events-payment',
            dataUrl: route('esewa.events-management.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <label className="cursor-pointer">
                    <input
                        type="radio"
                        name="paymentMethod"
                        value="esewa"
                        className="hidden"
                        checked={isSelected}
                        onChange={() => data?.onMethodChange?.('esewa')}
                        required
                    />
                    <div className={`p-4 border-2 rounded-lg transition-all hover:border-green-200 flex items-center ${
                        isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'
                    }`}>
                        <div className={`w-4 h-4 rounded-full border-2 mr-3 flex-shrink-0 ${
                            isSelected ? 'border-green-500 bg-green-500' : 'border-gray-300'
                        }`}>
                            {isSelected && <div className="w-2 h-2 bg-white rounded-full m-auto mt-0.5"></div>}
                        </div>
                        <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-8 w-8 mr-3" />
                        <span className="font-semibold">{t('Esewa')}</span>
                    </div>
                </label>
            )
        }];
    }
    return [];
};

export const holidayzPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const esewaEnabled = getCompanySetting('esewa_enabled');

    if (esewaEnabled === 'on') {
        return [{
            id: 'esewa',
            dataUrl: route('esewa.holidayz.payment.pay', { userSlug: userSlug }),
            component: (
                <div className="flex items-center space-x-3">
                    <img src={getPackageFavicon('Esewa')} alt="Esewa" className="h-8 w-8" />
                    <div>
                        <div className="font-medium text-gray-900">{t('Esewa')}</div>
                        <div className="text-sm text-gray-500">{t('Pay securely with Esewa')}</div>
                    </div>
                </div>
            )
        }];
    }
    return [];
};