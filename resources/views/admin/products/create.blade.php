@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Catalogo Administrativo</p>
            <h1 class="mt-2 text-4xl font-black">Criar produto</h1>
        </div>

        <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            @include('admin.products._form')
        </div>
    </section>
@endsection
