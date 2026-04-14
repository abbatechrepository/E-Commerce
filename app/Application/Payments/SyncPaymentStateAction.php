<?php

namespace App\Application\Payments;

use App\Application\Audit\AuditLogger;
use App\Enums\InventoryMovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;

class SyncPaymentStateAction
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function execute(Payment $payment, PaymentStatus $newStatus, array $context = []): void
    {
        $order = $payment->order;

        if (! $order) {
            return;
        }

        match ($newStatus) {
            PaymentStatus::APPROVED => $this->markOrderAsPaid($order, $context),
            PaymentStatus::DECLINED, PaymentStatus::CANCELLED => $this->cancelOrderAndReleaseInventory($order, $payment, $newStatus, $context),
            PaymentStatus::REFUNDED => $this->refundOrderAndReleaseInventory($order, $payment, $context),
            default => null,
        };
    }

    private function markOrderAsPaid(Order $order, array $context): void
    {
        if (! $order->status->canTransitionTo(OrderStatus::PAID)) {
            return;
        }

        $previousStatus = $order->status;

        $order->update([
            'status' => OrderStatus::PAID,
            'paid_at' => now(),
        ]);

        $order->statusHistory()->create([
            'from_status' => $previousStatus,
            'to_status' => OrderStatus::PAID,
            'changed_by' => null,
            'reason' => 'Pagamento aprovado pelo gateway fake',
            'metadata' => $context,
        ]);
    }

    private function cancelOrderAndReleaseInventory(Order $order, Payment $payment, PaymentStatus $newStatus, array $context): void
    {
        if (! in_array($order->status, [OrderStatus::PENDING_PAYMENT, OrderStatus::PAID, OrderStatus::PROCESSING], true)) {
            return;
        }

        $previousStatus = $order->status;

        $order->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        $order->statusHistory()->create([
            'from_status' => $previousStatus,
            'to_status' => OrderStatus::CANCELLED,
            'changed_by' => null,
            'reason' => 'Pagamento '.$newStatus->label().' pelo gateway fake',
            'metadata' => $context,
        ]);

        $this->releaseReservedInventory($order, $payment, $newStatus, 'Estoque liberado apos falha ou cancelamento do pagamento.');
    }

    private function refundOrderAndReleaseInventory(Order $order, Payment $payment, array $context): void
    {
        $previousStatus = $order->status;

        $order->update([
            'status' => OrderStatus::REFUNDED,
            'refunded_at' => now(),
        ]);

        $order->statusHistory()->create([
            'from_status' => $previousStatus,
            'to_status' => OrderStatus::REFUNDED,
            'changed_by' => null,
            'reason' => 'Pagamento estornado pelo gateway fake',
            'metadata' => $context,
        ]);

        $this->releaseReservedInventory($order, $payment, PaymentStatus::REFUNDED, 'Estoque recomposto apos estorno.');
    }

    private function releaseReservedInventory(Order $order, Payment $payment, PaymentStatus $status, string $reason): void
    {
        $order->loadMissing('items.product.inventory', 'customer');

        foreach ($order->items as $item) {
            $inventory = $item->product?->inventory;

            if (! $inventory) {
                continue;
            }

            $inventory->increment('available_quantity', $item->quantity);
            $inventory->decrement('reserved_quantity', min($inventory->reserved_quantity, $item->quantity));

            InventoryMovement::query()->create([
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'user_id' => $order->customer?->user_id,
                'type' => $status === PaymentStatus::REFUNDED ? InventoryMovementType::RETURN : InventoryMovementType::RELEASE,
                'quantity' => $item->quantity,
                'reason' => $reason,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'payment_status' => $status->value,
                ],
            ]);
        }

        $this->auditLogger->log('payment.inventory_released', $payment, null, [
            'order_id' => $order->id,
            'status' => $status->value,
        ]);
    }
}
