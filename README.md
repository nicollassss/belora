# Belora

**Sistema de Gestão Administrativa | Indústria Beleza Ltda.**

O **Belora** é um sistema web de gestão administrativa desenvolvido para a Indústria Beleza Ltda., como parte do projeto acadêmico de Banco de Dados II. A aplicação centraliza operações comerciais, disponibiliza indicadores operacionais e oferece relatórios baseados no modelo relacional oficial do projeto.

## Tecnologias

| Tecnologia | Finalidade |
|---|---|
| PHP 8.3+ | Linguagem de programação |
| Laravel | Framework de aplicação web |
| Blade | Renderização de interfaces |
| Bootstrap 5 | Estilização e responsividade |
| Microsoft SQL Server | Sistema gerenciador de banco de dados |

## Requisitos de ambiente

Antes de iniciar a instalação, verifique se o ambiente atende aos seguintes requisitos:

- PHP 8.3 ou superior, com a extensão `pdo_sqlsrv` habilitada.
- Composer instalado e configurado.
- Microsoft ODBC Driver para SQL Server compatível com a versão do PHP e do sistema operacional.
- Instância acessível do Microsoft SQL Server.
- Banco de dados `IndustriaBeleza`, estruturado conforme o schema oficial do projeto.

## Instalação e configuração

### 1. Instalar as dependências

Na raiz do projeto, execute:

```bash
composer install
```

### 2. Configurar as variáveis de ambiente

Copie o arquivo de configuração de exemplo:

```bash
cp .env.example .env
```

No Windows, também é possível utilizar:

```powershell
Copy-Item .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

### 3. Configurar a conexão com o banco de dados

Edite o arquivo `.env` e informe os parâmetros correspondentes à instância do SQL Server:

```dotenv
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=IndustriaBeleza
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Os valores apresentados são exemplos. Substitua-os pelas configurações reais do ambiente de desenvolvimento.

### 4. Preparar o banco de dados

Execute o script SQL oficial do grupo em uma instância de desenvolvimento e confirme que o banco `IndustriaBeleza` contém o schema e as tabelas esperadas pela aplicação.

**Importante:** a estrutura comercial do banco é definida pelo script SQL oficial. O projeto não utiliza migrations do Laravel para criar ou modificar essas tabelas. Alterações estruturais devem respeitar o modelo de dados estabelecido pelo grupo.

Não execute scripts de exclusão, limpeza ou recriação do banco em ambientes que contenham dados importantes.

### 5. Iniciar a aplicação

Execute o servidor de desenvolvimento:

```bash
php artisan serve
```

Acesse o endereço local informado pelo Laravel no terminal, normalmente `http://127.0.0.1:8000`.

## Funcionalidades

### Painel administrativo

- Indicadores consolidados de clientes, vendedores, produtos ativos e notas fiscais.
- Visão geral das principais informações operacionais do sistema.

### Gestão de cadastros

Operações de inclusão, consulta, edição, busca e exclusão, conforme as regras de cada entidade, para:

- Regiões;
- Pontos estratégicos;
- Vendedores;
- Veículos;
- Clientes;
- Produtos;
- Utilização de veículos.

As exclusões respeitam as restrições de integridade referencial aplicáveis ao modelo de dados.

### Faturamento

- Emissão de notas fiscais.
- Inclusão de itens vinculados às notas.
- Seleção de produtos ativos durante a emissão.
- Processamento transacional para preservar a consistência das operações.
- Consulta individual e visualização dos detalhes das notas fiscais.

### Relatórios gerenciais

Disponibilização dos relatórios identificados de **A a I**, alinhados às consultas definidas no projeto de Banco de Dados II.

## Arquitetura e regras de negócio

### Persistência de dados

O Microsoft SQL Server é a fonte oficial dos dados comerciais da aplicação. O sistema utiliza o schema fornecido pelo grupo, sem substituí-lo por uma estrutura paralela gerenciada por migrations.

As operações de faturamento são executadas em transação, reduzindo o risco de persistência parcial de uma nota fiscal e de seus respectivos itens.

### Identificadores

Os códigos das entidades e das notas fiscais são informados manualmente, pois as chaves correspondentes no schema disponibilizado não utilizam a propriedade `IDENTITY`.

A geração desses identificadores deve respeitar a unicidade exigida pelo banco de dados.

### Valores comerciais

O schema fornecido não contempla o preço dos produtos. Consequentemente, a aplicação não calcula nem apresenta valores monetários de vendas que não possam ser sustentados pelos dados disponíveis.

### Serviços auxiliares do Laravel

As sessões, o cache e as filas utilizam armazenamento local, evitando a dependência de tabelas auxiliares do Laravel no SQL Server para esses serviços.

Essa configuração é voltada ao ambiente previsto para o projeto e não representa, por si só, uma configuração de produção.

## Validação e testes

Para consultar as rotas registradas pela aplicação:

```bash
php artisan route:list
```

Para executar a suíte de testes:

```bash
php artisan test
```

Esses comandos auxiliam na verificação da estrutura da aplicação e dos testes automatizados disponíveis.

**Nota:** a execução dos comandos não comprova, isoladamente, o funcionamento integral do sistema. As funcionalidades de negócio dependem de uma conexão válida com o SQL Server e da presença das tabelas oficiais.

## Segurança e configuração

- Não versionar credenciais, tokens ou outros segredos.
- Manter o arquivo `.env` fora do controle de versão.
- Utilizar `.env.example` apenas para documentar as variáveis necessárias, sem credenciais reais.
- Realizar testes e alterações estruturais em ambientes de desenvolvimento.
- Validar a conectividade e as permissões do usuário do banco antes de executar operações comerciais.

Em caso de falha de conexão, verifique:

1. Se a extensão `pdo_sqlsrv` está habilitada na instalação do PHP utilizada pelo terminal.
2. Se o Microsoft ODBC Driver está instalado e é compatível com o ambiente.
3. Se o serviço SQL Server está em execução e aceita conexões TCP/IP.
4. Se o endereço, a porta e a instância estão configurados corretamente.
5. Se o banco existe e as credenciais possuem as permissões necessárias.

## Escopo do projeto

O Belora foi desenvolvido para atender aos requisitos funcionais e ao modelo de dados definidos no projeto acadêmico de Banco de Dados II. Suas funcionalidades estão condicionadas à estrutura e às informações disponibilizadas pelo schema oficial.

A aplicação não pressupõe funcionalidades ou dados que não estejam previstos nesse modelo.