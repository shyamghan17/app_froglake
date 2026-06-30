import { useEffect } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClinicalForm, {
    createEyeTestPrescriptionFormData,
} from './ClinicalForm';
import {
    EditEyeTestPrescriptionFormData,
    EditEyeTestPrescriptionProps,
    EyeTestPrescriptionsIndexProps,
} from './types';

export default function EditEyeTestPrescription({ eyetestprescription, onSuccess }: EditEyeTestPrescriptionProps) {
    const { eyepatients, opticaldoctors } = usePage<EyeTestPrescriptionsIndexProps>().props;
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm<EditEyeTestPrescriptionFormData>(
        createEyeTestPrescriptionFormData(eyetestprescription),
    );

    useEffect(() => {
        if (data.patient_id) {
            const patient = eyepatients.find((item) => item.id.toString() === data.patient_id.toString());
            if (patient?.preferred_doctor && !eyetestprescription.doctor_name) {
                setData((previousData) => ({
                    ...previousData,
                    doctor_name: patient.preferred_doctor!.toString(),
                }));
            }
        }
    }, [data.patient_id, eyepatients, eyetestprescription.doctor_name, setData]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        put(route('optical-and-eye-care-center.eye-test-prescriptions.update', eyetestprescription.id), {
            onSuccess: () => {
                onSuccess();
            },
        });
    };

    return (
        <ClinicalForm
            title={t('Edit Eye Test Prescription')}
            submitLabel={t('Update')}
            submittingLabel={t('Updating...')}
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
