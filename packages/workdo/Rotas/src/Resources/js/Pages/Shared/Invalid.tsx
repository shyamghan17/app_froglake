import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { AlertTriangle } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';

export default function Invalid() {
    const { t } = useTranslation();
    const { pageProps, companyAllSetting } = usePage().props as any;
    
    // Get company settings
    const logoDark = companyAllSetting?.logo_dark;
    const logoLight = companyAllSetting?.logo_light;
    const logoUrl = logoDark ? getImagePath(logoDark, pageProps) : (logoLight ? getImagePath(logoLight, pageProps) : '');
    const themeColor = companyAllSetting?.themeColor || 'blue';
    const customColor = companyAllSetting?.customColor || '#3b82f6';
    
    const colorMap: Record<string, string> = {
        blue: '#3b82f6',
        green: '#10b981', 
        purple: '#8b5cf6',
        orange: '#f97316',
        red: '#ef4444'
    };
    const primaryColor = themeColor === 'custom' ? customColor : (colorMap[themeColor] || '#3b82f6');

    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center">
            <Head title={t('Invalid Link')} />
            
            <Card className="w-full max-w-md">
                <CardHeader className="text-center">
                    {logoUrl && (
                        <img
                            src={logoUrl}
                            alt="Company Logo"
                            className="h-16 w-auto object-contain mx-auto mb-4"
                        />
                    )}
                    <div className="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <AlertTriangle className="h-6 w-6 text-red-600" />
                    </div>
                    <CardTitle style={{ color: primaryColor }}>{t('Invalid Link')}</CardTitle>
                    <p className="text-muted-foreground">
                        {t('This shared schedule link is invalid or has been corrupted.')}
                    </p>
                </CardHeader>
                <CardContent className="text-center">
                    <p className="text-sm text-muted-foreground mb-4">
                        {t('Please check the link or contact the person who shared this schedule.')}
                    </p>
                    <Button onClick={() => window.close()} variant="outline" style={{ borderColor: primaryColor, color: primaryColor }}>
                        {t('Close')}
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
}