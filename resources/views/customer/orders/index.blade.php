@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Pedidos</p>
                <h1 class="mt-2 text-4xl font-black">Seu historico de compras</h1>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-rust">Voltar para a conta</a>
        </div>

        <div class="space-y-4">
            @forelse ($orders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-4 rounded-[1.5rem] bg-white px-6 py-5 shadow-vinyl">
                    <div>
                        <p class="font-black">{{ $order->order_number }}</p>
                        <p class="text-sm text-ink/60">{{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-olive">{{ $order->status->label() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
                        <p class="text-sm text-ink/60">{{ $order->items->count() }} item(ns)</p>
                    </div>
                </a>
            @empty
                <div class="rounded-[1.5rem] bg-white px-6 py-5 shadow-vinyl">
                    <p class="text-sm text-ink/60">Nenhum pedido por enquanto.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    </section>
@endsection
