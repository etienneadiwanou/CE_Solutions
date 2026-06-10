<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\SmmApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderToProvider implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(private Order $order) {}

    public function handle(SmmApiService $smmApi): void
    {
        $smmApi->sendOrder($this->order);
    }
}
