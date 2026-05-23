<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case VIRTUAL_ACCOUNT = 'virtual_account';
    case EWALLET = 'ewallet';
    case CONVENIENCE_STORE = 'convenience_store';
    case QRIS = 'qris';
    case CREDIT_CARD = 'credit_card';
    case CASH = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER     => 'Transfer Bank',
            self::VIRTUAL_ACCOUNT   => 'Virtual Account',
            self::EWALLET           => 'E-Wallet',
            self::CONVENIENCE_STORE => 'Minimarket',
            self::QRIS              => 'QRIS',
            self::CREDIT_CARD       => 'Kartu Kredit',
            self::CASH              => 'Tunai',
        };
    }
}
