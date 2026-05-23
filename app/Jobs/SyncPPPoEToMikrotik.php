<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\PPPoEAccount;
use App\Services\Mikrotik\MikrotikServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPPPoEToMikrotik implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 30;

    public function __construct(
        public readonly PPPoEAccount $account,
        public readonly string $action, // add, update, delete
    ) {
    }

    public function handle(MikrotikServiceFactory $factory): void
    {
        $pppoe = $factory->pppoe($this->account->router);

        match ($this->action) {
            'add'     => $pppoe->addSecret($this->account),
            'update'  => $pppoe->updateSecret($this->account),
            'delete'  => $pppoe->deleteSecret($this->account->username),
            'enable'  => $pppoe->enableSecret($this->account->username),
            'disable' => $pppoe->disableSecret($this->account->username),
            default   => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }
}
