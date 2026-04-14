@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Area do Cliente</p>
                <h1 class="mt-2 text-4xl font-black">Bem-vindo de volta, {{ $customer->user->name }}.</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('customer.orders.index') }}" class="rounded-full border border-ink/15 px-5 py-3 text-sm font-bold uppercase tracking-[0.2em]">Meus pedidos</a>
                <a href="{{ route('customer.addresses.index') }}" class="rounded-full bg-ink px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Enderecos</a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Pedidos</p>
                <p class="mt-4 text-4xl font-black">{{ $customer->metric?->total_orders ?? $recentOrders->count() }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Total gasto</p>
                <p class="mt-4 text-4xl font-black">R$ {{ number_format((float) ($customer->metric?->total_spent ?? $recentOrders->sum('total')), 2, ',', '.') }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Ticket medio</p>
                <p class="mt-4 text-4xl font-black">R$ {{ number_format((float) ($customer->metric?->average_ticket ?? $recentOrders->avg('total')), 2, ',', '.') }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Recorrente</p>
                <p class="mt-4 text-4xl font-black">{{ ($customer->metric?->is_recurring ?? false) ? 'Sim' : 'Nao' }}</p>
            </article>
        </div>

        <div class="mt-10 rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-black">Pedidos recentes</h2>
                <a href="{{ route('customer.orders.index') }}" class="text-sm font-semibold text-rust">Ver historico completo</a>
            </div>
            <div class="space-y-4">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" class="flex items-center justify-between rounded-2xl border border-black/10 px-5 py-4 hover:border-rust/30">
                        <div>
                            <p class="font-bold">{{ $order->order_number }}</p>
                            <p class="text-sm text-ink/60">{{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold uppercase text-olive">{{ $order->status->label() }}</p>
                            <p class="text-sm text-ink/60">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-ink/60">Voce ainda nao tem pedidos. Seu proximo disco esta esperando no catalogo.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
