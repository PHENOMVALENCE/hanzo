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
            'category' => ['required', 'integer', 'exists:categories,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'destination' => ['required', 'string', 'max:100'],
        ]);

        $result = $this->estimatorService->estimate(
            (int) $request->category,
            (int) $request->qty,
            $request->destination
        );

        return response()->json($result);
    }
}
