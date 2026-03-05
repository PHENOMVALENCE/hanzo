<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EstimatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function __construct(
        private EstimatorService $estimatorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'qty' => ['required', 'integer', 'min:1'],
            'method' => ['required', 'string', 'max:50'],
            'destination' => ['required', 'string', 'max:100'],
        ]);

        $result = $this->estimatorService->estimate(
            $request->category,
            (int) $request->qty,
            $request->method,
            $request->destination
        );

        return response()->json($result);
    }
}
