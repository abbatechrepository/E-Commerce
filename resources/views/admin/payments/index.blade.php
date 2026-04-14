@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Pagamentos Administrativos</p>
            <h1 class="mt-2 text-4xl font-black">Operacoes do gateway e visibilidade de webhook</h1>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_0.95fr]">
            <div class="overflow-hidden rounded-[1.75rem] bg-white shadow-vinyl">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-sand/70 text-xs uppercase tracking-[0.25em] text-ink/50">
                        <tr>
                            <th class="px-6 py-4">Pedido</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Provedor</th>
                            <th class="px-6 py-4">Valor</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr class="border-t border-black/5">
                                <td class="px-6 py-4">{{ $payment->order?->order_number }}</td>
                                <td class="px-6 py-4"><span class="rounded-full bg-olive/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">{{ $payment->status?->label() }}</span></td>
                                <td class="px-6 py-4">{{ $payment->provider }}</td>
                                <td class="px-6 py-4">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right"><a href="{{ route('admin.payments.show', $payment) }}" class="text-sm font-semibold text-rust">Inspecionar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <h2 class="text-2xl font-black">Webhooks recentes</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($webhookLogs as $log)
                        <div class="rounded-2xl border border-black/10 px-5 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-bold">{{ $log->event_type }}</p>
                                <span class="text-xs font-bold uppercase tracking-[0.2em] {{ $log->processed ? 'text-olive' : 'text-rust' }}">{{ $log->processed ? 'Processado' : 'Pendente' }}</span>
                            </div>
                            <p class="mt-2 text-sm text-ink/60">{{ $log->payment?->order?->order_number ?: 'Pagamento nao vinculado' }}</p>
                            <p class="text-xs text-ink/50">{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-ink/60">Nenhum log de webhook ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-8">{{ $payments->links() }}</div>
    </section>
@endsection
