<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_schema extends CI_Migration {

    public function up() {
        // Garante que o InnoDB será usado, que suporta chaves estrangeiras
        $this->db->query('SET default_storage_engine=InnoDB');

        // --- 1. Tabela de Produtos (Apenas a "casca" do produto) ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('produtos');

        // --- 2. Tabela de Variações de Produto (O item real com preço) ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'produto_id' => ['type' => 'BIGINT', 'unsigned' => TRUE],
            'sku' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => TRUE],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 100], // Ex: "Cor: Azul, Tamanho: G"
            'preco' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_variacao_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE');
        $this->dbforge->create_table('produto_variacoes');

        // --- 3. Tabela de Estoque ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'produto_variacao_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'unique' => TRUE], // Relação 1-para-1
            'quantidade' => ['type' => 'INT', 'default' => 0],
            'updated_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_estoque_variacao FOREIGN KEY (produto_variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE');
        $this->dbforge->create_table('estoque');

        // --- 4. Tabela de Cupons ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => TRUE],
            'tipo_desconto' => ['type' => "ENUM('fixo', 'percentual')", 'null' => FALSE],
            'valor' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => FALSE],
            'valor_minimo_subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'data_validade' => ['type' => 'DATE', 'null' => FALSE],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cupons');

        // --- 5. Tabela de Pedidos ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'hash_id' => ['type' => 'VARCHAR', 'constraint' => 32, 'unique' => TRUE], // ID público para o cliente
            'cliente_nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'cliente_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'cep' => ['type' => 'VARCHAR', 'constraint' => 9],
            'endereco' => ['type' => 'VARCHAR', 'constraint' => 255],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'valor_frete' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'desconto' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'valor_total' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'status' => ['type' => "ENUM('pendente', 'pago', 'enviado', 'entregue', 'cancelado')", 'default' => 'pendente'],
            'cupom_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on update' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_pedido_cupom FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL');
        $this->dbforge->create_table('pedidos');
        
        // --- 6. Tabela de Itens do Pedido ---
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'pedido_id' => ['type' => 'BIGINT', 'unsigned' => TRUE],
            'produto_variacao_id' => ['type' => 'BIGINT', 'unsigned' => TRUE],
            'quantidade' => ['type' => 'INT', 'unsigned' => TRUE],
            'preco_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'nome_produto' => ['type' => 'VARCHAR', 'constraint' => 255] 
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT fk_item_variacao FOREIGN KEY (produto_variacao_id) REFERENCES produto_variacoes(id) ON DELETE RESTRICT');
        $this->dbforge->create_table('pedido_itens');
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