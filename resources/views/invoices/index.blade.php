@extends('layouts.app')

@section('title', 'Notas fiscais')

@section('content')
    <div class="page-heading">
        <div>
            <div class="page-kicker">Vendas</div>
            <h1>Notas fiscais</h1>
            <p>Consulte documentos e acompanhe as vendas registradas.</p>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn btn-success">
            + Emitir nota fiscal
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Data de emissão</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="fw-semibold">
                                #{{ $invoice->cod_nota }}
                            </td>

                            <td>{{ $invoice->nome_cliente }}</td>
                            <td>{{ $invoice->nome_vendedor }}</td>

                            <td>
                                {{ \Illuminate\Support\Carbon::parse($invoice->data_emissao)->format('d/m/Y') }}
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('invoices.show', $invoice->cod_nota) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    Ver detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="fw-semibold">
                                    Nenhuma nota fiscal encontrada
                                </div>

                                <div class="text-muted small mt-1">
                                    Emita a primeira nota para começar.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="p-3">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
@endsection