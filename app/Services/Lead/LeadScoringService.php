<?php

declare(strict_types=1);

namespace App\Services\Lead;

use App\Models\Lead;
use Illuminate\Support\Str;

class LeadScoringService
{
    /**
     * @var list<string>
     */
    protected array $freeEmailProviders = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com', 'mail.com',
    ];

    public function calculateScore(Lead $lead): int
    {
        $score = 0;

        // 1. Corporate Email vs Free Provider
        if (! empty($lead->email)) {
            $domain = Str::after($lead->email, '@');
            if (! in_array(strtolower($domain), $this->freeEmailProviders, true)) {
                $score += 25;
            } else {
                $score += 10;
            }
        }

        // 2. Phone Provided
        if (! empty($lead->phone)) {
            $score += 15;
        }

        // 3. Company Provided
        if (! empty($lead->company_name)) {
            $score += 15;
        }

        // 4. Decision Maker Job Title
        if (! empty($lead->title)) {
            $titleLower = strtolower($lead->title);
            if (Str::contains($titleLower, ['ceo', 'cto', 'cfo', 'coo', 'vp', 'vice president', 'director', 'head', 'founder', 'owner'])) {
                $score += 25;
            } elseif (Str::contains($titleLower, ['manager', 'lead', 'principal', 'specialist'])) {
                $score += 15;
            } else {
                $score += 5;
            }
        }

        // 5. Estimated Deal Value
        if ($lead->estimated_value && (float) $lead->estimated_value >= 10000) {
            $score += 20;
        } elseif ($lead->estimated_value && (float) $lead->estimated_value > 0) {
            $score += 10;
        }

        return min(100, $score);
    }
}
