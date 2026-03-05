<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(): View
    {
        $payments = Payment::with(['order.quotation.rfq', 'order.buyer'])->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function verify(Payment $payment): RedirectResponse
    {
        $this->paymentService->verify($payment, auth()->id());

        return back()->with('success', 'Payment verified.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $this->paymentService->reject($payment, auth()->id());

        return back()->with('success', 'Payment rejected.');
    }
}
