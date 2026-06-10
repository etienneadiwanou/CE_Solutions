<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\SmmApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncOrderStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SmmApiService $smmApi, OrderService $orderService): void
    {
        // Toutes les commandes actives en attente de mise à jour
        Order::whereIn('status', ['processing', 'in_progress'])
            ->whereNotNull('provider_order_id')
            ->with(['provider', 'service'])
            ->chunk(50, function ($orders) use ($smmApi, $orderService) {
                foreach ($orders as $order) {
                    $data = $smmApi->checkStatus($order);
                    if (empty($data)) continue;

                    $this->updateOrderFromProviderData($order, $data, $orderService);
                }
            });
    }

    private function updateOrderFromProviderData(
        Order $order,
        array $data,
        OrderService $orderService
    ): void {
        $statusMap = [
            'Completed' => Order::STATUS_COMPLETED,
            'In progress' => Order::STATUS_IN_PROGRESS,
            'Processing' => Order::STATUS_PROCESSING,
            'Partial' => Order::STATUS_PARTIAL,
            'Cancelled' => Order::STATUS_CANCELLED,
        ];

        $newStatus  = $statusMap[$data['status'] ?? ''] ?? null;
        $remains    = (int) ($data['remains'] ?? $order->remains);
        $startCount = (int) ($data['start_count'] ?? $order->start_count);

        if (! $newStatus || $newStatus === $order->status) return;

        $order->update([
            'status'      => $newStatus,
            'remains'     => $remains,
            'start_count' => $startCount,
            'completed_at'=> in_array($newStatus, ['completed', 'partial'])
                                ? now() : null,
        ]);

        // Remboursement automatique si annulé ou échoué
        if (in_array($newStatus, [Order::STATUS_CANCELLED, Order::STATUS_FAILED])) {
            $orderService->refund($order);
        }
    }
}
