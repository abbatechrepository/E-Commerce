<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Support\DemoCatalogCoverGenerator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('portfolio:check', function () {
    $this->components->info('Checking portfolio environment...');

    $this->newLine();
    $this->line('Application');
    $this->table(
        ['Key', 'Value'],
        [
            ['APP_NAME', config('app.name')],
            ['APP_ENV', config('app.env')],
            ['APP_URL', config('app.url')],
            ['QUEUE_CONNECTION', config('queue.default')],
            ['DB_CONNECTION', config('database.default')],
        ]
    );

    $this->newLine();
    $this->line('Database');

    try {
        DB::connection()->getPdo();
        $productCount = DB::table('products')->count();
        $orderCount = DB::table('orders')->count();
        $paymentCount = DB::table('payments')->count();

        $this->components->info('Database connection: OK');
        $this->table(
            ['Dataset', 'Count'],
            [
                ['products', $productCount],
                ['orders', $orderCount],
                ['payments', $paymentCount],
            ]
        );
    } catch (Throwable $exception) {
        $this->components->error('Database connection failed.');
        $this->line($exception->getMessage());
    }

    $this->newLine();
    $this->line('Routes');

    $importantRoutes = collect([
        'storefront.home',
        'cart.index',
        'checkout.create',
        'customer.dashboard',
        'admin.dashboard',
        'api.gateway.transactions.store',
        'api.gateway.webhooks.payment-status',
    ])->map(function (string $name): array {
        $route = Route::getRoutes()->getByName($name);

        return [
            $name,
            $route?->uri() ?? 'missing',
            $route?->methods() ? implode(',', $route->methods()) : 'missing',
        ];
    })->all();

    $this->table(['Name', 'URI', 'Methods'], $importantRoutes);

    $this->newLine();
    $this->components->info('Portfolio check finished.');
})->purpose('Validate core portfolio environment, database connectivity, and demo routes.');

Artisan::command('catalog:generate-demo-images {--force : Regenera tambem capas demo ja existentes}', function (DemoCatalogCoverGenerator $generator) {
    $force = (bool) $this->option('force');
    $products = Product::query()->with(['artist', 'genre', 'images'])->get();

    if ($products->isEmpty()) {
        $this->components->warn('Nenhum produto encontrado para gerar capas demo.');

        return self::SUCCESS;
    }

    $bar = $this->output->createProgressBar($products->count());
    $bar->start();

    $generated = 0;

    foreach ($products as $product) {
        $before = $product->images->count();
        $image = $generator->ensure($product, $force);

        if ($image) {
            $generated++;
        }

        if ($before === 0) {
            $product->refresh();
        }

        $bar->advance();
    }

    $bar->finish();
    $this->newLine(2);
    $this->components->info("Capas demo processadas para {$generated} produto(s).");

    return self::SUCCESS;
})->purpose('Gerar capas demo locais para os produtos do catalogo.');
