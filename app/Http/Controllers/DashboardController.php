<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $greeting = match (true) {
            now()->hour < 12 => 'Bom dia',
            now()->hour < 18 => 'Boa tarde',
            default => 'Boa noite',
        };

        $stats = [
            'clientes' => DB::table('Cliente')->count(),
            'vendedores' => DB::table('Vendedor')->count(),
            'produtos' => DB::table('Produto')->where('ativo', 1)->count(),
            'notas' => DB::table('Nota_Fiscal')->count(),
        ];

        $notas = DB::table('Nota_Fiscal as nf')
            ->join('Cliente as c', 'c.cod_cliente', '=', 'nf.cod_cliente')
            ->join('Vendedor as v', 'v.cod_vendedor', '=', 'nf.cod_vendedor')
            ->select('nf.cod_nota', 'nf.data_emissao', 'c.nome_cliente', 'v.nome_vendedor')
            ->orderByDesc('nf.data_emissao')
            ->orderByDesc('nf.cod_nota')
            ->limit(6)
            ->get();

        return view('dashboard', compact('greeting', 'stats', 'notas'));
    }
}
