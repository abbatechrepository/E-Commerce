@php
    $editing = $product->exists;
@endphp

<form method="post" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if ($editing)
        @method('put')
    @endif

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold">SKU</label>
            <input name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Nome</label>
            <input name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Slug</label>
            <input name="slug" value="{{ old('slug', $product->slug) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Titulo do album</label>
            <input name="album_title" value="{{ old('album_title', $product->album_title) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Artista</label>
            <select name="artist_id" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                @foreach ($artists as $artist)
                    <option value="{{ $artist->id }}" @selected(old('artist_id', $product->artist_id) == $artist->id)>{{ $artist->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Genero</label>
            <select name="genre_id" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" @selected(old('genre_id', $product->genre_id) == $genre->id)>{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Categoria</label>
            <select name="category_id" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Ano de lancamento</label>
            <input type="number" name="release_year" value="{{ old('release_year', $product->release_year) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Selo</label>
            <input name="label_name" value="{{ old('label_name', $product->label_name) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Pais</label>
            <input name="country" value="{{ old('country', $product->country) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Formato da midia</label>
            <input name="media_format" value="{{ old('media_format', $product->media_format) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Estado do disco</label>
            <input name="disc_condition" value="{{ old('disc_condition', $product->disc_condition) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Estado da capa</label>
            <input name="sleeve_condition" value="{{ old('sleeve_condition', $product->sleeve_condition) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Nivel de raridade</label>
            <input name="rarity_level" value="{{ old('rarity_level', $product->rarity_level) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Status</label>
            <select name="status" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
                @foreach (['draft', 'active', 'inactive', 'unavailable'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $product->status?->value) === $status)>{{ \App\Enums\ProductStatus::from($status)->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Preco</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Preco promocional</label>
            <input type="number" step="0.01" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Preco de custo</label>
            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Quantidade disponivel</label>
            <input type="number" min="0" name="available_quantity" value="{{ old('available_quantity', $product->inventory?->available_quantity ?? 0) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Quantidade reservada</label>
            <input type="number" min="0" name="reserved_quantity" value="{{ old('reserved_quantity', $product->inventory?->reserved_quantity ?? 0) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Quantidade minima</label>
            <input type="number" min="0" name="minimum_quantity" value="{{ old('minimum_quantity', $product->inventory?->minimum_quantity ?? 0) }}" class="w-full rounded-2xl border border-black/10 px-4 py-3">
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold">Descricao</label>
        <textarea name="description" rows="5" class="w-full rounded-2xl border border-black/10 px-4 py-3" required>{{ old('description', $product->description) }}</textarea>
    </div>

    @unless ($editing)
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold">Capa do produto</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                <p class="mt-2 text-sm text-ink/60">Se enviar uma imagem aqui, ela ja sera salva como capa principal do produto.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Texto alternativo da capa</label>
                    <input name="alt_text" value="{{ old('alt_text') }}" class="w-full rounded-2xl border border-black/10 px-4 py-3" placeholder="Ex.: Capa frontal do disco">
                </div>
                <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm">
                    <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary', true))>
                    Definir como imagem principal
                </label>
            </div>
        </div>
    @endunless

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Ativo</label>
        <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Em destaque</label>
        <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm"><input type="checkbox" name="is_rare" value="1" @checked(old('is_rare', $product->is_rare))> Raro</label>
        <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm"><input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival))> Novidade</label>
        <label class="flex items-center gap-3 rounded-2xl bg-sand px-4 py-3 text-sm"><input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller))> Mais vendido</label>
    </div>

    <div class="flex flex-wrap gap-3">
        <button class="rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">{{ $editing ? 'Salvar alteracoes' : 'Criar produto' }}</button>
        @if ($editing)
            <button formaction="{{ route('admin.products.publish', $product) }}" formmethod="post" class="rounded-full border border-rust/25 px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-rust">Publicar</button>
        @endif
    </div>
</form>
