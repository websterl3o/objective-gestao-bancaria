# Gestão de Cobrança

## Descrição do Projeto
Projeto Gestão de Cobrança é uma aplicação cujo objetivo é simular um sistema de cobrança de clientes, onde é possível cadastrar contas, e transações para as mesmas.

Regras:
- Uma conta pode ter várias transações.
- Uma transação pertence a uma conta.
- As trasações são do tipo debit (debito).
- As transações tem diferentes tipos de tipo de pagamento.
  - Cartão de débito.
  - Cartão de crédito.
  - Pix.
- As transações tem diferentes tipos de taxas para cada tipo de pagamento.
  - Taxa de 3% para pagamentos com cartão de débito.
  - Taxa de 5% para pagamentos com cartão de crédito.
  - Taxa de 0% para pagamentos com pix.
- Quando uma transação é criada para uma conta, é calculado o valor da taxa de acordo com o tipo de pagamento e o valor da transação e é debitado no saldo da conta.

## Tecnologias e Ferramentas Utilizadas

<div align="left">
    <img src="https://img.shields.io/badge/-Docker-%23fff?style=for-the-badge&logo=docker&logoColor=2496ED" target="_blank">
    <img src="https://img.shields.io/badge/-PHP-%23fff?style=for-the-badge&logo=php&logoColor=777BB4" target="_blank">
    <img src="https://img.shields.io/badge/-Laravel-%23fff?style=for-the-badge&logo=laravel&logoColor=FF2D20" target="_blank">
    <img src="https://img.shields.io/badge/-PHPUnit-%23fff?style=for-the-badge&logo=phpunit&logoColor=4FC08D" target="_blank">
    <img src="https://img.shields.io/badge/-Pest-%23fff?style=for-the-badge&logo=pest&logoColor=4FC08D" target="_blank">
    <img src="https://img.shields.io/badge/-Vite-%23fff?style=for-the-badge&logo=vite&logoColor=4FC08D" target="_blank">
</div>

## Configuração do Ambiente
1. Clone o repositório do projeto
    ```shellScript
        git clone git@github.com:websterl3o/objective-gestao-bancaria.git
    ```
2. Crie o arquivo .env apartir do .env.example
    ```shellScript
        cp .env.example .env
    ```
3. Execute o comando para subir o ambiente
    ```shellScript
        docker-compose up -d --build
    ```
4. Execute o comando para instalar as dependências do projeto
    ```shellScript
        docker-compose exec app composer install
    ```
5. Execute o comando para gerar a chave do projeto
    ```shellScript
        docker-compose exec app php artisan key:generate
    ```
6. Execute o comando para criar a estrutura do banco de dados
    ```shellScript
        docker-compose exec app php artisan migrate
    ```
7. Instalar dependencias do node
    ```shellScript
        docker-compose exec app npm install
    ```
8. Acesse o projeto em http://localhost:9695

## Comandos Úteis
### Executar testes
```shellScript
docker-compose exec app vendor/bin/pest
```

### Gerar coverage html
```shellScript
docker-compose exec app vendor/bin/pest --coverage-html=coverage
```

### Rotas da API

| Método | URI                                    | Descrição            | Body                                                                   |
| ------ | -------------------------------------- | -------------------- | ---------------------------------------------------------------------- |
| POST   | http://localhost:9695/api/account      | Criar uma nova conta | ``` { "numero_conta": "234", "saldo": 100.00 } ```                     |
| GET    | http://localhost:9695/api/account/{id} | Exibir uma conta     |                                                                        |
| POST   | http://localhost:9695/api/transaction  | Criar uma transação  | ``` { "forma_pagamento": "D", "numero_conta": 234, "valor": 10.5 } ``` |
