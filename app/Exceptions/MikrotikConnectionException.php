<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MikrotikConnectionException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to connect to Mikrotik router',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render(): array
    {
        return [
            'error'   => 'MIKROTIK_CONNECTION_ERROR',
            'message' => $this->getMessage(),
        ];
    }
}
