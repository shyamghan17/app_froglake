import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';
import { MessageSquare, Save, Eye, EyeOff } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { getPackageAlias } from '@/utils/helpers';

interface Notification {
  id: number;
  module: string;
  type: string;
  action: string;
  status: string;
  permissions: string;
}

interface SMSSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function SMSSettings({ userSettings = {}, auth }: SMSSettingsProps) {
  const { t } = useTranslation();
  const activatedPackages = auth?.user?.activatedPackages || [];
  const [smsNotifications, setSmsNotifications] = useState<Record<string, any>>({});
  const [isLoading, setIsLoading] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('edit-sms-settings');
  const [showPasswords, setShowPasswords] = useState<Record<string, boolean>>({});

  const [smsSettings, setSmsSettings] = useState({
    sms_notification_is: userSettings?.sms_notification_is === 'on',
    sms_provider: userSettings?.sms_provider || '',
    // AWS
    aws_access_key_id: userSettings?.aws_access_key_id || '',
    aws_secret_access_key: userSettings?.aws_secret_access_key || '',
    aws_default_region: userSettings?.aws_default_region || '',
    aws_sender_id: userSettings?.aws_sender_id || '',
    aws_message_type: userSettings?.aws_message_type || '',
    // Twilio
    twilio_account_sid: userSettings?.twilio_account_sid || '',
    twilio_auth_token: userSettings?.twilio_auth_token || '',
    twilio_from_number: userSettings?.twilio_from_number || '',
    // Clockwork
    clockwork_api_key: userSettings?.clockwork_api_key || '',
    clockwork_from_name: userSettings?.clockwork_from_name || '',
    // Melipayamak
    melipayamak_username: userSettings?.melipayamak_username || '',
    melipayamak_password: userSettings?.melipayamak_password || '',
    melipayamak_from_number: userSettings?.melipayamak_from_number || '',
    // Kavenegar
    kavenegar_api_key: userSettings?.kavenegar_api_key || '',
    kavenegar_sender: userSettings?.kavenegar_sender || '',
    // SMS Gateway Me
    sms_gateway_me_device_id: userSettings?.sms_gateway_me_device_id || '',
    sms_gateway_me_token: userSettings?.sms_gateway_me_token || '',
  });

  const [notificationSettings, setNotificationSettings] = useState<Record<string, string>>({});

  const providers = [
    { value: 'aws', label: 'AWS SNS' },
    { value: 'twilio', label: 'Twilio' },
    { value: 'clockwork', label: 'Clockwork' },
    { value: 'melipayamak', label: 'Melipayamak' },
    { value: 'kavenegar', label: 'Kavenegar' },
    { value: 'sms_gateway_me', label: 'SMS Gateway Me' },
  ];

  useEffect(() => {
    setSmsSettings({
      sms_notification_is: userSettings?.sms_notification_is === 'on',
      sms_provider: userSettings?.sms_provider || '',
      aws_access_key_id: userSettings?.aws_access_key_id || '',
      aws_secret_access_key: userSettings?.aws_secret_access_key || '',
      aws_default_region: userSettings?.aws_default_region || '',
      aws_sender_id: userSettings?.aws_sender_id || '',
      aws_message_type: userSettings?.aws_message_type || '',
      twilio_account_sid: userSettings?.twilio_account_sid || '',
      twilio_auth_token: userSettings?.twilio_auth_token || '',
      twilio_from_number: userSettings?.twilio_from_number || '',
      clockwork_api_key: userSettings?.clockwork_api_key || '',
      clockwork_from_name: userSettings?.clockwork_from_name || '',
      melipayamak_username: userSettings?.melipayamak_username || '',
      melipayamak_password: userSettings?.melipayamak_password || '',
      melipayamak_from_number: userSettings?.melipayamak_from_number || '',
      kavenegar_api_key: userSettings?.kavenegar_api_key || '',
      kavenegar_sender: userSettings?.kavenegar_sender || '',
      sms_gateway_me_device_id: userSettings?.sms_gateway_me_device_id || '',
      sms_gateway_me_token: userSettings?.sms_gateway_me_token || '',
    });

    fetch(route('sms.settings.index'))
      .then(response => response.json())
      .then(data => {
        setSmsNotifications(data.smsNotifications || {});

        const initial: Record<string, string> = {};
        Object.values(data.smsNotifications || {}).forEach((moduleNotifications: any) => {
          moduleNotifications.forEach((notification: Notification) => {
            const key = `SMS ${notification.action}`;
            initial[key] = userSettings?.[key] || 'off';
          });
        });
        setNotificationSettings(initial);
      })
      .catch(error => console.error('Error fetching SMS notifications:', error));
  }, [userSettings]);

