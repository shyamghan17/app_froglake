import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PhoneInputComponent } from '@/components/ui/phone-input';
import { DatePicker } from '@/components/ui/date-picker';
import { usePage } from '@inertiajs/react';
import { formatDate } from '@/utils/helpers';

interface LeadCreateWrapperProps {
  onSuccess: () => void;
  initialData?: {
    name?: string;
    email?: string;
    phone?: string;
    website?: string;
    google_contact_lead_id?: number;
  };
}

interface CreateLeadFormData {
  subject: string;
  user_id: string;
  name: string;
  email: string;
  phone: string;
  date: string;
  google_contact_lead_id?: number;
}

export default function LeadCreateWrapper({ onSuccess, initialData }: LeadCreateWrapperProps) {
  const { users } = usePage<any>().props;
  const { t } = useTranslation();
  
  const { data, setData, post, processing, errors } = useForm<CreateLeadFormData>({
    subject: '',
    user_id: '',
    name: initialData?.name || '',
    email: initialData?.email || '',
    phone: initialData?.phone || '',
    date: '',
    google_contact_lead_id: initialData?.google_contact_lead_id
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('lead.leads.store'), {
      onSuccess: () => {
        onSuccess();
      }
    });
  };

  return (
    <DialogContent className="max-w-2xl">
      <DialogHeader>
        <DialogTitle>{t('Create Lead')}</DialogTitle>
      </DialogHeader>
      <form onSubmit={submit} className="space-y-4">
        <div className="grid grid-cols-2 gap-4">
          <div>
            <Label htmlFor="name">{t('Name')}</Label>
            <Input
              id="name"
              type="text"
              value={data.name}
              onChange={(e) => setData('name', e.target.value)}
              placeholder={t('Enter Name')}
              required
            />
            <InputError message={errors.name} />
          </div>

          <div>
            <Label htmlFor="email">{t('Email')}</Label>
            <Input
              id="email"
              type="email"
              value={data.email}
              onChange={(e) => setData('email', e.target.value)}
              placeholder={t('Enter Email')}
              required
            />
            <InputError message={errors.email} />
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <Label htmlFor="subject">{t('Subject')}</Label>
            <Input
              id="subject"
              type="text"
              value={data.subject}
              onChange={(e) => setData('subject', e.target.value)}
              placeholder={t('Enter Subject')}
              required
            />
            <InputError message={errors.subject} />
          </div>

          <div>
            <Label htmlFor="user_id">{t('User')}</Label>
            <Select value={data.user_id?.toString() || ''} onValueChange={(value) => setData('user_id', value)}>
              <SelectTrigger>
                <SelectValue placeholder={t('Select User')} />
              </SelectTrigger>
              <SelectContent>
                {users?.map((item: any) => (
                  <SelectItem key={item.id} value={item.id.toString()}>
                    {item.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            <InputError message={errors.user_id} />
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <PhoneInputComponent
              label={t('Phone No')}
              value={data.phone}
              onChange={(value) => setData('phone', value || '')}
              error={errors.phone}
            />
          </div>

          <div>
            <Label>{t('Follow Up Date')}</Label>
            <DatePicker
              value={data.date}
              onChange={(date) => setData('date', formatDate(date))}
              placeholder={t('Select Follow Up Date')}
            />
            <InputError message={errors.date} />
          </div>
        </div>

        {data.google_contact_lead_id && (
          <input type="hidden" name="google_contact_lead_id" value={data.google_contact_lead_id} />
        )}

        <div className="flex justify-end gap-2">
          <Button type="button" variant="outline" onClick={onSuccess}>
            {t('Cancel')}
          </Button>
          <Button type="submit" disabled={processing}>
            {processing ? t('Creating...') : t('Create')}
          </Button>
        </div>
      </form>
    </DialogContent>
  );
}