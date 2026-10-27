@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
    <div class="page-heading">
        <div>
            <div class="page-kicker">Análise</div>
            <h1>Relatórios</h1>
            <p>
                As consultas A–I do trabalho de Banco de Dados II, prontas para explorar.
            </p>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($reports as $code => $title)
            <div class="col-md-6 col-xl-4">
                <a
                    class="card h-100 text-decoration-none text-dark"
                    href="{{ route('reports.show', $code) }}"
                >
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <span class="badge rounded-pill text-bg-light">
                                Consulta {{ strtoupper($code) }}
                            </span>

                            <span class="quick-arrow" aria-hidden="true">
                                ↗
                            </span>
                        </div>

                        <h2 class="section-title mt-3 mb-2">
                            {{ $title }}
                        </h2>

                        <p class="small text-muted mb-0">
                            Abrir resultado da consulta
                        </p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection