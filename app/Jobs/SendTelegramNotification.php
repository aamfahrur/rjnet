<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendTelegramNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public readonly string $chatId,
        public readonly string $message,
    ) {
    }

    public function handle(): void
    {
        $botToken = config('services.telegram.bot_token');
        if (!$botToken) {
            \Illuminate\Support\Facades\Log::warning('Telegram bot token not configured');
            return;
        }

        $response = Http::timeout(10)
            ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $this->chatId,
                'text'       => $this->message,
                'parse_mode' => 'Markdown',
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException("Telegram API error: {$response->body()}");
        }
    }
}
