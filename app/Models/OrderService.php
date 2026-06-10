<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private SmmApiService $smmApi) {}

    /**
     * Crée une commande, débite le solde, envoie au fournisseur.
     */
    public function place(User $user, Service $service, string $link, int $quantity): Order
    {
        $charge = $service->calculateCharge($quantity);

        return DB::transaction(function () use ($user, $service, $link, $quantity, $charge) {

            // 1. Vérifier et débiter le solde
            $balanceBefore = $user->balance;
            $user->deductBalance($charge);

            // 2. Créer la commande
            $order = Order::create([
                'user_id'    => $user->id,
                'service_id' => $service->id,
                'link'       => $link,
                'quantity'   => $quantity,
                'charge'     => $charge,
                'provider_id'=> $service->provider_id,
                'status'     => Order::STATUS_PENDING,
            ]);

            // 3. Enregistrer la transaction
            Transaction::create([
                'user_id'        => $user->id,
                'type'           => 'order',
                'amount'         => $charge,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->fresh()->balance,
                'description'    => "Commande #{$order->id} — {$service->name}",
                'reference_id'   => $order->id,
                'reference_type' => 'orders',
                'status'         => 'completed',
            ]);

            // 4. Envoyer au fournisseur API (async via Job)
            \App\Jobs\SendOrderToProvider::dispatch($order);

            return $order;
        });
    }

    /**
     * Rembourse une commande annulée/échouée.
     */
    public function refund(Order $order): void
    {
        if ($order->status === Order::STATUS_REFUNDED) return;

        DB::transaction(function () use ($order) {
            $user          = $order->user;
            $balanceBefore = $user->balance;

            $user->addBalance($order->charge);
            $order->update(['status' => Order::STATUS_REFUNDED]);

            Transaction::create([
                'user_id'        => $user->id,
                'type'           => 'refund',
                'amount'         => $order->charge,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->fresh()->balance,
                'description'    => "Remboursement commande #{$order->id}",
                'reference_id'   => $order->id,
                'reference_type' => 'orders',
                'status'         => 'completed',
            ]);
        });
    }
}
