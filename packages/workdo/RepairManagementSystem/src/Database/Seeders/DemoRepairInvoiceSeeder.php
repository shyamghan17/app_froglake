<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use Workdo\RepairManagementSystem\Models\RepairInvoice;
use Workdo\RepairManagementSystem\Models\RepairInvoicePayment;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoRepairInvoiceSeeder extends Seeder
{
    public function run($userId): void
    {
        if (RepairInvoice::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        if (!empty($userId)) {
            // Get completed repair orders (status 4 = End Testing, status 7 = Invoice Created)
            $completedRepairOrders = RepairOrderRequest::where('created_by', $userId)
                ->whereIn('status', [4, 7])
                ->with('repairParts')
                ->orderBy('created_at')
                ->get();

            if ($completedRepairOrders->isEmpty()) {
                return;
            }

            $invoiceData = [
                ['repair_charge' => 150.00, 'status' => '2', 'payment_method' => 'Cash', 'payment_ratio' => 1.0],
                ['repair_charge' => 200.00, 'status' => '1', 'payment_method' => 'Credit Card', 'payment_ratio' => 0.6],
                ['repair_charge' => 175.00, 'status' => '0', 'payment_method' => null, 'payment_ratio' => 0.0],
                ['repair_charge' => 250.00, 'status' => '2', 'payment_method' => 'Bank Transfer', 'payment_ratio' => 1.0],
                ['repair_charge' => 125.00, 'status' => '1', 'payment_method' => 'Cash', 'payment_ratio' => 0.4],
                ['repair_charge' => 300.00, 'status' => '2', 'payment_method' => 'Credit Card', 'payment_ratio' => 1.0],
                ['repair_charge' => 180.00, 'status' => '0', 'payment_method' => null, 'payment_ratio' => 0.0],
                ['repair_charge' => 220.00, 'status' => '1', 'payment_method' => 'Bank Transfer', 'payment_ratio' => 0.7],
                ['repair_charge' => 160.00, 'status' => '2', 'payment_method' => 'Cash', 'payment_ratio' => 1.0],
                ['repair_charge' => 190.00, 'status' => '1', 'payment_method' => 'Credit Card', 'payment_ratio' => 0.5],
                ['repair_charge' => 275.00, 'status' => '2', 'payment_method' => 'Bank Transfer', 'payment_ratio' => 1.0],
                ['repair_charge' => 145.00, 'status' => '0', 'payment_method' => null, 'payment_ratio' => 0.0],
                ['repair_charge' => 210.00, 'status' => '1', 'payment_method' => 'Cash', 'payment_ratio' => 0.8],
                ['repair_charge' => 185.00, 'status' => '2', 'payment_method' => 'Credit Card', 'payment_ratio' => 1.0],
                ['repair_charge' => 165.00, 'status' => '1', 'payment_method' => 'Bank Transfer', 'payment_ratio' => 0.3]
            ];

            $paymentMethods = ['Cash', 'Credit Card', 'Bank Transfer', 'Check', 'Digital Wallet'];

            foreach ($completedRepairOrders as $index => $repairOrder) {
                $invoiceInfo = $invoiceData[$index % count($invoiceData)];
                
                // Calculate total amount using repair order's method
                $totalAmount = $repairOrder->getTotal($invoiceInfo['repair_charge']);
                
                // Generate invoice number
                $invoiceNumber = '#INV' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                // Create invoice 1-2 days after repair completion
                $repairDate = Carbon::parse($repairOrder->date);
                $invoiceDate = $repairDate->copy()->addDays(rand(1, 2));
                
                $invoice = RepairInvoice::create([
                    'invoice_id' => $invoiceNumber,
                    'repair_id' => $repairOrder->id,
                    'repair_charge' => $invoiceInfo['repair_charge'],
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'status' => $invoiceInfo['status'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $invoiceDate->copy()->setTime(rand(0, 23), rand(0, 59), rand(0, 59)),
                ]);

                // Update repair order status to 7 (Invoice Created)
                $repairOrder->update(['status' => 7]);

                // Create payments based on status
                if ($invoiceInfo['status'] !== '0') {
                    $this->createPayments($invoice, $totalAmount, $invoiceInfo, $invoiceDate, $paymentMethods, $userId);
                }
            }
        }
    }

    private function createPayments($invoice, $totalAmount, $invoiceInfo, $invoiceDate, $paymentMethods, $userId)
    {
        $paymentAmount = $totalAmount * $invoiceInfo['payment_ratio'];
        $paymentDate = $invoiceDate->copy()->addDays(rand(0, 3));
        
        if ($invoiceInfo['status'] === '2') {
            // Fully paid - single payment
            RepairInvoicePayment::create([
                'invoice_id' => $invoice->id,
                'repair_id' => $invoice->repair_id,
                'amount' => $totalAmount,
                'payment_date' => $paymentDate->format('Y-m-d'),
                'payment_method' => $invoiceInfo['payment_method'],
                'notes' => 'Full payment received for repair invoice ' . $invoice->invoice_id,
                'creator_id' => $userId,
                'created_by' => $userId,
                'created_at' => $paymentDate->copy()->setTime(rand(0, 23), rand(0, 59), rand(0, 59)),
            ]);
            
            $invoice->update(['paid_amount' => $totalAmount]);
            
        } elseif ($invoiceInfo['status'] === '1') {
            // Partially paid - create partial payment
            RepairInvoicePayment::create([
                'invoice_id' => $invoice->id,
                'repair_id' => $invoice->repair_id,
                'amount' => $paymentAmount,
                'payment_date' => $paymentDate->format('Y-m-d'),
                'payment_method' => $invoiceInfo['payment_method'],
                'notes' => 'Partial payment received for repair invoice ' . $invoice->invoice_id,
                'creator_id' => $userId,
                'created_by' => $userId,
                'created_at' => $paymentDate->copy()->setTime(rand(0, 23), rand(0, 59), rand(0, 59)),
            ]);
            
            $invoice->update(['paid_amount' => $paymentAmount]);
        }
    }
}