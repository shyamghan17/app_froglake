import { useForm } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import InputError from '@/components/ui/input-error';
import { SlugInputComponent } from '@/components/ui/slug-input';

interface CreateProps {
    onSuccess: () => void;
}

export default function Create({ onSuccess }: CreateProps) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        title: '',
        slug: '',
        page_header: '',
        page_header_description: '',
        content: '',
        is_active: true
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.custom-pages.store'), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <DialogContent className="max-w-4xl max-h-[90vh]">
            <DialogHeader>
                <DialogTitle>{t('Create Custom Page')}</DialogTitle>
                <DialogDescription>
                    {t('Create a new custom page for your website')}
                </DialogDescription>
            </DialogHeader>

            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="title" required>{t('Title')}</Label>
                    <Input
                        id="title"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        placeholder={t('Enter page title')}
                        required
                    />
                    <InputError message={errors.title} />
                </div>

                <SlugInputComponent
                    id="slug"
                    label={t('URL Slug')}
                    value={data.slug}
                    onChange={(value) => setData('slug', value)}
                    placeholder={t('URL-friendly name (e.g., about-us, privacy-policy)')}
                    error={errors.slug}
                    sourceValue={data.title}
                    required
                />

                <div>
                    <Label htmlFor="page_header">{t('Page Header')}</Label>
                    <Input
                        id="page_header"
                        value={data.page_header}
                        onChange={(e) => setData('page_header', e.target.value)}
                        placeholder={t('Enter page header')}
                    />
                    <InputError message={errors.page_header} />
                </div>

                <div>
                    <Label htmlFor="page_header_description">{t('Page Header Description')}</Label>
                    <Textarea
                        id="page_header_description"
                        value={data.page_header_description}
                        onChange={(e) => setData('page_header_description', e.target.value)}
                        placeholder={t('Enter page header description')}
                        rows={3}
                    />
                    <InputError message={errors.page_header_description} />
                </div>

                <div>
                    <Label htmlFor="content" required>{t('Content')}</Label>
                    <RichTextEditor
                        content={data.content}
                        onChange={(content) => setData('content', content)}
                        placeholder={t('Enter page content')}
                        className="[&_.ProseMirror]:min-h-[200px] [&_.ProseMirror]:max-h-[300px]"
                        required
                    />
                    <InputError message={errors.content} />
                </div>

                <div className="flex items-center space-x-2">
                    <Switch
                        id="is_active"
                        checked={data.is_active}
                        onCheckedChange={(checked) => setData('is_active', checked)}
                    />
                    <Label htmlFor="is_active">{t('Active')}</Label>
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={() => onSuccess()}>
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
