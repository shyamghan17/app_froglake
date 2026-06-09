import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';

interface CreateProps {
  onSuccess: () => void;
}

interface CreateFormData {
  title: string;
  keyword: string;
  address: string;
}

export default function Create({ onSuccess }: CreateProps) {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors } = useForm<CreateFormData>({
    title: '',
    keyword: '',
    address: ''
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('find-google-leads.store'), {
      onSuccess: () => {
        onSuccess();
      }
    });
  };

  return (
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{t('Search Google Lead')}</DialogTitle>
      </DialogHeader>
      <form onSubmit={submit} className="space-y-4">
        <div>
          <Label htmlFor="title">{t('Title')}</Label>
          <Input
            id="title"
            type="text"
            value={data.title}
            onChange={(e) => setData('title', e.target.value)}
            placeholder={t('Enter Title')}
            required
          />
          <InputError message={errors.title} />
        </div>
        
        <div>
          <Label htmlFor="keyword">{t('Keyword')}</Label>
          <Input
            id="keyword"
            type="text"
            value={data.keyword}
            onChange={(e) => setData('keyword', e.target.value)}
            placeholder={t('Enter Keyword')}
            required
          />
          <InputError message={errors.keyword} />
        </div>
        
        <div>
          <Label htmlFor="address">{t('Address')}</Label>
          <Input
            id="address"
            type="text"
            value={data.address}
            onChange={(e) => setData('address', e.target.value)}
            placeholder={t('Enter Address')}
            required
          />
          <InputError message={errors.address} />
        </div>
        
        <div className="flex justify-end gap-2">
          <Button type="button" variant="outline" onClick={onSuccess}>
            {t('Cancel')}
          </Button>
          <Button type="submit" disabled={processing}>
            {processing ? t('Searching...') : t('Search')}
          </Button>
        </div>
      </form>
    </DialogContent>
  );
}