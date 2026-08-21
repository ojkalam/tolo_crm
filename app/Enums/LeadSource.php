<?php

declare(strict_types=1);

namespace App\Enums;

enum LeadSource: string
{
    case WEBSITE = 'website';
    case REFERRAL = 'referral';
    case COLD_OUTREACH = 'cold_outreach';
    case LINKEDIN = 'linkedin';
    case EVENT = 'event';
    case PARTNER = 'partner';
    case OTHER = 'other';
}
