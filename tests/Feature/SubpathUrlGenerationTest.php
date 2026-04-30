<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SubpathUrlGenerationTest extends TestCase
{
    public function test_named_routes_and_storage_urls_support_ecommerce_subpath(): void
    {
        config()->set('app.url', 'https://abbatech.dev.br/e-commerce');
        config()->set('filesystems.disks.public.url', rtrim(config('app.url'), '/').'/storage');
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');

        $this->assertSame('https://abbatech.dev.br/e-commerce', route('storefront.home'));
        $this->assertSame('https://abbatech.dev.br/e-commerce/admin/products/create', route('admin.products.create'));
        $this->assertSame('https://abbatech.dev.br/e-commerce', url('/'));
        $this->assertSame(
            'https://abbatech.dev.br/e-commerce/storage/products/demo/test.svg',
            Storage::disk('public')->url('products/demo/test.svg')
        );
    }
}
