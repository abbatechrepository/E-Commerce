<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'E-Commerce | Discos Antigos' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sand: '#efe1d1',
                        ink: '#1f1a17',
                        rust: '#ad5d3d',
                        olive: '#5b6643',
                        gold: '#d3a94e'
                    },
                    boxShadow: {
                        vinyl: '0 18px 40px rgba(31, 26, 23, 0.18)'
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(239,225,209,0.8),_rgba(250,247,242,1)_60%)] text-ink">
    <header class="border-b border-black/10 bg-white/70 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="{{ route('storefront.home') }}" class="text-xl font-black tracking-[0.3em] uppercase">Discos Antigos</a>
            <nav class="flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('storefront.products.index') }}" class="hover:text-rust">Catalogo</a>
                <a href="{{ route('cart.index') }}" class="hover:text-rust">Carrinho</a>
                @auth
                    @if (auth()->user()->customer)
                        <a href="{{ route('customer.dashboard') }}" class="hover:text-rust">Minha Conta</a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-ink/15 px-4 py-2 hover:border-rust hover:text-rust">Admin</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-full border border-ink/15 px-4 py-2 hover:border-rust hover:text-rust">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-rust">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-full border border-ink/15 px-4 py-2 hover:border-rust hover:text-rust">Criar Conta</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('status'))
            <div class="mx-auto max-w-7xl px-6 pt-6">
                <div class="rounded-2xl border border-olive/20 bg-olive/10 px-5 py-4 text-sm font-medium text-olive">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto max-w-7xl px-6 pt-6">
                <div class="rounded-2xl border border-rust/20 bg-rust/10 px-5 py-4 text-sm text-rust">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
