@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Catalogo Administrativo</p>
                <h1 class="mt-2 text-4xl font-black">Gestao de produtos</h1>
            </div>
            <a href="{{ route('admin.products.create') }}" class="rounded-full bg-ink px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Novo produto</a>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] bg-white shadow-vinyl">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-sand/70 text-xs uppercase tracking-[0.25em] text-ink/50">
                    <tr>
                        <th class="px-6 py-4">Album</th>
                        <th class="px-6 py-4">Artista</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Preco</th>
                        <th class="px-6 py-4">Estoque</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-t border-black/5">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if ($product->primaryImage)
                                        <img src="{{ $product->primaryImage->url }}" alt="{{ $product->primaryImage->alt_text }}" class="h-14 w-14 rounded-xl object-cover">
                                    @else
                                        <div class="h-14 w-14 rounded-xl bg-[linear-gradient(160deg,_#1f1a17,_#ad5d3d)]"></div>
                                    @endif
                                    <div>
                                        <p class="font-bold">{{ $product->album_title }}</p>
                                        <p class="text-xs text-ink/50">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $product->artist?->name }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-olive/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">{{ $product->status?->label() }}</span>
                            </td>
                            <td class="px-6 py-4">R$ {{ number_format((float) $product->effective_price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $product->inventory?->available_quantity }} / {{ $product->inventory?->reserved_quantity }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-semibold text-rust">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">{{ $products->links() }}</div>
    </section>
@endsection
