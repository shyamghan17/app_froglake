import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm, router } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Switch } from '@/components/ui/switch';
import { usePage } from '@inertiajs/react';
import { CreateTeamMemberProps, CreateTeamMemberFormData } from './types';

export default function Create({ onClose, users }: CreateTeamMemberProps) {
    const { t } = useTranslation();
    const { auth } = usePage<any>().props;
    const { data, setData, post, processing, errors } = useForm<CreateTeamMemberFormData>({
        user_id: '',
        designation: '',
        experience_year: '',
        skills: '',
        rate_per_hour: '',
        is_active: true,
        bio: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('photo-studio-management.team-members.store'), {
            onSuccess: () => onClose(),
        });
    };

    return (
        <DialogContent className="max-w-lg max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{t('Create Team Member')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="user_id" required>{t('User')}</Label>
                    <Select value={data.user_id} onValueChange={(value) => setData('user_id', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder={t('Select User')} />
                        </SelectTrigger>
                        <SelectContent>
                            {users?.map((item) => (
                                <SelectItem key={item.id} value={item.id.toString()}>
                                    {item.name} ({item.email})
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.user_id} />
                    {users?.length === 0 && auth?.user?.permissions?.includes('create-users') && (
                        <p className="text-xs text-gray-500 mt-1">
                            {t('No available users.')} <button type="button" onClick={() => router.get(route('users.index'))} className="text-blue-600 hover:underline">{t('Create user')}</button>
                        </p>
                    )}
                </div>

                <div>
                    <Label htmlFor="designation" >{t('Designation')}</Label>
                    <Input
                        id="designation"
                        value={data.designation}
                        onChange={(e) => setData('designation', e.target.value)}
                        placeholder={t('Enter Designation')} required
                    />
                    <InputError message={errors.designation} />
                </div>

                <div>
                    <Label htmlFor="experience_year" >{t('Experience Year')}</Label>
                    <Input
                        id="experience_year"
                        type="number"
                        min={0}
                        value={data.experience_year}
                        onChange={(e) => setData('experience_year', e.target.value)}
                        placeholder={t('Enter Years of Experience')} required
                    />
                    <InputError message={errors.experience_year} />
                </div>

                <div>
                    <Label htmlFor="skills">{t('Skills')}</Label>
                    <Input
                        id="skills"
                        value={data.skills}
                        onChange={(e) => setData('skills', e.target.value)}
                        placeholder={t('e.g. Portrait, Wedding, Lighting')} required
                    />
                    <InputError message={errors.skills} />
                </div>

                <div>
                    <Label htmlFor="rate_per_hour" required>{t('Rate Per Hour')}</Label>
                    <CurrencyInput
                        id="rate_per_hour"
                        value={data.rate_per_hour}
                        onChange={(value) => setData('rate_per_hour', value)}
                        placeholder={t('Enter Rate Per Hour')}
                        
                    />
                    <InputError message={errors.rate_per_hour} />
                </div>

                <div className="flex items-center gap-3">
                    <Label htmlFor="is_active">{t('Status')}</Label>
                    <Switch
                        id="is_active"
                        checked={data.is_active}
                        onCheckedChange={(checked) => setData('is_active', checked)}
                    />
                    <InputError message={errors.is_active} />
                </div>

                <div>
                    <Label htmlFor="bio">{t('Bio')}</Label>
                    <Textarea
                        id="bio"
                        value={data.bio}
                        onChange={(e) => setData('bio', e.target.value)}
                        placeholder={t('Enter Bio')}
                        rows={3} required
                    />
                    <InputError message={errors.bio} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onClose}>{t('Cancel')}</Button>
                    <Button type="submit" disabled={processing}>{processing ? t('Creating...') : t('Create')}</Button>
                </div>
            </form>
        </DialogContent>
    );
}
