import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { toast } from 'sonner';
import { Receipt, Save } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

type EBillingSettings = {
  ebilling_enabled: string;
  ebilling_invoice_prefix: string;
};

type EBillingSettingsProps = {
  userSettings?: Record<string, string>;
  auth?: any;
};

export default function EBillingSettings({ userSettings, auth }: EBillingSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('manage-ebilling');

  const [settings, setSettings] = useState<EBillingSettings>({
    ebilling_enabled: userSettings?.ebilling_enabled || 'off',
    ebilling_invoice_prefix: userSettings?.ebilling_invoice_prefix || 'EB-',
  });

  useEffect(() => {
    if (!userSettings) return;
    setSettings({
      ebilling_enabled: userSettings?.ebilling_enabled || 'off',
      ebilling_invoice_prefix: userSettings?.ebilling_invoice_prefix || 'EB-',
    });
  }, [userSettings]);

  const saveSettings = () => {
    setIsLoading(true);

    router.post(
      route('ebilling.settings.update'),
      { settings },
      {
        preserveScroll: true,
        onSuccess: (page) => {
          setIsLoading(false);
          const successMessage = (page.props.flash as any)?.success;
          const errorMessage = (page.props.flash as any)?.error;

          if (successMessage) {
            toast.success(successMessage);
            router.reload({ only: ['globalSettings'] });
            return;
          }

          if (errorMessage) {
            toast.error(errorMessage);
            return;
          }

          toast.success(t('Settings saved'));
        },
        onError: (errors) => {
          setIsLoading(false);
          const errorMessage = (errors.error as string) || Object.values(errors).join(', ') || t('Failed to save settings');
          toast.error(errorMessage);
        },
      },
    );
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <Receipt className="h-5 w-5" />
            {t('eBilling Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">{t('Configure basic eBilling module settings')}</p>
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
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <Label htmlFor="ebilling_enabled" className="text-base font-medium">
                {t('Enable eBilling')}
              </Label>
              <p className="text-sm text-muted-foreground">{t('Turn the module on or off for this tenant')}</p>
            </div>
            <Switch
              id="ebilling_enabled"
              checked={settings.ebilling_enabled === 'on'}
              onCheckedChange={(checked) => setSettings((p) => ({ ...p, ebilling_enabled: checked ? 'on' : 'off' }))}
              disabled={!canEdit}
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="ebilling_invoice_prefix">{t('Invoice Prefix')}</Label>
            <Input
              id="ebilling_invoice_prefix"
              name="ebilling_invoice_prefix"
              value={settings.ebilling_invoice_prefix}
              onChange={(e) => setSettings((p) => ({ ...p, ebilling_invoice_prefix: e.target.value }))}
              placeholder="EB-"
              disabled={!canEdit}
            />
          </div>
        </div>
      </CardContent>
    </Card>
  );
}

