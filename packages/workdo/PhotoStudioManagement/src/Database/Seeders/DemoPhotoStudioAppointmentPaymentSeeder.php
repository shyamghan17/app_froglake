<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointmentPayment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;

class DemoPhotoStudioAppointmentPaymentSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioAppointmentPayment::where('created_by', $userId)->exists()) {
            return;
        }

        $appointments = PhotoStudioAppointment::where('created_by', $userId)->get();

        if ($appointments->count() < 4) {
            return;
        }

        $selected = $appointments->count() >= 8 ? $appointments->random(8) : $appointments;

        foreach ($selected as $index => $appointment) {
            $paymentDate   = $index < 5 ? now()->subDays(rand(1, 10)) : now()->addDays(rand(1, 5));
            $paymentStatus = $index < 5 ? 'cleared' : 'pending';

            $paymentTypes = ['offline', 'stripe', 'paypal'];

            PhotoStudioAppointmentPayment::create([
                'appointment_id'     => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'customer_name'      => $appointment->name,
                'service_name'       => $appointment->service->name ?? '-',
                'payment_date'       => $paymentDate,
                'amount'             => $appointment->price,
                'payment_status'     => $paymentStatus,
                'payment_type'       => $paymentTypes[$index % count($paymentTypes)],
                'description'        => 'Payment for appointment ' . $appointment->appointment_number,
                'creator_id'         => $userId,
                'created_by'         => $userId,
            ]);

            if ($paymentStatus === 'cleared') {
                $appointment->update(['payment_status' => 'confirmed']);
            }
        }
    }
}
