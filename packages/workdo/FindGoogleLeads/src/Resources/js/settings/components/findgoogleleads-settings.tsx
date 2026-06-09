import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';
import { Save, MapPin } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import axios from 'axios';

interface FindGoogleLeadsSettings {
  findgoogleleads_api_key: string;
  finsgoogleleads_radius: string;
  finsgoogleleads_pipelines: string;
  finsgoogleleads_lead_stages: string;
}

interface Pipeline {
  id: string;
  name: string;
}

interface LeadStage {
  id: string;
  name: string;
}

interface FindGoogleLeadsSettingsProps {
  userSettings?: Record<string, string>;
  pipelines?: Pipeline[];
  leadStages?: LeadStage[];
  auth?: any;
  initialPipelines?: Pipeline[];
  initialLeadStages?: LeadStage[];
}

export default function FindGoogleLeadsSettings({
  userSettings,
  pipelines = [],
  leadStages = [],
  auth,
  initialPipelines = [],
  initialLeadStages = []
}: FindGoogleLeadsSettingsProps) {
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const [availablePipelines, setAvailablePipelines] = useState<Pipeline[]>(initialPipelines);
  const [availableStages, setAvailableStages] = useState<LeadStage[]>(initialLeadStages);
  const canEdit = auth?.user?.permissions?.includes('edit-findgoogleleads-settings');

  const [settings, setSettings] = useState<FindGoogleLeadsSettings>({
    findgoogleleads_api_key: userSettings?.findgoogleleads_api_key || '',
    finsgoogleleads_radius: userSettings?.finsgoogleleads_radius || '',
    finsgoogleleads_pipelines: userSettings?.finsgoogleleads_pipelines || '',
    finsgoogleleads_lead_stages: userSettings?.finsgoogleleads_lead_stages || '',
  });

  useEffect(() => {
    if (userSettings) {
      setSettings({
        findgoogleleads_api_key: userSettings?.findgoogleleads_api_key || '',
        finsgoogleleads_radius: userSettings?.finsgoogleleads_radius || '',
        finsgoogleleads_pipelines: userSettings?.finsgoogleleads_pipelines || '',
        finsgoogleleads_lead_stages: userSettings?.finsgoogleleads_lead_stages || '',
      });
    }
  }, [userSettings]);

  useEffect(() => {
    fetchPipelinesAndStages();
  }, []);

  const fetchPipelinesAndStages = async () => {
    try {
      const response = await axios.get(route('findgoogleleads.pipelines.stages'));
      const data = response.data;
      setAvailablePipelines(data.pipelines);
      if (settings.finsgoogleleads_pipelines) {
        const filteredStages = data.leadStages.filter(
          (stage: any) => stage.pipeline_id == settings.finsgoogleleads_pipelines
        );
        setAvailableStages(filteredStages);
      }
    } catch (error) {
      toast.error(t('Failed to fetch pipelines and stages'));
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setSettings(prev => ({ ...prev, [name]: value }));
  };

  const handleSelectChange = (name: string, value: string) => {
    setSettings(prev => ({ ...prev, [name]: value }));

    if (name === 'finsgoogleleads_pipelines') {
      fetchStages(value);
      setSettings(prev => ({ ...prev, finsgoogleleads_lead_stages: '' }));
    }
  };

  const fetchStages = async (pipelineId: string) => {
    try {
      const response = await axios.post(route('findgooglelead.setting.get.stage'), {
        id: pipelineId
      });

      const result = response.data;
      if (result.status === 0) {
        if (result.message === 'Lead module is not active') {
          toast.error(t('Lead module is not active'));
        } else {
          toast.error(t('No lead stages found for this pipeline.'));
        }
        setAvailableStages([]);
        return;
      }

      if (result.status === 1) {
        const stages = Object.entries(result.data).map(([id, name]) => ({
          id,
          name: name as string
        }));
        setAvailableStages(stages);
      }
    } catch (error) {
      toast.error(t('Failed to fetch lead stages'));
      setAvailableStages([]);
    }
  };

  const saveSettings = () => {
    setIsLoading(true);

    router.post(route('findgoogleleads.settings.update'), {
      settings: settings
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
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to save settings');
        toast.error(errorMessage);
      }
    });
  };

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div className="order-1 rtl:order-2">
          <CardTitle className="flex items-center gap-2 text-lg">
            <MapPin className="h-5 w-5" />
            {t('Find Google Leads Settings')}
          </CardTitle>
          <p className="text-sm text-muted-foreground mt-1">
            {t('Configure Find Google Leads API and pipeline settings')}
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
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="space-y-2">
            <Label htmlFor="findgoogleleads_api_key">{t('API Key')}</Label>
            <Input
              id="findgoogleleads_api_key"
              name="findgoogleleads_api_key"
              value={settings.findgoogleleads_api_key}
              onChange={handleInputChange}
              placeholder={t('Enter API Key')}
              disabled={!canEdit}
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="finsgoogleleads_radius">{t('Radius')}</Label>
            <Input
              id="finsgoogleleads_radius"
              name="finsgoogleleads_radius"
              type="number"
              value={settings.finsgoogleleads_radius}
              onChange={handleInputChange}
              placeholder={t('Enter Radius (in meters) that will be used to search e.g 1000')}
              disabled={!canEdit}
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="finsgoogleleads_pipelines">{t('Select Pipeline')}</Label>
            <Select
              value={settings.finsgoogleleads_pipelines || ''}
              onValueChange={(value) => handleSelectChange('finsgoogleleads_pipelines', value)}
              disabled={!canEdit}
            >
              <SelectTrigger>
                <SelectValue placeholder={t('Select Pipeline')} />
              </SelectTrigger>
              <SelectContent>
                {availablePipelines.map((pipeline) => (
                  <SelectItem key={pipeline.id} value={pipeline.id.toString()}>
                    {pipeline.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label htmlFor="finsgoogleleads_lead_stages">{t('Select Stages')}</Label>
            <Select
              value={settings.finsgoogleleads_lead_stages || ''}
              onValueChange={(value) => handleSelectChange('finsgoogleleads_lead_stages', value)}
              disabled={!canEdit || !settings.finsgoogleleads_pipelines}
            >
              <SelectTrigger>
                <SelectValue placeholder={t('Select Stages')} />
              </SelectTrigger>
              <SelectContent>
                {availableStages.map((stage) => (
                  <SelectItem key={stage.id} value={stage.id.toString()}>
                    {stage.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
