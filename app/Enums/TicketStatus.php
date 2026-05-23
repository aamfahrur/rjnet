<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_CUSTOMER = 'waiting_customer';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN             => 'Open',
            self::IN_PROGRESS      => 'In Progress',
            self::WAITING_CUSTOMER => 'Waiting Customer',
            self::RESOLVED         => 'Resolved',
            self::CLOSED           => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN             => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::IN_PROGRESS      => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::WAITING_CUSTOMER => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            self::RESOLVED         => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::CLOSED           => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }
}
