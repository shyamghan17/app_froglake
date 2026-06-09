import { SelectItem } from '@/components/ui/select';
import { useTranslation } from 'react-i18next';

export const paymentGateway = () => {
    const { t } = useTranslation();
    return [{
        id: 'esewa-gateway',
        order: 1940,
        component: (
            <SelectItem value="Esewa">{t('Esewa')}</SelectItem>
        )
    }];
};