@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-3xl px-6 py-14">
        <div class="rounded-[2rem] border border-black/10 bg-white p-8 shadow-vinyl">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Novo Cliente</p>
            <h1 class="mt-3 text-4xl font-black">Crie sua conta para finalizar compras, acompanhar pedidos e voltar a comprar.</h1>

            <form method="post" action="{{ route('register.store') }}" class="mt-8 grid gap-5 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold">Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Data de nascimento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                </div>
                <div></div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Senha</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Confirmar senha</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <label class="md:col-span-2 flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm">
                    <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent'))>
                    Quero receber recomendacoes futuras e promocoes selecionadas.
                </label>
                <div class="md:col-span-2">
                    <button class="w-full rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Criar conta</button>
                </div>
            </form>
        </div>
    </section>
@endsection
