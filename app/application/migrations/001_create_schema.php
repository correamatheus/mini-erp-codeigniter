<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_schema extends CI_Migration {

    public function up() {
        $this->db->query('SET default_storage_engine=InnoDB');

        // --- Tabela de Produtos ---
        $this->db->query("CREATE TABLE produtos (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        // --- Tabela de Variações de Produto ---
        $this->db->query("CREATE TABLE produto_variacoes (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            produto_id BIGINT UNSIGNED NOT NULL,
            sku VARCHAR(100) NOT NULL UNIQUE,
            nome VARCHAR(100) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT fk_variacao_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        // --- Tabela de Estoque ---
        $this->db->query("CREATE TABLE estoque (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            produto_variacao_id BIGINT UNSIGNED NOT NULL UNIQUE,
            quantidade INT NOT NULL DEFAULT 0,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT fk_estoque_variacao FOREIGN KEY (produto_variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        // --- Tabela de Cupons ---
        $this->db->query("CREATE TABLE cupons (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            tipo_desconto ENUM('fixo', 'percentual') NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            valor_minimo_subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            data_validade DATE NOT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        // --- Tabela de Pedidos ---
        $this->db->query("CREATE TABLE pedidos (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            hash_id VARCHAR(32) NOT NULL UNIQUE,
            cliente_nome VARCHAR(255) NOT NULL,
            cliente_email VARCHAR(255) NOT NULL,
            cep VARCHAR(9) NOT NULL,
            endereco VARCHAR(255) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            valor_frete DECIMAL(10,2) NOT NULL,
            desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            valor_total DECIMAL(10,2) NOT NULL,
            status ENUM('pendente', 'pago', 'enviado', 'entregue', 'cancelado') NOT NULL DEFAULT 'pendente',
            cupom_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT fk_pedido_cupom FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        // --- Tabela de Itens do Pedido ---
        $this->db->query("CREATE TABLE pedido_itens (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            pedido_id BIGINT UNSIGNED NOT NULL,
            produto_variacao_id BIGINT UNSIGNED NOT NULL,
            quantidade INT UNSIGNED NOT NULL,
            preco_unitario DECIMAL(10,2) NOT NULL,
            nome_produto VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
            CONSTRAINT fk_item_variacao FOREIGN KEY (produto_variacao_id) REFERENCES produto_variacoes(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    }

    public function down() {
        $this->dbforge->drop_table('pedido_itens', TRUE);
        $this->dbforge->drop_table('pedidos', TRUE);
        $this->dbforge->drop_table('cupons', TRUE);
        $this->dbforge->drop_table('estoque', TRUE);
        $this->dbforge->drop_table('produto_variacoes', TRUE);
        $this->dbforge->drop_table('produtos', TRUE);
    }
}
