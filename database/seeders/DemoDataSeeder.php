<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use App\Services\Lead\LeadScoringService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::first();
        if (! $org) {
            return;
        }

        $admin = User::where('email', 'admin@acmecorp.com')->first() ?? User::first();
        $salesManager = User::where('email', 'salesmanager@acmecorp.com')->first() ?? $admin;
        $salesRep = User::where('email', 'salesrep@acmecorp.com')->first() ?? $admin;

        // 1. Seed Real-world Enterprise Companies
        $companiesData = [
            [
                'name' => 'Stark Industries',
                'domain' => 'starkindustries.com',
                'industry' => 'Defense & Energy',
                'phone' => '+1 (555) 234-5678',
                'website' => 'https://starkindustries.com',
                'annual_revenue' => 450000000.00,
                'address_city' => 'New York',
                'address_country' => 'United States',
                'custom_attributes' => [
                    'account_tier' => 'Tier 1 Enterprise',
                    'tech_stack' => ['Kubernetes', 'PostgreSQL', 'Go', 'React'],
                    'employee_count' => 12500,
                    'renewal_quarter' => 'Q4',
                ],
            ],
            [
                'name' => 'Wayne Enterprises',
                'domain' => 'waynecorp.com',
                'industry' => 'Conglomerate & Tech',
                'phone' => '+1 (555) 345-6789',
                'website' => 'https://waynecorp.com',
                'annual_revenue' => 380000000.00,
                'address_city' => 'Gotham',
                'address_country' => 'United States',
                'custom_attributes' => [
                    'account_tier' => 'Tier 1 Enterprise',
                    'tech_stack' => ['AWS', 'Laravel', 'Python', 'Redis'],
                    'employee_count' => 8400,
                    'renewal_quarter' => 'Q2',
                ],
            ],
            [
                'name' => 'Cyberdyne Systems',
                'domain' => 'cyberdyne.ai',
                'industry' => 'Artificial Intelligence & Robotics',
                'phone' => '+1 (555) 456-7890',
                'website' => 'https://cyberdyne.ai',
                'annual_revenue' => 120000000.00,
                'address_city' => 'Sunnyvale',
                'address_country' => 'United States',
                'custom_attributes' => [
                    'account_tier' => 'High Growth',
                    'tech_stack' => ['PyTorch', 'TensorFlow', 'PostgreSQL', 'C++'],
                    'employee_count' => 2100,
                    'renewal_quarter' => 'Q3',
                ],
            ],
            [
                'name' => 'Aperture Science Innovations',
                'domain' => 'aperturescience.com',
                'industry' => 'Quantum Research',
                'phone' => '+1 (555) 567-8901',
                'website' => 'https://aperturescience.com',
                'annual_revenue' => 85000000.00,
                'address_city' => 'Cleveland',
                'address_country' => 'United States',
                'custom_attributes' => [
                    'account_tier' => 'Strategic',
                    'tech_stack' => ['Rust', 'PostgreSQL', 'Docker'],
                    'employee_count' => 950,
                    'renewal_quarter' => 'Q1',
                ],
            ],
            [
                'name' => 'Initech Global Solutions',
                'domain' => 'initech.com',
                'industry' => 'Financial Software',
                'phone' => '+1 (555) 678-9012',
                'website' => 'https://initech.com',
                'annual_revenue' => 45000000.00,
                'address_city' => 'Austin',
                'address_country' => 'United States',
                'custom_attributes' => [
                    'account_tier' => 'Mid-Market',
                    'tech_stack' => ['Java', 'Spring Boot', 'PostgreSQL'],
                    'employee_count' => 450,
                    'renewal_quarter' => 'Q4',
                ],
            ],
        ];

        $createdCompanies = [];
        foreach ($companiesData as $cData) {
            $createdCompanies[] = Company::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $cData['name']],
                array_merge($cData, ['owner_id' => $salesManager->id])
            );
        }

        // 2. Seed Key Enterprise Contacts
        $contactsData = [
            [
                'company_idx' => 0, // Stark
                'first_name' => 'Pepper',
                'last_name' => 'Potts',
                'email' => 'pepper.potts@starkindustries.com',
                'phone' => '+1 (555) 234-1111',
                'job_title' => 'Chief Executive Officer',
                'department' => 'Executive',
                'lifecycle_stage' => 'customer',
            ],
            [
                'company_idx' => 0,
                'first_name' => 'Happy',
                'last_name' => 'Hogan',
                'email' => 'happy.hogan@starkindustries.com',
                'phone' => '+1 (555) 234-2222',
                'job_title' => 'Head of Corporate Security',
                'department' => 'Operations',
                'lifecycle_stage' => 'customer',
            ],
            [
                'company_idx' => 1, // Wayne
                'first_name' => 'Lucius',
                'last_name' => 'Fox',
                'email' => 'lucius.fox@waynecorp.com',
                'phone' => '+1 (555) 345-1111',
                'job_title' => 'Director of Advanced Technologies',
                'department' => 'R&D',
                'lifecycle_stage' => 'opportunity',
            ],
            [
                'company_idx' => 2, // Cyberdyne
                'first_name' => 'Miles',
                'last_name' => 'Dyson',
                'email' => 'miles.dyson@cyberdyne.ai',
                'phone' => '+1 (555) 456-1111',
                'job_title' => 'Lead Neural Network Architect',
                'department' => 'Engineering',
                'lifecycle_stage' => 'lead',
            ],
            [
                'company_idx' => 3, // Aperture
                'first_name' => 'Cave',
                'last_name' => 'Johnson',
                'email' => 'cave.johnson@aperturescience.com',
                'phone' => '+1 (555) 567-1111',
                'job_title' => 'Founder & CEO',
                'department' => 'Executive',
                'lifecycle_stage' => 'customer',
            ],
            [
                'company_idx' => 4, // Initech
                'first_name' => 'Peter',
                'last_name' => 'Gibbons',
                'email' => 'peter.gibbons@initech.com',
                'phone' => '+1 (555) 678-1111',
                'job_title' => 'Senior Systems Consultant',
                'department' => 'IT',
                'lifecycle_stage' => 'opportunity',
            ],
        ];

        $createdContacts = [];
        foreach ($contactsData as $c) {
            $company = $createdCompanies[$c['company_idx']];
            $createdContacts[] = Contact::firstOrCreate(
                ['organization_id' => $org->id, 'email' => $c['email']],
                [
                    'company_id' => $company->id,
                    'assigned_user_id' => $salesRep->id,
                    'first_name' => $c['first_name'],
                    'last_name' => $c['last_name'],
                    'phone' => $c['phone'],
                    'job_title' => $c['job_title'],
                    'department' => $c['department'],
                    'lifecycle_stage' => $c['lifecycle_stage'],
                    'custom_attributes' => ['decision_maker' => true, 'preferred_contact' => 'email'],
                ]
            );
        }

        // 3. Seed Custom Sales Pipelines & Stages
        $pipeline = Pipeline::firstOrCreate(
            ['organization_id' => $org->id, 'is_default' => true],
            ['name' => 'Enterprise SaaS Sales Pipeline']
        );

        $stagesConfig = [
            ['name' => 'Discovery & Qualification', 'win_probability' => 10, 'order_column' => 1, 'color_code' => '#60A5FA'],
            ['name' => 'Technical Demo & Architecture', 'win_probability' => 30, 'order_column' => 2, 'color_code' => '#818CF8'],
            ['name' => 'Security & Compliance Review', 'win_probability' => 50, 'order_column' => 3, 'color_code' => '#C084FC'],
            ['name' => 'Proposal & Commercials', 'win_probability' => 70, 'order_column' => 4, 'color_code' => '#FBBF24'],
            ['name' => 'Legal & Procurement', 'win_probability' => 90, 'order_column' => 5, 'color_code' => '#FB923C'],
            ['name' => 'Closed Won', 'win_probability' => 100, 'order_column' => 6, 'color_code' => '#34D399'],
            ['name' => 'Closed Lost', 'win_probability' => 0, 'order_column' => 7, 'color_code' => '#F87171'],
        ];

        $createdStages = [];
        foreach ($stagesConfig as $s) {
            $createdStages[] = PipelineStage::firstOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $s['name']],
                $s
            );
        }

        // 4. Seed High-Value Deals
        $dealsData = [
            [
                'name' => 'Stark Global Clean Energy Platform',
                'company_idx' => 0,
                'contact_idx' => 0,
                'stage_idx' => 5, // Closed Won
                'amount' => 450000.00,
                'status' => 'won',
                'expected_close_date' => now()->subDays(5)->format('Y-m-d'),
            ],
            [
                'name' => 'Wayne Tech Infrastructure Modernization',
                'company_idx' => 1,
                'contact_idx' => 2,
                'stage_idx' => 3, // Proposal
                'amount' => 280000.00,
                'status' => 'open',
                'expected_close_date' => now()->addDays(20)->format('Y-m-d'),
            ],
            [
                'name' => 'Cyberdyne Neural Cloud Expansion',
                'company_idx' => 2,
                'contact_idx' => 3,
                'stage_idx' => 1, // Demo
                'amount' => 175000.00,
                'status' => 'open',
                'expected_close_date' => now()->addDays(45)->format('Y-m-d'),
            ],
            [
                'name' => 'Aperture Quantum Lab Seat Licenses',
                'company_idx' => 3,
                'contact_idx' => 4,
                'stage_idx' => 5, // Closed Won
                'amount' => 195000.00,
                'status' => 'won',
                'expected_close_date' => now()->subDays(12)->format('Y-m-d'),
            ],
            [
                'name' => 'Initech Legacy Database Migration',
                'company_idx' => 4,
                'contact_idx' => 5,
                'stage_idx' => 4, // Legal & Procurement
                'amount' => 85000.00,
                'status' => 'open',
                'expected_close_date' => now()->addDays(10)->format('Y-m-d'),
            ],
        ];

        $createdDeals = [];
        foreach ($dealsData as $d) {
            $createdDeals[] = Deal::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $d['name']],
                [
                    'pipeline_id' => $pipeline->id,
                    'stage_id' => $createdStages[$d['stage_idx']]->id,
                    'company_id' => $createdCompanies[$d['company_idx']]->id,
                    'contact_id' => $createdContacts[$d['contact_idx']]->id,
                    'assigned_to' => $salesRep->id,
                    'amount' => $d['amount'],
                    'currency' => 'USD',
                    'expected_close_date' => $d['expected_close_date'],
                    'status' => $d['status'],
                    'custom_attributes' => ['deal_source' => 'Enterprise Referral', 'contract_term_months' => 24],
                ]
            );
        }

        // 5. Seed Leads with LeadScoringService
        $scoringService = new LeadScoringService;
        $leadsData = [
            [
                'first_name' => 'Diana',
                'last_name' => 'Prince',
                'company_name' => 'Themyscira Cultural Heritage Corp',
                'email' => 'diana.prince@themyscira.org',
                'phone' => '+1 (555) 789-0123',
                'title' => 'Head of Global Strategic Partnerships',
                'source' => LeadSource::WEBSITE,
                'status' => LeadStatus::QUALIFIED,
                'estimated_value' => 320000.00,
                'notes' => 'Looking for scalable CRM to manage worldwide cultural artifact inventory and partners.',
            ],
            [
                'first_name' => 'Clark',
                'last_name' => 'Kent',
                'company_name' => 'Daily Planet Media Group',
                'email' => 'c.kent@dailyplanet.com',
                'phone' => '+1 (555) 890-1234',
                'title' => 'Senior Investigative Journalist & Tech Editor',
                'source' => LeadSource::LINKEDIN,
                'status' => LeadStatus::CONTACTED,
                'estimated_value' => 65000.00,
                'notes' => 'Evaluating multi-channel newsroom collaboration and real-time news alerts integration.',
            ],
            [
                'first_name' => 'Barry',
                'last_name' => 'Allen',
                'company_name' => 'Central City Forensics Lab',
                'email' => 'barry.allen@ccpd.gov',
                'phone' => '+1 (555) 901-2345',
                'title' => 'Chief Forensic Analyst',
                'source' => LeadSource::REFERRAL,
                'status' => LeadStatus::NEW,
                'estimated_value' => 95000.00,
                'notes' => 'High speed search and PostgreSQL vector indexing required for chain-of-custody tracking.',
            ],
            [
                'first_name' => 'Victor',
                'last_name' => 'Stone',
                'company_name' => 'S.T.A.R. Labs Advanced Cybernetics',
                'email' => 'victor.stone@starlabs.com',
                'phone' => '+1 (555) 012-3456',
                'title' => 'VP of Autonomous Systems',
                'source' => LeadSource::PARTNER,
                'status' => LeadStatus::NURTURING,
                'estimated_value' => 500000.00,
                'notes' => 'Direct API integration with telemetry streams and automated WebSockets.',
            ],
        ];

        foreach ($leadsData as $l) {
            $lead = Lead::create(
                array_merge($l, [
                    'organization_id' => $org->id,
                    'assigned_user_id' => $salesRep->id,
                    'custom_attributes' => ['buying_timeline' => 'Within 3 months', 'budget_approved' => true],
                ])
            );
            $score = $scoringService->calculateScore($lead);
            $lead->update(['score' => $score]);
        }

        // 6. Seed Polymorphic Activities (Calls, Meetings, Tasks, Notes)
        $activitiesData = [
            [
                'subjectable_type' => Company::class,
                'subjectable_id' => $createdCompanies[0]->id, // Stark
                'type' => ActivityType::MEETING,
                'title' => 'Executive Q3 Business Review',
                'description' => 'Met with Pepper Potts and engineering leads to review multi-cloud rollout plan.',
                'due_date' => now()->subDays(2),
                'completed_at' => now()->subDays(2),
                'metadata' => ['outcome' => 'Contract Signed', 'duration_minutes' => 60],
            ],
            [
                'subjectable_type' => Deal::class,
                'subjectable_id' => $createdDeals[1]->id, // Wayne Tech Deal
                'type' => ActivityType::CALL,
                'title' => 'Pricing & Security Architecture Walkthrough',
                'description' => 'Discussed PostgreSQL data isolation and RBAC permission models with Lucius Fox.',
                'due_date' => now()->addDays(3),
                'completed_at' => null,
                'metadata' => ['agenda' => 'SOC2 compliance, database encryption at rest'],
            ],
            [
                'subjectable_type' => Contact::class,
                'subjectable_id' => $createdContacts[3]->id, // Miles Dyson
                'type' => ActivityType::TASK,
                'title' => 'Send Custom PostgreSQL Benchmark Whitepaper',
                'description' => 'Share benchmark metrics comparing tsvector vs elasticsearch for high concurrency.',
                'due_date' => now()->addDays(1),
                'completed_at' => null,
                'metadata' => ['priority' => 'High'],
            ],
            [
                'subjectable_type' => Company::class,
                'subjectable_id' => $createdCompanies[3]->id, // Aperture
                'type' => ActivityType::NOTE,
                'title' => 'Aperture Portal License Expansion',
                'description' => 'Cave Johnson confirmed budget approval for 500 additional seat licenses in Q1.',
                'due_date' => null,
                'completed_at' => now()->subDays(10),
                'metadata' => ['notes_author' => 'Sarah Connor'],
            ],
        ];

        foreach ($activitiesData as $act) {
            Activity::firstOrCreate(
                [
                    'organization_id' => $org->id,
                    'subjectable_type' => $act['subjectable_type'],
                    'subjectable_id' => $act['subjectable_id'],
                    'title' => $act['title'],
                ],
                array_merge($act, ['user_id' => $salesRep->id])
            );
        }

        // 7. Seed Spatie Model Audit Events
        activity()->performedOn($createdCompanies[0])
            ->causedBy($salesManager)
            ->withProperties(['attributes' => ['annual_revenue' => 450000000.00], 'old' => ['annual_revenue' => 400000000.00]])
            ->log('updated');

        activity()->performedOn($createdDeals[0])
            ->causedBy($salesRep)
            ->withProperties(['attributes' => ['status' => 'won'], 'old' => ['status' => 'open']])
            ->log('updated');
    }
}
