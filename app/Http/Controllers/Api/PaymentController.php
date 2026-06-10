<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** Initier un dépôt */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:500', // Minimum 500 FCFA
            'method' => 'required|in:mtn_mobile_money,moov_money,wave,orange_money,crypto_usdt,crypto_btc',
        ]);

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'amount'  => $data['amount'],
            'method'  => $data['method'],
            'status'  => 'pending',
        ]);

        // Rediriger vers la gateway de paiement appropriée
        $redirectUrl = $this->getPaymentUrl($payment);

        return response()->json([
            'payment'      => $payment,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /** Callback de confirmation de paiement (webhook) */
    public function callback(Request $request, string $method)
    {
        // Chaque gateway a son propre format — adapter selon le provider
        $ref  = $request->input('transaction_ref') ?? $request->input('ref');
        $payment = Payment::where('transaction_ref', $ref)
            ->where('status', 'pending')
            ->firstOrFail();

        // TODO : Vérifier la signature du webhook selon la gateway

        $user          = $payment->user;
        $balanceBefore = $user->balance;

        $payment->update(['status' => 'completed', 'paid_at' => now()]);
        $user->addBalance($payment->amount);

        Transaction::create([
            'user_id'        => $user->id,
            'type'           => 'deposit',
            'amount'         => $payment->amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $user->fresh()->balance,
            'description'    => "Dépôt via {$payment->method}",
            'reference_id'   => $payment->id,
            'reference_type' => 'payments',
            'status'         => 'completed',
        ]);

        return response()->json(['message' => 'Paiement confirmé.']);
    }

    /** Historique des paiements */
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($payments);
    }

    private function getPaymentUrl(Payment $payment): string
    {
        // À implémenter selon chaque gateway (MTN, Wave, Cryptomus…)
        return url("/pay/{$payment->id}");
    }
}
