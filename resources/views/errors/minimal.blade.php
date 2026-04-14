<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 text-slate-100">
    <main class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-3xl rounded-3xl border border-white/10 bg-white/5 px-8 py-16 text-center shadow-2xl backdrop-blur">
            <p class="text-sm font-bold uppercase tracking-[0.35em] text-amber-300">E-Commerce</p>
            <div class="mt-8 flex items-center justify-center gap-6 text-slate-300">
                <span class="text-5xl font-black">@yield('code')</span>
                <span class="h-12 w-px bg-white/20"></span>
                <span class="text-xl font-medium">@yield('message')</span>
            </div>
            <a href="{{ url('/') }}" class="mt-10 inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-slate-900">Voltar para a loja</a>
        </div>
    </main>
</body>
</html>
