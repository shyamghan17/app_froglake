import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { MessageSquareDot, Save, Eye, EyeOff } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { Switch } from '@/components/ui/switch';

interface BulkSMSSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function BulkSMSSettings({ userSettings = {}, auth }: BulkSMSSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('manage-bulk-sms');
  const [showPassword, setShowPassword] = useState(false);

  const [bulkSMSSettings, setBulkSMSSettings] = useState({
    bulksms_notification_is: userSettings?.bulksms_notification_is === 'on',
    bulksms_username: userSettings?.bulksms_username || '',
    bulksms_password: userSettings?.bulksms_password || '',
  });

  useEffect(() => {
    setBulkSMSSettings({
      bulksms_notification_is: userSettings?.bulksms_notification_is === 'on',
      bulksms_username: userSettings?.bulksms_username || '',
      bulksms_password: userSettings?.bulksms_password || '',
    });
  }, [userSettings]);

  const handleSettingsChange = (field: string, value: string | boolean) => {
    setBulkSMSSettings(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const saveBulkSMSSettings = () => {
    setIsLoading(true);

    router.post(route('bulksms.settings.store'), {
      settings: {
        bulksms_username: bulkSMSSettings.bulksms_username,
        bulksms_password: bulkSMSSettings.bulksms_password,
        bulksms_notification_is: bulkSMSSettings.bulksms_notification_is ? 'on' : 'off'
      }
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
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save BulkSMS settings');
        toast.error(errorMessage);
      }
    });
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <MessageSquareDot className="h-5 w-5" />
            {t('Bulk SMS Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure BulkSMS.com API credentials')}
          </p>
        </div>
        {canEdit && (
          <Button className="order-2 rtl:order-1" onClick={saveBulkSMSSettings} disabled={isLoading} size="sm">
            <Save className="h-4 w-4 mr-2" />
            {isLoading ? t('Saving...') : t('Save Changes')}
          </Button>
        )}
      </CardHeader>
      <CardContent>
        <div className="space-y-6">
          {/* Enable/Disable BulkSMS */}
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <Label htmlFor="bulksms_notification_is" className="text-base font-medium">
                {t('Enable Bulk SMS Integration')}
              </Label>
              <p className="text-sm text-muted-foreground mt-1">
                  {t('Allow bulk SMS to be sent via BulkSMS.com')}
              </p>
            </div>
            <div className="flex items-center">
              <Switch
                id="bulksms_notification_is"
                checked={bulkSMSSettings.bulksms_notification_is}
                onCheckedChange={(checked) => handleSettingsChange('bulksms_notification_is', checked)}
                disabled={!canEdit}
              />
            </div>
          </div>

          {bulkSMSSettings.bulksms_notification_is && (
            <div className="space-y-4">
              <div>
                <Label htmlFor="bulksms_username">{t('BulkSMS Username')}</Label>
                <Input
                  id="bulksms_username"
                  value={bulkSMSSettings.bulksms_username}
                  onChange={(e) => handleSettingsChange('bulksms_username', e.target.value)}
                  placeholder={t('Enter BulkSMS Username')}
                  disabled={!canEdit}
                />
              </div>
              <div>
                <Label htmlFor="bulksms_password">{t('BulkSMS Password')}</Label>
                <div className="relative">
                  <Input
                    id="bulksms_password"
                    type={showPassword ? 'text' : 'password'}
                    value={bulkSMSSettings.bulksms_password}
                    onChange={(e) => handleSettingsChange('bulksms_password', e.target.value)}
                    placeholder={t('Enter BulkSMS Password')}
                    disabled={!canEdit}
                    className="pr-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                    onClick={() => setShowPassword(!showPassword)}
                    disabled={!canEdit}
                  >
                    {showPassword ? (
                      <EyeOff className="h-4 w-4" />
                    ) : (
                      <Eye className="h-4 w-4" />
                    )}
                  </Button>
                </div>
              </div>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}