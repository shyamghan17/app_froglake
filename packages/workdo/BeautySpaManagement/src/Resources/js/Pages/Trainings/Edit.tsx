import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { DatePicker } from '@/components/ui/date-picker';
import { Textarea } from '@/components/ui/textarea';
import { EditTrainingProps, EditTrainingFormData } from './types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function EditTraining({ training, onSuccess }: EditTrainingProps) {
    const { } = usePage<any>().props;

    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditTrainingFormData>({
        training_name: training.training_name ?? '',
        trainer: training.trainer ?? '',
        date: training.date || '',
        duration: training.duration ?? '',
        location: training.location ?? '',
        description: training.description ?? '',
    });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('beauty-spa-management.trainings.update', training.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t('Edit Training')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="training_name">{t('Training Name')}</Label>
                    <Input
                        id="training_name"
                        type="text"
                        value={data.training_name}
                        onChange={(e) => setData('training_name', e.target.value)}
                        placeholder={t('Enter Training Name')}
                        required
                    />
                    <InputError message={errors.training_name} />
                </div>

                <div>
                    <Label htmlFor="trainer">{t('Trainer')}</Label>
                    <Input
                        id="trainer"
                        type="text"
                        value={data.trainer}
                        onChange={(e) => setData('trainer', e.target.value)}
                        placeholder={t('Enter Trainer')}
                        required
                    />
                    <InputError message={errors.trainer} />
                </div>

                <div>
                    <Label required>{t('Date')}</Label>
                    <DatePicker
                        value={data.date}
                        onChange={(date) => setData('date', date)}
                        placeholder={t('Select Date')}
                    />
                    <InputError message={errors.date} />
                </div>

                <div>
                    <Label htmlFor="duration">{t('Duration')}</Label>
                    <Input
                        id="duration"
                        type="text"
                        value={data.duration}
                        onChange={(e) => setData('duration', e.target.value)}
                        placeholder={t('Enter Duration')}
                        required
                    />
                    <InputError message={errors.duration} />
                </div>

                <div>
                    <Label htmlFor="location">{t('Location')}</Label>
                    <Input
                        id="location"
                        type="text"
                        value={data.location}
                        onChange={(e) => setData('location', e.target.value)}
                        placeholder={t('Enter Location')}
                        required
                    />
                    <InputError message={errors.location} />
                </div>

                <div>
                    <Label htmlFor="description">{t('Description')}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder={t('Enter Description')}
                        rows={3}
                    />
                    <InputError message={errors.description} />
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