<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $orders = Order::where('user_id', $user->id);

        return response()->json([
            'balance'           => $user->balance,
            'total_orders'      => (clone $orders)->count(),
            'completed_orders'  => (clone $orders)->where('status', 'completed')->count(),
            'pending_orders'    => (clone $orders)->whereIn('status', ['pending', 'processing', 'in_progress'])->count(),
            'total_spent'       => (clone $orders)->whereNotIn('status', ['cancelled', 'failed', 'refunded'])->sum('charge'),
            'recent_orders'     => (clone $orders)->with('service.category')->latest()->limit(5)->get(),
        ]);
    }
}
