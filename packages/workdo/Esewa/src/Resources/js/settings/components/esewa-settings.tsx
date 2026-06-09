import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { CreditCard, Save, Eye, EyeOff, AlertTriangle } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { Switch } from '@/components/ui/switch';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

interface EsewaSettings {
  esewa_merchant_id: string;
  esewa_secret_key: string;
  esewa_mode: string;
  esewa_enabled: string;
  [key: string]: any;
}

interface EsewaSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function EsewaSettings({ userSettings, auth }: EsewaSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const [showSecretKey, setShowSecretKey] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('edit-esewa-settings');
  const defaultCurrency = userSettings?.defaultCurrency || '';
  const isNPR = defaultCurrency === 'NPR';
  const [settings, setSettings] = useState<EsewaSettings>({
    esewa_merchant_id: userSettings?.esewa_merchant_id || '',
    esewa_secret_key: userSettings?.esewa_secret_key || '',
    esewa_mode: userSettings?.esewa_mode || 'sandbox',
    esewa_enabled: userSettings?.esewa_enabled || 'off',
  });

  useEffect(() => {
    if (userSettings) {
      setSettings({
        esewa_merchant_id: userSettings?.esewa_merchant_id || '',
        esewa_secret_key: userSettings?.esewa_secret_key || '',
        esewa_mode: userSettings?.esewa_mode || 'sandbox',
        esewa_enabled: userSettings?.esewa_enabled || 'off',
      });
    }
  }, [userSettings]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setSettings(prev => ({ ...prev, [name]: value }));
  };

  const handleSelectChange = (name: string, value: string) => {
    setSettings(prev => ({ ...prev, [name]: value }));
  };

  const handleSwitchChange = (name: string, checked: boolean) => {
    setSettings(prev => ({ ...prev, [name]: checked ? 'on' : 'off' }));
  };

  const saveSettings = () => {
    setIsLoading(true);

    const payload = {
      ...settings,
      esewa_enabled: settings.esewa_enabled === 'on' ? 'on' : 'off'
    };

    router.post(route('esewa.settings.update'), {
      settings: payload
    }, {
      preserveScroll: true,
      onSuccess: (page) => {
        setIsLoading(false);
        const successMessage = (page.props.flash as any)?.success;
        const errorMessage = (page.props.flash as any)?.error;

        if (successMessage) {
          toast.success(successMessage);
          router.reload({ only: ['globalSettings'] });
        } else if (errorMessage) {
          toast.error(errorMessage);
        }
      },
      onError: (errors) => {
        setIsLoading(false);
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save esewa settings');
        toast.error(errorMessage);
      }
    });
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <CreditCard className="h-5 w-5" />
            {t('Esewa Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure Esewa payment gateway settings')}
          </p>
        </div>
        {canEdit && (
          <Button className="order-2 rtl:order-1" onClick={saveSettings} disabled={isLoading} size="sm">
            <Save className="h-4 w-4 mr-2" />
            {isLoading ? t('Saving...') : t('Save Changes')}
          </Button>
        )}
      </CardHeader>
      <CardContent>
        <div className="space-y-6">

          {/* Enable/Disable Esewa */}
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <Label htmlFor="esewa_enabled" className="text-base font-medium">
                {t('Enable Esewa')}
              </Label>
              <p className="text-sm text-muted-foreground mt-1">
                {t('Enable or disable Esewa payment gateway')}
              </p>
            </div>
            <Switch
              id="esewa_enabled"
              checked={settings.esewa_enabled === 'on'}
              onCheckedChange={(checked) => handleSwitchChange('esewa_enabled', checked)}
              disabled={!canEdit}
            />
          </div>

          {settings.esewa_enabled === 'on' && (
            <>
              {/* Currency Alert */}
              {!isNPR && (
                <div className="flex items-start gap-3 p-4 border border-yellow-200 bg-yellow-50 dark:bg-yellow-950/20 rounded-lg">
                  <AlertTriangle className="h-4 w-4 text-yellow-600 mt-0.5 flex-shrink-0" />
                  <div className="text-sm text-yellow-800 dark:text-yellow-200">
                    {t('eSewa primarily supports NPR currency. Your current default currency is')} <strong>{defaultCurrency}</strong>. {t('Please set your default currency to NPR for optimal eSewa integration.')}
                  </div>
                </div>
              )}
              
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Side - Form Fields */}
                <div className="lg:col-span-2 space-y-6">
                  {/* Esewa Mode */}
                  <div className="space-y-3">
                    <Label>{t('Esewa Mode')}</Label>
                    <RadioGroup
                      value={settings.esewa_mode}
                      onValueChange={(value) => handleSelectChange('esewa_mode', value)}
                      disabled={!canEdit}
                      className="flex gap-6"
                    >
                      <div className="flex items-center space-x-2">
                        <RadioGroupItem value="sandbox" id="esewa-sandbox" />
                        <Label htmlFor="esewa-sandbox">{t('Sandbox')}</Label>
                      </div>
                      <div className="flex items-center space-x-2">
                        <RadioGroupItem value="live" id="esewa-live" />
                        <Label htmlFor="esewa-live">{t('Live')}</Label>
                      </div>
                    </RadioGroup>
                    <p className="text-xs text-muted-foreground">
                      {settings.esewa_mode === 'sandbox'
                        ? t('Use sandbox credentials for development and testing')
                        : t('Use live credentials for production transactions')
                      }
                    </p>
                  </div>

                  {/* Esewa Merchant ID */}
                  <div className="space-y-3">
                    <Label htmlFor="esewa_merchant_id">{t('Esewa Merchant ID')}</Label>
                    <Input
                      id="esewa_merchant_id"
                      name="esewa_merchant_id"
                      type="text"
                      value={settings.esewa_merchant_id}
                      onChange={handleInputChange}
                      placeholder={t('Enter Esewa merchant ID')}
                      disabled={!canEdit}
                    />
                    <p className="text-xs text-muted-foreground">
                      {t('Esewa merchant ID for payment integration')}
                    </p>
                  </div>

                  {/* Esewa Secret Key */}
                  <div className="space-y-3">
                    <Label htmlFor="esewa_secret_key">{t('Esewa Secret Key')}</Label>
                    <div className="relative">
                      <Input
                        id="esewa_secret_key"
                        name="esewa_secret_key"
                        type={showSecretKey ? 'text' : 'password'}
                        value={settings.esewa_secret_key}
                        onChange={handleInputChange}
                        placeholder={t('Enter Esewa secret key')}
                        disabled={!canEdit}
                        className="pr-10"
                      />
                      <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                        onClick={() => setShowSecretKey(!showSecretKey)}
                      >
                        {showSecretKey ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                      </Button>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      {t('Esewa secret key for payment processing')}
                    </p>
                  </div>
                </div>

                {/* Right Side - Guide */}
                <div className="lg:col-span-1 border rounded-lg p-4 bg-blue-50 dark:bg-blue-950/20">
                  <h4 className="font-medium mb-3 text-blue-900 dark:text-blue-100">
                    {t('How to get Esewa credentials')}
                  </h4>
                  <div className="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">{t('1.')} </span>
                      <span>{t('Go to')} <a href="https://esewa.com.np/" target="_blank" rel="noopener noreferrer" className="underline hover:no-underline">{t('Esewa Portal')}</a></span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">{t('2.')} </span>
                      <span>{t('Sign in to your Esewa merchant account')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">{t('3.')} </span>
                      <span>{t('Navigate to Developer Settings')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">{t('4.')} </span>
                      <span>{t('Copy merchant ID and secret key to the fields above')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">{t('5.')} </span>
                      <span>{t('Select \"Sandbox\" mode for testing or \"Live\" mode for production')}</span>
                    </div>
                  </div>
                </div>
              </div>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  );
}