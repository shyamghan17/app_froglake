import React, { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

interface CheckoutData {
    action_url: string;
    method: string;
    form_data: Record<string, any>;
}

export default function EsewaCheckoutForm() {
    const { t } = useTranslation();
    const { props } = usePage();
    const checkoutData = (props.checkoutData || null) as CheckoutData | null;

    useEffect(() => {
        if (checkoutData) {
            const form = document.createElement('form');
            form.method = checkoutData.method.toUpperCase();
            form.action = checkoutData.action_url;
            
            Object.entries(checkoutData.form_data).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = String(value);
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }, [checkoutData]);

    return (
        <div className="flex items-center justify-center min-h-screen">
            <div className="text-center">
                <h2 className="text-xl mb-2">
                    {t('Redirecting to eSewa...')}
                </h2>
            </div>
        </div>
    );
}