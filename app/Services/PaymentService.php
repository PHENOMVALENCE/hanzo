<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function create(Order $order, string $type, float $amount, ?string $method, ?string $reference, ?UploadedFile $proof = null): Payment
    {
        $proofPath = null;
        if ($proof) {
            $proofPath = $proof->store('payments/' . $order->id, 'private');
        }

        return Payment::create([
            'order_id' => $order->id,
            'type' => $type,
            'amount_usd' => $amount,
            'method' => $method,
            'reference' => $reference,
            'proof_path' => $proofPath,
            'status' => 'pending',
        ]);
    }

    public function verify(Payment $payment, int $verifiedBy): void
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);

        $order = $payment->order;
        if ($payment->type === 'deposit' || $payment->type === 'full') {
            (new OrderService)->updateMilestone($order, 'deposit_paid');
        }
    }

    public function reject(Payment $payment, int $verifiedBy): void
    {
        $payment->update([
            'status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);
    }
}
