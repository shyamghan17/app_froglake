import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { toast } from 'sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DatePicker } from '@/components/ui/date-picker';
import { Copy, ExternalLink } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { formatDate } from '@/utils/helpers';

interface ShareDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    startDate: string;
    endDate: string;
}

export default function ShareDialog({
    open,
    onOpenChange,
    startDate,
    endDate
}: ShareDialogProps) {
    const { t } = useTranslation();
    const [loading, setLoading] = useState(false);
    const [shareUrl, setShareUrl] = useState('');
    const [formData, setFormData] = useState({
        description: '',
        hasExpiry: false,
        expiryDate: '',
        hasPassword: false,
        password: '',
        confirmPassword: ''
    });

    const handleCreateLink = async () => {
        if (formData.hasPassword && formData.password !== formData.confirmPassword) {
            toast.error(t('Passwords do not match'));
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(route('rotas.share.create'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate,
                    description: formData.description,
                    has_expiry: formData.hasExpiry,
                    expiry_date: formData.hasExpiry ? formData.expiryDate : null,
                    has_password: formData.hasPassword,
                    password: formData.hasPassword ? formData.password : null,
                })
            });

            const data = await response.json();

            if (data.success) {
                setShareUrl(data.share_url);
            } else {
                toast.error(data.error || t('Failed to create share link'));
            }

        } catch (error) {
            toast.error(t('Failed to create share link'));
        } finally {
            setLoading(false);
        }
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(shareUrl);
        toast.success(t('Link copied to clipboard'));
    };

    const openInNewTab = () => {
        window.open(shareUrl, '_blank');
    };

    const resetForm = () => {
        setFormData({
            description: '',
            hasExpiry: false,
            expiryDate: '',
            hasPassword: false,
            password: '',
            confirmPassword: ''
        });
        setShareUrl('');
    };

    const handleClose = () => {
        resetForm();
        onOpenChange(false);
    };

    return (
        <Dialog open={open} onOpenChange={handleClose}>
            <DialogContent className="max-w-md">
                <DialogHeader>
                    <DialogTitle>{t('Share Schedule')}</DialogTitle>
                    <DialogDescription>
                        {t('Create a shareable link for the schedule from')} {formatDate(startDate)} {t('to')} {formatDate(endDate)}
                    </DialogDescription>
                </DialogHeader>

                {!shareUrl ? (
                    <div className="space-y-4">
                        <div>
                            <Label>{t('Description (Optional)')}</Label>
                            <Textarea
                                value={formData.description}
                                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                placeholder={t('Add a description for this shared schedule')}
                                rows={3}
                            />
                        </div>

                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="hasExpiry"
                                checked={formData.hasExpiry}
                                onCheckedChange={(checked) => setFormData({ ...formData, hasExpiry: !!checked })}
                            />
                            <Label htmlFor="hasExpiry">{t('Set expiry date')}</Label>
                        </div>

                        {formData.hasExpiry && (
                            <div>
                                <Label>{t('Expiry Date')}</Label>
                                <DatePicker
                                    value={formData.expiryDate}
                                    onChange={(date) => setFormData({ ...formData, expiryDate: date })}
                                    placeholder={t('Select expiry date')}
                                />
                            </div>
                        )}

                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="hasPassword"
                                checked={formData.hasPassword}
                                onCheckedChange={(checked) => setFormData({ ...formData, hasPassword: !!checked })}
                            />
                            <Label htmlFor="hasPassword">{t('Password protect')}</Label>
                        </div>

                        {formData.hasPassword && (
                            <div className="space-y-3">
                                <div>
                                    <Label>{t('Password')}</Label>
                                    <Input
                                        type="password"
                                        value={formData.password}
                                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                        placeholder={t('Enter password')}
                                    />
                                </div>
                                <div>
                                    <Label>{t('Confirm Password')}</Label>
                                    <Input
                                        type="password"
                                        value={formData.confirmPassword}
                                        onChange={(e) => setFormData({ ...formData, confirmPassword: e.target.value })}
                                        placeholder={t('Confirm password')}
                                    />
                                </div>
                            </div>
                        )}

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" onClick={handleClose}>
                                {t('Cancel')}
                            </Button>
                            <Button onClick={handleCreateLink} disabled={loading}>
                                {loading ? t('Creating...') : t('Create Link')}
                            </Button>
                        </div>
                    </div>
                ) : (
                    <div className="space-y-4">
                        <div>
                            <Label>{t('Share Link')}</Label>
                            <div className="flex gap-2">
                                <Input
                                    value={shareUrl}
                                    readOnly
                                    className="flex-1"
                                />
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button size="sm" variant="outline" onClick={copyToClipboard}>
                                                <Copy className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Copy Link')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button size="sm" variant="outline" onClick={openInNewTab}>
                                                <ExternalLink className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t('Open in New Tab')}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                        </div>
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}