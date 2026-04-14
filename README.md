# E-Commerce | Vintage Records

Projeto de portfólio em Laravel 11 para venda de discos antigos, desenhado para demonstrar arquitetura sólida, modelagem consistente, fake payment gateway, painel administrativo, API REST, auditoria e base para retenção de clientes.

## Stack

- Laravel 11
- PHP 8.2
- MySQL
- Docker + Docker Compose
- Blade
- Tailwind CSS via CDN nesta fase
- Eloquent ORM
- Migrations, Seeders, Factories
- Requests, Policies, Events, Listeners, Jobs
- Queue database

## Visão do produto

O sistema simula uma operação real de e-commerce para uma loja de discos antigos, com foco em:

- catálogo rico para colecionadores
- itens raros com estoque unitário
- checkout com reserva de estoque
- pedidos com snapshot comercial
- fake gateway de pagamento com webhook
- painel admin com pedidos, pagamentos e auditoria
- base para retenção e recompra

## Contextos principais

- `Catalog`
- `Inventory`
- `Cart`
- `Checkout`
- `Orders`
- `Payments`
- `Customers`
- `Admin`
- `Coupons`
- `Audit`
- `Analytics`

## Estrutura técnica

### Camadas práticas

- `app/Models`: entidades e relacionamentos
- `app/Enums`: enums do domínio
- `app/Application`: actions e orquestrações de negócio
- `app/Services`: integrações e serviços técnicos
- `app/Http/Controllers`: web, api, admin e payments
- `app/Http/Requests`: validação de entrada
- `app/Http/Resources`: serialização da API
- `app/Policies`: autorização
- `app/Events`, `app/Listeners`, `app/Jobs`: assíncrono e integração interna

### Arquivos-chave

- `routes/web.php`
- `routes/api.php`
- `database/migrations/2026_04_14_000100_create_commerce_tables.php`
- `app/Application/Checkout/PlaceOrderAction.php`
- `app/Application/Payments/ProcessPaymentWebhookAction.php`
- `app/Application/Payments/SyncPaymentStateAction.php`
- `app/Services/Payments/FakePaymentGatewayService.php`

## Funcionalidades já implementadas

### Storefront

- home de vitrine
- listagem de produtos
- página de detalhe do produto
- carrinho web
- checkout web
- registro e login de cliente
- dashboard do cliente
- histórico de pedidos
- endereços do cliente

### Admin

- dashboard operacional
- CRUD base de produtos
- publicação de produto
- fila de pedidos
- inspeção de pagamentos
- visualização de webhook logs
- visualização de audit logs

### API

- catálogo
- perfil do cliente
- pedidos do cliente
- checkout
- admin products
- admin dashboard

### Fake Payment Gateway

- criação de transação
- consulta de transação
- simulação de status
- cancelamento
- estorno
- webhook fake
- sincronização de pagamento, pedido e estoque

## Fluxo principal de compra

1. Cliente navega no catálogo
2. Adiciona disco ao carrinho
3. Faz login ou cadastro
4. Escolhe endereço
5. Seleciona frete e pagamento
6. Checkout cria pedido
7. Estoque é reservado
8. Payment é criado
9. Fake gateway gera transação
10. Webhook fake atualiza o pagamento
11. Pedido é sincronizado
12. Estoque é recomposto em recusa/cancelamento/estorno

## Setup local

### Requisitos

- PHP 8.2+
- Composer
- Docker Desktop
- MySQL já existente na máquina

### Banco de dados

Este projeto foi preparado para reutilizar o MySQL existente, sem subir outro container de banco. O ideal é criar um novo schema:

```sql
CREATE DATABASE `e-commerce` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Se seu MySQL não usa `root/root`, ajuste o `.env`.

### Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8088
```

### Com Docker

```bash
docker compose up --build
```

A aplicação foi configurada para usar:

- app web: `http://localhost:8088`
- mysql externo: `host.docker.internal:3306`
- database: `e-commerce`

Se for usar upload de imagens no painel admin, mantenha o link simbólico de storage criado:

```bash
php artisan storage:link
```

## Seed de demonstração

O seeder cria:

- usuário admin
- clientes
- artistas
- gêneros
- categorias
- produtos
- estoque
- imagens demo
- cupons
- pedidos em múltiplos status
- pagamentos fake
- transações e envios
- movimentações de estoque

### Credenciais sugeridas

O admin padrão criado pelo seed:

- email: `admin@ecommerce.test`
- senha: `password`

Observação:

- o seeder atual define o usuário admin via factory
- se quiser trocar a senha padrão, ajuste `database/factories/UserFactory.php`

## Rotas importantes

### Web

- `/`
- `/products`
- `/cart`
- `/checkout`
- `/customer/dashboard`
- `/admin/dashboard`

### Fake gateway

- `POST /api/v1/gateway/fake-payments/transactions`
- `GET /api/v1/gateway/fake-payments/transactions/{transactionCode}`
- `POST /api/v1/gateway/fake-payments/transactions/{transactionCode}/simulate-status`
- `POST /api/v1/gateway/fake-payments/transactions/{transactionCode}/cancel`
- `POST /api/v1/gateway/fake-payments/transactions/{transactionCode}/refund`
- `POST /api/v1/gateway/fake-payments/webhooks/payment-status`

## Exemplo de payload do gateway

### Criar transação

```json
{
  "payment_id": 10,
  "order_number": "ECM-AB12CD34EF",
  "amount": 189.90,
  "simulate_status": "pending",
  "callback_url": "http://localhost:8088/api/v1/gateway/fake-payments/webhooks/payment-status"
}
```

### Simular aprovação

```json
{
  "status": "approved",
  "reason": "Manual approval for portfolio demo"
}
```

## Segurança aplicada nesta fase

- autenticação com hash de senha do Laravel
- validação por Form Requests
- policies para recursos sensíveis
- middleware `admin`
- rate limiter de API
- proteção de rotas administrativas
- logs de auditoria para ações críticas

## Testes

Executar:

```bash
php artisan test
```

Cobertura atual inclui:

- resposta da home
- fluxo de checkout com fake payment
- aprovação e estorno refletindo em pedido e estoque

## Próximos passos recomendados

- upload real de imagens de produto
- CRUD admin de clientes, cupons e estoque
- filtros mais ricos no catálogo
- cancelamento de pedido pela área do cliente/admin
- testes de autorização
- documentação visual com screenshots

## Valor de portfólio

Este projeto foi estruturado para mostrar:

- domínio de Laravel 11
- modelagem relacional madura
- arquitetura organizada por contexto
- integração de pagamento realista
- preocupação com segurança
- operação comercial coerente
- capacidade de evolução
