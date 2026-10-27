<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceController extends Controller
{
    private const RESOURCES = [
        'regioes' => ['table' => 'Regiao', 'key' => 'cod_regiao', 'title' => 'Regiões', 'fields' => ['cod_regiao' => ['Código', 'number'], 'nome_regiao' => ['Nome da região', 'text']]],
        'pontos' => ['table' => 'Ponto_Estrategico', 'key' => 'cod_ponto', 'title' => 'Pontos estratégicos', 'fields' => ['cod_ponto' => ['Código', 'number'], 'nome_ponto' => ['Nome do ponto', 'text'], 'cod_regiao' => ['Região', 'select:Regiao:cod_regiao:nome_regiao']]],
        'vendedores' => ['table' => 'Vendedor', 'key' => 'cod_vendedor', 'title' => 'Vendedores', 'fields' => ['cod_vendedor' => ['Código', 'number'], 'nome_vendedor' => ['Nome do vendedor', 'text'], 'cod_regiao' => ['Região', 'select:Regiao:cod_regiao:nome_regiao']]],
        'veiculos' => ['table' => 'Veiculo', 'key' => 'placa_veiculo', 'title' => 'Veículos', 'fields' => ['placa_veiculo' => ['Placa', 'plate'], 'modelo' => ['Modelo', 'text']]],
        'clientes' => ['table' => 'Cliente', 'key' => 'cod_cliente', 'title' => 'Clientes', 'fields' => ['cod_cliente' => ['Código', 'number'], 'nome_cliente' => ['Nome do cliente', 'text'], 'cpf' => ['CPF', 'cpf']]],
        'produtos' => ['table' => 'Produto', 'key' => 'cod_produto', 'title' => 'Produtos', 'fields' => ['cod_produto' => ['Código', 'number'], 'nome_produto' => ['Nome do produto', 'text'], 'ativo' => ['Ativo para venda', 'checkbox']]],
        'utilizacoes' => ['table' => 'Vendedor_Veiculo', 'key' => null, 'title' => 'Utilização de veículos', 'fields' => ['placa_veiculo' => ['Veículo', 'select:Veiculo:placa_veiculo:modelo'], 'data_utilizacao' => ['Data de utilização', 'date'], 'cod_vendedor' => ['Vendedor', 'select:Vendedor:cod_vendedor:nome_vendedor']]],
    ];

    private function definition(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }

    public function index(string $resource): View
    {
        $definition = $this->definition($resource);
        $query = DB::table($definition['table']);

        if ($resource === 'pontos') {
            $query->leftJoin('Regiao', 'Ponto_Estrategico.cod_regiao', '=', 'Regiao.cod_regiao')
                ->select('Ponto_Estrategico.*', 'Regiao.nome_regiao as regiao');
        } elseif ($resource === 'vendedores') {
            $query->leftJoin('Regiao', 'Vendedor.cod_regiao', '=', 'Regiao.cod_regiao')
                ->select('Vendedor.*', 'Regiao.nome_regiao as regiao');
        } elseif ($resource === 'utilizacoes') {
            $query->join('Veiculo', 'Vendedor_Veiculo.placa_veiculo', '=', 'Veiculo.placa_veiculo')
                ->join('Vendedor', 'Vendedor_Veiculo.cod_vendedor', '=', 'Vendedor.cod_vendedor')
                ->select('Vendedor_Veiculo.*', 'Veiculo.modelo', 'Vendedor.nome_vendedor');
        }

        if ($search = request('q')) {
            $searchable = match ($resource) {
                'regioes' => ['nome_regiao'],
                'pontos' => ['nome_ponto'],
                'vendedores' => ['nome_vendedor'],
                'veiculos' => ['placa_veiculo', 'modelo'],
                'clientes' => ['nome_cliente', 'cpf'],
                'produtos' => ['nome_produto'],
                default => [],
            };
            if ($searchable) {
                $query->where(function ($builder) use ($searchable, $search): void {
                    foreach ($searchable as $column) {
                        $builder->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }
        }

        $records = $query->orderBy($definition['key'] ?? 'data_utilizacao', $definition['key'] ? 'asc' : 'desc')
            ->paginate(12)->withQueryString();

        return view('resources.index', compact('resource', 'definition', 'records'));
    }

    public function create(string $resource): View
    {
        $definition = $this->definition($resource);
        $options = $this->formOptions($definition);

        return view('resources.form', compact('resource', 'definition', 'options'))->with('record', null);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource);
        $data = $this->validated($request, $definition, $resource);
        try {
            DB::table($definition['table'])->insert($data);
        } catch (QueryException $exception) {
            if (! $this->isConstraintViolation($exception)) {
                throw $exception;
            }

            return back()->withInput()->with('error', 'Não foi possível salvar. Verifique se o código já existe e se os registros relacionados são válidos.');
        }

        return redirect()->route('resources.index', $resource)->with('success', 'Cadastro realizado com sucesso.');
    }

    public function edit(string $resource, string $id): View
    {
        $definition = $this->definition($resource);
        $record = $this->find($definition, $resource, $id);
        $options = $this->formOptions($definition);

        return view('resources.form', compact('resource', 'definition', 'record', 'options'));
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        $definition = $this->definition($resource);
        $record = $this->find($definition, $resource, $id);
        $data = $this->validated($request, $definition, $resource, $record);
        $query = DB::table($definition['table']);
        $this->whereKey($query, $definition, $resource, $record);
        try {
            $query->update($data);
        } catch (QueryException $exception) {
            if (! $this->isConstraintViolation($exception)) {
                throw $exception;
            }

            return back()->withInput()->with('error', 'Não foi possível atualizar. Verifique se os dados não duplicam outro registro e se as relações são válidas.');
        }

        return redirect()->route('resources.index', $resource)->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $definition = $this->definition($resource);
        $record = $this->find($definition, $resource, $id);
        $query = DB::table($definition['table']);
        $this->whereKey($query, $definition, $resource, $record);

        try {
            $query->delete();
        } catch (QueryException $exception) {
            if (! $this->isConstraintViolation($exception)) {
                throw $exception;
            }

            return back()->with('error', 'Não foi possível excluir: este registro pode estar relacionado a outros dados.');
        }

        return redirect()->route('resources.index', $resource)->with('success', 'Registro excluído com sucesso.');
    }

    private function formOptions(array $definition): array
    {
        $options = [];

        foreach ($definition['fields'] as $name => [, $type]) {
            if (! str_starts_with($type, 'select:')) {
                continue;
            }

            [, $table, $key, $label] = explode(':', $type);
            $options[$name] = DB::table($table)
                ->select($key, $label)
                ->orderBy($label)
                ->get();
        }

        return $options;
    }

    private function find(array $definition, string $resource, string $id): object
    {
        $query = DB::table($definition['table']);
        if ($resource === 'utilizacoes') {
            $query->where('placa_veiculo', request()->route('id', $id))
                ->where('data_utilizacao', request()->query('data'));
            abort_unless(request()->query('data'), 404);
        } else {
            $query->where($definition['key'], $id);
        }
        $record = $query->first();
        abort_unless($record, 404);

        return $record;
    }

    private function whereKey(Builder $query, array $definition, string $resource, object $record): void
    {
        if ($resource === 'utilizacoes') {
            $query->where('placa_veiculo', $record->placa_veiculo)
                ->where('data_utilizacao', $record->data_utilizacao);
        } else {
            $query->where($definition['key'], $record->{$definition['key']});
        }
    }

    private function validated(Request $request, array $definition, string $resource, ?object $record = null): array
    {
        $rules = [];
        foreach ($definition['fields'] as $name => [$label, $type]) {
            if (str_starts_with($type, 'select:')) {
                $parts = explode(':', $type);
                $rules[$name] = ['required', Rule::exists($parts[1], $parts[2])];
            } else {
                $rules[$name] = match ($type) {
                    'number' => ['required', 'integer', 'min:1', Rule::unique($definition['table'], $name)->ignore($record?->{$name}, $name)],
                    'plate' => ['required', 'string', 'size:7', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', Rule::unique($definition['table'], $name)->ignore($record?->{$name}, $name)],
                    'cpf' => ['required', 'digits:11', Rule::unique($definition['table'], $name)->ignore($record?->{$name}, $name)],
                    'checkbox' => ['sometimes', 'boolean'],
                    'date' => ['required', 'date'],
                    default => ['required', 'string', 'max:100', Rule::unique($definition['table'], $name)->ignore($record?->{$name}, $name)],
                };
            }
        }
        $data = $request->validate($rules);
        if (isset($definition['fields']['ativo'])) {
            $data['ativo'] = $request->boolean('ativo');
        }

        return $data;
    }

    private function isConstraintViolation(QueryException $exception): bool
    {
        $errorInfo = $exception->errorInfo ?? [];

        return ($errorInfo[0] ?? null) === '23000'
            || in_array((int) ($errorInfo[1] ?? 0), [2601, 2627, 547], true);
    }
}
