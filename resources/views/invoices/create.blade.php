@extends('layouts.app')

@section('title', 'Emitir nota fiscal')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('invoices.index') }}" class="text-decoration-none text-muted small">
                ← Notas fiscais
            </a>

            <div class="page-kicker mt-3">Vendas</div>
            <h1>Emitir nota fiscal</h1>
            <p>Informe os dados da venda e adicione um ou mais produtos ativos.</p>
        </div>
    </div>

    <form method="post" action="{{ route('invoices.store') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger rounded-3">
                Confira os dados informados. {{ $errors->first() }}
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body p-4">
                <h2 class="section-title mb-3">Dados da nota</h2>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="cod_nota">
                            Número da nota
                        </label>

                        <input
                            class="form-control @error('cod_nota') is-invalid @enderror"
                            type="number"
                            min="1"
                            id="cod_nota"
                            name="cod_nota"
                            value="{{ old('cod_nota') }}"
                            required
                        >

                        <div class="form-text">
                            Código manual conforme a chave primária do banco.
                        </div>

                        @error('cod_nota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="data_emissao">
                            Data de emissão
                        </label>

                        <input
                            class="form-control @error('data_emissao') is-invalid @enderror"
                            type="date"
                            id="data_emissao"
                            name="data_emissao"
                            value="{{ old('data_emissao', now()->toDateString()) }}"
                        >

                        @error('data_emissao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="cod_cliente">
                            Cliente
                        </label>

                        <select
                            class="form-select @error('cod_cliente') is-invalid @enderror"
                            id="cod_cliente"
                            name="cod_cliente"
                            required
                        >
                            <option value="">Selecione o cliente</option>

                            @foreach ($clients as $client)
                                <option
                                    value="{{ $client->cod_cliente }}"
                                    @selected(old('cod_cliente') == $client->cod_cliente)
                                >
                                    {{ $client->nome_cliente }}
                                </option>
                            @endforeach
                        </select>

                        @error('cod_cliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="cod_vendedor">
                            Vendedor
                        </label>

                        <select
                            class="form-select @error('cod_vendedor') is-invalid @enderror"
                            id="cod_vendedor"
                            name="cod_vendedor"
                            required
                        >
                            <option value="">Selecione o vendedor</option>

                            @foreach ($sellers as $seller)
                                <option
                                    value="{{ $seller->cod_vendedor }}"
                                    @selected(old('cod_vendedor') == $seller->cod_vendedor)
                                >
                                    {{ $seller->nome_vendedor }}
                                </option>
                            @endforeach
                        </select>

                        @error('cod_vendedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="section-title mb-1">Itens da nota</h2>
                        <p class="text-muted small mb-0">
                            Produtos inativos não são oferecidos para venda.
                        </p>
                    </div>

                    <button
                        class="btn btn-sm btn-outline-success"
                        type="button"
                        id="add-item"
                    >
                        + Adicionar produto
                    </button>
                </div>

                <div id="items" class="d-flex flex-column gap-3">
                    @foreach (old('itens', [['cod_produto' => '', 'quantidade' => 1]]) as $index => $item)
                        <div class="row g-2 align-items-end invoice-item">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">
                                    Produto
                                </label>

                                <select
                                    class="form-select @error("itens.$index.cod_produto") is-invalid @enderror"
                                    name="itens[{{ $index }}][cod_produto]"
                                    required
                                >
                                    <option value="">Selecione um produto ativo</option>

                                    @foreach ($products as $product)
                                        <option
                                            value="{{ $product->cod_produto }}"
                                            @selected(($item['cod_produto'] ?? '') == $product->cod_produto)
                                        >
                                            {{ $product->nome_produto }}
                                        </option>
                                    @endforeach
                                </select>

                                @error("itens.$index.cod_produto")
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">
                                    Quantidade
                                </label>

                                <input
                                    class="form-control @error("itens.$index.quantidade") is-invalid @enderror"
                                    name="itens[{{ $index }}][quantidade]"
                                    type="number"
                                    min="1"
                                    step="1"
                                    value="{{ $item['quantidade'] ?? 1 }}"
                                    required
                                >

                                @error("itens.$index.quantidade")
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-1">
                                <button
                                    class="btn btn-light text-danger remove-item w-100"
                                    type="button"
                                    aria-label="Remover item"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-success px-4" type="submit">
                Salvar nota fiscal
            </button>

            <a href="{{ route('invoices.index') }}" class="btn btn-light px-4">
                Cancelar
            </a>
        </div>
    </form>

    <template id="item-template">
        <div class="row g-2 align-items-end invoice-item">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Produto</label>

                <select class="form-select" data-name="product" required>
                    <option value="">Selecione um produto ativo</option>

                    @foreach ($products as $product)
                        <option value="{{ $product->cod_produto }}">
                            {{ $product->nome_produto }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Quantidade</label>

                <input
                    class="form-control"
                    data-name="quantity"
                    type="number"
                    min="1"
                    step="1"
                    value="1"
                    required
                >
            </div>

            <div class="col-md-1">
                <button
                    class="btn btn-light text-danger remove-item w-100"
                    type="button"
                    aria-label="Remover item"
                >
                    ×
                </button>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            const items = document.getElementById('items');
            const template = document.getElementById('item-template');

            let nextIndex = [...items.querySelectorAll('[name^="itens["]')]
                .map(input => Number(input.name.match(/\d+/)?.[0] ?? -1))
                .reduce((highest, current) => Math.max(highest, current), -1) + 1;

            document.getElementById('add-item').addEventListener('click', () => {
                const row = template.content.firstElementChild.cloneNode(true);
                const index = nextIndex++;

                row.querySelector('[data-name="product"]').name =
                    `itens[${index}][cod_produto]`;

                row.querySelector('[data-name="quantity"]').name =
                    `itens[${index}][quantidade]`;

                items.appendChild(row);
            });

            items.addEventListener('click', event => {
                const removeButton = event.target.closest('.remove-item');

                if (removeButton && items.querySelectorAll('.invoice-item').length > 1) {
                    removeButton.closest('.invoice-item').remove();
                }
            });
        </script>
    @endpush
@endsection