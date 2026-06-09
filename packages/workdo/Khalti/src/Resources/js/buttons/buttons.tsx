import { RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { useTranslation } from 'react-i18next';
import { usePage } from '@inertiajs/react';
import { getAdminSetting, getCompanySetting, isPackageActive, getPackageFavicon } from '@/utils/helpers';

export const paymentMethodBtn = (data?: any) => {

    const { t } = useTranslation();
    const { auth } = usePage().props as any;

    const khaltiEnabled = getAdminSetting('khalti_enabled');

    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-payment',
            dataUrl: route('khalti.plan.pay'),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg w-full">
                    <RadioGroupItem value="khalti" id="khalti" />
                    <Label htmlFor="khalti" className="cursor-pointer flex items-center space-x-2">
                        <div>
                            <div className="font-medium text-gray-900 dark:text-white">{t('Khalti')}</div>
                        </div>
                        <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-10 w-10" />
                    </Label>
                </div>
            )
        }];
    }
    else {
        return [];
    }
};

export const bookingPayment = (data?: any) => {

    const { t } = useTranslation();
    const { auth, userSlug } = usePage().props as any;

    const khaltiEnabled = getCompanySetting('khalti_enabled');
    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-booking-payment',
            dataUrl: route('khalti.booking.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg w-full">
                    <Label htmlFor="khalti-booking" className="cursor-pointer flex items-center space-x-2">
                        <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-10 w-10" />
                        <div>
                            <div className="font-medium text-gray-900 dark:text-white">{t('Khalti')}</div>
                        </div>
                    </Label>
                    <RadioGroupItem value="khalti" id="khalti-booking" />
                </div>
            )
        }];
    }
    else {
        return [];
    }
};

export const beautySpaPayment = (data?: any) => {

    const { t } = useTranslation();
    const { auth, userSlug } = usePage().props as any;

    const khaltiEnabled = getCompanySetting('khalti_enabled');
    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-beauty-spa-payment',
            dataUrl: route('khalti.beauty-spa.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <Label htmlFor="khalti-beauty-payment"
                    className="block border border-gray-200 rounded-lg p-4 hover:border-[#df9896] cursor-pointer transition-all duration-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 rounded-full overflow-hidden bg-white border">
                                <img src={getPackageFavicon('Khalti')} alt="Khalti Logo" className="object-contain w-full h-full" />
                            </div>
                            <div>
                                <h5 className="text-base font-medium text-gray-800">{t('Khalti')}</h5>
                            </div>
                        </div>
                        <RadioGroupItem value="khalti" id="khalti-beauty-payment" />

                    </div>
                </Label>
            )
        }];
    }
    else {
        return [];
    }
};

export const lmsPayment = (data?: any) => {
    const { t } = useTranslation();
    const { auth, userSlug } = usePage().props as any;

    const khaltiEnabled = getCompanySetting('khalti_enabled');
    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-lms-payment',
            dataUrl: route('khalti.lms.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg w-full hover:border-blue-300 transition-colors cursor-pointer">
                    <RadioGroupItem value="khalti" id="khalti-lms" />
                    <Label htmlFor="khalti-lms" className="cursor-pointer flex items-center space-x-3 flex-1">
                        <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-8 w-8" />
                        <div>
                            <div className="font-medium text-gray-900">{t('Khalti')}</div>
                            <div className="text-sm text-gray-500">{t('Pay securely with Khalti')}</div>
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
    const khaltiEnabled = getCompanySetting('khalti_enabled');

    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-laundry-payment',
            dataUrl: route('khalti.laundry.payment.pay', { userSlug: userSlug }),
            component: (
                <Label htmlFor="khalti-laundry-payment"
                    className="block border border-gray-200 rounded-lg p-4 hover:border-purple-500 cursor-pointer transition-all duration-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 rounded-full overflow-hidden bg-white border">
                                <img src={getPackageFavicon('Khalti')} alt="Khalti Logo" className="object-contain w-full h-full" />
                            </div>
                            <div>
                                <h5 className="text-base font-medium text-gray-800">{t('Khalti')}</h5>
                            </div>
                        </div>
                        <RadioGroupItem value="khalti" id="khalti-laundry-payment" />
                    </div>
                </Label>
            )
        }];
    }
    return [];
};

export const parkingPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const khaltiEnabled = getCompanySetting('khalti_enabled');

    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-parking-payment',
            dataUrl: route('khalti.parking.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <div className="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-600 transition-colors">
                    <RadioGroupItem value="khalti" id="khalti-parking" />
                    <Label htmlFor="khalti-parking" className="cursor-pointer flex items-center space-x-3 flex-1">
                        <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-8 w-8" />
                        <div>
                            <div className="font-medium text-gray-900">{t('Khalti')}</div>
                        </div>
                    </Label>
                </div>
            )
        }];
    }
    return [];
};

export const eventsPayment = (data?: any) => {
    const { t } = useTranslation();
    const { userSlug } = usePage().props as any;
    const khaltiEnabled = getCompanySetting('khalti_enabled');
    const isSelected = data?.selectedMethod === 'khalti';

    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti-events-payment',
            dataUrl: route('khalti.events-management.payment.pay', { userSlug: userSlug }),
            onFormSubmit: data?.onFormSubmit,
            component: (
                <label className="cursor-pointer">
                    <input
                        type="radio"
                        name="paymentMethod"
                        value="khalti"
                        className="hidden"
                        checked={isSelected}
                        onChange={() => data?.onMethodChange?.('khalti')}
                        required
                    />
                    <div className={`p-4 border-2 rounded-lg transition-all hover:border-purple-200 flex items-center ${
                        isSelected ? 'border-purple-500 bg-purple-50' : 'border-gray-200'
                    }`}>
                        <div className={`w-4 h-4 rounded-full border-2 mr-3 flex-shrink-0 ${
                            isSelected ? 'border-purple-500 bg-purple-500' : 'border-gray-300'
                        }`}>
                            {isSelected && <div className="w-2 h-2 bg-white rounded-full m-auto mt-0.5"></div>}
                        </div>
                        <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-8 w-8 mr-3" />
                        <span className="font-semibold">{t('Khalti')}</span>
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
    const khaltiEnabled = getCompanySetting('khalti_enabled');

    if (khaltiEnabled === 'on') {
        return [{
            id: 'khalti',
            dataUrl: route('khalti.holidayz.payment.pay', { userSlug: userSlug }),
            component: (
                <div className="flex items-center space-x-3">
                    <img src={getPackageFavicon('Khalti')} alt="Khalti" className="h-8 w-8" />
                    <div>
                        <div className="font-medium text-gray-900">{t('Khalti')}</div>
                        <div className="text-sm text-gray-500">{t('Pay securely with Khalti')}</div>
                    </div>
                </div>
            )
        }];
    }
    return [];
};
