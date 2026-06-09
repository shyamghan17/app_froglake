import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { usePage } from '@inertiajs/react';

interface CreateFormData {
    group_id: string;
    sms: string;
}

interface CreateProps {
    onSuccess: () => void;
}

export default function Create({ onSuccess }: CreateProps) {
    const { bulksmsgroups } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateFormData>({
        group_id: '',
        sms: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bulk-s-m-s.bulksms-group-sms.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Send Bulk SMS')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="group_id" required>{t('Group Name')}</Label>
                    <Select value={data.group_id} onValueChange={(value) => setData('group_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Group')} />
                        </SelectTrigger>
                        <SelectContent>
                            {bulksmsgroups?.map((item: any) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.group_id} />
                </div>
                
                <div>
                    <Label htmlFor="sms" required>{t('SMS Message')}</Label>
                    <Textarea
                        id="sms"
                        value={data.sms}
                        onChange={(e) => setData('sms', e.target.value)}
                        placeholder={t('Enter SMS Message')}
                        rows={4}
                    />
                    <InputError message={errors.sms} />
                </div>
                
                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Sending...') : t('Send SMS')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}