@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Finalizacao</p>
            <h1 class="mt-2 text-4xl font-black">Confirme endereco, frete e pagamento.</h1>
        </div>

        <form method="post" action="{{ route('checkout.store') }}" class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            @csrf
            <input type="hidden" name="cart_id" value="{{ $cart->id }}">

            <div class="space-y-6">
                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Endereco de entrega</h2>
                    <div class="mt-6 space-y-3">
                        @foreach ($customer->addresses as $address)
                            <label class="flex items-start gap-4 rounded-2xl border border-black/10 px-5 py-4">
                                <input type="radio" name="address_id" value="{{ $address->id }}" @checked($address->is_default || old('address_id') == $address->id) class="mt-1">
                                <span class="text-sm text-ink/70">
                                    <strong class="block text-ink">{{ $address->recipient_name }}</strong>
                                    {{ $address->street }}, {{ $address->number }}{{ $address->complement ? ' - '.$address->complement : '' }}<br>
                                    {{ $address->district }} - {{ $address->city }}/{{ $address->state }}<br>
                                    {{ $address->zip_code }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                    <h2 class="text-2xl font-black">Frete e pagamento</h2>
                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Servico de entrega</label>
                            <select name="shipping_service" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                                <option value="Standard">Padrao</option>
                                <option value="Express">Expresso</option>
                                <option value="Collector Care">Cuidado para Colecionador</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Forma de pagamento</label>
                            <select name="payment_method" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                                <option value="credit_card">Cartao de credito</option>
                                <option value="pix">Pix</option>
                                <option value="bank_slip">Boleto bancario</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Codigo do cupom</label>
                            <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Observacoes</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>
                    </div>
                </div>
            </div>

            <aside class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <h2 class="text-2xl font-black">Resumo do pedido</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($cart->items as $item)
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold">{{ $item->product->album_title }}</p>
                                <p class="text-sm text-ink/60">{{ $item->product->artist?->name }} · {{ $item->quantity }}x</p>
                            </div>
                            <p class="font-semibold">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <dl class="mt-8 space-y-3 border-t border-black/10 pt-6 text-sm text-ink/70">
                    <div class="flex justify-between"><dt>Subtotal</dt><dd>R$ {{ number_format((float) $subtotal, 2, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt>Previsao de frete</dt><dd>{{ $subtotal >= 350 ? 'Gratis' : 'R$ 24,90' }}</dd></div>
                </dl>
                <button class="mt-8 w-full rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Finalizar pedido</button>
            </aside>
        </form>
    </section>
@endsection
