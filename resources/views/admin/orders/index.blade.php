@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Pedidos Administrativos</p>
            <h1 class="mt-2 text-4xl font-black">Fila operacional de pedidos</h1>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] bg-white shadow-vinyl">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-sand/70 text-xs uppercase tracking-[0.25em] text-ink/50">
                    <tr>
                        <th class="px-6 py-4">Pedido</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Pagamento</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-t border-black/5">
                            <td class="px-6 py-4">
                                <p class="font-bold">{{ $order->order_number }}</p>
                                <p class="text-xs text-ink/50">{{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $order->customer?->user?->name }}</td>
                            <td class="px-6 py-4"><span class="rounded-full bg-olive/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">{{ $order->status?->label() }}</span></td>
                            <td class="px-6 py-4">{{ $order->payment?->status?->label() }}</td>
                            <td class="px-6 py-4">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold text-rust">Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    </section>
@endsection
