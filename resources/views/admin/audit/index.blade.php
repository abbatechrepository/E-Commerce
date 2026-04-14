@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-olive">Auditoria Administrativa</p>
            <h1 class="mt-2 text-4xl font-black">Linha do tempo das operacoes sensiveis</h1>
        </div>

        <div class="space-y-4">
            @foreach ($auditLogs as $audit)
                <article class="rounded-[1.5rem] bg-white p-6 shadow-vinyl">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-black">{{ $audit->action }}</p>
                            <p class="text-sm text-ink/60">{{ class_basename($audit->entity_type) }} #{{ $audit->entity_id }}</p>
                        </div>
                        <div class="text-right text-sm text-ink/60">
                            <p>{{ $audit->user?->name ?: 'Sistema' }}</p>
                            <p>{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    @if ($audit->context)
                        <pre class="mt-4 overflow-x-auto rounded-xl bg-sand p-4 text-xs text-ink/70">{{ json_encode($audit->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $auditLogs->links() }}</div>
    </section>
@endsection
