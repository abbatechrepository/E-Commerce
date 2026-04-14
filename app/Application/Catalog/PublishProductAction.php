<?php

namespace App\Application\Catalog;

use App\Application\Audit\AuditLogger;
use App\Enums\ProductStatus;
use App\Events\ProductPublished;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;

class PublishProductAction
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    /**
     * @throws AuthorizationException
     */
    public function execute(Product $product): Product
    {
        if (! $product->canBePublished()) {
            throw new AuthorizationException('O produto ainda nao esta pronto para publicacao.');
        }

        $before = $product->getOriginal();

        $product->forceFill([
            'status' => ProductStatus::ACTIVE,
            'is_active' => true,
            'published_at' => CarbonImmutable::now(),
        ])->save();

        $this->auditLogger->log('product.published', $product, $before, $product->fresh()?->toArray(), [
            'sku' => $product->sku,
        ]);

        ProductPublished::dispatch($product);

        return $product->refresh();
    }
}
