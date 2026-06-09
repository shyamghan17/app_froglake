import { useTranslation } from 'react-i18next';
import { DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import { User, Mail, Phone, Briefcase, Clock, Wrench, DollarSign, FileText } from 'lucide-react';
import { PhotoStudioTeamMember } from './types';
import { formatCurrency, getImagePath } from '@/utils/helpers';

interface ViewProps {
    teamMember: PhotoStudioTeamMember;
    onClose: () => void;
}

export default function View({ teamMember }: ViewProps) {
    const { t } = useTranslation();

    return (
        <DialogContent className="max-w-lg max-h-[90vh] overflow-y-auto">
            <DialogHeader className="mb-4">
                <DialogTitle className="flex items-center gap-2">
                    <User className="h-5 w-5" />
                    {t('Team Member Details')}
                </DialogTitle>
            </DialogHeader>

            <div className="space-y-3">

                {/* Profile Card */}
                <Card>
                    <CardContent className="p-4">
                        <div className="flex gap-4 items-center">
                            {/* Avatar */}
                            <div className="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 flex items-center justify-center shrink-0">
                                {teamMember.user?.avatar ? (
                                    <img src={getImagePath(teamMember.user.avatar)} alt="Avatar" className="w-full h-full object-cover" />
                                ) : (
                                    <User className="w-9 h-9 text-gray-300" />
                                )}
                            </div>

                            {/* Identity */}
                            <div className="flex-1 min-w-0">
                                <p className="text-base font-bold text-gray-900 truncate">{teamMember.user?.name || '-'}</p>
                                <p className="text-sm text-gray-500 truncate">{teamMember.designation || '-'}</p>
                                <span className={`inline-block mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full ${teamMember.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`}>
                                    {teamMember.is_active ? t('Active') : t('Inactive')}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Email & Phone */}
                <div className="grid grid-cols-12 gap-3">
                    <div className="col-span-6">
                        <Card>
                            <CardContent className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    <Mail className="h-4 w-4 text-blue-600" />
                                    <h4 className="text-sm font-semibold">{t('Email')}</h4>
                                </div>
                                <div className="text-sm text-gray-700 truncate">{teamMember.user?.email || '-'}</div>
                            </CardContent>
                        </Card>
                    </div>
                    <div className="col-span-6">
                        <Card>
                            <CardContent className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    <Phone className="h-4 w-4 text-green-600" />
                                    <h4 className="text-sm font-semibold">{t('Phone')}</h4>
                                </div>
                                <div className="text-sm text-gray-700">{teamMember.user?.mobile_no || '-'}</div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Designation & Experience */}
                <div className="grid grid-cols-12 gap-3">
                    <div className="col-span-6">
                        <Card>
                            <CardContent className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    <Briefcase className="h-4 w-4 text-purple-600" />
                                    <h4 className="text-sm font-semibold">{t('Designation')}</h4>
                                </div>
                                <div className="text-sm text-gray-700">{teamMember.designation || '-'}</div>
                            </CardContent>
                        </Card>
                    </div>
                    <div className="col-span-6">
                        <Card>
                            <CardContent className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    <Clock className="h-4 w-4 text-orange-500" />
                                    <h4 className="text-sm font-semibold">{t('Experience')}</h4>
                                </div>
                                <div className="text-sm text-gray-700">{teamMember.experience_year ?? '-'} {t('years')}</div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Rate Per Hour */}
                <Card>
                    <CardContent className="p-4">
                        <div className="flex items-center gap-2 mb-2">
                            <DollarSign className="h-4 w-4 text-green-600" />
                            <h4 className="text-sm font-semibold">{t('Rate Per Hour')}</h4>
                        </div>
                        <div className="text-sm font-bold text-green-600">{teamMember.rate_per_hour ? formatCurrency(teamMember.rate_per_hour) : '-'}</div>
                    </CardContent>
                </Card>

                {/* Skills */}
                <Card>
                    <CardContent className="p-4">
                        <div className="flex items-center gap-2 mb-2">
                            <Wrench className="h-4 w-4 text-gray-600" />
                            <h4 className="text-sm font-semibold">{t('Skills')}</h4>
                        </div>
                        <div className="text-sm text-gray-700">{teamMember.skills || '-'}</div>
                    </CardContent>
                </Card>

                {/* Bio */}
                {teamMember.bio && (
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <FileText className="h-4 w-4 text-gray-600" />
                                <h4 className="text-sm font-semibold">{t('Bio')}</h4>
                            </div>
                            <div className="text-sm text-gray-700 leading-relaxed">{teamMember.bio}</div>
                        </CardContent>
                    </Card>
                )}

            </div>
        </DialogContent>
    );
}
