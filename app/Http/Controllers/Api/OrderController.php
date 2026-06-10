<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    /** Historique des commandes de l'utilisateur */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['service.category'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    /** Passer une commande */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'link'       => 'required|url',
            'quantity'   => 'required|integer|min:1',
        ]);

        $service = Service::where('id', $data['service_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Validation quantité min/max
        if ($data['quantity'] < $service->min_quantity || $data['quantity'] > $service->max_quantity) {
            return response()->json([
                'message' => "La quantité doit être entre {$service->min_quantity} et {$service->max_quantity}.",
            ], 422);
        }

        // Vérification du solde
        $charge = $service->calculateCharge($data['quantity']);
        if ($request->user()->balance < $charge) {
            return response()->json([
                'message' => 'Solde insuffisant. Veuillez recharger votre compte.',
                'required' => $charge,
                'balance'  => $request->user()->balance,
            ], 422);
        }

        $order = $this->orderService->place(
            $request->user(),
            $service,
            $data['link'],
            $data['quantity']
        );

        return response()->json($order->load('service'), 201);
    }

    /** Détail d'une commande */
    public function show(Request $request, Order $order)
    {
        // Un client ne peut voir que ses propres commandes
        if ($request->user()->id !== $order->user_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        return response()->json($order->load(['service.category', 'provider']));
    }

    /** Commandes groupées (mass order) */
    public function massStore(Request $request)
    {
        $request->validate([
            'orders'             => 'required|array|max:100',
            'orders.*.service_id'=> 'required|integer|exists:services,id',
            'orders.*.link'      => 'required|url',
            'orders.*.quantity'  => 'required|integer|min:1',
        ]);

        $results = [];
        foreach ($request->orders as $item) {
            try {
                $service = Service::findOrFail($item['service_id']);
                $order   = $this->orderService->place(
                    $request->user(),
                    $service,
                    $item['link'],
                    $item['quantity']
                );
                $results[] = ['status' => 'success', 'order_id' => $order->id];
            } catch (\Exception $e) {
                $results[] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return response()->json($results, 201);
    }
}
