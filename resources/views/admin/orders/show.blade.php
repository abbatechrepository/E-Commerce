@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Pedidos Administrativos</p>
                <h1 class="mt-2 text-4xl font-black">{{ $order->order_number }}</h1>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-rust">Voltar para pedidos</a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Itens</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($order->items as $item)
                            <div class="rounded-2xl border border-black/10 px-5 py-4">
                                <div class="flex justify-between gap-4">
                                    <div>
                                        <p class="font-bold">{{ $item->album_title_snapshot ?: $item->product_name_snapshot }}</p>
                                        <p class="text-sm text-ink/60">{{ $item->artist_name_snapshot }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</p>
                                        <p class="text-sm text-ink/60">{{ $item->quantity }}x</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Historico de status</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($order->statusHistory->sortByDesc('created_at') as $history)
                            <div class="rounded-2xl border border-black/10 px-5 py-4">
                                <p class="font-bold uppercase text-olive">{{ $history->to_status->label() }}</p>
                                <p class="text-sm text-ink/60">{{ optional($history->created_at)->format('d/m/Y H:i') }}</p>
                                <p class="mt-2 text-sm text-ink/70">{{ $history->reason ?: 'Nenhum motivo informado' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Resumo comercial</h2>
                    <dl class="mt-6 space-y-3 text-sm text-ink/70">
                        <div class="flex justify-between"><dt>Cliente</dt><dd>{{ $order->customer?->user?->name }}</dd></div>
                        <div class="flex justify-between"><dt>Email</dt><dd>{{ $order->customer?->user?->email }}</dd></div>
                        <div class="flex justify-between"><dt>Status</dt><dd class="font-semibold uppercase">{{ $order->status->label() }}</dd></div>
                        <div class="flex justify-between"><dt>Subtotal</dt><dd>R$ {{ number_format((float) $order->subtotal, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt>Desconto</dt><dd>R$ {{ number_format((float) $order->discount_total, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt>Frete</dt><dd>R$ {{ number_format((float) $order->shipping_total, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between border-t border-black/10 pt-3 text-base font-black text-ink"><dt>Total</dt><dd>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Pagamento e entrega</h2>
                    <dl class="mt-6 space-y-3 text-sm text-ink/70">
                        <div class="flex justify-between"><dt>Status do pagamento</dt><dd class="font-semibold uppercase">{{ $order->payment?->status?->label() }}</dd></div>
                        <div class="flex justify-between"><dt>Provedor</dt><dd>{{ $order->payment?->provider }}</dd></div>
                        <div class="flex justify-between"><dt>Status da entrega</dt><dd class="font-semibold uppercase">{{ $order->shipment?->status?->label() }}</dd></div>
                        <div class="flex justify-between"><dt>Rastreio</dt><dd>{{ $order->shipment?->tracking_code ?: 'Aguardando envio' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </section>
@endsection
