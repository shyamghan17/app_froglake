import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface ResponseModalProps {
    suggestion: any;
    onSuccess: () => void;
}

interface ResponseFormData {
    status: string;
    admin_response: string;
}

export default function ResponseModal({ suggestion, onSuccess }: ResponseModalProps) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<ResponseFormData>({
        status: suggestion.status,
        admin_response: suggestion.admin_response || ''
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('suggestion-admin.respond', suggestion.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Admin Response')}</DialogTitle>
            </DialogHeader>

            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label required htmlFor="status">{t('Status')}</Label>
                    <Select value={data.status} onValueChange={(value) => setData('status', value)} required>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select Status')} />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="new">{t('New')}</SelectItem>
                            <SelectItem value="accepted">{t('Accepted')}</SelectItem>
                            <SelectItem value="rejected">{t('Rejected')}</SelectItem>
                            <SelectItem value="under_review">{t('Under Review')}</SelectItem>
                            <SelectItem value="complete">{t('Complete')}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.status} />
                </div>

                <div>
                    <Label htmlFor="admin_response">
                        {t('Response')}
                        
                    </Label>
                    <Textarea
                        id="admin_response"
                        value={data.admin_response}
                        onChange={(e) => setData('admin_response', e.target.value)}
                        placeholder={t('Enter response...')}
                        rows={3}
                    />
                    <InputError message={errors.admin_response} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t('Cancel')}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t('Updating...') : t('Update')}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}