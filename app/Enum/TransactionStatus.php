<?php

namespace App\Enum;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    public static function completedStatuses(): array
    {
        return [
            self::SUCCESS,
            self::FAILED,
        ];
    }
}
