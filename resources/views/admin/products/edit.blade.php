@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Catalogo Administrativo</p>
                <h1 class="mt-2 text-4xl font-black">Editar {{ $product->album_title }}</h1>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-rust">Voltar para o catalogo</a>
        </div>

        <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
            @include('admin.products._form')
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Midia</p>
                <h2 class="mt-2 text-3xl font-black">Enviar imagens do produto</h2>

                <form method="post" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="mt-8 space-y-4">
                    @csrf
                    <input type="file" name="image" accept="image/*" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                    <input name="alt_text" placeholder="Texto alternativo" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                    <label class="flex items-center gap-3 text-sm">
                        <input type="checkbox" name="is_primary" value="1">
                        Definir como imagem principal
                    </label>
                    <button class="rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Enviar imagem</button>
                </form>
            </div>

            <div class="rounded-[1.75rem] bg-white p-8 shadow-vinyl">
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Galeria</p>
                <h2 class="mt-2 text-3xl font-black">Imagens atuais do produto</h2>

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    @forelse ($product->images()->orderBy('position')->get() as $image)
                        <article class="rounded-[1.5rem] border border-black/10 p-4">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="h-52 w-full rounded-[1rem] object-cover">
                            <div class="mt-4 flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold">{{ $image->alt_text ?: 'Sem texto alternativo' }}</p>
                                    @if ($image->is_primary)
                                        <p class="mt-1 text-xs font-bold uppercase tracking-[0.2em] text-olive">Imagem principal</p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    @if (! $image->is_primary)
                                        <form method="post" action="{{ route('admin.products.images.primary', [$product, $image]) }}">
                                            @csrf
                                            <button class="rounded-full border border-ink/15 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em]">Principal</button>
                                        </form>
                                    @endif
                                    <form method="post" action="{{ route('admin.products.images.destroy', [$product, $image]) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="rounded-full border border-rust/20 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em] text-rust">Excluir</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-ink/60">Nenhuma imagem enviada ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
