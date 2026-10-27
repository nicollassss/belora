@extends('layouts.app')
@section('title', ($record ? 'Editar ' : 'Novo cadastro · ').$definition['title'])
@section('content')
@php($editing=(bool)$record)
<div class="page-heading"><div><a href="{{ route('resources.index',$resource) }}" class="text-decoration-none text-muted small">← {{ $definition['title'] }}</a><div class="page-kicker mt-3">{{ $definition['title'] }}</div><h1>{{ $editing ? 'Editar cadastro' : 'Novo cadastro' }}</h1><p>{{ $editing ? 'Atualize os dados abaixo.' : 'Preencha os campos para incluir um registro.' }}</p></div></div>
<div class="card"><div class="card-body p-4 p-md-5"><form method="post" action="{{ $editing ? route('resources.update',['resource'=>$resource,'id'=>$resource==='utilizacoes'?$record->placa_veiculo:$record->{$definition['key']},'data'=>$resource==='utilizacoes'?$record->data_utilizacao:null]) : route('resources.store',$resource) }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="row g-4">
    @foreach($definition['fields'] as $name=>[$label,$type])
        <div class="col-md-6"><label class="form-label fw-semibold" for="{{ $name }}">{{ $label }}</label>
        @if(str_starts_with($type,'select:'))
                    @php($parts=explode(':',$type))
                    <select class="form-select @error($name)is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" required><option value="">Selecione</option>@foreach($options[$name] as $option)<option value="{{ $option->{$parts[2]} }}" @selected(old($name,$record?->{$name})==$option->{$parts[2]})>{{ $option->{$parts[3]} }}</option>@endforeach</select>
        @elseif($type==='checkbox')
            <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" role="switch" id="{{ $name }}" name="{{ $name }}" value="1" @checked(old($name,$record?->{$name} ?? 1))><label class="form-check-label" for="{{ $name }}">Disponível para novas vendas</label></div>
        @else
            @php($inputType=match($type){'number'=>'number','date'=>'date',default=>'text'})
            <input class="form-control @error($name)is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name,$record?->{$name}) }}" @if($type==='number')min="1" step="1" @elseif($type==='plate')maxlength="7" placeholder="ABC1D23" @elseif($type==='cpf')maxlength="11" inputmode="numeric" placeholder="Somente números" @endif required>
        @endif
        @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if(in_array($name,['cod_regiao','cod_ponto','cod_vendedor','cod_cliente','cod_produto','cod_nota']))<div class="form-text">O código é definido manualmente conforme o banco de dados.</div>@endif
        </div>
    @endforeach
    </div>
    @if($errors->any())<div class="alert alert-danger mt-4 mb-0">Revise os campos destacados e tente novamente.</div>@endif
    <div class="d-flex gap-2 mt-5"><button class="btn btn-success px-4">{{ $editing ? 'Salvar alterações' : 'Salvar cadastro' }}</button><a class="btn btn-light px-4" href="{{ route('resources.index',$resource) }}">Cancelar</a></div>
</form></div></div>
@endsection
