@extends('layouts.app')

@section('content')
    <section class="mx-auto grid max-w-7xl gap-10 px-6 py-16 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="space-y-6">
            <p class="inline-flex rounded-full bg-rust/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-rust">Projeto de Portifolio</p>
            <h1 class="max-w-3xl text-5xl font-black leading-tight">Discos raros, historias bem curadas e uma arquitetura Laravel com cara de produto real.</h1>
            <p class="max-w-2xl text-lg text-ink/70">Esta loja foi desenhada para a venda de vinis antigos, com catalogo rico em metadados, itens raros sensiveis a estoque, base para retencao de clientes e um fluxo isolado de gateway fake de pagamento.</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('storefront.products.index') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Explorar Catalogo</a>
                <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-ink/15 px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] hover:border-rust hover:text-rust">Abrir Painel</a>
            </div>
        </div>
        <div class="rounded-[2rem] border border-black/10 bg-white p-8 shadow-vinyl">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Foco do Negocio</p>
            <ul class="mt-6 space-y-4 text-sm text-ink/75">
                <li>Catalogo pensado para colecionadores, com descoberta por artista, genero, raridade e estado de conservacao.</li>
                <li>Finalizacao preparada para reserva de estoque, rastreabilidade de pagamento e snapshots do pedido.</li>
                <li>Base administrativa com auditoria, metricas, integracao com gateway fake e evolucao para retencao.</li>
            </ul>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 pb-16">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Colecao em Destaque</p>
                <h2 class="mt-2 text-3xl font-black">Pronta para uma demonstracao de portifolio convincente</h2>
            </div>
            <a href="{{ route('storefront.products.index') }}" class="text-sm font-semibold text-rust">Ver todos os discos</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($featuredProducts as $product)
                <article class="overflow-hidden rounded-[1.75rem] border border-black/10 bg-white shadow-vinyl">
                    <div class="relative">
                        @if ($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}" alt="{{ $product->primaryImage->alt_text }}" class="h-60 w-full object-cover">
                        @else
                            <div class="h-60 bg-[linear-gradient(135deg,_#241d18,_#6c4f3d)]"></div>
                        @endif
                        <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                            @if ($product->genre?->name)
                                <span class="rounded-full bg-ink/70 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.28em] text-white backdrop-blur">{{ $product->genre->name }}</span>
                            @endif
                            @if ($product->is_rare)
                                <span class="rounded-full bg-rust/85 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.28em] text-white">Raro</span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3 p-6">
                        <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.25em] text-olive">
                            <span>{{ $product->media_format }}</span>
                            <span>{{ $product->genre?->name }}</span>
                        </div>
                        <h3 class="text-xl font-black">{{ $product->album_title }}</h3>
                        <p class="text-sm text-ink/70">{{ $product->artist?->name }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-black text-rust">R$ {{ number_format((float) $product->effective_price, 2, ',', '.') }}</span>
                            <span class="rounded-full bg-sand px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-ink">{{ $product->inventory?->available_quantity }} em estoque</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
