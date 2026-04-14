@extends('layouts.app')

@section('content')
    <section class="mx-auto grid max-w-7xl gap-8 px-6 py-14 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Enderecos</p>
                    <h1 class="mt-2 text-3xl font-black">Enderecos de entrega salvos</h1>
                </div>
                <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-rust">Voltar para a conta</a>
            </div>

            <div class="space-y-4">
                @foreach ($addresses as $address)
                    <article class="rounded-2xl border border-black/10 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold">{{ $address->label ?: 'Endereco' }}</p>
                                <p class="text-sm text-ink/60">{{ $address->recipient_name }}</p>
                            </div>
                            @if ($address->is_default)
                                <span class="rounded-full bg-olive/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">Padrao</span>
                            @endif
                        </div>
                        <p class="mt-4 text-sm text-ink/70">{{ $address->street }}, {{ $address->number }}{{ $address->complement ? ' - '.$address->complement : '' }}</p>
                        <p class="text-sm text-ink/70">{{ $address->district }} - {{ $address->city }}/{{ $address->state }}</p>
                        <p class="text-sm text-ink/70">{{ $address->zip_code }} - {{ $address->country }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Novo endereco</p>
            <h2 class="mt-2 text-3xl font-black">Adicionar novo ponto de entrega</h2>

            <form method="post" action="{{ route('customer.addresses.store') }}" class="mt-8 space-y-4">
                @csrf
                <input name="label" placeholder="Rotulo" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                <input name="recipient_name" placeholder="Nome do destinatario" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                <input name="zip_code" placeholder="CEP" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                <input name="street" placeholder="Rua" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                <div class="grid gap-4 md:grid-cols-2">
                    <input name="number" placeholder="Numero" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                    <input name="complement" placeholder="Complemento" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                </div>
                <input name="district" placeholder="Bairro" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                <div class="grid gap-4 md:grid-cols-2">
                    <input name="city" placeholder="Cidade" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                    <input name="state" placeholder="Estado" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <input name="country" value="Brasil" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                <input name="reference" placeholder="Referencia" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                <label class="flex items-center gap-3 text-sm">
                    <input type="checkbox" name="is_default" value="1">
                    Definir como endereco padrao
                </label>
                <button class="w-full rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Salvar endereco</button>
            </form>
        </div>
    </section>
@endsection
