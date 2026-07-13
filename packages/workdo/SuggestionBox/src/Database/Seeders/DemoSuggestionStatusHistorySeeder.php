<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use Workdo\SuggestionBox\Models\SuggestionStatusHistory;
use Workdo\SuggestionBox\Models\Suggestion;
use Illuminate\Database\Seeder;

class DemoSuggestionStatusHistorySeeder extends Seeder
{
    public function run($userId): void
    {
        if (SuggestionStatusHistory::where('created_by', $userId)->exists()) {
            return;
        }

        $suggestions = Suggestion::where('created_by', $userId)
            ->whereNotNull('admin_response')
            ->get();

        if ($suggestions->isEmpty()) {
            return;
        }

        $statusHistories = [
            [
                'suggestion_title' => 'Implement Remote Work Policy',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'Initial review started by HR team to assess feasibility and policy requirements.',
            ],
            [
                'suggestion_title' => 'Implement Remote Work Policy',
                'old_status'       => 'under_review',
                'new_status'       => 'accepted',
                'comment'          => 'After thorough review with management, this suggestion has been approved for implementation.',
            ],
            [
                'suggestion_title' => 'Implement Remote Work Policy',
                'old_status'       => 'accepted',
                'new_status'       => 'complete',
                'comment'          => 'Remote work policy has been fully implemented and rolled out company-wide.',
            ],
            [
                'suggestion_title' => 'Upgrade Development Tools',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'IT department is evaluating costs and timeline for development tool upgrades.',
            ],
            [
                'suggestion_title' => 'Customer Feedback Dashboard',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'Product team reviewing technical requirements and implementation approach.',
            ],
            [
                'suggestion_title' => 'Customer Feedback Dashboard',
                'old_status'       => 'under_review',
                'new_status'       => 'accepted',
                'comment'          => 'Development team has capacity to implement this in the next sprint. Moving to approved status.',
            ],
            [
                'suggestion_title' => 'Green Energy Initiative',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'Facilities team consulting with environmental specialists for feasibility assessment.',
            ],
            [
                'suggestion_title' => 'Two-Factor Authentication',
                'old_status'       => 'new',
                'new_status'       => 'accepted',
                'comment'          => 'Critical security improvement. Fast-tracked for immediate implementation.',
            ],
            [
                'suggestion_title' => 'Mobile App for Employees',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'Evaluating budget requirements and resource allocation for mobile app development.',
            ],
            [
                'suggestion_title' => 'Mobile App for Employees',
                'old_status'       => 'under_review',
                'new_status'       => 'rejected',
                'comment'          => 'Budget constraints and resource limitations prevent implementation at this time.',
            ],
            [
                'suggestion_title' => 'Quality Assurance Automation',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'QA team researching automation tools and frameworks suitable for our tech stack.',
            ],
            [
                'suggestion_title' => 'Flexible Working Hours',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'HR reviewing current attendance policies and operational impact assessment.',
            ],
            [
                'suggestion_title' => 'Flexible Working Hours',
                'old_status'       => 'under_review',
                'new_status'       => 'accepted',
                'comment'          => 'Policy approved with core hours requirement. Implementation starting next month.',
            ],
            [
                'suggestion_title' => 'Flexible Working Hours',
                'old_status'       => 'accepted',
                'new_status'       => 'complete',
                'comment'          => 'Flexible working hours policy has been successfully implemented and employees are using the new system.',
            ],
            [
                'suggestion_title' => 'Employee Training Portal',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'HR and IT teams evaluating existing LMS solutions and budget requirements.',
            ],
            [
                'suggestion_title' => 'Social Media Marketing Boost',
                'old_status'       => 'new',
                'new_status'       => 'accepted',
                'comment'          => 'Marketing team has allocated budget for enhanced social media presence.',
            ],
            [
                'suggestion_title' => 'Social Media Marketing Boost',
                'old_status'       => 'accepted',
                'new_status'       => 'complete',
                'comment'          => 'Social media campaigns launched successfully with increased engagement and reach.',
            ],
            [
                'suggestion_title' => 'Customer Support Chatbot',
                'old_status'       => 'new',
                'new_status'       => 'under_review',
                'comment'          => 'Customer service team researching AI chatbot solutions and integration requirements.',
            ],
        ];

        foreach ($statusHistories as $historyData) {
            $suggestion = $suggestions->where('title', $historyData['suggestion_title'])->first();

            if ($suggestion) {
                SuggestionStatusHistory::create([
                    'old_status'    => $historyData['old_status'],
                    'new_status'    => $historyData['new_status'],
                    'comment'       => $historyData['comment'],
                    'suggestion_id' => $suggestion->id,
                    'changed_by'    => $userId,
                    'creator_id'    => $userId,
                    'created_by'    => $userId,
                    'created_at'    => now()->subDays(rand(1, 45)),
                    'updated_at'    => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
