<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\FakeGatewayOperationRequest;
use App\Http\Requests\Payments\FakeGatewayTransactionRequest;
use App\Http\Requests\Payments\FakeGatewayWebhookSimulationRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Enums\PaymentStatus;
use App\Services\Payments\FakePaymentGatewayService;
use Illuminate\Http\JsonResponse;

class FakeGatewayController extends Controller
{
    public function store(FakeGatewayTransactionRequest $request, FakePaymentGatewayService $fakePaymentGatewayService): JsonResponse
    {
        $payment = Payment::query()->findOrFail($request->integer('payment_id'));
        $response = $fakePaymentGatewayService->createTransaction($payment, $request->validated());

        return response()->json([
            'payment' => new PaymentResource($payment->fresh()->load('transactions')),
            'gateway_response' => $response,
        ], 201);
    }

    public function show(string $transactionCode, FakePaymentGatewayService $fakePaymentGatewayService): JsonResponse
    {
        $transaction = PaymentTransaction::query()
            ->with('payment.order')
            ->where('transaction_code', $transactionCode)
            ->firstOrFail();

        return response()->json($fakePaymentGatewayService->getTransactionDetails($transaction));
    }

    public function refund(string $transactionCode, FakeGatewayOperationRequest $request, FakePaymentGatewayService $fakePaymentGatewayService): JsonResponse
    {
        $payment = Payment::query()->whereHas('transactions', fn ($query) => $query->where('transaction_code', $transactionCode))->firstOrFail();

        return response()->json($fakePaymentGatewayService->refund(
            $payment,
            $request->string('reason')->toString() ?: null,
            $request->boolean('simulate_webhook', true),
        ));
    }

    public function cancel(string $transactionCode, FakeGatewayOperationRequest $request, FakePaymentGatewayService $fakePaymentGatewayService): JsonResponse
    {
        $payment = Payment::query()->whereHas('transactions', fn ($query) => $query->where('transaction_code', $transactionCode))->firstOrFail();

        return response()->json($fakePaymentGatewayService->cancel(
            $payment,
            $request->string('reason')->toString() ?: null,
            $request->boolean('simulate_webhook', true),
        ));
    }

    public function simulateStatus(string $transactionCode, FakeGatewayWebhookSimulationRequest $request, FakePaymentGatewayService $fakePaymentGatewayService): JsonResponse
    {
        $payment = Payment::query()->whereHas('transactions', fn ($query) => $query->where('transaction_code', $transactionCode))->firstOrFail();

        return response()->json($fakePaymentGatewayService->simulateWebhookStatus(
            $payment,
            $transactionCode,
            PaymentStatus::from($request->string('status')->toString()),
            $request->string('reason')->toString() ?: null,
        ));
    }
}
