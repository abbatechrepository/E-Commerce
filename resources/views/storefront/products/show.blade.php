@extends('layouts.app')

@section('content')
    <section class="mx-auto grid max-w-7xl gap-10 px-6 py-14 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-[2rem] border border-black/10 bg-[linear-gradient(145deg,_#1f1a17,_#ad5d3d)] p-10 shadow-vinyl">
            @if ($product->images->isNotEmpty())
                <div class="space-y-4">
                    <img src="{{ $product->images->firstWhere('is_primary', true)?->url ?? $product->images->first()->url }}" alt="{{ $product->images->firstWhere('is_primary', true)?->alt_text ?? $product->album_title }}" class="h-[26rem] w-full rounded-[1.5rem] border border-white/10 object-cover">
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($product->images->take(4) as $image)
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="h-20 w-full rounded-xl border border-white/10 object-cover">
                        @endforeach
                    </div>
                </div>
            @else
                <div class="h-full min-h-[28rem] rounded-[1.5rem] border border-white/10 bg-black/10"></div>
            @endif
        </div>
        <div class="space-y-6">
            <div class="flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-[0.25em] text-olive">
                <span>{{ $product->media_format }}</span>
                <span>{{ $product->genre?->name }}</span>
                <span>{{ $product->disc_condition }}</span>
                @if ($product->is_rare)
                    <span class="text-rust">Raro</span>
                @endif
            </div>
            <div>
                <h1 class="text-5xl font-black">{{ $product->album_title }}</h1>
                <p class="mt-3 text-lg text-ink/70">{{ $product->artist?->name }}</p>
            </div>
            <p class="max-w-2xl text-base leading-8 text-ink/70">{{ $product->description }}</p>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-[1.5rem] border border-black/10 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-olive">Resumo Comercial</p>
                    <dl class="mt-4 space-y-2 text-sm text-ink/70">
                        <div class="flex justify-between"><dt>SKU</dt><dd>{{ $product->sku }}</dd></div>
                        <div class="flex justify-between"><dt>Formato</dt><dd>{{ $product->media_format }}</dd></div>
                        <div class="flex justify-between"><dt>Estado do Disco</dt><dd>{{ $product->disc_condition }}</dd></div>
                        <div class="flex justify-between"><dt>Estado da Capa</dt><dd>{{ $product->sleeve_condition }}</dd></div>
                    </dl>
                </div>
                <div class="rounded-[1.5rem] border border-black/10 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-olive">Resumo Operacional</p>
                    <dl class="mt-4 space-y-2 text-sm text-ink/70">
                        <div class="flex justify-between"><dt>Disponivel</dt><dd>{{ $product->inventory?->available_quantity }}</dd></div>
                        <div class="flex justify-between"><dt>Reservado</dt><dd>{{ $product->inventory?->reserved_quantity }}</dd></div>
                        <div class="flex justify-between"><dt>Status</dt><dd>{{ $product->status?->label() }}</dd></div>
                        <div class="flex justify-between"><dt>Publicado em</dt><dd>{{ optional($product->published_at)->format('d/m/Y') }}</dd></div>
                    </dl>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-5 rounded-[1.5rem] border border-black/10 bg-white p-6 shadow-vinyl">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Preco</p>
                    <p class="mt-2 text-4xl font-black text-rust">R$ {{ number_format((float) $product->effective_price, 2, ',', '.') }}</p>
                </div>
                <form method="post" action="{{ route('cart.items.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Adicionar ao carrinho</button>
                </form>
            </div>
        </div>
    </section>
@endsection
