# 🛒 E-commerce Simples - Desafio Backend com CodeIgniter 3 e Docker

Este projeto é uma implementação de um e-commerce simples, focado em demonstrar habilidades de desenvolvimento backend com CodeIgniter 3 e boas práticas de conteinerização com Docker. Ele simula o fluxo de um cliente que compra um produto, desde a visualização do catálogo até a finalização do pedido com controle de estoque e cálculo de frete.

## ✨ Destaques do Projeto

* **Arquitetura MVC:** Estrutura clara e organizada utilizando o framework CodeIgniter 3.
* **Gerenciamento de Carrinho:** Funcionalidade completa de adição/remoção de itens, atualização de quantidade e exibição de subtotal.
* **Controle de Estoque:** Validação de estoque durante a adição ao carrinho e baixa automática no momento da finalização do pedido.
* **Cálculo de Frete Dinâmico:** Implementação de regras de frete baseadas no subtotal do carrinho, garantindo flexibilidade e customização.
    * **Frete Grátis:** Para subtotais acima de R$ 200,00.
    * **Frete Fixo (R$ 15,00):** Para subtotais entre R$ 52,00 e R$ 166,59.
    * **Outros Valores (R$ 20,00):** Para demais faixas de subtotal.
* **Integração com ViaCEP:** Consumo de API externa para preenchimento automático de endereço com base no CEP, melhorando a experiência do usuário.
* **Transações de Banco de Dados:** Utilização de transações ACID para garantir a integridade dos dados durante a finalização do pedido (inserção de pedido e itens, baixa de estoque).
* **Validação de Formulários:** Validação robusta dos dados de entrada utilizando a biblioteca de validação do CodeIgniter.
* **Conteinerização com Docker:** Ambiente de desenvolvimento e produção padronizado e isolado, utilizando Nginx, PHP-FPM e MySQL.

# 🚀 Como Rodar o Projeto

Este projeto utiliza Docker para facilitar a configuração do ambiente.

### Pré-requisitos

* [Docker](https://www.docker.com/get-started)
* [Docker Compose](https://docs.docker.com/compose/install/)

### Passos para Configuração e Execução

1.  **Clone o Repositório:**
    ```bash
    git clone https://github.com/correamatheus/mini-erp-codeigniter
    cd mini-erp
    ```

2.  **Verifique as Variáveis de Ambiente (no `docker-compose.yml`):**
    As credenciais do banco de dados e outras configurações de ambiente estão definidas diretamente no arquivo `docker-compose.yml`. Certifique-se de que os valores para `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD` (no serviço `db`) e as configurações de conexão do CodeIgniter (no serviço `app` ou onde seu PHP está rodando, se aplicável via variáveis de ambiente) estejam alinhadas.

    *Exemplo de como essas variáveis podem aparecer no seu `docker-compose.yml` (apenas para ilustração):*
    ```yaml
    # ...
    services:
      db:
        image: mysql:8.0
        environment:
          MYSQL_DATABASE: ecommerce_db
          MYSQL_USER: user_db
          MYSQL_PASSWORD: password
          MYSQL_ROOT_PASSWORD: root_password
        # ...
      app: # Exemplo de como CodeIgniter pode acessar variáveis do DB
        # ...
        environment:
          DB_HOST: db
          DB_USERNAME: user_db
          DB_PASSWORD: password
          DB_NAME: ecommerce_db
        # ...
    # ...
    ```
    *Se seu CodeIgniter lê diretamente de `application/config/database.php` e os valores fixos lá, basta garantir que eles correspondam aos do serviço `db`.*

3.  **Construa e Inicie os Contêineres:**
    Na raiz do projeto, execute:
    ```bash
    docker-compose up --build -d
    ```
    * `--build`: Garante que as imagens sejam construídas (ou reconstruídas) se houver alterações no `Dockerfile`.
    * `-d`: Inicia os contêineres em modo "detached" (em segundo plano).

4.  **Acesse o Projeto no Navegador:**
    O projeto estará disponível em:
    ```
    http://localhost:8080
    ```
    (A porta pode variar dependendo da configuração do seu `docker-compose.yml`).

5.  **Execute as Migrações do Banco de Dados:**
    Para criar as tabelas necessárias, acesse a rota de migração no navegador:
    ```
    http://localhost:8080/migrates
    ```
    * Isso executará a lógica de criação de tabelas (incluindo `pedidos`, `pedido_itens`, `produtos`, `produto_variacoes`, etc.) definida no seu `Migrates` Controller.
### Estrutura do Banco de Dados (SQL)

Para facilitar a revisão e o entendimento do esquema do banco de dados, você pode encontrar o arquivo SQL completo das tabelas criadas pelas migrations aqui:

* [`database/ecommerce_db.sql`](./database/ecommerce_db.sql) (Este arquivo será gerado e adicionado ao repositório conforme as instruções acima)

## 🧩 Funcionalidades Implementadas

* **Página de Produtos:** Exibição de produtos com variações (tamanho, cor, etc.)
* **Adicionar ao Carrinho:** Adição de produtos (com suas variações e quantidade) ao carrinho, com validação de estoque.
* **Visualização e Gestão do Carrinho:**
    * Remoção de itens.
    * Atualização de quantidade.
    * Exibição de subtotal e total geral.
* **Checkout:**
    * Coleta de dados do cliente (nome, email, telefone).
    * Busca de endereço via CEP (integração ViaCEP).
    * Cálculo de frete dinâmico.
    * Validação de todos os campos do formulário.
* **Finalização de Pedido:**
    * Criação de um `hash_id` único para cada pedido.
    * Registro do pedido e seus itens no banco de dados.
    * Baixa automática no estoque das variações de produtos compradas.
    * Uso de transações para garantir a atomicidade da operação.
    * Página de sucesso com detalhes do pedido.

## 🔮 Futuras Funcionalidades (Roadmap)

* **Painel Administrativo:** Para gerenciamento de produtos, pedidos, usuários, cupons, etc.
* **Autenticação de Usuários:** Sistema de login/cadastro para clientes e administradores.
* **Processamento de Pagamento:** Integração com gateways de pagamento (ex: Mercado Pago, Stripe, PagSeguro).
* **Aplicação de Cupons de Desconto:** Geração e validação de cupons na finalização do pedido.
* **Histórico de Pedidos:** Área para o cliente visualizar seus pedidos anteriores.
* **Notificações por Email:** Envio de emails de confirmação de pedido e atualização de status.
* **Busca e Filtros de Produtos:** Melhorar a navegação no catálogo de produtos.

## ✉️ Contato

Sinta-se à vontade para entrar em contato para discutir este projeto ou outras oportunidades!

* **Nome:** Matheus Correa
* **Email:** matheuscorreati@gmail.com
* **LinkedIn:** https://www.linkedin.com/m/in/matheus-correa-8273388b


---
