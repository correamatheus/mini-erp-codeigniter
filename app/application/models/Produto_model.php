<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Produto_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
   

    public function get_all_products_with_details() {
        $this->db->select('p.id, p.nome, p.descricao, p.created_at, p.updated_at');
        $this->db->from('produtos p');
        $query = $this->db->get();
        $produtos = $query->result();

        foreach ($produtos as &$produto) {
            $this->db->select('pv.id as variacao_id, pv.sku, pv.nome as nome_variacao, pv.preco, e.quantidade as estoque_quantidade');
            $this->db->from('produto_variacoes pv');
            $this->db->join('estoque e', 'e.produto_variacao_id = pv.id', 'left');
            $this->db->where('pv.produto_id', $produto->id);
            $variacoes_query = $this->db->get();
            $produto->variacoes = $variacoes_query->result();
        }

        return $produtos;
    }


    public function get_product_by_id_with_details($id){
            $this->db->select('p.id, p.nome, p.descricao, p.created_at, p.updated_at');
            $this->db->from('produtos p');
            $this->db->where('p.id', $id);
            $query = $this->db->get();
            $produto = $query->row();

            if ($produto) {
                $this->db->select('pv.id as variacao_id, pv.sku, pv.nome as nome_variacao, pv.preco, e.quantidade as estoque_quantidade');
                $this->db->from('produto_variacoes pv');
                $this->db->join('estoque e', 'e.produto_variacao_id = pv.id', 'left');
                $this->db->where('pv.produto_id', $produto->id);
                $variacoes_query = $this->db->get();
                $produto->variacoes = $variacoes_query->result();
            }

            return $produto;
    }

    public function save_product_with_details($data_produto, $data_variacoes) {
        $this->db->trans_start();

        
        $this->db->insert('produtos', $data_produto);
        $produto_id = $this->db->insert_id(); 

        if (!$produto_id) {
            $this->db->trans_rollback();
            return FALSE;
        }

        foreach ($data_variacoes as $variacao) {
            $data_variacao = [
                'produto_id' => $produto_id,
                'sku' => $variacao['sku'],
                'nome' => $variacao['nome_variacao'],
                'preco' => $variacao['preco'],
            ];
            $this->db->insert('produto_variacoes', $data_variacao);
            $variacao_id = $this->db->insert_id();

            if (!$variacao_id) {
                $this->db->trans_rollback();
                return FALSE;
            }

            $data_estoque = [
                'produto_variacao_id' => $variacao_id,
                'quantidade' => $variacao['quantidade_estoque'],
            ];
            $this->db->insert('estoque', $data_estoque);
            if ($this->db->affected_rows() === 0) { 
                 $this->db->trans_rollback();
                 return FALSE;
            }
        }

        $this->db->trans_complete(); 

        return $this->db->trans_status();
    }

    public function update_product_with_details($produto_id, $data_produto, $data_variacoes) {
        $this->db->trans_start();

        $this->db->where('id', $produto_id)->update('produtos', $data_produto);
        foreach ($data_variacoes as $variacao) {
            switch ($variacao['acao']) {
                case 'nova':
                    $data_variacao = [
                        'produto_id' => $produto_id,
                        'sku' => $variacao['sku'],
                        'nome' => $variacao['nome_variacao'],
                        'preco' => $variacao['preco'],
                    ];
                    $this->db->insert('produto_variacoes', $data_variacao);
                    $variacao_id = $this->db->insert_id();

                    if (!$variacao_id) {
                        $this->db->trans_rollback();
                        return FALSE;
                    }

                    $data_estoque = [
                        'produto_variacao_id' => $variacao_id,
                        'quantidade' => $variacao['quantidade_estoque'],
                    ];
                    $this->db->insert('estoque', $data_estoque);
                    if ($this->db->affected_rows() === 0) {
                         $this->db->trans_rollback();
                         return FALSE;
                    }
                    break;

                case 'atualizar':
                    $data_variacao = [
                        'sku' => $variacao['sku'],
                        'nome' => $variacao['nome_variacao'],
                        'preco' => $variacao['preco'],
                    ];
                    $this->db->where('id', $variacao['variacao_id'])->update('produto_variacoes', $data_variacao);

                    $data_estoque = [
                        'quantidade' => $variacao['quantidade_estoque'],
                    ];
                   
                    $this->db->where('produto_variacao_id', $variacao['variacao_id'])->update('estoque', $data_estoque);
                    break;

                case 'deletar':
                    $this->db->where('produto_variacao_id', $variacao['variacao_id'])->delete('estoque');
                    $this->db->where('id', $variacao['variacao_id'])->delete('produto_variacoes');
                    break;
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function delete_product($id) {
        $this->db->where('id', $id)->delete('produtos');
        return $this->db->affected_rows() > 0;
    }

    public function update_variacao_estoque($variacao_id, $nova_quantidade) {
        $this->db->where('produto_variacao_id', $variacao_id);
        $this->db->set('quantidade', $nova_quantidade);
        $this->db->update('estoque');
        return $this->db->affected_rows() > 0;
    }

    public function get_variacao_estoque($variacao_id) {
        $this->db->select('e.quantidade');
        $this->db->from('estoque e');
        $this->db->where('e.produto_variacao_id', $variacao_id);
        $query = $this->db->get();
        $result = $query->row();
        return $result ? (int)$result->quantidade : null;
    }

}