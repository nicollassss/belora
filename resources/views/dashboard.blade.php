@extends('layouts.app')
@section('title', 'Painel')
@section('content')
<div class="dashboard-hero">
    <div class="dashboard-hero-copy">
        <h1>{{ $greeting }}, equipe.</h1>
        <p>Esse é o seu painel de controle. Aqui você pode acessar todas as funcionalidades do sistema.</p>
    </div>
    <div class="hero-date"><small>Hoje</small><strong>{{ now()->format('d.m.Y') }}</strong></div>
    <a class="btn" href="{{ route('invoices.create') }}">＋ <span>Emitir nota fiscal</span></a>
</div>
<div class="row g-3 mb-4">
    @foreach([['Clientes', $stats['clientes'], 'Base de clientes', '#a77a68'],['Vendedores',$stats['vendedores'],'Equipe comercial','#758d73'],['Produtos ativos',$stats['produtos'],'Disponíveis para venda','#b39a62'],['Notas fiscais',$stats['notas'],'Documentos emitidos','#778d9b']] as [$label,$value,$hint,$accent])
        <div class="col-6 col-xl-3"><div class="card stat-card" style="--stat-accent: {{ $accent }}"><div class="stat-topline"><div class="stat-label">{{ $label }}</div><span class="stat-symbol" aria-hidden="true">
            @if($label==='Clientes')
                <svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3.5 19v-1.2A4.8 4.8 0 0 1 8.3 13h1.4a4.8 4.8 0 0 1 4.8 4.8V19"/><path d="M15 5.3a3 3 0 0 1 0 5.5M17 13.4a4.7 4.7 0 0 1 3.5 4.5V19"/></svg>
            @elseif($label==='Vendedores')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path d="M5 19v-1a7 7 0 0 1 14 0v1z"/></svg>
            @elseif($label==='Produtos ativos')
                <svg viewBox="0 0 24 24"><path d="M12 3.5c1.7 2.2 3.4 3.1 5.3 2.9-.2 1.9.7 3.6 2.9 5.3-2.2 1.7-3.1 3.4-2.9 5.3-1.9-.2-3.6.7-5.3 2.9-1.7-2.2-3.4-3.1-5.3-2.9.2-1.9-.7-3.6-2.9-5.3C6 10 6.9 8.3 6.7 6.4 8.6 6.6 10.3 5.7 12 3.5Z"/><circle cx="12" cy="11.7" r="2.1"/></svg>
            @else
                <svg viewBox="0 0 24 24"><path d="M7 3.8h7l4 4v12.4H7a2 2 0 0 1-2-2V5.8a2 2 0 0 1 2-2Z"/><path d="M14 3.8v4h4M8.5 12h7M8.5 15.5h7"/></svg>
            @endif
        </span></div><div class="stat-value">{{ number_format($value) }}</div><div class="stat-hint">{{ $hint }}</div></div></div>
    @endforeach
</div>
<div class="row g-3">
    <div class="col-xl-8"><div class="card h-100"><div class="card-body p-0"><div class="d-flex justify-content-between align-items-center p-4"><div><h2 class="section-title mb-1">Notas recentes</h2><p class="section-caption mb-0">Últimos documentos registrados</p></div><a class="btn btn-sm btn-outline-secondary" href="{{ route('invoices.index') }}">Ver todas</a></div>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Nota</th><th>Cliente</th><th>Vendedor</th><th>Emissão</th><th></th></tr></thead><tbody>
        @forelse($notas as $nota)<tr><td class="fw-semibold">#{{ $nota->cod_nota }}</td><td>{{ $nota->nome_cliente }}</td><td>{{ $nota->nome_vendedor }}</td><td>{{ \Illuminate\Support\Carbon::parse($nota->data_emissao)->format('d/m/Y') }}</td><td><a href="{{ route('invoices.show',$nota->cod_nota) }}" class="btn btn-sm btn-light">Ver detalhes</a></td></tr>
        @empty<tr><td colspan="5" class="text-center text-muted py-5">Nenhuma nota cadastrada ainda.</td></tr>@endforelse
        </tbody></table></div></div></div></div>
    <div class="col-xl-4"><div class="card h-100"><div class="card-body p-4"><h2 class="section-title mb-1">Acesso rápido</h2><p class="section-caption mb-3">Atalhos para as tarefas mais comuns.</p>
        <a class="quick-link mb-2" href="{{ route('resources.create','produtos') }}"><span><strong>Novo produto</strong><small>Atualize o catálogo</small></span><span class="quick-arrow" aria-hidden="true">↗</span></a>
        <a class="quick-link mb-2" href="{{ route('resources.create','utilizacoes') }}"><span><strong>Registrar utilização</strong><small>Atribua veículo à equipe</small></span><span class="quick-arrow" aria-hidden="true">↗</span></a>
        <a class="quick-link" href="{{ route('reports.index') }}"><span><strong>Consultar relatórios</strong><small>Acesse as consultas A–I</small></span><span class="quick-arrow" aria-hidden="true">↗</span></a>
    </div></div></div>
</div>
@endsection
