<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Services\RfqService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RfqController extends Controller
{
    public function __construct(
        private RfqService $rfqService
    ) {}

    public function index(): View
    {
        $rfqs = Rfq::with(['buyer', 'category', 'assignedFactory'])->latest()->paginate(20);

        return view('admin.rfqs.index', compact('rfqs'));
    }

    public function show(Rfq $rfq): View
    {
        $rfq->load(['buyer', 'category', 'assignedFactory', 'attachments', 'factoryQuotes']);

        return view('admin.rfqs.show', compact('rfq'));
    }

    public function assign(Request $request, Rfq $rfq): RedirectResponse
    {
        $request->validate([
            'factory_id' => ['required', 'integer', 'exists:factories,id'],
        ]);

        $this->rfqService->assignFactory($rfq, (int) $request->factory_id, $request->user());

        return back()->with('success', 'Factory assigned successfully.');
    }
}
