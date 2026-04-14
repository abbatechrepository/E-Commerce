<?php

namespace App\Listeners;

use App\Application\Audit\AuditLogger;
use App\Events\ProductPublished;

class RecordProductPublicationAudit
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function handle(ProductPublished $event): void
    {
        $this->auditLogger->log('product.publication_event', $event->product, null, $event->product->toArray());
    }
}
