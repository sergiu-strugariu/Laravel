<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuotationRequest;
use App\Providers\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class QuotationController extends Controller
{
    protected QuotationService $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    public function index(): Response
    {
        return Inertia::render('Quotation');
    }

    public function getQuotation(QuotationRequest $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized. You are not logged in.'], 401);
            }

            if (!$user->api_token) {
                return response()->json(['error' => 'Unauthorized. No token was found.'], 401);
            }

            $ages = explode(',', $request->input('age'));
            $currencyId = $request->input('currency_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $quotation = $this->quotationService->calculateQuotation($ages, $currencyId, $startDate, $endDate);

            return response()->json($quotation);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
