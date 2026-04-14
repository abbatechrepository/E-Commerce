@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Carrinho</p>
                <h1 class="mt-2 text-4xl font-black">Revise sua selecao antes de finalizar a compra</h1>
            </div>
            <a href="{{ route('storefront.products.index') }}" class="text-sm font-semibold text-rust">Continuar comprando</a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-4">
                @forelse ($cart->items as $item)
                    <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-xl font-black">{{ $item->product->album_title }}</p>
                                <p class="text-sm text-ink/60">{{ $item->product->artist?->name }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-olive">{{ $item->product->media_format }} · {{ $item->product->disc_condition }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-rust">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</p>
                                <p class="text-sm text-ink/60">{{ $item->product->inventory?->available_quantity }} disponivel(is)</p>
                            </div>
                        </div>
                        <div class="mt-5 flex flex-wrap items-center gap-3">
                            <form method="post" action="{{ route('cart.items.update', $item) }}" class="flex items-center gap-3">
                                @csrf
                                @method('patch')
                                <input type="number" min="1" name="quantity" value="{{ $item->quantity }}" class="w-24 rounded-2xl border border-black/10 px-4 py-3">
                                <button class="rounded-full border border-ink/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em]">Atualizar</button>
                            </form>
                            <form method="post" action="{{ route('cart.items.destroy', $item) }}">
                                @csrf
                                @method('delete')
                                <button class="rounded-full border border-rust/20 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-rust">Remover</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                        <p class="text-sm text-ink/60">Seu carrinho esta vazio no momento.</p>
                    </article>
                @endforelse
            </div>

            <aside class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Resumo</p>
                <dl class="mt-6 space-y-3 text-sm text-ink/70">
                    <div class="flex justify-between"><dt>Itens</dt><dd>{{ $cart->items->count() }}</dd></div>
                    <div class="flex justify-between"><dt>Subtotal</dt><dd>R$ {{ number_format((float) $subtotal, 2, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt>Frete estimado</dt><dd>{{ $subtotal >= 350 ? 'Gratis' : 'R$ 24,90' }}</dd></div>
                </dl>
                <div class="mt-8">
                    @auth
                        <a href="{{ route('checkout.create') }}" class="block rounded-full bg-ink px-6 py-3 text-center text-sm font-bold uppercase tracking-[0.2em] text-white">Ir para o checkout</a>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-full bg-ink px-6 py-3 text-center text-sm font-bold uppercase tracking-[0.2em] text-white">Entrar para finalizar</a>
                    @endauth
                </div>
            </aside>
        </div>
    </section>
@endsection
