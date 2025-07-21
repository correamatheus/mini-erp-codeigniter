<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carrinho_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
    }

    public function adicionar_item($produto_id, $variacao_id, $quantidade = 1) {
        $this->load->model('Produto_model');
        $produto_info = $this->Produto_model->get_product_by_id_with_details($produto_id);

        if (!$produto_info) {
            return false; 
        }

        $variacao_info = null;
        foreach ($produto_info->variacoes as $v) {
            if ($v->variacao_id == $variacao_id) {
                $variacao_info = $v;
                break;
            }
        }

        if (!$variacao_info) {
            return false; 
        }

        $carrinho = $this->session->userdata('carrinho') ?: [];
        $item_key = $produto_id . '_' . $variacao_id; 

        $quantidade_atual_no_carrinho = isset($carrinho[$item_key]) ? $carrinho[$item_key]['quantidade'] : 0;
        $nova_quantidade_total = $quantidade_atual_no_carrinho + $quantidade;

        if ($nova_quantidade_total > $variacao_info->estoque_quantidade) {
            log_message('error', 'Estoque insuficiente para adicionar ' . $quantidade . ' de SKU ' . $variacao_info->sku);
            return false;
        }

        if (isset($carrinho[$item_key])) {
            $carrinho[$item_key]['quantidade'] = $nova_quantidade_total;
        } else {
            $carrinho[$item_key] = [
                'produto_id'      => $produto_id,
                'variacao_id'     => $variacao_id,
                'sku'             => $variacao_info->sku,
                'nome_produto'    => $produto_info->nome,
                'nome_variacao'   => $variacao_info->nome_variacao,
                'preco_unitario'  => (float)$variacao_info->preco,
                'quantidade'      => $quantidade,
                'estoque_disponivel' => $variacao_info->estoque_quantidade // Manter para futura verificação
            ];
        }

        $this->session->set_userdata('carrinho', $carrinho);
        return true;
    }

    public function atualizar_quantidade($produto_id, $variacao_id, $nova_quantidade) {
        $carrinho = $this->session->userdata('carrinho') ?: [];
        $item_key = $produto_id . '_' . $variacao_id;

        if (!isset($carrinho[$item_key])) {
            return false;
        }

        if ($nova_quantidade <= 0) {
            return $this->remover_item($produto_id, $variacao_id); 
        }

        $this->load->model('Produto_model');
        $produto_info = $this->Produto_model->get_product_by_id_with_details($produto_id);
        $variacao_info = null;
        if ($produto_info) {
            foreach ($produto_info->variacoes as $v) {
                if ($v->variacao_id == $variacao_id) {
                    $variacao_info = $v;
                    break;
                }
            }
        }

        if (!$variacao_info || $nova_quantidade > $variacao_info->estoque_quantidade) {
            log_message('error', 'Estoque insuficiente para atualizar SKU ' . ($variacao_info ? $variacao_info->sku : 'N/A') . ' para ' . $nova_quantidade);
            return false;
        }

        $carrinho[$item_key]['quantidade'] = $nova_quantidade;
        $this->session->set_userdata('carrinho', $carrinho);
        return true;
    }

    public function remover_item($produto_id, $variacao_id) {
        $carrinho = $this->session->userdata('carrinho') ?: [];
        $item_key = $produto_id . '_' . $variacao_id;

        if (isset($carrinho[$item_key])) {
            unset($carrinho[$item_key]);
            $this->session->set_userdata('carrinho', $carrinho);
            return true;
        }
        return false;
    }

    public function get_carrinho_itens() {
        return $this->session->userdata('carrinho') ?: [];
    }

    public function get_total_itens_carrinho() {
        return count($this->session->userdata('carrinho') ?: []);
    }

    public function get_total_quantidade_carrinho() {
        $total_quantidade = 0;
        foreach ($this->get_carrinho_itens() as $item) {
            $total_quantidade += $item['quantidade'];
        }
        return $total_quantidade;
    }

    public function get_subtotal_carrinho() {
        $subtotal = 0;
        foreach ($this->get_carrinho_itens() as $item) {
            $subtotal += ($item['preco_unitario'] * $item['quantidade']);
        }
        return $subtotal;
    }

    public function esvaziar_carrinho() {
        $this->session->unset_userdata('carrinho');
    }

}

