import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import { Button } from "@/components/ui/button";
import { Label } from '@/components/ui/label';
import InputError from '@/components/ui/input-error';
import { Input } from '@/components/ui/input';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import { DatePicker } from '@/components/ui/date-picker';
import { Switch } from '@/components/ui/switch';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { MultiSelectEnhanced } from '@/components/ui/multi-select-enhanced';
import MediaPicker from '@/components/MediaPicker';
import { EditNoticeProps, EditNoticeFormData } from './types';
import { useState, useEffect } from 'react';
import { isPackageActive } from '@/utils/helpers';

export default function EditNotice({ notice, onSuccess }: EditNoticeProps) {
    const { t } = useTranslation();
    const [descriptionError, setDescriptionError] = useState('');
    const [targetOptions, setTargetOptions] = useState<{ value: string; label: string }[]>([]);
    const [loadingOptions, setLoadingOptions] = useState(false);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const formatDate = (date: string | null | undefined): string => {
        if (!date) return '';
        return new Date(date).toISOString().split('T')[0];
    };

    const { data, setData, put, processing, errors } = useForm<EditNoticeFormData>({
        title: notice.title ?? '',
        description: notice.description ?? '',
        attachments: notice.attachments ?? [],
        start_date: formatDate(notice.start_date),
        expiry_date: formatDate(notice.expiry_date),
        priority: notice.priority ?? 'normal',
        require_acknowledgment: notice.require_acknowledgment ?? false,
        target_type: notice.target_type ?? 'all',
        target_ids: notice.target_ids ?? [],
        allow_comments: notice.allow_comments ?? false,
    });

    useEffect(() => {
        if (data.target_type === 'all') {
            setTargetOptions([]);
            return;
        }

        setLoadingOptions(true);

        fetch(route('notice-board.notices.target-options') + `?type=${data.target_type}`)
            .then((res) => res.json())
            .then((json) => {
                const options = json.map((item: any) => ({ value: item.id.toString(), label: item.name }));
                if (data.target_type === 'department') {
                    setTargetOptions([{ value: 'all_departments', label: t('All Departments') }, ...options]);
                } else {
                    setTargetOptions(options);
                }
            })
            .finally(() => setLoadingOptions(false));
    }, [data.target_type]);

    const handleTargetChange = (selected: string[]) => {
        if (data.target_type === 'department') {
            const allIds = targetOptions.filter((o) => o.value !== 'all_departments').map((o) => o.value);
            const allSelected = allIds.every((id) => data.target_ids.map(String).includes(id));

            if (selected.includes('all_departments')) {
                if (allSelected) {
                    setData('target_ids', []);
                } else {
                    setData('target_ids', allIds.map(Number));
                }
            } else {
                setData('target_ids', selected.map(Number));
            }
        } else {
            setData('target_ids', selected.map(Number));
        }
    };

    const getTargetLabel = () => {
        if (data.target_type === 'department') return t('Select Departments');
        if (data.target_type === 'role') return t('Select Roles');
        if (data.target_type === 'specific_users') return t('Select Users');
        return '';
    };

    // whichever is earlier — so existing past start dates aren't forced to change
    const startMinDate = notice.start_date && new Date(notice.start_date) < today
        ? new Date(notice.start_date)
        : today;

    // expiry minDate = same day as start_date or after
    const expiryMinDate = data.start_date
        ? new Date(data.start_date)
        : today;

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!data.description.trim()) {
            setDescriptionError(t('Description is required'));
            return;
        }

        setDescriptionError('');

        put(route('notice-board.notices.update', notice.id), {
            onSuccess: () => onSuccess(),
        });
    };

    return (
        <DialogContent className="max-w-2xl">
            <DialogHeader>
                <DialogTitle>{t('Edit Notice')}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">

                {/* Title */}
                <div>
                    <Label htmlFor="title">{t('Title')}</Label>
                    <Input
                        id="title"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        placeholder={t('Enter Title')}
                        required
                    />
                    <InputError message={errors.title} />
                </div>

                {/* Description */}
                <div>
                    <Label htmlFor="description" required>{t('Description')}</Label>
                    <RichTextEditor
                        id="description"
                        content={data.description}
                        onChange={(content) => {
                            setData('description', content);
                            if (descriptionError) setDescriptionError('');
                        }}
                        placeholder={t('Enter Description')}
                        required
                    />
                    <InputError message={errors.description || descriptionError} />
                </div>

                {/* Attachments */}
                <div>
                    <Label htmlFor="attachments">{t('Attachments')}</Label>
                    <MediaPicker
                        id="attachments"
                        multiple
                        value={data.attachments}
                        onChange={(value) => setData('attachments', value)}
                    />
                    <InputError message={errors.attachments} />
                </div>

                {/* Start Date & Expiry Date */}
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <Label htmlFor="start_date">{t('Start Date')}</Label>
                        <DatePicker
                            id="start_date"
                            value={data.start_date}
                            onChange={(date) => {
                                setData('start_date', date);
                                if (data.expiry_date && new Date(data.expiry_date) < new Date(date)) {
                                    setData('expiry_date', '');
                                }
                            }}
                            placeholder={t('Select Start Date')}
                            minDate={startMinDate}
                            required
                        />
                        <InputError message={errors.start_date} />
                    </div>
                    <div>
                        <Label htmlFor="expiry_date">{t('Expiry Date')}</Label>
                        <DatePicker
                            id="expiry_date"
                            value={data.expiry_date}
                            onChange={(date) => setData('expiry_date', date)}
                            placeholder={t('Select Expiry Date')}
                            minDate={expiryMinDate}
                        />
                        <InputError message={errors.expiry_date} />
                    </div>
                </div>

                {/* Priority */}
                <div>
                    <Label htmlFor="priority">{t('Priority')}</Label>
                    <RadioGroup
                        id="priority"
                        value={data.priority}
                        onValueChange={(value) => {
                            setData('priority', value);
                            if (value !== 'critical') {
                                setData('require_acknowledgment', false);
                            }
                        }}
                        className="flex gap-6 mt-2"
                    >
                        {[
                            { value: 'normal', label: t('Normal') },
                            { value: 'urgent', label: t('Urgent') },
                            { value: 'critical', label: t('Critical') },
                        ].map((p) => (
                            <div key={p.value} className="flex items-center gap-2">
                                <RadioGroupItem value={p.value} id={`priority_${p.value}`} />
                                <Label htmlFor={`priority_${p.value}`} className="cursor-pointer">{p.label}</Label>
                            </div>
                        ))}
                    </RadioGroup>
                    <InputError message={errors.priority} />
                </div>

                {/* Target Audience */}
                <div>
                    <Label htmlFor="target_type">{t('Target Audience')}</Label>
                    <RadioGroup
                        id="target_type"
                        value={data.target_type}
                        onValueChange={(value) => {
                            setData('target_type', value);
                            setData('target_ids', []);
                        }}
                        className="flex gap-6 mt-2"
                    >
                        {[
                            { value: 'all', label: t('All') },
                            ...(isPackageActive('Hrm') ? [{ value: 'department', label: t('Department') }] : []),
                            { value: 'role', label: t('Role') },
                            { value: 'specific_users', label: t('Specific Users') },
                        ].map((type) => (
                            <div key={type.value} className="flex items-center gap-2">
                                <RadioGroupItem value={type.value} id={`target_type_${type.value}`} />
                                <Label htmlFor={`target_type_${type.value}`} className="cursor-pointer">{type.label}</Label>
                            </div>
                        ))}
                    </RadioGroup>
                    <InputError message={errors.target_type} />
                </div>

                {data.target_type !== 'all' && (
                    <div>
                        <Label htmlFor="target_ids" required>{getTargetLabel()}</Label>
                        <MultiSelectEnhanced
                            id="target_ids"
                            options={targetOptions}
                            value={data.target_ids.map(String)}
                            onValueChange={handleTargetChange}
                            placeholder={loadingOptions ? t('Loading...') : getTargetLabel()}
                            searchable
                            required
                        />
                        <InputError message={errors.target_ids} />
                    </div>
                )}

                {/* Allow Comments & Require Acknowledgment */}
                <div className="grid grid-cols-2 gap-4">
                    <div className="flex items-center gap-2">
                        <Switch
                            id="allow_comments"
                            checked={data.allow_comments}
                            onCheckedChange={(checked) => setData('allow_comments', checked)}
                        />
                        <Label htmlFor="allow_comments" className="cursor-pointer">{t('Allow Comments')}</Label>
                        <InputError message={errors.allow_comments} />
                    </div>

                    {data.priority === 'critical' && (
                        <div className="flex items-center gap-2">
                            <Switch
                                id="require_acknowledgment"
                                checked={data.require_acknowledgment}
                                onCheckedChange={(checked) => setData('require_acknowledgment', checked)}
                            />
                            <Label htmlFor="require_acknowledgment" className="cursor-pointer">{t('Require Acknowledgment')}</Label>
                            <InputError message={errors.require_acknowledgment} />
                        </div>
                    )}
                </div>

                {/* Buttons */}
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
