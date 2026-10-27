<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = DB::table('Nota_Fiscal as nf')
            ->join('Cliente as c', 'c.cod_cliente', '=', 'nf.cod_cliente')
            ->join('Vendedor as v', 'v.cod_vendedor', '=', 'nf.cod_vendedor')
            ->select('nf.*', 'c.nome_cliente', 'v.nome_vendedor')
            ->orderByDesc('nf.data_emissao')->orderByDesc('nf.cod_nota')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        $clients = DB::table('Cliente')->orderBy('nome_cliente')->get();
        $sellers = DB::table('Vendedor')->orderBy('nome_vendedor')->get();
        $products = DB::table('Produto')->where('ativo', 1)->orderBy('nome_produto')->get();

        return view('invoices.create', compact('clients', 'sellers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cod_nota' => ['required', 'integer', 'min:1', 'unique:Nota_Fiscal,cod_nota'],
            'data_emissao' => ['nullable', 'date'],
            'cod_cliente' => ['required', 'integer', 'exists:Cliente,cod_cliente'],
            'cod_vendedor' => ['required', 'integer', 'exists:Vendedor,cod_vendedor'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.cod_produto' => ['required', 'integer', 'distinct', 'exists:Produto,cod_produto'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ]);
        $activeProducts = DB::table('Produto')->where('ativo', 1)->pluck('cod_produto')->map(fn ($id) => (int) $id)->all();
        foreach ($data['itens'] as $index => $item) {
            if (! in_array((int) $item['cod_produto'], $activeProducts, true)) {
                return back()->withErrors(["itens.$index.cod_produto" => 'Escolha um produto ativo.'])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($data): void {
                $invoice = [
                    'cod_nota' => $data['cod_nota'],
                    'cod_cliente' => $data['cod_cliente'],
                    'cod_vendedor' => $data['cod_vendedor'],
                ];
                if (! empty($data['data_emissao'])) {
                    $invoice['data_emissao'] = $data['data_emissao'];
                }
                DB::table('Nota_Fiscal')->insert($invoice);
                $items = array_map(fn ($item) => [
                    'cod_nota' => $data['cod_nota'],
                    'cod_produto' => $item['cod_produto'],
                    'quantidade' => $item['quantidade'],
                ], $data['itens']);
                DB::table('Nota_Fiscal_Produto')->insert($items);
            });
        } catch (QueryException $exception) {
            $errorInfo = $exception->errorInfo ?? [];
            $constraintViolation = ($errorInfo[0] ?? null) === '23000'
                || in_array((int) ($errorInfo[1] ?? 0), [2601, 2627, 547, 50001], true);
            if (! $constraintViolation) {
                throw $exception;
            }

            return back()->withErrors(['database' => 'Não foi possível emitir a nota. Verifique os códigos, as restrições e se todos os produtos continuam ativos.'])->withInput();
        }

        return redirect()->route('invoices.show', $data['cod_nota'])->with('success', 'Nota fiscal emitida com sucesso.');
    }

    public function show(string $id): View
    {
        $invoice = DB::table('Nota_Fiscal as nf')
            ->join('Cliente as c', 'c.cod_cliente', '=', 'nf.cod_cliente')
            ->join('Vendedor as v', 'v.cod_vendedor', '=', 'nf.cod_vendedor')
            ->where('nf.cod_nota', $id)
            ->select('nf.*', 'c.nome_cliente', 'v.nome_vendedor')
            ->first();
        abort_unless($invoice, 404);
        $items = DB::table('Nota_Fiscal_Produto as nfp')
            ->join('Produto as p', 'p.cod_produto', '=', 'nfp.cod_produto')
            ->where('nfp.cod_nota', $id)
            ->select('p.nome_produto', 'nfp.quantidade')
            ->orderBy('p.nome_produto')->get();

        return view('invoices.show', compact('invoice', 'items'));
    }
}
