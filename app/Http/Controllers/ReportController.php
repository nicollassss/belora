<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const REPORTS = [
        'a' => 'Pontos estratégicos por região',
        'b' => 'Regiões cadastradas',
        'c' => 'Veículos utilizados no último mês',
        'd' => 'Vendedores por região',
        'e' => 'Produtos vendidos por vendedor',
        'f' => 'Vendedores por produto',
        'g' => 'Produtos nunca vendidos',
        'h' => 'Histórico de utilização de veículo',
        'i' => 'Itens por nota fiscal',
    ];

    public function index(): View
    {
        return view('reports.index', ['reports' => self::REPORTS]);
    }

    public function show(Request $request, string $report): View
    {
        abort_unless(isset(self::REPORTS[$report]), 404);
        $filters = [];
        if ($report === 'c') {
            $dates = $request->validate([
                'data_inicio' => ['nullable', 'date'],
                'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            ]);
            $filters['data_inicio'] = $dates['data_inicio']
                ?? (isset($dates['data_fim'])
                    ? Carbon::parse($dates['data_fim'])->subMonth()->toDateString()
                    : now()->subMonth()->toDateString());
            $filters['data_fim'] = $dates['data_fim'] ?? now()->toDateString();
        }

        $rows = match ($report) {
            'a' => DB::table('Regiao as r')->leftJoin('Ponto_Estrategico as p', 'p.cod_regiao', '=', 'r.cod_regiao')->select('r.nome_regiao', 'p.nome_ponto')->orderBy('r.nome_regiao')->orderBy('p.nome_ponto')->get(),
            'b' => DB::table('Regiao')->select('nome_regiao')->orderBy('nome_regiao')->get(),
            'c' => $this->vehicleUsage($filters['data_inicio'], $filters['data_fim']),
            'd' => DB::table('Regiao as r')->leftJoin('Vendedor as v', 'v.cod_regiao', '=', 'r.cod_regiao')->select('r.nome_regiao', 'v.nome_vendedor')->orderBy('r.nome_regiao')->orderBy('v.nome_vendedor')->get(),
            'e' => $this->sellerProducts($request, $filters),
            'f' => $this->productSellers($request, $filters),
            'g' => DB::table('Produto as p')->whereNotExists(fn ($q) => $q->selectRaw('1')->from('Nota_Fiscal_Produto as nfp')->whereColumn('nfp.cod_produto', 'p.cod_produto'))->select('p.cod_produto', 'p.nome_produto', 'p.ativo')->orderBy('p.nome_produto')->get(),
            'h' => $this->vehicleHistory($request, $filters),
            'i' => DB::table('Nota_Fiscal as nf')->leftJoin('Nota_Fiscal_Produto as nfp', 'nfp.cod_nota', '=', 'nf.cod_nota')->select('nf.cod_nota', 'nf.data_emissao', DB::raw('COUNT(nfp.cod_produto) as qtd_itens_distintos'), DB::raw('COALESCE(SUM(nfp.quantidade), 0) as total_unidades'))->groupBy('nf.cod_nota', 'nf.data_emissao')->orderBy('nf.cod_nota')->get(),
        };

        return view('reports.show', [
            'report' => $report,
            'title' => self::REPORTS[$report],
            'rows' => $rows,
            'filters' => $filters,
        ]);
    }

    private function vehicleUsage(string $startDate, string $endDate): Collection
    {
        return DB::table('Vendedor as v')
            ->leftJoin('Vendedor_Veiculo as vv', function ($join) use ($startDate, $endDate): void {
                $join->on('vv.cod_vendedor', '=', 'v.cod_vendedor')
                    ->whereBetween('vv.data_utilizacao', [$startDate, $endDate]);
            })
            ->leftJoin('Veiculo as ve', 've.placa_veiculo', '=', 'vv.placa_veiculo')
            ->select('v.nome_vendedor', 've.modelo', 'vv.placa_veiculo', 'vv.data_utilizacao')
            ->orderBy('v.nome_vendedor')
            ->orderBy('vv.data_utilizacao')
            ->get();
    }

    private function sellerProducts(Request $request, array &$filters): Collection
    {
        $filters['vendedores'] = DB::table('Vendedor')->orderBy('nome_vendedor')->get();
        $seller = $request->integer('cod_vendedor');
        $filters['selected_seller'] = $seller;

        return DB::table('Vendedor as v')->join('Nota_Fiscal as nf', 'nf.cod_vendedor', '=', 'v.cod_vendedor')->join('Nota_Fiscal_Produto as nfp', 'nfp.cod_nota', '=', 'nf.cod_nota')->join('Produto as p', 'p.cod_produto', '=', 'nfp.cod_produto')->when($seller, fn ($q) => $q->where('v.cod_vendedor', $seller))->select('v.nome_vendedor', 'p.nome_produto')->distinct()->orderBy('p.nome_produto')->get();
    }

    private function productSellers(Request $request, array &$filters): Collection
    {
        $filters['produtos'] = DB::table('Produto')->orderBy('nome_produto')->get();
        $product = $request->integer('cod_produto');
        $filters['selected_product'] = $product;

        return DB::table('Produto as p')->join('Nota_Fiscal_Produto as nfp', 'nfp.cod_produto', '=', 'p.cod_produto')->join('Nota_Fiscal as nf', 'nf.cod_nota', '=', 'nfp.cod_nota')->join('Vendedor as v', 'v.cod_vendedor', '=', 'nf.cod_vendedor')->when($product, fn ($q) => $q->where('p.cod_produto', $product))->select('p.nome_produto', 'v.nome_vendedor')->distinct()->orderBy('v.nome_vendedor')->get();
    }

    private function vehicleHistory(Request $request, array &$filters): Collection
    {
        $filters['veiculos'] = DB::table('Veiculo')->orderBy('placa_veiculo')->get();
        $plate = $request->string('placa')->toString();
        $filters['selected_plate'] = $plate;

        return DB::table('Veiculo as ve')->join('Vendedor_Veiculo as vv', 'vv.placa_veiculo', '=', 've.placa_veiculo')->join('Vendedor as v', 'v.cod_vendedor', '=', 'vv.cod_vendedor')->when($plate, fn ($q) => $q->where('ve.placa_veiculo', $plate))->select('ve.placa_veiculo', 've.modelo', 'vv.data_utilizacao', 'v.nome_vendedor')->orderByDesc('vv.data_utilizacao')->get();
    }
}
