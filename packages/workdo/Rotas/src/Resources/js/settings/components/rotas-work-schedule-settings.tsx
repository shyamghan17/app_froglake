import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { toast } from 'sonner';
import { Calendar, Save } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';

interface WorkScheduleSettings {
  rotas_work_schedule_monday: boolean;
  rotas_work_schedule_tuesday: boolean;
  rotas_work_schedule_wednesday: boolean;
  rotas_work_schedule_thursday: boolean;
  rotas_work_schedule_friday: boolean;
  rotas_work_schedule_saturday: boolean;
  rotas_work_schedule_sunday: boolean;
  [key: string]: any;
}

interface WorkScheduleSettingsProps {
  userSettings?: Record<string, string>;
  auth?: any;
}

export default function WorkScheduleSettings({ userSettings, auth }: WorkScheduleSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const canEdit = auth?.user?.permissions?.includes('edit-work-schedule-settings');
  
  const [settings, setSettings] = useState<WorkScheduleSettings>({
    rotas_work_schedule_monday: userSettings?.rotas_work_schedule_monday === '1' || userSettings?.rotas_work_schedule_monday === 'true' || true,
    rotas_work_schedule_tuesday: userSettings?.rotas_work_schedule_tuesday === '1' || userSettings?.rotas_work_schedule_tuesday === 'true' || true,
    rotas_work_schedule_wednesday: userSettings?.rotas_work_schedule_wednesday === '1' || userSettings?.rotas_work_schedule_wednesday === 'true' || true,
    rotas_work_schedule_thursday: userSettings?.rotas_work_schedule_thursday === '1' || userSettings?.rotas_work_schedule_thursday === 'true' || true,
    rotas_work_schedule_friday: userSettings?.rotas_work_schedule_friday === '1' || userSettings?.rotas_work_schedule_friday === 'true' || true,
    rotas_work_schedule_saturday: userSettings?.rotas_work_schedule_saturday === '1' || userSettings?.rotas_work_schedule_saturday === 'true' || false,
    rotas_work_schedule_sunday: userSettings?.rotas_work_schedule_sunday === '1' || userSettings?.rotas_work_schedule_sunday === 'true' || false,
  });

  useEffect(() => {
    if (userSettings) {
      setSettings({
        rotas_work_schedule_monday: userSettings?.rotas_work_schedule_monday === '1' || userSettings?.rotas_work_schedule_monday === 'true',
        rotas_work_schedule_tuesday: userSettings?.rotas_work_schedule_tuesday === '1' || userSettings?.rotas_work_schedule_tuesday === 'true',
        rotas_work_schedule_wednesday: userSettings?.rotas_work_schedule_wednesday === '1' || userSettings?.rotas_work_schedule_wednesday === 'true',
        rotas_work_schedule_thursday: userSettings?.rotas_work_schedule_thursday === '1' || userSettings?.rotas_work_schedule_thursday === 'true',
        rotas_work_schedule_friday: userSettings?.rotas_work_schedule_friday === '1' || userSettings?.rotas_work_schedule_friday === 'true',
        rotas_work_schedule_saturday: userSettings?.rotas_work_schedule_saturday === '1' || userSettings?.rotas_work_schedule_saturday === 'true',
        rotas_work_schedule_sunday: userSettings?.rotas_work_schedule_sunday === '1' || userSettings?.rotas_work_schedule_sunday === 'true',
      });
    }
  }, [userSettings]);

  const handleSwitchChange = (day: string, checked: boolean) => {
    setSettings(prev => ({ ...prev, [`rotas_work_schedule_${day}`]: checked }));
  };

  const saveSettings = () => {
    setIsLoading(true);

    router.post(route('rotas.settings.update.work-schedule'), {
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
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save work schedule settings');
        toast.error(errorMessage);
      }
    });
  };

  const weekDays = [
    { key: 'monday', label: t('Monday') },
    { key: 'tuesday', label: t('Tuesday') },
    { key: 'wednesday', label: t('Wednesday') },
    { key: 'thursday', label: t('Thursday') },
    { key: 'friday', label: t('Friday') },
    { key: 'saturday', label: t('Saturday') },
    { key: 'sunday', label: t('Sunday') },
  ];

  const workingDays = weekDays.filter(day => settings[`rotas_work_schedule_${day.key}`]);
  const offDays = weekDays.filter(day => !settings[`rotas_work_schedule_${day.key}`]);

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div>
          <CardTitle className="flex items-center gap-2 text-lg">
            <Calendar className="h-5 w-5" />
            {t('Work Schedule Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure working days and off days for the organization')}
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
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Side - Day Settings */}
          <div className="lg:col-span-2 space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {weekDays.map((day) => (
                <div key={day.key} className="flex items-center justify-between p-3 border rounded-lg">
                  <Label htmlFor={day.key} className="font-medium">
                    {day.label}
                  </Label>
                  <div className="flex items-center space-x-2">
                    <span className="text-sm text-muted-foreground">
                      {settings[`rotas_work_schedule_${day.key}`] ? t('Working') : t('Off')}
                    </span>
                    <Switch
                      id={day.key}
                      checked={settings[`rotas_work_schedule_${day.key}`]}
                      onCheckedChange={(checked) => handleSwitchChange(day.key, checked)}
                      disabled={!canEdit}
                    />
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Right Side - Summary */}
          <div className="border rounded-lg p-4 bg-muted/30">
            <h4 className="font-medium mb-3 flex items-center gap-2">
              <Calendar className="h-4 w-4" />
              {t('Schedule Summary')}
            </h4>
            
            <div className="space-y-4 text-sm">
              <div>
                <h5 className="font-medium text-green-700 mb-2">{t('Working Days')} ({workingDays.length})</h5>
                <div className="space-y-1">
                  {workingDays.length > 0 ? (
                    workingDays.map(day => (
                      <div key={day.key} className="flex items-center gap-2">
                        <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span>{day.label}</span>
                      </div>
                    ))
                  ) : (
                    <span className="text-muted-foreground italic">{t('No working days selected')}</span>
                  )}
                </div>
              </div>

              <div>
                <h5 className="font-medium text-red-700 mb-2">{t('Off Days')} ({offDays.length})</h5>
                <div className="space-y-1">
                  {offDays.length > 0 ? (
                    offDays.map(day => (
                      <div key={day.key} className="flex items-center gap-2">
                        <div className="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span>{day.label}</span>
                      </div>
                    ))
                  ) : (
                    <span className="text-muted-foreground italic">{t('No off days selected')}</span>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}