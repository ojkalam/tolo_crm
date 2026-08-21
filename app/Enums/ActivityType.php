<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityType: string
{
    case CALL = 'call';
    case MEETING = 'meeting';
    case TASK = 'task';
    case NOTE = 'note';
    case EMAIL = 'email';
}
