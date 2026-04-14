<?php

namespace App\Jobs;

use App\Application\Payments\ProcessPaymentWebhookAction;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SimulatePaymentWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $paymentId,
        private readonly string $transactionCode,
    ) {
    }

    public function handle(ProcessPaymentWebhookAction $processPaymentWebhookAction): void
    {
        $payment = Payment::query()->find($this->paymentId);

        if (! $payment) {
            return;
        }

        $processPaymentWebhookAction->execute([
            'event_type' => 'payment.status.updated',
            'transaction_code' => $this->transactionCode,
            'status' => PaymentStatus::APPROVED->value,
            'gateway_reference' => 'FAKE-GW-WEBHOOK-'.$payment->id,
            'response_code' => '200',
            'message' => 'Asynchronous payment approval simulated successfully.',
        ]);
    }
}
