<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(): View
    {
        $payments = Payment::with(['order.quotation.rfq', 'order.buyer', 'verifiedBy'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order.quotation.rfq', 'order.buyer', 'verifiedBy']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $this->paymentService->verify($payment, auth()->id(), $request->input('admin_notes'));

        return back()->with('success', 'Payment verified.');
    }

    public function proof(Payment $payment): StreamedResponse
    {
        if (! $payment->proof_path || ! Storage::disk('private')->exists($payment->proof_path)) {
            abort(404, 'Proof not found.');
        }

        $filename = 'payment-proof-' . $payment->id . '.' . pathinfo($payment->proof_path, PATHINFO_EXTENSION);

        return Storage::disk('private')->response(
            $payment->proof_path,
            $filename,
            ['Content-Type' => $payment->mime_type ?? 'application/octet-stream']
        );
    }

    public function reject(\Illuminate\Http\Request $request, Payment $payment): RedirectResponse
    {
        $this->paymentService->reject($payment, auth()->id(), $request->input('rejection_reason'));

        return back()->with('success', 'Payment rejected.');
    }
}
