import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Lock } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';

interface PasswordProps {
    userSlug: string;
    token: string;
    errors?: { password?: string };
}

export default function Password({ userSlug, token, errors }: PasswordProps) {
    const { t } = useTranslation();
    const { pageProps, companyAllSetting } = usePage().props as any;
    const { data, setData, post, processing } = useForm({
        password: ''
    });
    
    // Get company settings
    const companyName = companyAllSetting?.titleText || 'Company Name';
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

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('rotas.frontend.shared.authenticate', { token: token, userSlug: userSlug }), {
            onError: (errors) => console.log('Errors:', errors),
            onSuccess: () => console.log('Success')
        });
    };

    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center">
            <Head title={t('Password Required')} />
            
            <Card className="w-full max-w-md">
                <CardHeader className="text-center">
                    {logoUrl && (
                        <img
                            src={logoUrl}
                            alt="Company Logo"
                            className="h-16 w-auto object-contain mx-auto mb-4"
                        />
                    )}
                    <div className="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4" style={{ backgroundColor: `${primaryColor}20` }}>
                        <Lock className="h-6 w-6" style={{ color: primaryColor }} />
                    </div>
                    <CardTitle style={{ color: primaryColor }}>{t('Password Required')}</CardTitle>
                    <p className="text-muted-foreground">
                        {t('This shared schedule is password protected. Please enter the password to continue.')}
                    </p>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <Label htmlFor="password">{t('Password')}</Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder={t('Enter password')}
                                required
                            />
                            {errors?.password && (
                                <p className="text-sm text-red-600 mt-1">{errors.password}</p>
                            )}
                        </div>
                        <Button type="submit" className="w-full" disabled={processing} style={{ backgroundColor: primaryColor, borderColor: primaryColor }}>
                            {processing ? t('Verifying...') : t('Access Schedule')}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}