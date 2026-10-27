@extends('layouts.app')
@section('title', $definition['title'])
@section('content')
@php($createLabel=match($resource){'regioes'=>'Nova região','pontos'=>'Novo ponto','vendedores'=>'Novo vendedor','veiculos'=>'Novo veículo','utilizacoes'=>'Nova utilização','clientes'=>'Novo cliente','produtos'=>'Novo produto'})
<div class="page-heading">
    <div><div class="page-kicker">Cadastros</div><h1>{{ $definition['title'] }}</h1><p>Consulte e mantenha os registros do sistema.</p></div>
    <a href="{{ route('resources.create',$resource) }}" class="btn btn-success">＋ {{ $createLabel }}</a>
</div>
<div class="card"><div class="card-body p-0">
    @if(in_array($resource,['regioes','pontos','vendedores','veiculos','clientes','produtos']))
    <form method="get" role="search" class="p-3 border-bottom d-flex gap-2"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar registros..." aria-label="Buscar registros"><button class="btn btn-outline-secondary">Buscar</button>@if(request('q'))<a href="{{ route('resources.index',$resource) }}" class="btn btn-light">Limpar</a>@endif</form>
    @endif
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr>
        @foreach($definition['fields'] as $field=>$meta)<th>{{ $meta[0] }}</th>@endforeach
        @if(in_array($resource,['pontos','vendedores']))<th>Região</th>@endif
        @if($resource==='utilizacoes')<th>Modelo</th><th>Vendedor</th>@endif
        <th class="text-end">Ações</th></tr></thead><tbody>
        @forelse($records as $record)
            <tr>
            @foreach($definition['fields'] as $field=>$meta)
                <td>@if($field==='ativo')<span class="badge {{ $record->ativo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $record->ativo ? 'Ativo' : 'Inativo' }}</span>
                    @elseif($field==='cpf'){{ substr($record->$field,0,3) }}.{{ substr($record->$field,3,3) }}.{{ substr($record->$field,6,3) }}-{{ substr($record->$field,9,2) }}
                    @elseif($field==='data_utilizacao'){{ \Illuminate\Support\Carbon::parse($record->$field)->format('d/m/Y') }}
                    @else{{ $record->$field }}@endif</td>
            @endforeach
            @if(in_array($resource,['pontos','vendedores']))<td>{{ $record->regiao ?? '—' }}</td>@endif
            @if($resource==='utilizacoes')<td>{{ $record->modelo }}</td><td>{{ $record->nome_vendedor }}</td>@endif
            <td class="text-end text-nowrap">
                @if($resource==='utilizacoes')
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('resources.edit',['resource'=>$resource,'id'=>$record->placa_veiculo,'data'=>$record->data_utilizacao]) }}">Editar</a>
                    <form method="post" class="d-inline" action="{{ route('resources.destroy',['resource'=>$resource,'id'=>$record->placa_veiculo,'data'=>$record->data_utilizacao]) }}" onsubmit="return confirm('Excluir esta utilização?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                @else
                    @if($resource==='veiculos')
                        <a class="btn btn-sm btn-light" href="{{ route('reports.show',['report'=>'h','placa'=>$record->placa_veiculo]) }}">Histórico</a>
                    @endif
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('resources.edit',['resource'=>$resource,'id'=>$record->{$definition['key']}]) }}">Editar</a>
                    <form method="post" class="d-inline" action="{{ route('resources.destroy',['resource'=>$resource,'id'=>$record->{$definition['key']}]) }}" onsubmit="return confirm('Excluir este registro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                @endif
            </td></tr>
        @empty<tr><td colspan="{{ count($definition['fields'])+3 }}" class="text-center py-5"><div class="fw-semibold">Nenhum registro encontrado</div><div class="text-muted small mt-1">Adicione um cadastro ou ajuste sua busca.</div></td></tr>@endforelse
    </tbody></table></div>
    @if($records->hasPages())<div class="p-3">{{ $records->links() }}</div>@endif
</div></div>
@endsection
