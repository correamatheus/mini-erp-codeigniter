<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedido_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Produto_model');
        $this->load->helper('string'); 
    }

    public function finalizar_pedido($dados_form_checkout, $itens_carrinho, $valor_frete) {
        $this->db->trans_start();

        $subtotal_carrinho = 0;
        foreach ($itens_carrinho as $item) {
            $subtotal_carrinho += ($item['preco_unitario'] * $item['quantidade']);
        }
        $total_final = $subtotal_carrinho + $valor_frete;

        $hash_id = random_string('alnum', 32);
        while ($this->db->where('hash_id', $hash_id)->count_all_results('pedidos') > 0) {
            $hash_id = random_string('alnum', 32); 
        }

        $endereco_completo = $dados_form_checkout['logradouro'] . ', ' . $dados_form_checkout['numero'];
        if (!empty($dados_form_checkout['complemento'])) {
            $endereco_completo .= ' - ' . $dados_form_checkout['complemento'];
        }
        $endereco_completo .= ' - ' . $dados_form_checkout['bairro'];
        $endereco_completo .= ' - ' . $dados_form_checkout['localidade'] . '/' . $dados_form_checkout['uf'];


        $dados_pedido_header = [
            'hash_id'        => $hash_id,
            'cliente_nome'   => $dados_form_checkout['nome_cliente'],
            'cliente_email'  => $dados_form_checkout['email_cliente'],
            'cep'            => $dados_form_checkout['cep'], 
            'endereco'       => $endereco_completo, 
            'subtotal'       => $subtotal_carrinho,
            'valor_frete'    => $valor_frete,
            'desconto'       => 0.00, 
            'valor_total'    => $total_final,
            'status'         => 'pendente',
            'cupom_id'       => NULL, 
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('pedidos', $dados_pedido_header);
        $pedido_id = $this->db->insert_id(); 

        if (!$pedido_id) {
            log_message('error', 'Falha ao inserir o pedido principal.');
            $this->db->trans_rollback();
            return FALSE;
        }

        foreach ($itens_carrinho as $item) {
            $variacao_id = $item['variacao_id'];
            $quantidade_comprada = $item['quantidade'];

            $estoque_atual = $this->Produto_model->get_variacao_estoque($variacao_id);

            if ($estoque_atual === null || $estoque_atual < $quantidade_comprada) {
                log_message('error', 'Estoque insuficiente para a variação_id: ' . $variacao_id . '. Estoque: ' . ($estoque_atual ?? 'N/A') . ', Solicitado: ' . $quantidade_comprada);
                $this->db->trans_rollback(); 
                return FALSE; 
            }

            $novo_estoque = $estoque_atual - $quantidade_comprada;
            $estoque_atualizado = $this->Produto_model->update_variacao_estoque($variacao_id, $novo_estoque);

            if (!$estoque_atualizado) {
                log_message('error', 'Falha ao atualizar o estoque para a variação_id: ' . $variacao_id);
                $this->db->trans_rollback();
                return FALSE;
            }

            $dados_item = [
                'pedido_id'          => $pedido_id,
                'produto_variacao_id' => $variacao_id, 
                'quantidade'         => $quantidade_comprada,
                'preco_unitario'     => $item['preco_unitario'],
                'nome_produto'       => $item['nome_produto'] . ' - ' . $item['nome_variacao'], // Consolida nome do produto e variação
            ];

            $this->db->insert('pedido_itens', $dados_item);
            if ($this->db->affected_rows() === 0) {
                log_message('error', 'Falha ao inserir item do pedido para a variação_id: ' . $variacao_id);
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Transação de pedido falhou. Rolling back.');
            return FALSE;
        } else {
            $this->session->unset_userdata('carrinho');
            return $hash_id; 
        }
    }

    public function get_pedido_by_hash($hash_id) {
        $this->db->where('hash_id', $hash_id);
        $query = $this->db->get('pedidos');
        return $query->row();
    }

    public function get_pedido_itens($pedido_id) {
        $this->db->where('pedido_id', $pedido_id);
        $query = $this->db->get('pedido_itens');
        return $query->result();
    }

}