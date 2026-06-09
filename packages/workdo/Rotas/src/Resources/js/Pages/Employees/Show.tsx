import { Head, usePage } from "@inertiajs/react";
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Eye } from 'lucide-react';
import { formatDate, getImagePath, getCurrencySymbol } from '@/utils/helpers';

export default function Show() {
    const { employee, documents } = usePage<any>().props;
    const { t } = useTranslation();

    const getGenderText = (gender: string) => {
        // Handle both old numeric values and new string values
        const genders: any = { "0": "Male", "1": "Female", "2": "Other" };
        return genders[gender] || gender || "Male";
    };

    const getEmploymentTypeText = (type: string) => {
        const types: any = { "0": "Full Time", "1": "Part Time", "2": "Temporary", "3": "Contract" };
        return types[type] || type;
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Rotas'), url: route('rotas.dashboard.index') },
                { label: t('Employees'), url: route('rotas.employees.index') },
                { label: t('View Employee') }
            ]}
            pageTitle={t('Employee Details')}
        >
            <Head title={t('Employee Details')} />

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Left Sidebar - Profile */}
                <div className="lg:col-span-1">
                    <Card className="shadow-sm">
                        <CardContent className="p-6 text-center">
                            <div className="mb-4">
                                <img
                                    src={employee.user?.avatar ? getImagePath(employee.user.avatar) : '/default-avatar.png'}
                                    alt={employee.user?.name || 'Employee'}
                                    className="w-24 h-24 rounded-full object-cover mx-auto border-4 border-gray-100"
                                    onError={(e) => { e.currentTarget.src = '/default-avatar.png'; }}
                                />
                            </div>
                            <h3 className="text-xl font-semibold mb-2">{employee.user?.name}</h3>
                            <p className="text-muted-foreground mb-4">{employee.user?.email}</p>

                            <div className="space-y-3 text-left">
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Employee ID')}</p>
                                    <p className="font-medium">{employee.employee_id}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Date of Birth')}</p>
                                    <p className="font-medium">{formatDate(employee.date_of_birth)}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Gender')}</p>
                                    <p className="font-medium">{t(getGenderText(employee.gender))}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Branch')}</p>
                                    <p className="font-medium">{employee.branch?.branch_name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Department')}</p>
                                    <p className="font-medium">{employee.department?.department_name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">{t('Designation')}</p>
                                    <p className="font-medium">{employee.designation?.designation_name}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Right Content - Tabs */}
                <div className="lg:col-span-3">
                    <Card className="shadow-sm">
                        <CardContent className="p-6">
                            <Tabs defaultValue="employment" className="w-full">
                                <TabsList className="grid w-full grid-cols-5">
                                    <TabsTrigger value="employment">{t('Employment')}</TabsTrigger>
                                    <TabsTrigger value="contact">{t('Contact')}</TabsTrigger>
                                    <TabsTrigger value="banking">{t('Banking')}</TabsTrigger>
                                    <TabsTrigger value="hours">{t('Hours & Rates')}</TabsTrigger>
                                    <TabsTrigger value="documents">{t('Documents')}</TabsTrigger>
                                </TabsList>

                                <TabsContent value="employment" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Employment Type')}</p>
                                            <p className="font-medium">{t(getEmploymentTypeText(employee.employment_type))}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Date of Joining')}</p>
                                            <p className="font-medium">{formatDate(employee.date_of_joining)}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Shift')}</p>
                                            <p className="font-medium">{employee.shifts?.shift_name || '-'}</p>
                                        </div>
                                    </div>
                                </TabsContent>

                                <TabsContent value="contact" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Address Line 1')}</p>
                                            <p className="font-medium">{employee.address_line_1}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Address Line 2')}</p>
                                            <p className="font-medium">{employee.address_line_2 || '-'}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('City')}</p>
                                            <p className="font-medium">{employee.city}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('State')}</p>
                                            <p className="font-medium">{employee.state}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Country')}</p>
                                            <p className="font-medium">{employee.country}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Postal Code')}</p>
                                            <p className="font-medium">{employee.postal_code}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Emergency Contact Name')}</p>
                                            <p className="font-medium">{employee.emergency_contact_name}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Emergency Contact Relationship')}</p>
                                            <p className="font-medium">{employee.emergency_contact_relationship}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Emergency Contact Number')}</p>
                                            <p className="font-medium">{employee.emergency_contact_number}</p>
                                        </div>
                                    </div>
                                </TabsContent>

                                <TabsContent value="banking" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Bank Name')}</p>
                                            <p className="font-medium">{employee.bank_name}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Account Holder Name')}</p>
                                            <p className="font-medium">{employee.account_holder_name}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Account Number')}</p>
                                            <p className="font-medium">{employee.account_number}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Bank Identifier Code')}</p>
                                            <p className="font-medium">{employee.bank_identifier_code}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Bank Branch')}</p>
                                            <p className="font-medium">{employee.bank_branch}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Tax Payer ID')}</p>
                                            <p className="font-medium">{employee.tax_payer_id || '-'}</p>
                                        </div>
                                    </div>
                                </TabsContent>

                                <TabsContent value="hours" className="space-y-6 mt-6">
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Hours Per Day')}</p>
                                            <p className="font-medium">{employee.hours_per_day || '-'}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Days Per Week')}</p>
                                            <p className="font-medium">{employee.days_per_week || '-'}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground mb-1">{t('Rate Per Hour')}</p>
                                            <p className="font-medium">{employee.rate_per_hour ? `${getCurrencySymbol()}${employee.rate_per_hour}` : '-'}</p>
                                        </div>
                                    </div>
                                </TabsContent>

                                <TabsContent value="documents" className="space-y-6 mt-6">
                                    {documents?.length > 0 ? (
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {documents.map((doc: any) => (
                                                <Card key={doc.id} className="p-4">
                                                    <div className="flex justify-between items-center">
                                                        <div>
                                                            <p className="font-medium">{doc.document_name}</p>
                                                            <p className="text-sm text-muted-foreground">
                                                                {doc.file_path.split('/').pop()}
                                                            </p>
                                                        </div>
                                                        <a
                                                            href={getImagePath(doc.file_path)}
                                                            target="_blank"
                                                            className="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9"
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                        </a>
                                                    </div>
                                                </Card>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-8 text-muted-foreground">
                                            {t('No documents uploaded.')}
                                        </div>
                                    )}
                                </TabsContent>
                            </Tabs>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}