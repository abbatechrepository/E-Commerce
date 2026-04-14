@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Administracao de Usuarios</p>
                <h1 class="mt-2 text-4xl font-black">Gestao de acessos administrativos</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-rust">Voltar para o dashboard</a>
        </div>

        <div class="mb-8 grid gap-4 md:grid-cols-3">
            <article class="rounded-[1.25rem] bg-white p-5 shadow-vinyl">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-olive">Admins</p>
                <p class="mt-3 text-3xl font-black">{{ $roleCounts['admin'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white p-5 shadow-vinyl">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-olive">Managers</p>
                <p class="mt-3 text-3xl font-black">{{ $roleCounts['manager'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white p-5 shadow-vinyl">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-olive">Clientes</p>
                <p class="mt-3 text-3xl font-black">{{ $roleCounts['customer'] }}</p>
            </article>
        </div>

        <div class="mb-8 rounded-[1.5rem] bg-white p-6 shadow-vinyl">
            <form method="get" class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Buscar por nome ou e-mail"
                    class="w-full rounded-2xl border border-black/10 px-4 py-3"
                >
                <select name="role" class="w-full rounded-2xl border border-black/10 px-4 py-3">
                    <option value="">Todos os perfis</option>
                    <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                    <option value="manager" @selected($filters['role'] === 'manager')>Manager</option>
                    <option value="customer" @selected($filters['role'] === 'customer')>Cliente</option>
                </select>
                <button class="rounded-full bg-ink px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Filtrar</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] bg-white shadow-vinyl">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-sand/70 text-xs uppercase tracking-[0.25em] text-ink/50">
                    <tr>
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">E-mail</th>
                        <th class="px-6 py-4">Perfil</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Acao</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t border-black/5">
                            <td class="px-6 py-4 font-semibold">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4 uppercase">{{ $user->role?->value }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] {{ $user->is_active ? 'bg-olive/10 text-olive' : 'bg-rust/10 text-rust' }}">
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="post" action="{{ route('admin.users.status', $user) }}" class="inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                    <button class="rounded-full border border-ink/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] hover:border-rust hover:text-rust">
                                        {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-ink/60">Nenhum usuario encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">{{ $users->links() }}</div>
    </section>
@endsection
