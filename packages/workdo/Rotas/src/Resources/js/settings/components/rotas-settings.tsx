import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { toast } from 'sonner';
import { Cog, Save } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';

interface RotasSettings {
  rotas_show_employee_price: boolean;
  rotas_show_employee_avatars: boolean;
  rotas_hide_employee_hours: boolean;
  rotas_include_unpublished_shifts: boolean;
  rotas_employees_see_only_themselves: boolean;
  rotas_week_starts: string;
  rotas_break_type: string;
  [key: string]: any;
}

interface RotasSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function RotasSettings({ userSettings, auth }: RotasSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('edit-rotas-settings');
  const [settings, setSettings] = useState<RotasSettings>({
    rotas_show_employee_price: userSettings?.rotas_show_employee_price === '1' || userSettings?.rotas_show_employee_price === 'true' || false,
    rotas_show_employee_avatars: userSettings?.rotas_show_employee_avatars === '1' || userSettings?.rotas_show_employee_avatars === 'true' || true,
    rotas_hide_employee_hours: userSettings?.rotas_hide_employee_hours === '1' || userSettings?.rotas_hide_employee_hours === 'true' || false,
    rotas_include_unpublished_shifts: userSettings?.rotas_include_unpublished_shifts === '1' || userSettings?.rotas_include_unpublished_shifts === 'true' || false,
    rotas_employees_see_only_themselves: userSettings?.rotas_employees_see_only_themselves === '1' || userSettings?.rotas_employees_see_only_themselves === 'true' || false,
    rotas_week_starts: userSettings?.rotas_week_starts || 'monday',
    rotas_break_type: userSettings?.rotas_break_type || 'paid',
  });

  useEffect(() => {
    if (userSettings) {
      setSettings({
        rotas_show_employee_price: userSettings?.rotas_show_employee_price === '1' || userSettings?.rotas_show_employee_price === 'true',
        rotas_show_employee_avatars: userSettings?.rotas_show_employee_avatars === '1' || userSettings?.rotas_show_employee_avatars === 'true',
        rotas_hide_employee_hours: userSettings?.rotas_hide_employee_hours === '1' || userSettings?.rotas_hide_employee_hours === 'true',
        rotas_include_unpublished_shifts: userSettings?.rotas_include_unpublished_shifts === '1' || userSettings?.rotas_include_unpublished_shifts === 'true',
        rotas_employees_see_only_themselves: userSettings?.rotas_employees_see_only_themselves === '1' || userSettings?.rotas_employees_see_only_themselves === 'true',
        rotas_week_starts: userSettings?.rotas_week_starts || 'monday',
        rotas_break_type: userSettings?.rotas_break_type || 'paid',
      });
    }
  }, [userSettings]);

  const handleSwitchChange = (key: string, checked: boolean) => {
    setSettings(prev => ({ ...prev, [key]: checked }));
  };

  const handleSelectChange = (key: string, value: string) => {
    setSettings(prev => ({ ...prev, [key]: value }));
  };

  const saveSettings = () => {
    setIsLoading(true);

    router.post(route('rotas.settings.update'), {
      settings: settings
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
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save rotas settings');
        toast.error(errorMessage);
      }
    });
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div>
          <CardTitle className="flex items-center gap-2 text-lg">
            <Cog className="h-5 w-5" />
            {t('Rotas Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure rotas module settings and preferences')}
          </p>
        </div>
        {canEdit && (
          <Button onClick={saveSettings} disabled={isLoading} size="sm">
            <Save className="h-4 w-4 mr-2" />
            {isLoading ? t('Saving...') : t('Save Changes')}
          </Button>
        )}
      </CardHeader>
      <CardContent>
        <div className="space-y-6">
          {/* Display Settings */}
          <div>
            <h4 className="font-medium mb-4">{t('Display Settings')}</h4>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="cursor-pointer" onClick={() => canEdit && handleSwitchChange('rotas_show_employee_price', !settings.rotas_show_employee_price)}>
                  <Label className="font-medium cursor-pointer">{t('Show Employee Rotas Price')}</Label>
                  <p className="text-sm text-muted-foreground">{t('Display pricing information on rotas')}</p>
                </div>
                <Switch
                  checked={settings.rotas_show_employee_price}
                  onCheckedChange={(checked) => handleSwitchChange('rotas_show_employee_price', checked)}
                  disabled={!canEdit}
                />
              </div>
              
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="cursor-pointer" onClick={() => canEdit && handleSwitchChange('rotas_show_employee_avatars', !settings.rotas_show_employee_avatars)}>
                  <Label className="font-medium cursor-pointer">{t('Show Employee Avatars')}</Label>
                  <p className="text-sm text-muted-foreground">{t('Display employee photos on rota')}</p>
                </div>
                <Switch
                  checked={settings.rotas_show_employee_avatars}
                  onCheckedChange={(checked) => handleSwitchChange('rotas_show_employee_avatars', checked)}
                  disabled={!canEdit}
                />
              </div>
              
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="cursor-pointer" onClick={() => canEdit && handleSwitchChange('rotas_hide_employee_hours', !settings.rotas_hide_employee_hours)}>
                  <Label className="font-medium cursor-pointer">{t('Hide Employee Hours')}</Label>
                  <p className="text-sm text-muted-foreground">{t('Hide working hours from employees')}</p>
                </div>
                <Switch
                  checked={settings.rotas_hide_employee_hours}
                  onCheckedChange={(checked) => handleSwitchChange('rotas_hide_employee_hours', checked)}
                  disabled={!canEdit}
                />
              </div>
              
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="cursor-pointer" onClick={() => canEdit && handleSwitchChange('rotas_include_unpublished_shifts', !settings.rotas_include_unpublished_shifts)}>
                  <Label className="font-medium cursor-pointer">{t('Include Unpublished Shifts')}</Label>
                  <p className="text-sm text-muted-foreground">{t('Show unpublished shifts in dashboard and reports')}</p>
                </div>
                <Switch
                  checked={settings.rotas_include_unpublished_shifts}
                  onCheckedChange={(checked) => handleSwitchChange('rotas_include_unpublished_shifts', checked)}
                  disabled={!canEdit}
                />
              </div>
              
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="cursor-pointer" onClick={() => canEdit && handleSwitchChange('rotas_employees_see_only_themselves', !settings.rotas_employees_see_only_themselves)}>
                  <Label className="font-medium cursor-pointer">{t('Employees See Only Themselves')}</Label>
                  <p className="text-sm text-muted-foreground">{t('Restrict employees to see only their own rota')}</p>
                </div>
                <Switch
                  checked={settings.rotas_employees_see_only_themselves}
                  onCheckedChange={(checked) => handleSwitchChange('rotas_employees_see_only_themselves', checked)}
                  disabled={!canEdit}
                />
              </div>
              

            </div>
          </div>

          {/* Calendar Settings */}
          <div>
            <h4 className="font-medium mb-4">{t('Calendar Settings')}</h4>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label>{t('Week Starts')}</Label>
                <Select value={settings.rotas_week_starts} onValueChange={(value) => handleSelectChange('rotas_week_starts', value)} disabled={!canEdit}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="monday">{t('Monday')}</SelectItem>
                    <SelectItem value="tuesday">{t('Tuesday')}</SelectItem>
                    <SelectItem value="wednesday">{t('Wednesday')}</SelectItem>
                    <SelectItem value="thursday">{t('Thursday')}</SelectItem>
                    <SelectItem value="friday">{t('Friday')}</SelectItem>
                    <SelectItem value="saturday">{t('Saturday')}</SelectItem>
                    <SelectItem value="sunday">{t('Sunday')}</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>

          {/* Break Settings */}
          <div>
            <h4 className="font-medium mb-4">{t('Break Settings')}</h4>
            <RadioGroup value={settings.rotas_break_type} onValueChange={(value) => handleSelectChange('rotas_break_type', value)} disabled={!canEdit}>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="paid" id="break-paid" />
                <Label htmlFor="break-paid">{t('Paid Break')}</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="unpaid" id="break-unpaid" />
                <Label htmlFor="break-unpaid">{t('Unpaid Break')}</Label>
              </div>
            </RadioGroup>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}