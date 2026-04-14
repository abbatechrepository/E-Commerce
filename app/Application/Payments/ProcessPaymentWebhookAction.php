<?php

namespace App\Application\Payments;

use App\Application\Audit\AuditLogger;
use App\Enums\PaymentStatus;
use App\Events\PaymentStatusUpdated;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use Illuminate\Support\Facades\DB;

class ProcessPaymentWebhookAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly SyncPaymentStateAction $syncPaymentStateAction,
    ) {
    }

    public function execute(array $payload, array $headers = []): PaymentWebhookLog
    {
        return DB::transaction(function () use ($payload, $headers): PaymentWebhookLog {
            $payment = Payment::query()
                ->whereHas('transactions', fn ($query) => $query->where('transaction_code', $payload['transaction_code'] ?? null))
                ->first();

            $log = PaymentWebhookLog::query()->create([
                'payment_id' => $payment?->id,
                'event_type' => $payload['event_type'] ?? 'payment.status.updated',
                'external_transaction_code' => $payload['transaction_code'] ?? null,
                'payload' => $payload,
                'headers' => $headers,
                'processed' => false,
                'processing_attempts' => 1,
            ]);

            if (! $payment) {
                return $log;
            }

            $newStatus = PaymentStatus::from($payload['status']);

            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === PaymentStatus::APPROVED ? now() : $payment->paid_at,
                'cancelled_at' => $newStatus === PaymentStatus::CANCELLED ? now() : $payment->cancelled_at,
                'refunded_at' => $newStatus === PaymentStatus::REFUNDED ? now() : $payment->refunded_at,
                'external_reference' => $payload['gateway_reference'] ?? $payment->external_reference,
            ]);

            $payment->transactions()->latest()->first()?->update([
                'status' => $newStatus,
                'response_payload' => $payload,
                'provider_response_code' => $payload['response_code'] ?? null,
                'provider_message' => $payload['message'] ?? null,
                'responded_at' => now(),
            ]);

            $this->syncPaymentStateAction->execute($payment, $newStatus, [
                'webhook' => true,
                'transaction_code' => $payload['transaction_code'] ?? null,
            ]);

            $log->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

            $this->auditLogger->log('payment.webhook_processed', $payment, null, $payload, [
                'webhook_log_id' => $log->id,
            ]);

            PaymentStatusUpdated::dispatch($payment);

            return $log->refresh();
        });
    }
}
