<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('Cliente', function (Blueprint $table): void {
            $table->integer('cod_cliente')->primary();
            $table->string('nome_cliente');
        });
        Schema::create('Vendedor', function (Blueprint $table): void {
            $table->integer('cod_vendedor')->primary();
            $table->string('nome_vendedor');
        });
        Schema::create('Produto', function (Blueprint $table): void {
            $table->integer('cod_produto')->primary();
            $table->string('nome_produto');
            $table->boolean('ativo');
        });
        Schema::create('Nota_Fiscal', function (Blueprint $table): void {
            $table->integer('cod_nota')->primary();
            $table->date('data_emissao');
            $table->integer('cod_cliente');
            $table->integer('cod_vendedor');
        });
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertSee('equipe.')->assertSee('Notas recentes');
    }
}
