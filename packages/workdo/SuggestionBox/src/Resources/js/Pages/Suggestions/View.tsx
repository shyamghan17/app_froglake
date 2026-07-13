import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Lightbulb, User, Calendar, Eye, ThumbsUp, ArrowLeft } from 'lucide-react';
import { Suggestion } from './types';
import { formatDate } from '@/utils/helpers';

interface ShowSuggestionProps {
    suggestion: Suggestion;
    auth: {
        user: {
            id: number;
            name: string;
            permissions: string[];
        };
    };
}

export default function View({ suggestion, auth }: ShowSuggestionProps) {
    const { t } = useTranslation();
    const [votesCount] = useState(suggestion.votes_count);

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            'new': {
                label: 'New',
                color: 'bg-blue-100 text-blue-800'
            },
            'under_review': {
                label: 'Under Review',
                color: 'bg-yellow-100 text-yellow-800'
            },
            'accepted': {
                label: 'Accepted',
                color: 'bg-purple-100 text-purple-800'
            },
            'rejected': {
                label: 'Rejected',
                color: 'bg-red-100 text-red-800'
            },
            'complete': {
                label: 'Complete',
                color: 'bg-green-100 text-green-800'
            }
        };

        const config = statusConfig[status as keyof typeof statusConfig];

        return (
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}>
                {t(config?.label || status)}
            </span>
        );
    };

    const getCategoryBadge = (category: any) => {
        return (
            <span
                className="px-2 py-1 rounded-full text-sm font-medium"
                style={{
                    backgroundColor: `${category?.color || '#273247'}20`,
                    color: category?.color || '#384b69'
                }}
            >
                {category?.name}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Suggestion Box') },
                { label: t('Suggestions'), url: route('suggestions.index') },
                { label: t('View Suggestion') }
            ]}
            pageTitle={t('View Suggestion')}
            pageActions={
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.visit(route('suggestions.index'))}
                >
                    <ArrowLeft className="h-4 w-4" />
                    {t('Back')}
                </Button>
            }
        >
            <Head title={`${t('View Suggestion')} - ${suggestion.title}`} />
            
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-primary/10 rounded-lg">
                                <Lightbulb className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <h1 className="text-xl font-semibold">{suggestion.title}</h1>
                            </div>
                        </div>
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-8">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div className="lg:col-span-2 space-y-6">
                            {/* Basic Information */}
                            <div>
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Basic Information')}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-sm font-medium text-gray-700">{t('Submitted By')}</label>
                                        <p className="text-gray-900 font-semibold mt-1">
                                            {suggestion.is_anonymous ? t('Anonymous') : (suggestion.user?.name || '-')}
                                        </p>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-sm font-medium text-gray-700">{t('Category')}</label>
                                        <div className="mt-1">
                                            {suggestion.category ? 
                                                getCategoryBadge(suggestion.category) : 
                                                <span className="text-gray-500 text-sm">{t('No Category')}</span>
                                            }
                                        </div>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <label className="text-sm font-medium text-gray-700">{t('Status')}</label>
                                        <div className="mt-1">
                                            {getStatusBadge(suggestion.status)}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Engagement Stats */}
                            <div>
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Engagement Stats')}</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                        <label className="text-sm font-medium text-blue-700 flex items-center gap-2">
                                            <ThumbsUp className="h-4 w-4" />
                                            {t('Votes Count')}
                                        </label>
                                        <p className="text-xl font-bold text-blue-800 mt-1">{votesCount || 0}</p>
                                    </div>
                                    <div className="bg-green-50 p-4 rounded-lg border border-green-200">
                                        <label className="text-sm font-medium text-green-700 flex items-center gap-2">
                                            <Eye className="h-4 w-4" />
                                            {t('Views Count')}
                                        </label>
                                        <p className="text-xl font-bold text-green-800 mt-1">{suggestion.views_count || 0}</p>
                                    </div>
                                </div>
                            </div>

                            {/* Voters Section */}
                            {suggestion.voters && suggestion.voters.length > 0 && (
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Voted By')}</h3>
                                    <div className="rounded-lg">
                                        <div className="flex flex-wrap gap-2">
                                            {suggestion.voters.map((voterName, index) => (
                                                <span 
                                                    key={index}
                                                    className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200"
                                                >
                                                    {voterName}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Viewers Section */}
                            {suggestion.viewers && suggestion.viewers.length > 0 && (
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Viewed By')}</h3>
                                    <div className="rounded-lg">
                                        <div className="flex flex-wrap gap-2">
                                            {suggestion.viewers.map((viewerName, index) => (
                                                <span 
                                                    key={index}
                                                    className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200"
                                                >
                                                    {viewerName}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Description */}
                            {suggestion.description && (
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Description')}</h3>
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <p className="text-gray-700 leading-relaxed whitespace-pre-wrap">{suggestion.description}</p>
                                    </div>
                                </div>
                            )}

                            {/* Admin Response */}
                            {suggestion.admin_response && (
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Admin Response')}</h3>
                                    <div className={`p-4 rounded-lg border-l-4 ${
                                        suggestion.status === 'accepted' ? 'bg-green-50 border-l-green-500' :
                                        suggestion.status === 'rejected' ? 'bg-red-50 border-l-red-500' :
                                        suggestion.status === 'under_review' ? 'bg-yellow-50 border-l-yellow-500' :
                                        suggestion.status === 'complete' ? 'bg-purple-50 border-l-purple-500' :
                                        'bg-blue-50 border-l-blue-500'
                                    }`}>
                                        <p className="text-gray-900 leading-relaxed whitespace-pre-wrap">{suggestion.admin_response}</p>
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="lg:col-span-1 space-y-6">
                            {/* Submission Details */}
                            <div className="bg-white border rounded-lg p-6 shadow-sm">
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Submission Details')}</h3>
                                <div className="space-y-4">
                                    <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <Calendar className="h-5 w-5 text-gray-600" />
                                        <div>
                                            <p className="text-sm font-medium text-gray-700">{t('Submission Date')}</p>
                                            <p className="text-sm text-gray-900">
                                                {suggestion.created_at ? formatDate(suggestion.created_at) : '-'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <User className="h-5 w-5 text-gray-600" />
                                        <div>
                                            <p className="text-sm font-medium text-gray-700">{t('Submission Type')}</p>
                                            <p className="text-sm text-gray-900">
                                                {suggestion.is_anonymous ? t('Anonymous') : t('Public')}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Response Details */}
                            {(suggestion.responded_by || suggestion.responded_at) && (
                                <div className="bg-white border rounded-lg p-6 shadow-sm">
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4">{t('Response Details')}</h3>
                                    <div className="space-y-4">
                                        {suggestion.responded_by && (
                                            <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                                <User className="h-5 w-5 text-gray-600" />
                                                <div>
                                                    <p className="text-sm font-medium text-gray-700">{t('Responded By')}</p>
                                                    <p className="text-sm text-gray-900">{suggestion.responded_by.name || '-'}</p>
                                                </div>
                                            </div>
                                        )}
                                        {suggestion.responded_at && (
                                            <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                                <Calendar className="h-5 w-5 text-gray-600" />
                                                <div>
                                                    <p className="text-sm font-medium text-gray-700">{t('Response Date')}</p>
                                                    <p className="text-sm text-gray-900">
                                                        {suggestion.responded_at ? formatDate(suggestion.responded_at) : '-'}
                                                    </p>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}