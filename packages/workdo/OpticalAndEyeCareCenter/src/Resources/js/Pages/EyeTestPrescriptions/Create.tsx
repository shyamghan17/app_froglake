import { useEffect } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClinicalForm, {
    createEyeTestPrescriptionFormData,
} from './ClinicalForm';
import {
    CreateEyeTestPrescriptionProps,
    CreateEyeTestPrescriptionFormData,
    EyeTestPrescriptionsIndexProps,
} from './types';

export default function Create({ onSuccess }: CreateEyeTestPrescriptionProps) {
    const { eyepatients, opticaldoctors } = usePage<EyeTestPrescriptionsIndexProps>().props;
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm<CreateEyeTestPrescriptionFormData>(
        createEyeTestPrescriptionFormData(),
    );

    useEffect(() => {
        if (data.patient_id) {
            const patient = eyepatients.find((item) => item.id.toString() === data.patient_id.toString());
            if (patient?.preferred_doctor) {
                setData((previousData) => ({
                    ...previousData,
                    doctor_name: patient.preferred_doctor!.toString(),
                }));
            }
        }
    }, [data.patient_id, eyepatients, setData]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        post(route('optical-and-eye-care-center.eye-test-prescriptions.store'), {
            onSuccess: () => {
                onSuccess();
            },
        });
    };

    return (
        <ClinicalForm
            title={t('Create Eye Test Prescription')}
            submitLabel={t('Create')}
            submittingLabel={t('Creating...')}
            data={data}
            setData={setData}
            errors={errors}
            processing={processing}
            eyepatients={eyepatients}
            opticaldoctors={opticaldoctors.filter((doctor) => doctor.name)}
            onCancel={onSuccess}
            onSubmit={submit}
        />
    );
}
