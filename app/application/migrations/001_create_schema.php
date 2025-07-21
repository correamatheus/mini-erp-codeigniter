<?php
defined('BASEPATH') OR exit('Tente novamente mais tarde!');

class Migration_Create_schema extends CI_Migration {
    
    public function up() {
        // Configuração do engine padrão para InnoDB (suporta chaves estrangeiras)
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Tabela de Produtos
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'preco' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ],
            'estoque' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => FALSE
            ],
            'criado_em' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => NULL 
            ],
            'atualizado_em' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL,
                'on update' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('produtos', TRUE);

        // Tabela de Variações
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'produto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'criado_em' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => NULL 
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_variacao_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE');
        $this->dbforge->create_table('variacoes', TRUE);

        // Tabela de Estoque
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'produto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'variacao_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'quantidade' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => FALSE
            ],
            'atualizado_em' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL,
                'on update' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT fk_estoque_variacao FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE');
        $this->dbforge->create_table('estoques', TRUE);

        // Tabela de Cupons
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE,
                'unique' => TRUE
            ],
            'desconto_percentual' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE
            ],
            'desconto_fixo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'valor_minimo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'null' => FALSE
            ],
            'validade' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => FALSE
            ],
            'criado_em' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => NULL 
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cupons', TRUE);

        // Tabela de Pedidos
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'cliente_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'cliente_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'cliente_cep' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => TRUE
            ],
            'endereco' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ],
            'frete' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ],
            'cupom_aplicado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM("pendente","pago","cancelado")',
                'default' => 'pendente',
                'null' => FALSE
            ],
            'criado_em' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => NULL 
            ],
            'atualizado_em' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL,
                'on update' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_pedido_cupom FOREIGN KEY (cupom_aplicado) REFERENCES cupons(codigo) ON DELETE SET NULL');
        $this->dbforge->create_table('pedidos', TRUE);

        // Tabela de Itens do Pedido
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'pedido_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'produto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'variacao_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'quantidade' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'preco_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)');
        $this->dbforge->add_field('CONSTRAINT fk_item_variacao FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE SET NULL');
        $this->dbforge->create_table('pedido_itens', TRUE);

        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down() {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        
        $this->dbforge->drop_table('pedido_itens', TRUE);
        $this->dbforge->drop_table('pedidos', TRUE);
        $this->dbforge->drop_table('cupons', TRUE);
        $this->dbforge->drop_table('estoques', TRUE);
        $this->dbforge->drop_table('variacoes', TRUE);
        $this->dbforge->drop_table('produtos', TRUE);
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}