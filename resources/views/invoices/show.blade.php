@extends('layouts.app')

@section('title', 'Nota fiscal ' . $invoice->cod_nota)

@section('content')
    <div class="page-heading">
        <div>
            <a
                href="{{ route('invoices.index') }}"
                class="text-decoration-none text-muted small"
            >
                ← Notas fiscais
            </a>

            <div class="page-kicker mt-3">Documento emitido</div>
            <h1>Nota fiscal #{{ $invoice->cod_nota }}</h1>
            <p>Detalhes da venda e produtos registrados.</p>
        </div>

        <button
            class="btn btn-outline-secondary"
            type="button"
            onclick="window.print()"
        >
            Imprimir
        </button>
    </div>

    <div class="card mb-3">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-muted small">Cliente</div>
                    <div class="fw-semibold mt-1">
                        {{ $invoice->nome_cliente }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Vendedor</div>
                    <div class="fw-semibold mt-1">
                        {{ $invoice->nome_vendedor }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Data de emissão</div>
                    <div class="fw-semibold mt-1">
                        {{ \Illuminate\Support\Carbon::parse($invoice->data_emissao)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="p-4">
                <h2 class="section-title mb-0">Produtos</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="text-end">Quantidade</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->nome_produto }}</td>
                                <td class="text-end">
                                    {{ $item->quantidade }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    Nenhum item nesta nota.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Total de unidades</th>
                            <th class="text-end">
                                {{ $items->sum('quantidade') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection