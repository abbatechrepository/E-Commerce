@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Catalogo</p>
                <h1 class="mt-2 text-4xl font-black">Discos antigos selecionados para descoberta</h1>
            </div>
            <form class="flex gap-3" method="get">
                <input name="search" value="{{ request('search') }}" placeholder="Buscar por artista ou album" class="w-72 rounded-full border border-black/10 bg-white px-5 py-3 outline-none ring-0 placeholder:text-ink/40">
                <button class="rounded-full bg-rust px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Buscar</button>
            </form>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($products as $product)
                <article class="rounded-[1.5rem] border border-black/10 bg-white p-6 shadow-vinyl">
                    <div class="relative mb-5">
                        @if ($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}" alt="{{ $product->primaryImage->alt_text }}" class="h-52 w-full rounded-[1.25rem] object-cover">
                        @else
                            <div class="h-52 rounded-[1.25rem] bg-[linear-gradient(160deg,_#1f1a17,_#ad5d3d)]"></div>
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
                    <div class="space-y-3">
                        <div class="flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-[0.25em] text-olive">
                            <span>{{ $product->media_format }}</span>
                            <span>{{ $product->disc_condition }}</span>
                            @if ($product->is_rare)
                                <span class="text-rust">Raro</span>
                            @endif
                        </div>
                        <h2 class="text-2xl font-black">{{ $product->album_title }}</h2>
                        <p class="text-sm text-ink/70">{{ $product->artist?->name }}</p>
                        <p class="line-clamp-2 text-sm text-ink/60">{{ $product->description }}</p>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xl font-black text-rust">R$ {{ number_format((float) $product->effective_price, 2, ',', '.') }}</span>
                            <a href="{{ route('storefront.products.show', $product) }}" class="rounded-full border border-ink/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] hover:border-rust hover:text-rust">Detalhes</a>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-sm text-ink/70">Nenhum disco corresponde aos filtros atuais.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    </section>
@endsection
