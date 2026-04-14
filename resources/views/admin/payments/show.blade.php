@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Pagamentos Administrativos</p>
                <h1 class="mt-2 text-4xl font-black">Pagamento #{{ $payment->id }}</h1>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="text-sm font-semibold text-rust">Voltar para pagamentos</a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">
            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <h2 class="text-2xl font-black">Resumo do pagamento</h2>
                <dl class="mt-6 space-y-3 text-sm text-ink/70">
                    <div class="flex justify-between"><dt>Pedido</dt><dd>{{ $payment->order?->order_number }}</dd></div>
                    <div class="flex justify-between"><dt>Status</dt><dd class="font-semibold uppercase">{{ $payment->status?->label() }}</dd></div>
                    <div class="flex justify-between"><dt>Provedor</dt><dd>{{ $payment->provider }}</dd></div>
                    <div class="flex justify-between"><dt>Metodo</dt><dd>{{ $payment->method }}</dd></div>
                    <div class="flex justify-between"><dt>Referencia externa</dt><dd>{{ $payment->external_reference ?: 'Pendente' }}</dd></div>
                    <div class="flex justify-between border-t border-black/10 pt-3 text-base font-black text-ink"><dt>Valor</dt><dd>R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</dd></div>
                </dl>
            </div>

            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <h2 class="text-2xl font-black">Logs de webhook</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($payment->webhookLogs as $log)
                        <div class="rounded-2xl border border-black/10 px-5 py-4">
                            <p class="font-bold">{{ $log->event_type }}</p>
                            <p class="mt-2 text-sm text-ink/60">{{ $log->external_transaction_code }}</p>
                            <pre class="mt-3 overflow-x-auto rounded-xl bg-sand p-4 text-xs text-ink/70">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            <h2 class="text-2xl font-black">Transacoes</h2>
            <div class="mt-6 space-y-4">
                @foreach ($payment->transactions as $transaction)
                    <div class="rounded-2xl border border-black/10 px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="font-bold">{{ $transaction->transaction_code }}</p>
                            <span class="rounded-full bg-olive/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">{{ $transaction->status?->label() }}</span>
                        </div>
                        <p class="mt-2 text-sm text-ink/60">{{ $transaction->provider_message }}</p>
                        <div class="mt-3 grid gap-4 lg:grid-cols-2">
                            <pre class="overflow-x-auto rounded-xl bg-sand p-4 text-xs text-ink/70">{{ json_encode($transaction->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            <pre class="overflow-x-auto rounded-xl bg-sand p-4 text-xs text-ink/70">{{ json_encode($transaction->response_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
