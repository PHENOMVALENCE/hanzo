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
        $mimeType = null;
        if ($proof) {
            $proofPath = $proof->store('payments/' . $order->id, 'private');
            $mimeType = $proof->getMimeType();
        }

        return Payment::create([
            'order_id' => $order->id,
            'type' => $type,
            'amount_usd' => $amount,
            'method' => $method,
            'reference' => $reference,
            'proof_path' => $proofPath,
            'mime_type' => $mimeType,
            'status' => 'pending',
        ]);
    }

    public function verify(Payment $payment, int $verifiedBy, ?string $adminNotes = null): void
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        $order = $payment->order;
        if ($payment->type === 'deposit' || $payment->type === 'full') {
            (new OrderService)->updateMilestone($order, 'deposit_paid');
        }
    }

    public function reject(Payment $payment, int $verifiedBy, ?string $rejectionReason = null): void
    {
        $payment->update([
            'status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'rejection_reason' => $rejectionReason,
        ]);
    }
}
