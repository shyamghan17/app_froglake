<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use Workdo\SuggestionBox\Models\Suggestion;
use Illuminate\Database\Seeder;
use Workdo\SuggestionBox\Models\SuggestionCategory;
use App\Models\User;

class DemoSuggestionSeeder extends Seeder
{
    public function run($userId): void
    {
        if (Suggestion::where('created_by', $userId)->exists()) {
            return;
        }

        if (!empty($userId)) {
            $categories = SuggestionCategory::where('created_by', $userId)
                ->where('is_active', true)
                ->orderBy('display_order')->get();

            $users = User::where('created_by', $userId)->emp()->get();
            $companyUser = User::find($userId);
            
            if ($companyUser && !$users->contains('id', $companyUser->id)) {
                $users = $users->push($companyUser);
            }

            if ($categories->isEmpty() || $users->isEmpty()) {
                return;
            }

            $suggestions = [
                [
                    'title'          => 'Implement Remote Work Policy',
                    'description'    => 'With the changing work environment, I suggest implementing a comprehensive remote work policy that allows employees to work from home 2-3 days per week. This could improve work-life balance and reduce commute stress.',
                    'category_name'  => 'HR',
                    'is_anonymous'   => false,
                    'status'         => 'complete',
                    'admin_response' => 'Great suggestion! We have approved this and will be rolling out the remote work policy starting next quarter. Thank you for your input.',
                    'votes_count'    => 15,
                    'views_count'    => 38,
                ],
                [
                    'title'          => 'Upgrade Development Tools',
                    'description'    => 'Our current development environment is outdated. I suggest upgrading to newer IDEs, implementing Docker containers, and setting up CI/CD pipelines to improve development efficiency and code quality.',
                    'category_name'  => 'Technology',
                    'is_anonymous'   => false,
                    'status'         => 'under_review',
                    'admin_response' => 'We are currently evaluating the costs and timeline for this upgrade. Will update soon with our decision.',
                    'votes_count'    => 12,
                    'views_count'    => 29,
                ],
                [
                    'title'          => 'Monthly Team Building Activities',
                    'description'    => 'To improve team morale and collaboration, I suggest organizing monthly team building activities like game nights, outdoor activities, or skill-sharing sessions.',
                    'category_name'  => 'HR',
                    'is_anonymous'   => true,
                    'status'         => 'new',
                    'admin_response' => null,
                    'votes_count'    => 8,
                    'views_count'    => 25,
                ],
                [
                    'title'          => 'Customer Feedback Dashboard',
                    'description'    => 'Create a real-time dashboard to track customer feedback, complaints, and satisfaction scores. This will help us respond faster to customer issues and improve our service quality.',
                    'category_name'  => 'Customer Service',
                    'is_anonymous'   => false,
                    'status'         => 'accepted',
                    'admin_response' => 'Excellent idea! The development team will start working on this dashboard next sprint.',
                    'votes_count'    => 13,
                    'views_count'    => 35,
                ],
                [
                    'title'          => 'Green Energy Initiative',
                    'description'    => 'Switch to renewable energy sources for our office buildings. Install solar panels and energy-efficient equipment to reduce our carbon footprint and operational costs.',
                    'category_name'  => 'General',
                    'is_anonymous'   => false,
                    'status'         => 'under_review',
                    'admin_response' => 'We are consulting with environmental specialists to assess the feasibility and costs of this initiative.',
                    'votes_count'    => 7,
                    'views_count'    => 22,
                ],
                [
                    'title'          => 'Two-Factor Authentication',
                    'description'    => 'Implement mandatory 2FA for all employee accounts to enhance security. Also consider security awareness training sessions to educate staff about cybersecurity threats.',
                    'category_name'  => 'Security',
                    'is_anonymous'   => true,
                    'status'         => 'accepted',
                    'admin_response' => 'Security is our top priority. 2FA implementation is already in progress and will be mandatory by next month.',
                    'votes_count'    => 14,
                    'views_count'    => 40,
                ],
                [
                    'title'          => 'Automated Expense Reporting',
                    'description'    => 'Replace the manual expense reporting system with an automated solution that integrates with our accounting software. This will reduce processing time and minimize errors.',
                    'category_name'  => 'Finance',
                    'is_anonymous'   => false,
                    'status'         => 'new',
                    'admin_response' => null,
                    'votes_count'    => 6,
                    'views_count'    => 21,
                ],
                [
                    'title'          => 'Mobile App for Employees',
                    'description'    => 'Develop a mobile application for employees to access company resources, check schedules, submit requests, and communicate with colleagues on the go.',
                    'category_name'  => 'Technology',
                    'is_anonymous'   => false,
                    'status'         => 'rejected',
                    'admin_response' => 'While this is a good idea, we currently lack the resources and budget for mobile app development. We will reconsider this in the future.',
                    'votes_count'    => 5,
                    'views_count'    => 28,
                ],
                [
                    'title'          => 'Quality Assurance Automation',
                    'description'    => 'Implement automated testing tools and continuous integration to improve our quality assurance process. This will help catch bugs earlier and ensure consistent product quality.',
                    'category_name'  => 'Quality',
                    'is_anonymous'   => false,
                    'status'         => 'under_review',
                    'admin_response' => 'The QA team is evaluating different automation tools. We expect to make a decision within the next two weeks.',
                    'votes_count'    => 11,
                    'views_count'    => 33,
                ],
                [
                    'title'          => 'Flexible Working Hours',
                    'description'    => 'Allow flexible working hours within core business hours (10 AM - 4 PM) to help employees manage their personal commitments better while maintaining productivity.',
                    'category_name'  => 'HR',
                    'is_anonymous'   => true,
                    'status'         => 'complete',
                    'admin_response' => 'We have approved flexible working hours policy. Employees can now choose their start time between 8 AM - 10 AM.',
                    'votes_count'    => 15,
                    'views_count'    => 39,
                ],
                [
                    'title'          => 'Innovation Lab Setup',
                    'description'    => 'Create a dedicated space for innovation and experimentation where employees can work on creative projects, prototype new ideas, and collaborate on innovative solutions.',
                    'category_name'  => 'Innovation',
                    'is_anonymous'   => false,
                    'status'         => 'new',
                    'admin_response' => null,
                    'votes_count'    => 9,
                    'views_count'    => 26,
                ],
                [
                    'title'          => 'Employee Training Portal',
                    'description'    => 'Develop an online training portal with courses on professional development, technical skills, and compliance training. Include progress tracking and certification features.',
                    'category_name'  => 'Training',
                    'is_anonymous'   => false,
                    'status'         => 'under_review',
                    'admin_response' => 'HR is working with the IT team to evaluate existing learning management systems for this purpose.',
                    'votes_count'    => 10,
                    'views_count'    => 32,
                ],
                [
                    'title'          => 'Social Media Marketing Boost',
                    'description'    => 'Increase our social media presence by creating engaging content, running targeted campaigns, and collaborating with influencers in our industry.',
                    'category_name'  => 'Marketing',
                    'is_anonymous'   => true,
                    'status'         => 'complete',
                    'admin_response' => 'Marketing team has allocated budget for enhanced social media campaigns starting this quarter.',
                    'votes_count'    => 8,
                    'views_count'    => 24,
                ],
                [
                    'title'          => 'Paperless Office Initiative',
                    'description'    => 'Transition to a completely paperless office by digitizing all documents, implementing electronic signatures, and using cloud-based document management systems.',
                    'category_name'  => 'Operation',
                    'is_anonymous'   => false,
                    'status'         => 'new',
                    'admin_response' => null,
                    'votes_count'    => 6,
                    'views_count'    => 20,
                ],
                [
                    'title'          => 'Customer Support Chatbot',
                    'description'    => 'Implement an AI-powered chatbot for initial customer support to handle common queries 24/7, reducing response time and workload on human support staff.',
                    'category_name'  => 'Customer Service',
                    'is_anonymous'   => false,
                    'status'         => 'under_review',
                    'admin_response' => 'We are researching chatbot solutions and will pilot test with a few selected customers first.',
                    'votes_count'    => 12,
                    'views_count'    => 37,
                ],
            ];

            // First create 3 suggestions from the company user
            if ($companyUser) {
                $companySuggestions = [
                    [
                        'title'          => 'Digital Transformation Strategy',
                        'description'    => 'We need a comprehensive digital transformation strategy to modernize our business processes, improve customer experience, and stay competitive in the digital age. This includes adopting cloud technologies, AI integration, and data-driven decision making.',
                        'category_name'  => 'Technology',
                        'is_anonymous'   => false,
                        'status'         => 'new',
                        'admin_response' => null,
                        'votes_count'    => 12,
                        'views_count'    => 35,
                    ],
                    [
                        'title'          => 'Employee Wellness Program Enhancement',
                        'description'    => 'I propose expanding our employee wellness program to include mental health support, fitness subsidies, healthy meal options, and stress management workshops. A healthy workforce is more productive and engaged.',
                        'category_name'  => 'HR',
                        'is_anonymous'   => false,
                        'status'         => 'under_review',
                        'admin_response' => 'This is a valuable suggestion. HR team is preparing a comprehensive proposal for management review.',
                        'votes_count'    => 15,
                        'views_count'    => 42,
                    ],
                    [
                        'title'          => 'Sustainable Business Practices Initiative',
                        'description'    => 'Implement sustainable business practices including renewable energy adoption, waste reduction programs, eco-friendly packaging, and carbon footprint reduction. This will improve our corporate image and reduce operational costs.',
                        'category_name'  => 'General',
                        'is_anonymous'   => false,
                        'status'         => 'accepted',
                        'admin_response' => 'Excellent strategic thinking! We will form a sustainability committee to implement these initiatives company-wide.',
                        'votes_count'    => 18,
                        'views_count'    => 48,
                    ],
                ];

                foreach ($companySuggestions as $suggestionData) {
                    $category    = $categories->where('name', $suggestionData['category_name'])->first();
                    $respondedBy = $suggestionData['admin_response'] ? $users->random() : null;

                    if ($category) {
                        Suggestion::create([
                            'title'          => $suggestionData['title'],
                            'description'    => $suggestionData['description'],
                            'category_id'    => $category->id,
                            'is_anonymous'   => $suggestionData['is_anonymous'],
                            'status'         => $suggestionData['status'],
                            'admin_response' => $suggestionData['admin_response'],
                            'votes_count'    => $suggestionData['votes_count'],
                            'views_count'    => $suggestionData['views_count'],
                            'responded_at'   => $suggestionData['admin_response'] ? now()->subDays(rand(1, 15))->format('Y-m-d H:i:s') : null,
                            'user_id'        => $companyUser->id,                                                                                
                            'responded_by'   => $respondedBy?->id,
                            'creator_id'     => $userId,
                            'created_by'     => $userId,
                            'created_at'     => now()->subDays(rand(1, 30)),
                            'updated_at'     => now()->subDays(rand(1, 15)),
                        ]);
                    }
                }
            }

            foreach ($suggestions as $suggestionData) {
                $category    = $categories->where('name', $suggestionData['category_name'])->first();
                $user        = $users->random();
                $respondedBy = $suggestionData['admin_response'] ? $users->random() : null;

                if ($category && $user) {
                    Suggestion::create([
                        'title'          => $suggestionData['title'],
                        'description'    => $suggestionData['description'],
                        'category_id'    => $category->id,
                        'is_anonymous'   => $suggestionData['is_anonymous'],
                        'status'         => $suggestionData['status'],
                        'admin_response' => $suggestionData['admin_response'],
                        'votes_count'    => $suggestionData['votes_count'],
                        'views_count'    => $suggestionData['views_count'],
                        'responded_at'   => now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                        'user_id'        => $user->id,
                        'responded_by'   => $respondedBy?->id,
                        'creator_id'     => $userId,
                        'created_by'     => $userId,
                        'created_at'     => now()->subDays(rand(1, 60)),
                        'updated_at'     => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }
}
