<?php

namespace App\Http\Controllers\Payments;

use App\Application\Payments\ProcessPaymentWebhookAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, ProcessPaymentWebhookAction $processPaymentWebhookAction): JsonResponse
    {
        $log = $processPaymentWebhookAction->execute($request->all(), $request->headers->all());

        return response()->json([
            'processed' => $log->processed,
            'webhook_log_id' => $log->id,
        ]);
    }
}
