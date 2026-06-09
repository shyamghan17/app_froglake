<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use Workdo\RepairManagementSystem\Models\RepairWarranty;
use Workdo\RepairManagementSystem\Models\RepairPart;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoRepairWarrantySeeder extends Seeder
{
    public function run($userId): void
    {
        if (RepairWarranty::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        if (!empty($userId)) {
            // Get repair parts from completed repair orders (status 4 or 7 only)
            $repairParts = RepairPart::whereHas('repairOrderRequest', function($query) use ($userId) {
                $query->where('created_by', $userId)
                      ->whereIn('status', [4, 7]); // Only End Testing (4) or Invoice Created (7)
            })->where('created_by', $userId)
              ->with('repairOrderRequest')
              ->orderBy('created_at')
              ->get();

            if ($repairParts->isEmpty()) {
                return;
            }

            $warranties = [
                ['warranty_number' => 'WRN-001', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Standard 12-month warranty covering workmanship and part quality with manufacturing defects protection.'],
                ['warranty_number' => 'WRN-002', 'warranty_months' => 6, 'claim_status' => '1', 'warranty_terms' => 'Limited 6-month warranty includes free replacement of defective parts and labor costs coverage.'],
                ['warranty_number' => 'WRN-003', 'warranty_months' => 18, 'claim_status' => '0', 'warranty_terms' => 'Extended 18-month warranty with comprehensive coverage for authorized repairs and genuine components.'],
                ['warranty_number' => 'WRN-004', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Comprehensive 12-month warranty covering all repair work performed with quality assurance guarantee.'],
                ['warranty_number' => 'WRN-005', 'warranty_months' => 24, 'claim_status' => '2', 'warranty_terms' => 'Premium 24-month extended warranty with full coverage and priority service support.'],
                ['warranty_number' => 'WRN-006', 'warranty_months' => 6, 'claim_status' => '0', 'warranty_terms' => 'Basic 6-month warranty for repair services covering workmanship issues under normal usage.'],
                ['warranty_number' => 'WRN-007', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Standard warranty covering repair quality and component reliability excluding physical damage.'],
                ['warranty_number' => 'WRN-008', 'warranty_months' => 9, 'claim_status' => '3', 'warranty_terms' => 'Professional 9-month warranty with full coverage including diagnostic and parts replacement.'],
                ['warranty_number' => 'WRN-009', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Complete warranty package covering manufacturing defects and workmanship with quality guarantee.'],
                ['warranty_number' => 'WRN-010', 'warranty_months' => 6, 'claim_status' => '1', 'warranty_terms' => 'Limited warranty for repair services with manufacturing defects coverage and labor protection.'],
                ['warranty_number' => 'WRN-011', 'warranty_months' => 18, 'claim_status' => '0', 'warranty_terms' => 'Professional warranty with full service coverage for authorized repairs and genuine components.'],
                ['warranty_number' => 'WRN-012', 'warranty_months' => 12, 'claim_status' => '2', 'warranty_terms' => 'Standard 12-month warranty covering workmanship and part quality with comprehensive protection.'],
                ['warranty_number' => 'WRN-013', 'warranty_months' => 24, 'claim_status' => '0', 'warranty_terms' => 'Extended premium warranty with comprehensive protection and complete service coverage guarantee.'],
                ['warranty_number' => 'WRN-014', 'warranty_months' => 6, 'claim_status' => '0', 'warranty_terms' => 'Basic warranty covering repair services with workmanship issues under normal usage conditions.'],
                ['warranty_number' => 'WRN-015', 'warranty_months' => 12, 'claim_status' => '1', 'warranty_terms' => 'Comprehensive warranty including diagnostic repair and part replacement services with quality assurance.'],
                ['warranty_number' => 'WRN-016', 'warranty_months' => 18, 'claim_status' => '0', 'warranty_terms' => 'Extended warranty with comprehensive coverage for authorized repairs and priority service support.'],
                ['warranty_number' => 'WRN-017', 'warranty_months' => 12, 'claim_status' => '2', 'warranty_terms' => 'Standard warranty covering manufacturing defects and workmanship under normal usage conditions.'],
                ['warranty_number' => 'WRN-018', 'warranty_months' => 6, 'claim_status' => '0', 'warranty_terms' => 'Limited warranty for repair services covering workmanship issues with original receipt validation.'],
                ['warranty_number' => 'WRN-019', 'warranty_months' => 24, 'claim_status' => '1', 'warranty_terms' => 'Premium extended warranty with comprehensive protection and complete service coverage support.'],
                ['warranty_number' => 'WRN-020', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Complete warranty package covering workmanship and part quality with manufacturing defects protection.'],
                ['warranty_number' => 'WRN-021', 'warranty_months' => 9, 'claim_status' => '3', 'warranty_terms' => 'Professional warranty with full coverage including diagnostic and parts replacement protection.'],
                ['warranty_number' => 'WRN-022', 'warranty_months' => 18, 'claim_status' => '0', 'warranty_terms' => 'Extended warranty with full service coverage and genuine parts guarantee for authorized repairs.'],
                ['warranty_number' => 'WRN-023', 'warranty_months' => 12, 'claim_status' => '2', 'warranty_terms' => 'Standard warranty covering repair quality and component reliability excluding physical damage.'],
                ['warranty_number' => 'WRN-024', 'warranty_months' => 6, 'claim_status' => '0', 'warranty_terms' => 'Basic warranty for repair services with manufacturing defects coverage under normal usage.'],
                ['warranty_number' => 'WRN-025', 'warranty_months' => 24, 'claim_status' => '1', 'warranty_terms' => 'Premium warranty with full coverage including complete service guarantee and priority support.'],
                ['warranty_number' => 'WRN-026', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Comprehensive warranty covering all repair work and component quality with original receipt validation.'],
                ['warranty_number' => 'WRN-027', 'warranty_months' => 18, 'claim_status' => '3', 'warranty_terms' => 'Professional warranty with extended coverage for repair services and parts replacement protection.'],
                ['warranty_number' => 'WRN-028', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Standard warranty covering workmanship and part quality excluding external damage factors.'],
                ['warranty_number' => 'WRN-029', 'warranty_months' => 6, 'claim_status' => '2', 'warranty_terms' => 'Limited warranty for repair services with manufacturing defects coverage and labor protection.'],
                ['warranty_number' => 'WRN-030', 'warranty_months' => 24, 'claim_status' => '0', 'warranty_terms' => 'Extended premium warranty with comprehensive protection including priority service and complete coverage.'],
                ['warranty_number' => 'WRN-031', 'warranty_months' => 12, 'claim_status' => '1', 'warranty_terms' => 'Standard warranty with full coverage for repair work and component reliability protection.'],
                ['warranty_number' => 'WRN-032', 'warranty_months' => 9, 'claim_status' => '0', 'warranty_terms' => 'Professional warranty covering diagnostic services and parts replacement with quality assurance.'],
                ['warranty_number' => 'WRN-033', 'warranty_months' => 18, 'claim_status' => '2', 'warranty_terms' => 'Extended warranty with comprehensive coverage including authorized repairs and genuine components.'],
                ['warranty_number' => 'WRN-034', 'warranty_months' => 12, 'claim_status' => '0', 'warranty_terms' => 'Complete warranty package covering manufacturing defects and workmanship with original receipt.'],
                ['warranty_number' => 'WRN-035', 'warranty_months' => 6, 'claim_status' => '3', 'warranty_terms' => 'Basic warranty for repair services covering workmanship issues and manufacturing defects.']
            ];

            foreach ($repairParts as $index => $repairPart) {
                if ($index >= count($warranties)) break;

                $repairOrder = $repairPart->repairOrderRequest;
                $warrantyData = $warranties[$index];
                
                // Calculate warranty dates based on repair completion
                $repairDate = Carbon::parse($repairOrder->date);
                $warrantyStartDate = $repairDate->copy()->addDays(rand(1, 3));
                $warrantyEndDate = $warrantyStartDate->copy()->addMonths($warrantyData['warranty_months']);
                
                RepairWarranty::create([
                    'warranty_number' => $warrantyData['warranty_number'],
                    'warranty_period' => $warrantyStartDate->format('Y-m-d') . ' - ' . $warrantyEndDate->format('Y-m-d'),
                    'warranty_terms' => $warrantyData['warranty_terms'],
                    'claim_status' => $warrantyData['claim_status'],
                    'repair_order_id' => $repairOrder->id,
                    'part_id' => $repairPart->id,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);
            }
        }
    }
}