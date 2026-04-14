<?php

namespace App\Services\Payments;

use App\Application\Payments\ProcessPaymentWebhookAction;
use App\Enums\PaymentStatus;
use App\Jobs\SimulatePaymentWebhookJob;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Str;

class FakePaymentGatewayService
{
    public function __construct(private readonly ProcessPaymentWebhookAction $processPaymentWebhookAction)
    {
    }

    public function createTransaction(Payment $payment, array $payload): array
    {
        $simulatedStatus = PaymentStatus::from($payload['simulate_status'] ?? PaymentStatus::PENDING->value);
        $transactionCode = 'TXN-'.strtoupper(Str::random(14));
        $gatewayReference = 'FAKE-GW-'.strtoupper(Str::random(10));

        $response = [
            'gateway_reference' => $gatewayReference,
            'transaction_code' => $transactionCode,
            'status' => $simulatedStatus->value,
            'response_code' => $simulatedStatus === PaymentStatus::DECLINED ? '402' : '200',
            'message' => match ($simulatedStatus) {
                PaymentStatus::APPROVED => 'Payment approved by fake gateway.',
                PaymentStatus::DECLINED => 'Payment declined by fake gateway.',
                default => 'Payment pending asynchronous confirmation.',
            },
            'processed_at' => now()->toIso8601String(),
        ];

        $transaction = PaymentTransaction::query()->create([
            'payment_id' => $payment->id,
            'transaction_code' => $transactionCode,
            'provider' => $payment->provider,
            'status' => $simulatedStatus,
            'amount' => $payment->amount,
            'request_payload' => $payload,
            'response_payload' => $response,
            'provider_response_code' => $response['response_code'],
            'provider_message' => $response['message'],
            'requested_at' => now(),
            'responded_at' => now(),
        ]);

        if ($simulatedStatus === PaymentStatus::PENDING) {
            SimulatePaymentWebhookJob::dispatch($payment->id, $transactionCode)->delay(now()->addSeconds(5));
        } else {
            $this->processPaymentWebhookAction->execute([
                'event_type' => 'payment.status.updated',
                'transaction_code' => $transactionCode,
                'status' => $simulatedStatus->value,
                'gateway_reference' => $gatewayReference,
                'response_code' => $response['response_code'],
                'message' => $response['message'],
            ]);
        }

        return $this->buildTransactionPayload($transaction->fresh(), $payment->fresh());
    }

    public function refund(Payment $payment, ?string $reason = null, bool $simulateWebhook = true): array
    {
        return $this->applyOperation($payment, PaymentStatus::REFUNDED, $reason, $simulateWebhook);
    }

    public function cancel(Payment $payment, ?string $reason = null, bool $simulateWebhook = true): array
    {
        return $this->applyOperation($payment, PaymentStatus::CANCELLED, $reason, $simulateWebhook);
    }

    public function simulateWebhookStatus(Payment $payment, string $transactionCode, PaymentStatus $status, ?string $reason = null): array
    {
        $transaction = $payment->transactions()->where('transaction_code', $transactionCode)->firstOrFail();
        $payload = $this->buildWebhookPayload($transactionCode, $status, $reason);

        $this->processPaymentWebhookAction->execute($payload, [
            'x-fake-gateway' => ['portfolio-gateway'],
        ]);

        return $this->buildTransactionPayload($transaction->fresh(), $payment->fresh());
    }

    public function getTransactionDetails(PaymentTransaction $transaction): array
    {
        return $this->buildTransactionPayload($transaction->load('payment.order'), $transaction->payment);
    }

    private function applyOperation(Payment $payment, PaymentStatus $targetStatus, ?string $reason, bool $simulateWebhook): array
    {
        $transaction = $payment->transactions()->latest()->firstOrFail();
        $payload = $this->buildWebhookPayload($transaction->transaction_code, $targetStatus, $reason);

        if ($simulateWebhook) {
            $this->processPaymentWebhookAction->execute($payload, [
                'x-fake-gateway' => ['portfolio-gateway'],
            ]);
        } else {
            $transaction->update([
                'status' => $targetStatus,
                'response_payload' => $payload,
                'provider_response_code' => $payload['response_code'],
                'provider_message' => $payload['message'],
                'responded_at' => now(),
            ]);
        }

        return $this->buildTransactionPayload($transaction->fresh(), $payment->fresh());
    }

    private function buildWebhookPayload(string $transactionCode, PaymentStatus $status, ?string $reason = null): array
    {
        return [
            'event_type' => 'payment.status.updated',
            'transaction_code' => $transactionCode,
            'status' => $status->value,
            'gateway_reference' => 'FAKE-GW-OPS-'.strtoupper(Str::random(8)),
            'response_code' => match ($status) {
                PaymentStatus::DECLINED => '402',
                PaymentStatus::FAILED => '500',
                default => '200',
            },
            'message' => $reason ?: match ($status) {
                PaymentStatus::REFUNDED => 'Refund processed by fake gateway.',
                PaymentStatus::CANCELLED => 'Transaction cancelled by fake gateway.',
                PaymentStatus::DECLINED => 'Payment declined by fake gateway.',
                PaymentStatus::APPROVED => 'Payment approved by fake gateway.',
                PaymentStatus::FAILED => 'Gateway processing failed.',
                default => 'Gateway status updated.',
            },
            'processed_at' => now()->toIso8601String(),
        ];
    }

    private function buildTransactionPayload(PaymentTransaction $transaction, Payment $payment): array
    {
        return [
            'payment_id' => $payment->id,
            'order_number' => $payment->order?->order_number,
            'gateway_reference' => data_get($transaction->response_payload, 'gateway_reference', $payment->external_reference),
            'transaction_code' => $transaction->transaction_code,
            'status' => $transaction->status?->value,
            'amount' => $transaction->amount,
            'provider' => $transaction->provider,
            'response_code' => $transaction->provider_response_code,
            'message' => $transaction->provider_message,
            'requested_at' => optional($transaction->requested_at)->toIso8601String(),
            'responded_at' => optional($transaction->responded_at)->toIso8601String(),
            'request_payload' => $transaction->request_payload,
            'response_payload' => $transaction->response_payload,
        ];
    }
}
