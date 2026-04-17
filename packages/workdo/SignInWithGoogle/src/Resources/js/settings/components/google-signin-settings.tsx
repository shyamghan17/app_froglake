import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { LogIn, Save, Copy, Eye, EyeOff } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router, usePage } from '@inertiajs/react';
import { Switch } from '@/components/ui/switch';
import MediaPicker from '@/components/MediaPicker';
import { getImagePath } from '@/utils/helpers';

interface GoogleSignInSettings {
  google_client_id: string;
  google_client_secret: string;
  google_signin_enabled: string;
  google_signin_logo: string;
  [key: string]: any;
}

interface GoogleSignInSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function GoogleSignInSettings({ userSettings, auth }: GoogleSignInSettingsProps) {
  const { t } = useTranslation();
  const { props } = usePage();
  const [isLoading, setIsLoading] = useState(false);
  const [showSecret, setShowSecret] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('edit-google-signin-settings');
  const redirectUrl = (props as any).googleConfig?.redirectUrl || route('google.callback');
  const [settings, setSettings] = useState<GoogleSignInSettings>({
    google_client_id: userSettings?.google_client_id || '',
    google_client_secret: userSettings?.google_client_secret || '',
    google_signin_enabled: userSettings?.google_signin_enabled || 'off',
    google_signin_logo: userSettings?.google_signin_logo || '',
  });

  useEffect(() => {
    if (userSettings) {
      setSettings({
        google_client_id: userSettings?.google_client_id || '',
        google_client_secret: userSettings?.google_client_secret || '',
        google_signin_enabled: userSettings?.google_signin_enabled || 'off',
        google_signin_logo: userSettings?.google_signin_logo || '',
      });
    }
  }, [userSettings]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setSettings(prev => ({ ...prev, [name]: value }));
  };

  const handleSwitchChange = (name: string, checked: boolean) => {
    setSettings(prev => ({ ...prev, [name]: checked }));
  };

  const copyRedirectUrl = () => {
    navigator.clipboard.writeText(redirectUrl).then(() => {
      toast.success(t('Redirect URL copied to clipboard'));
    }).catch(() => {
      toast.error(t('Failed to copy redirect URL'));
    });
  };

  const saveSettings = () => {
    setIsLoading(true);

    const payload = settings;

    router.post(route('google-signin.settings.update'), {
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
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save Sign-In With Google settings');
        toast.error(errorMessage);
      }
    });
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <LogIn className="h-5 w-5" />
            {t('Sign-In With Google Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure Google OAuth integration for user authentication')}
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
          {/* Enable/Disable Google Sign-In */}
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <Label htmlFor="google_signin_enabled" className="text-base font-medium">
                {t('Enable Sign-In With Google')}
              </Label>
              <p className="text-sm text-muted-foreground mt-1">
                {t('Allow users to sign in with their Google account')}
              </p>
            </div>
            <Switch
              id="google_signin_enabled"
              checked={settings.google_signin_enabled === 'on'}
              onCheckedChange={(checked) => handleSwitchChange('google_signin_enabled', checked ? 'on' : 'off')}
              disabled={!canEdit}
            />
          </div>

          {settings.google_signin_enabled === 'on' && (
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              {/* Left Side - Form Fields */}
              <div className="lg:col-span-2 space-y-6">
                {/* Client ID and Secret */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-3">
                    <Label htmlFor="google_client_id">{t('Google Client ID')}</Label>
                    <Input
                      id="google_client_id"
                      name="google_client_id"
                      value={settings.google_client_id}
                      onChange={handleInputChange}
                      placeholder={t('Enter Google Client ID')}
                      disabled={!canEdit}
                    />
                    <p className="text-xs text-muted-foreground">
                      {t('OAuth 2.0 Client ID from Google Console')}
                    </p>
                  </div>

                  <div className="space-y-3">
                    <Label htmlFor="google_client_secret">{t('Google Client Secret')}</Label>
                    <div className="relative">
                      <Input
                        id="google_client_secret"
                        name="google_client_secret"
                        type={showSecret ? 'text' : 'password'}
                        value={settings.google_client_secret}
                        onChange={handleInputChange}
                        placeholder={t('Enter Google Client Secret')}
                        disabled={!canEdit}
                        className="pr-10"
                      />
                      <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                        onClick={() => setShowSecret(!showSecret)}
                      >
                        {showSecret ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                      </Button>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      {t('OAuth 2.0 Client Secret from Google Console')}
                    </p>
                  </div>
                </div>

                {/* Logo and Redirect URL */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-3">
                    <Label>{t('Sign-In With Google Logo')}</Label>
                    <div className="flex gap-4 items-center">
                      <div className="w-16 h-16 rounded border-2 border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                        {settings.google_signin_logo ? (
                          <img
                            src={getImagePath(settings.google_signin_logo)}
                            alt="Google Logo Preview"
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="text-gray-400 text-center">
                            <LogIn className="w-6 h-6 mx-auto mb-1" />
                            <span className="text-xs">{t('No Logo')}</span>
                          </div>
                        )}
                      </div>
                      <div className="flex-1">
                        <MediaPicker
                          value={settings.google_signin_logo}
                          onChange={(value) => setSettings(prev => ({ ...prev, google_signin_logo: value }))}
                          accept="image/*"
                          disabled={!canEdit}
                          showPreview={false}
                        />
                      </div>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      {t('Upload a custom logo for the Sign-In With Google button')}
                    </p>
                  </div>

                  <div className="space-y-3">
                    <Label htmlFor="redirect_url">{t('Redirect URL')}</Label>
                    <div className="flex gap-2">
                      <Input
                        id="redirect_url"
                        type="text"
                        value={redirectUrl}
                        readOnly
                        className="bg-muted"
                      />
                      <Button type="button" variant="outline" size="sm" onClick={copyRedirectUrl}>
                        <Copy className="h-4 w-4" />
                      </Button>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      {t('Copy this URL and add it to your Google Console authorized redirect URIs')}
                    </p>
                  </div>
                </div>
              </div>

              {/* Right Side - Setup Guide */}
              <div className="lg:col-span-1">
                <div className="p-4 bg-muted/30 rounded-lg border">
                  <h4 className="font-medium mb-3">{t('How to get Google OAuth credentials')}</h4>
                  <div className="space-y-2 text-sm text-muted-foreground">
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">1.</span>
                      <span>{t('Go to')} <a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer" className="underline hover:no-underline text-primary">Google Cloud Console</a></span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">2.</span>
                      <span>{t('Create a new project or select an existing one')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">3.</span>
                      <span>{t('Enable the Google+ API in the API Library')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">4.</span>
                      <span>{t('Go to Credentials → Create Credentials → OAuth 2.0 Client IDs')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">5.</span>
                      <span>{t('Select "Web application" as application type')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">6.</span>
                      <span>{t('Add the redirect URL above to "Authorized redirect URIs"')}</span>
                    </div>
                    <div className="flex items-start gap-2">
                      <span className="font-medium min-w-[20px]">7.</span>
                      <span>{t('Copy the Client ID and Client Secret to the fields above')}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}