<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183c35">
    <title>@yield('title', 'Belora') · Gestão comercial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/belora.css') }}" rel="stylesheet">
</head>
<body>
@php($navigation = [
    'Visão geral' => [
        ['label' => 'Painel', 'route' => 'dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Notas fiscais', 'route' => 'invoices.*', 'href' => route('invoices.index'), 'active' => request()->routeIs('invoices.*')],
        ['label' => 'Relatórios', 'route' => 'reports.*', 'href' => route('reports.index'), 'active' => request()->routeIs('reports.*')],
    ],
    'Cadastros' => collect([
        ['regioes', 'Regiões'],
        ['pontos', 'Pontos estratégicos'],
        ['vendedores', 'Vendedores'],
        ['veiculos', 'Veículos'],
        ['utilizacoes', 'Utilização de veículos'],
        ['clientes', 'Clientes'],
        ['produtos', 'Produtos'],
    ])->map(fn ($entry) => [
        'label' => $entry[1],
        'route' => null,
        'href' => route('resources.index', $entry[0]),
        'active' => request()->route('resource') === $entry[0],
    ]),
])
<div class="layout-shell">
    <aside class="sidebar">
        <a class="brand-lockup" href="{{ route('dashboard') }}" aria-label="Belora — início">
            <span class="brand-mark" aria-hidden="true">B</span>
            <span class="brand-word">belora<span class="brand-period">.</span></span>
        </a>
        <div class="brand-caption"></div>

        @foreach($navigation as $group => $links)
            <div class="nav-group">
                <div class="nav-label">{{ $group }}</div>
                <nav class="sidebar-nav" aria-label="{{ $group }}">
                    @foreach($links as $link)
                        <a class="nav-link {{ $link['active'] ? 'active' : '' }}" href="{{ $link['href'] }}" @if($link['active']) aria-current="page" @endif>
                            @if($group === 'Visão geral')
                                <span class="nav-icon" aria-hidden="true">
                                    @if($link['label'] === 'Painel')
                                        <svg viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                                    @elseif($link['label'] === 'Notas fiscais')
                                        <svg viewBox="0 0 24 24"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/><path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24"><path d="M4 19.5V12m5 7.5V5m5 14.5V9m5 10.5V3.5"/></svg>
                                    @endif
                                </span>
                            @else
                                <span class="nav-dot" aria-hidden="true"></span>
                            @endif
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        @endforeach

        <div class="sidebar-note">
            <span class="status-orb" aria-hidden="true"></span>
            <span><strong>Área administrativa</strong><small>Gestão comercial</small></span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="workspace-bar">
            <div>
                <div class="eyebrow">INDÚSTRIA BELEZA <span>/</span> OPERAÇÃO COMERCIAL</div>
                <div class="workspace-title">@yield('title', 'Painel')</div>
            </div>
            <div class="workspace-date"><span class="date-mark" aria-hidden="true"></span>{{ now()->format('d/m/Y') }}</div>
        </header>

        <div class="page-content">
            @if(session('success'))<div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger border-0 rounded-3">{{ session('error') }}</div>@endif
            @if($errors->has('database'))<div class="alert alert-danger border-0 rounded-3">{{ $errors->first('database') }}</div>@endif
            @yield('content')
            <footer class="page-footer"><span>Belora</span><span>Um olhar cuidadoso sobre cada detalhe.</span></footer>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
