@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-2xl px-6 py-14">
        <div class="rounded-[2rem] border border-black/10 bg-white p-8 shadow-vinyl">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Acesso do Cliente</p>
            <h1 class="mt-3 text-4xl font-black">Entre para continuar sua colecao.</h1>

            <form method="post" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Senha</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                </div>
                <button class="w-full rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Entrar</button>
            </form>
        </div>
    </section>
@endsection
