@extends('layouts.app')
@section('title', 'Consulta '.strtoupper($report))
@section('content')
<div class="page-heading">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted small">← Relatórios</a>
        <div class="page-kicker mt-3">Consulta {{ strtoupper($report) }}</div>
        <h1>{{ $title }}</h1>
        <p>Resultado consultado diretamente no banco de dados.</p>
    </div>
</div>
@if(in_array($report,['c','e','f','h']))
<form method="get" class="card mb-3">
    <div class="card-body d-flex flex-wrap align-items-end gap-3">
        @if($report==='c')
            <div class="flex-grow-1"><label class="form-label fw-semibold" for="data_inicio">De</label><input class="form-control" type="date" id="data_inicio" name="data_inicio" value="{{ $filters['data_inicio'] }}"></div>
            <div class="flex-grow-1"><label class="form-label fw-semibold" for="data_fim">Até</label><input class="form-control" type="date" id="data_fim" name="data_fim" value="{{ $filters['data_fim'] }}">@error('data_fim')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        @elseif($report==='e')
            <div class="flex-grow-1"><label class="form-label fw-semibold" for="cod_vendedor">Vendedor</label><select class="form-select" id="cod_vendedor" name="cod_vendedor"><option value="">Todos os vendedores</option>@foreach($filters['vendedores'] as $vendedor)<option value="{{ $vendedor->cod_vendedor }}" @selected($filters['selected_seller']==$vendedor->cod_vendedor)>{{ $vendedor->nome_vendedor }}</option>@endforeach</select></div>
        @elseif($report==='f')
            <div class="flex-grow-1"><label class="form-label fw-semibold" for="cod_produto">Produto</label><select class="form-select" id="cod_produto" name="cod_produto"><option value="">Todos os produtos</option>@foreach($filters['produtos'] as $produto)<option value="{{ $produto->cod_produto }}" @selected($filters['selected_product']==$produto->cod_produto)>{{ $produto->nome_produto }}</option>@endforeach</select></div>
        @else
            <div class="flex-grow-1"><label class="form-label fw-semibold" for="placa">Veículo</label><select class="form-select" id="placa" name="placa"><option value="">Todos os veículos</option>@foreach($filters['veiculos'] as $veiculo)<option value="{{ $veiculo->placa_veiculo }}" @selected($filters['selected_plate']==$veiculo->placa_veiculo)>{{ $veiculo->placa_veiculo }} · {{ $veiculo->modelo }}</option>@endforeach</select></div>
        @endif
        <button class="btn btn-success">Filtrar</button><a class="btn btn-light" href="{{ route('reports.show',$report) }}">Limpar</a>
    </div>
</form>
@endif
@php($columns=match($report){'a'=>['nome_regiao'=>'Região','nome_ponto'=>'Ponto estratégico'],'b'=>['nome_regiao'=>'Região'],'c'=>['nome_vendedor'=>'Vendedor','modelo'=>'Veículo','placa_veiculo'=>'Placa','data_utilizacao'=>'Data de utilização'],'d'=>['nome_vendedor'=>'Vendedor'],'e'=>['nome_vendedor'=>'Vendedor','nome_produto'=>'Produto'],'f'=>['nome_produto'=>'Produto','nome_vendedor'=>'Vendedor'],'g'=>['cod_produto'=>'Código','nome_produto'=>'Produto','ativo'=>'Situação'],'h'=>['placa_veiculo'=>'Placa','modelo'=>'Modelo','data_utilizacao'=>'Data de utilização','nome_vendedor'=>'Vendedor'],'i'=>['cod_nota'=>'Nota fiscal','data_emissao'=>'Data de emissão','qtd_itens_distintos'=>'Itens distintos','total_unidades'=>'Total de unidades']})
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr>@foreach($columns as $label)<th>{{ $label }}</th>@endforeach</tr></thead><tbody>
@php($previousRegion = null)
@forelse($rows as $row)
    @if($report === 'd' && $previousRegion !== $row->nome_regiao)
        <tr class="region-group"><th scope="rowgroup" colspan="{{ count($columns) }}">{{ $row->nome_regiao }}</th></tr>
        @php($previousRegion = $row->nome_regiao)
    @endif
    <tr>
        @if($report === 'd')
            <td>{{ $row->nome_vendedor ?? 'Sem vendedores cadastrados' }}</td>
        @elseif($report === 'c' && ! $row->placa_veiculo)
            <td>{{ $row->nome_vendedor }}</td><td colspan="3"><span class="no-activity">Nenhuma utilização no período selecionado</span></td>
        @else
            @foreach($columns as $field=>$label)
                <td>@if($field==='data_utilizacao'||$field==='data_emissao'){{ $row->$field ? \Illuminate\Support\Carbon::parse($row->$field)->format('d/m/Y') : '—' }}@elseif($field==='ativo')<span class="badge {{ $row->ativo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $row->ativo ? 'Ativo' : 'Inativo' }}</span>@else{{ $row->$field ?? '—' }}@endif</td>
            @endforeach
        @endif
    </tr>
@empty
    <tr><td colspan="{{ count($columns) }}" class="text-center py-5"><div class="fw-semibold">Nenhum resultado encontrado</div><div class="text-muted small mt-1">Não há dados para os filtros escolhidos.</div></td></tr>
@endforelse
</tbody></table></div></div>
@endsection
