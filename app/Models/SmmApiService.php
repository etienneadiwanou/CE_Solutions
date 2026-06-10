<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmmApiService
{
    /**
     * Envoie la commande au fournisseur principal,
     * bascule sur le fallback si échec.
     */
    public function sendOrder(Order $order): bool
    {
        $providers = Provider::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($providers as $provider) {
            try {
                $response = Http::timeout(15)->post($provider->api_url, [
                    'key'      => $provider->api_key,
                    'action'   => 'add',
                    'service'  => $order->service->provider_service_id,
                    'link'     => $order->link,
                    'quantity' => $order->quantity,
                ]);

                $data = $response->json();

                if (isset($data['order'])) {
                    $order->update([
                        'provider_order_id' => $data['order'],
                        'provider_id'       => $provider->id,
                        'status'            => Order::STATUS_PROCESSING,
                    ]);
                    return true;
                }

                Log::warning("Provider {$provider->name} a refusé la commande", $data ?? []);

            } catch (\Throwable $e) {
                Log::error("Erreur provider {$provider->name} : {$e->getMessage()}");
            }
        }

        // Tous les providers ont échoué
        $order->update(['status' => Order::STATUS_FAILED]);
        return false;
    }

    /**
     * Vérifie le statut d'une commande chez le fournisseur.
     */
    public function checkStatus(Order $order): array
    {
        if (! $order->provider || ! $order->provider_order_id) return [];

        $response = Http::timeout(10)->post($order->provider->api_url, [
            'key'    => $order->provider->api_key,
            'action' => 'status',
            'order'  => $order->provider_order_id,
        ]);

        return $response->json() ?? [];
    }

    /**
     * Récupère tous les services disponibles chez un fournisseur.
     */
    public function getProviderServices(Provider $provider): array
    {
        $response = Http::timeout(20)->post($provider->api_url, [
            'key'    => $provider->api_key,
            'action' => 'services',
        ]);

        return $response->json() ?? [];
    }

    /**
     * Récupère le solde restant chez un fournisseur.
     */
    public function getBalance(Provider $provider): float
    {
        $response = Http::timeout(10)->post($provider->api_url, [
            'key'    => $provider->api_key,
            'action' => 'balance',
        ]);

        return (float) ($response->json()['balance'] ?? 0);
    }
}
