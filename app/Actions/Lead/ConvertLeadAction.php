<?php

declare(strict_types=1);

namespace App\Actions\Lead;

use App\DTOs\ConvertLeadDTO;
use App\Enums\LeadStatus;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConvertLeadAction
{
    /**
     * @return array{company: Company, contact: Contact, deal: ?Deal, lead: Lead}
     */
    public function execute(Lead $lead, ConvertLeadDTO $dto, ?User $actor = null): array
    {
        return DB::transaction(function () use ($lead, $dto, $actor) {
            $orgId = $lead->organization_id;

            // 1. Create or Find Company
            $companyName = $dto->companyData['name'] ?? $lead->company_name ?? ($lead->last_name . ' Household');
            $company = Company::firstOrCreate(
                [
                    'organization_id' => $orgId,
                    'name' => $companyName,
                ],
                array_merge([
                    'owner_id' => $lead->assigned_user_id ?? $actor?->id,
                    'domain' => $dto->companyData['domain'] ?? null,
                    'industry' => $dto->companyData['industry'] ?? null,
                    'annual_revenue' => $dto->companyData['annual_revenue'] ?? $lead->estimated_value,
                    'phone' => $lead->phone,
                    'custom_attributes' => $lead->custom_attributes,
                ], $dto->companyData)
            );

            // 2. Create Contact
            $contact = Contact::create(array_merge([
                'organization_id' => $orgId,
                'company_id' => $company->id,
                'assigned_user_id' => $lead->assigned_user_id ?? $actor?->id,
                'first_name' => $dto->contactData['first_name'] ?? $lead->first_name,
                'last_name' => $dto->contactData['last_name'] ?? $lead->last_name,
                'email' => $dto->contactData['email'] ?? $lead->email,
                'phone' => $dto->contactData['phone'] ?? $lead->phone,
                'job_title' => $dto->contactData['job_title'] ?? $lead->title,
                'lifecycle_stage' => 'prospect',
                'custom_attributes' => array_merge($lead->custom_attributes ?? [], $dto->contactData['custom_attributes'] ?? []),
            ], $dto->contactData));

            // 3. Create Deal if requested
            $deal = null;
            if ($dto->createDeal) {
                $pipelineId = $dto->dealData['pipeline_id'] ?? null;
                $stageId = $dto->dealData['stage_id'] ?? null;

                if (! $pipelineId) {
                    $pipeline = Pipeline::firstOrCreate(
                        ['organization_id' => $orgId, 'is_default' => true],
                        ['name' => 'Standard Sales Pipeline']
                    );
                    $pipelineId = $pipeline->id;
                }

                if (! $stageId) {
                    $firstStage = PipelineStage::where('pipeline_id', $pipelineId)
                        ->orderBy('order_column')
                        ->first();

                    if (! $firstStage) {
                        $firstStage = PipelineStage::create([
                            'pipeline_id' => $pipelineId,
                            'name' => 'Qualification',
                            'win_probability' => 20,
                            'order_column' => 1,
                            'color_code' => '#3B82F6',
                        ]);
                    }
                    $stageId = $firstStage->id;
                }

                $deal = Deal::create(array_merge([
                    'organization_id' => $orgId,
                    'pipeline_id' => $pipelineId,
                    'stage_id' => $stageId,
                    'company_id' => $company->id,
                    'contact_id' => $contact->id,
                    'assigned_to' => $lead->assigned_user_id ?? $actor?->id,
                    'name' => $dto->dealData['name'] ?? ($company->name . ' - Initial Deal'),
                    'amount' => $dto->dealData['amount'] ?? $lead->estimated_value ?? 0,
                    'currency' => $dto->dealData['currency'] ?? 'USD',
                    'expected_close_date' => $dto->dealData['expected_close_date'] ?? now()->addDays(30),
                    'status' => 'open',
                ], $dto->dealData));
            }

            // 4. Update Lead Status to Converted
            $lead->update([
                'status' => LeadStatus::CONVERTED,
                'converted_at' => now(),
            ]);

            return [
                'company' => $company,
                'contact' => $contact,
                'deal' => $deal,
                'lead' => $lead->fresh(['assignedUser']),
            ];
        });
    }
}