  const handleSettingsChange = (field: string, value: string | boolean) => {
    setSmsSettings(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const togglePasswordVisibility = (fieldName: string) => {
    setShowPasswords(prev => ({
      ...prev,
      [fieldName]: !prev[fieldName]
    }));
  };

  const handleNotificationToggle = (key: string, checked: boolean) => {
    setNotificationSettings(prev => ({
      ...prev,
      [key]: checked ? 'on' : 'off'
    }));
  };


  const saveSmsSettings = () => {
    setIsLoading(true);

    router.post(route('sms.settings.store'), {
      settings: {
        ...smsSettings,
        ...notificationSettings,
        sms_notification_is: smsSettings.sms_notification_is ? 'on' : 'off'
      }
    }, {
      preserveScroll: true,
      onSuccess: (page) => {
        setIsLoading(false);
        const successMessage = (page.props.flash as any)?.success;
        const errorMessage = (page.props.flash as any)?.error;

        if (successMessage) {
          toast.success(successMessage);
        } else if (errorMessage) {
          toast.error(errorMessage);
        }
      },
      onError: (errors) => {
        setIsLoading(false);
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save SMS settings');
        toast.error(errorMessage);
      }
    });
  };

  const renderProviderFields = () => {
    switch (smsSettings.sms_provider) {
      case 'aws':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="aws_access_key_id">{t('AWS Access Key ID')}</Label>
              <Input
                id="aws_access_key_id"
                value={smsSettings.aws_access_key_id}
                onChange={(e) => handleSettingsChange('aws_access_key_id', e.target.value)}
                placeholder={t('Enter AWS Access Key ID')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="aws_secret_access_key">{t('AWS Secret Access Key')}</Label>
              <div className="relative">
                <Input
                  id="aws_secret_access_key"
                  type={showPasswords.aws_secret_access_key ? 'text' : 'password'}
                  value={smsSettings.aws_secret_access_key}
                  onChange={(e) => handleSettingsChange('aws_secret_access_key', e.target.value)}
                  placeholder={t('Enter AWS Secret Access Key')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('aws_secret_access_key')}
                  disabled={!canEdit}
                >
                  {showPasswords.aws_secret_access_key ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
            <div>
              <Label htmlFor="aws_default_region">{t('AWS Default Region')}</Label>
              <Input
                id="aws_default_region"
                value={smsSettings.aws_default_region}
                onChange={(e) => handleSettingsChange('aws_default_region', e.target.value)}
                placeholder={t('Enter AWS Default Region')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="aws_sender_id">{t('AWS Sender ID')}</Label>
              <Input
                id="aws_sender_id"
                value={smsSettings.aws_sender_id}
                onChange={(e) => handleSettingsChange('aws_sender_id', e.target.value)}
                placeholder={t('Enter AWS Sender ID')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="aws_message_type">{t('AWS Message Type')}</Label>
              <Input
                id="aws_message_type"
                value={smsSettings.aws_message_type}
                onChange={(e) => handleSettingsChange('aws_message_type', e.target.value)}
                placeholder={t('Enter AWS Message Type')}
                disabled={!canEdit}
              />
            </div>
          </div>
        );
      case 'twilio':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="twilio_account_sid">{t('Twilio Account SID')}</Label>
              <Input
                id="twilio_account_sid"
                value={smsSettings.twilio_account_sid}
                onChange={(e) => handleSettingsChange('twilio_account_sid', e.target.value)}
                placeholder={t('Enter Twilio Account SID')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="twilio_auth_token">{t('Twilio Auth Token')}</Label>
              <div className="relative">
                <Input
                  id="twilio_auth_token"
                  type={showPasswords.twilio_auth_token ? 'text' : 'password'}
                  value={smsSettings.twilio_auth_token}
                  onChange={(e) => handleSettingsChange('twilio_auth_token', e.target.value)}
                  placeholder={t('Enter Twilio Auth Token')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('twilio_auth_token')}
                  disabled={!canEdit}
                >
                  {showPasswords.twilio_auth_token ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
            <div>
              <Label htmlFor="twilio_from_number">{t('Twilio From Number')}</Label>
              <Input
                id="twilio_from_number"
                value={smsSettings.twilio_from_number}
                onChange={(e) => handleSettingsChange('twilio_from_number', e.target.value)}
                placeholder={t('Enter Twilio From Number')}
                disabled={!canEdit}
              />
            </div>
          </div>
        );
      case 'clockwork':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="clockwork_api_key">{t('Clockwork API Key')}</Label>
              <div className="relative">
                <Input
                  id="clockwork_api_key"
                  type={showPasswords.clockwork_api_key ? 'text' : 'password'}
                  value={smsSettings.clockwork_api_key}
                  onChange={(e) => handleSettingsChange('clockwork_api_key', e.target.value)}
                  placeholder={t('Enter Clockwork API Key')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('clockwork_api_key')}
                  disabled={!canEdit}
                >
                  {showPasswords.clockwork_api_key ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
            <div>
              <Label htmlFor="clockwork_from_name">{t('Clockwork From Name')}</Label>
              <Input
                id="clockwork_from_name"
                value={smsSettings.clockwork_from_name}
                onChange={(e) => handleSettingsChange('clockwork_from_name', e.target.value)}
                placeholder={t('Enter Clockwork From Name')}
                disabled={!canEdit}
              />
            </div>
          </div>
        );
      case 'melipayamak':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="melipayamak_username">{t('Melipayamak Username')}</Label>
              <Input
                id="melipayamak_username"
                value={smsSettings.melipayamak_username}
                onChange={(e) => handleSettingsChange('melipayamak_username', e.target.value)}
                placeholder={t('Enter Melipayamak Username')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="melipayamak_password">{t('Melipayamak Password')}</Label>
              <div className="relative">
                <Input
                  id="melipayamak_password"
                  type={showPasswords.melipayamak_password ? 'text' : 'password'}
                  value={smsSettings.melipayamak_password}
                  onChange={(e) => handleSettingsChange('melipayamak_password', e.target.value)}
                  placeholder={t('Enter Melipayamak Password')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('melipayamak_password')}
                  disabled={!canEdit}
                >
                  {showPasswords.melipayamak_password ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
            <div>
              <Label htmlFor="melipayamak_from_number">{t('Melipayamak From Number')}</Label>
              <Input
                id="melipayamak_from_number"
                value={smsSettings.melipayamak_from_number}
                onChange={(e) => handleSettingsChange('melipayamak_from_number', e.target.value)}
                placeholder={t('Enter Melipayamak From Number')}
                disabled={!canEdit}
              />
            </div>
          </div>
        );
      case 'kavenegar':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="kavenegar_api_key">{t('Kavenegar API Key')}</Label>
              <div className="relative">
                <Input
                  id="kavenegar_api_key"
                  type={showPasswords.kavenegar_api_key ? 'text' : 'password'}
                  value={smsSettings.kavenegar_api_key}
                  onChange={(e) => handleSettingsChange('kavenegar_api_key', e.target.value)}
                  placeholder={t('Enter Kavenegar API Key')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('kavenegar_api_key')}
                  disabled={!canEdit}
                >
                  {showPasswords.kavenegar_api_key ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
            <div>
              <Label htmlFor="kavenegar_sender">{t('Kavenegar Sender')}</Label>
              <Input
                id="kavenegar_sender"
                value={smsSettings.kavenegar_sender}
                onChange={(e) => handleSettingsChange('kavenegar_sender', e.target.value)}
                placeholder={t('Enter Kavenegar Sender')}
                disabled={!canEdit}
              />
            </div>
          </div>
        );
      case 'sms_gateway_me':
        return (
          <div className="space-y-3">
            <div>
              <Label htmlFor="sms_gateway_me_device_id">{t('SMS Gateway Me Device ID')}</Label>
              <Input
                id="sms_gateway_me_device_id"
                value={smsSettings.sms_gateway_me_device_id}
                onChange={(e) => handleSettingsChange('sms_gateway_me_device_id', e.target.value)}
                placeholder={t('Enter SMS Gateway Me Device ID')}
                disabled={!canEdit}
              />
            </div>
            <div>
              <Label htmlFor="sms_gateway_me_token">{t('SMS Gateway Me Token')}</Label>
              <div className="relative">
                <Input
                  id="sms_gateway_me_token"
                  type={showPasswords.sms_gateway_me_token ? 'text' : 'password'}
                  value={smsSettings.sms_gateway_me_token}
                  onChange={(e) => handleSettingsChange('sms_gateway_me_token', e.target.value)}
                  placeholder={t('Enter SMS Gateway Me Token')}
                  disabled={!canEdit}
                  className="pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => togglePasswordVisibility('sms_gateway_me_token')}
                  disabled={!canEdit}
                >
                  {showPasswords.sms_gateway_me_token ? (
                    <EyeOff className="h-4 w-4" />
                  ) : (
                    <Eye className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <MessageSquare className="h-5 w-5" />
            {t('SMS Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure SMS provider and notification settings')}
          </p>
        </div>
        {canEdit && (
          <Button className="order-2 rtl:order-1" onClick={saveSmsSettings} disabled={isLoading} size="sm">
            <Save className="h-4 w-4 mr-2" />
            {isLoading ? t('Saving...') : t('Save Changes')}
          </Button>
        )}
      </CardHeader>
      <CardContent>
        <div className="space-y-6">
          {/* Enable/Disable SMS */}
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <Label htmlFor="sms_notification_is" className="text-base font-medium">
                {t('Enable SMS Integration')}
              </Label>
              <p className="text-sm text-muted-foreground mt-1">
                {t('Allow notifications to be sent via SMS')}
              </p>
            </div>
            <Switch
              id="sms_notification_is"
              checked={smsSettings.sms_notification_is}
              onCheckedChange={(checked) => handleSettingsChange('sms_notification_is', checked)}
              disabled={!canEdit}
            />
          </div>

          {smsSettings.sms_notification_is && (
            <>
              <div className="space-y-3">
                <Label htmlFor="sms_provider">{t('SMS Provider')}</Label>
                <Select
                  value={smsSettings.sms_provider}
                  onValueChange={(value) => handleSettingsChange('sms_provider', value)}
                  disabled={!canEdit}
                >
                  <SelectTrigger>
                    <SelectValue placeholder={t('Select SMS Provider')} />
                  </SelectTrigger>
                  <SelectContent>
                    {providers.map((provider) => (
                      <SelectItem key={provider.value} value={provider.value}>
                        {provider.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              {smsSettings.sms_provider && renderProviderFields()}

              {(() => {
                const filteredModules = Object.keys(smsNotifications || {}).filter(module =>
                  module.toLowerCase() === 'general' || activatedPackages.includes(module)
                );
                return filteredModules.length > 0 && (
                  <div className="space-y-3">
                    <div>
                      <Label>{t('Notification Settings')}</Label>
                    </div>
                    <Tabs defaultValue={filteredModules[0]}>
                      <TabsList className="flex-wrap h-auto">
                        {filteredModules.map((module) => (
                          <TabsTrigger key={module} value={module} className="capitalize">
                            {getPackageAlias(module)}
                          </TabsTrigger>
                        ))}
                      </TabsList>
                      {filteredModules.map((module) => (
                        <TabsContent key={module} value={module}>
                          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {(smsNotifications[module] || []).map((notification: Notification) => (
                              <div key={notification.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span className="text-sm font-medium">
                                  {notification.action}
                                </span>
                                <Switch
                                  checked={notificationSettings[`SMS ${notification.action}`] === 'on'}
                                  onCheckedChange={(checked) => handleNotificationToggle(`SMS ${notification.action}`, checked)}
                                  disabled={!canEdit}
                                />
                              </div>
                            ))}
                          </div>
                        </TabsContent>
                      ))}
                    </Tabs>
                  </div>
                );
              })()}
            </>
          )}
        </div>
      </CardContent>
    </Card>
  );
}