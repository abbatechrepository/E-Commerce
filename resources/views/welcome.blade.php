<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Commerce | Bem-vindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-stone-950 text-stone-100">
    <main class="mx-auto flex min-h-screen max-w-5xl items-center px-6 py-16">
        <section class="w-full rounded-[2rem] border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur">
            <p class="text-sm font-bold uppercase tracking-[0.35em] text-amber-300">E-Commerce</p>
            <h1 class="mt-6 max-w-3xl text-5xl font-black leading-tight">Loja virtual de discos antigos em Laravel 11.</h1>
            <p class="mt-6 max-w-2xl text-lg text-stone-300">A aplicacao principal desta demonstracao esta pronta para navegacao com vitrine, carrinho, checkout, area do cliente, painel administrativo e gateway fake de pagamento.</p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('storefront.home') }}" class="rounded-full bg-white px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-stone-950">Abrir loja</a>
                <a href="{{ route('login') }}" class="rounded-full border border-white/20 px-6 py-3 text-sm font-bold uppercase tracking-[0.2em]">Entrar</a>
            </div>
        </section>
    </main>
</body>
</html>
