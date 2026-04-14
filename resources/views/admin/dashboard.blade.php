@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Painel Administrativo</p>
            <h1 class="mt-2 text-4xl font-black">Centro operacional para catalogo, pedidos e retencao.</h1>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Produtos</p>
                <p class="mt-4 text-4xl font-black">{{ $metrics['products'] }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Clientes</p>
                <p class="mt-4 text-4xl font-black">{{ $metrics['customers'] }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Pedidos</p>
                <p class="mt-4 text-4xl font-black">{{ $metrics['orders'] }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Receita</p>
                <p class="mt-4 text-4xl font-black">R$ {{ number_format((float) $metrics['revenue'], 2, ',', '.') }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-olive">Pagamentos Pendentes</p>
                <p class="mt-4 text-4xl font-black">{{ $metrics['pending_payments'] }}</p>
            </article>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_0.95fr]">
            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-black">Pedidos Recentes</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-rust">Abrir fila</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-black/10 text-xs uppercase tracking-[0.25em] text-ink/50">
                                <th class="pb-4">Pedido</th>
                                <th class="pb-4">Status</th>
                                <th class="pb-4">Total</th>
                                <th class="pb-4">Criado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr class="border-b border-black/5">
                                    <td class="py-4 font-semibold">{{ $order->order_number }}</td>
                                    <td class="py-4">{{ $order->status?->label() }}</td>
                                    <td class="py-4">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                                    <td class="py-4">{{ optional($order->placed_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-8">
                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-black">Webhooks Recentes</h2>
                        <a href="{{ route('admin.payments.index') }}" class="text-sm font-semibold text-rust">Abrir pagamentos</a>
                    </div>
                    <div class="space-y-4">
                        @foreach ($recentWebhooks as $webhook)
                            <div class="rounded-2xl border border-black/10 px-5 py-4">
                                <p class="font-bold">{{ $webhook->event_type }}</p>
                                <p class="mt-1 text-sm text-ink/60">{{ $webhook->payment?->order?->order_number ?: 'Nao vinculado' }}</p>
                                <p class="text-xs text-ink/50">{{ optional($webhook->created_at)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-black">Eventos de Auditoria Recentes</h2>
                        <a href="{{ route('admin.audit.index') }}" class="text-sm font-semibold text-rust">Abrir trilha de auditoria</a>
                    </div>
                    <div class="space-y-4">
                        @foreach ($recentAudits as $audit)
                            <div class="rounded-2xl border border-black/10 px-5 py-4">
                                <p class="font-bold">{{ $audit->action }}</p>
                                <p class="mt-1 text-sm text-ink/60">{{ $audit->user?->name ?: 'Sistema' }}</p>
                                <p class="text-xs text-ink/50">{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
