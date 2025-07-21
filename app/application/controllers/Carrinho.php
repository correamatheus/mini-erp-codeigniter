<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carrinho extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Carrinho_model');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['itens_carrinho'] = $this->Carrinho_model->get_carrinho_itens();
        $data['subtotal'] = $this->Carrinho_model->get_subtotal_carrinho();
        $this->load->view('carrinho/index', $data);
    }


    public function adicionar_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('produto_id', 'ID do Produto', 'required|integer');
        $this->form_validation->set_rules('variacao_id', 'ID da Variação', 'required|integer');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'Dados inválidos para adicionar ao carrinho.',
                'errors'  => validation_errors()
            ];
        } else {
            $produto_id  = $this->input->post('produto_id');
            $variacao_id = $this->input->post('variacao_id');
            $quantidade  = $this->input->post('quantidade');

            if ($this->Carrinho_model->adicionar_item($produto_id, $variacao_id, $quantidade)) {
                $response = [
                    'success'           => true,
                    'message'           => 'Produto adicionado ao carrinho!',
                    'total_itens'       => $this->Carrinho_model->get_total_quantidade_carrinho(), // Total de *unidades*
                    'total_itens_unicos' => $this->Carrinho_model->get_total_itens_carrinho(), // Total de *tipos* de item
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Não foi possível adicionar o produto ao carrinho. Verifique o estoque ou dados.',
                ];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function atualizar_quantidade_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('produto_id', 'ID do Produto', 'required|integer');
        $this->form_validation->set_rules('variacao_id', 'ID da Variação', 'required|integer');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'Dados inválidos para atualizar quantidade.',
                'errors'  => validation_errors()
            ];
        } else {
            $produto_id  = $this->input->post('produto_id');
            $variacao_id = $this->input->post('variacao_id');
            $quantidade  = $this->input->post('quantidade');

            if ($this->Carrinho_model->atualizar_quantidade($produto_id, $variacao_id, $quantidade)) {
                $response = [
                    'success'           => true,
                    'message'           => 'Quantidade atualizada!',
                    'total_itens'       => $this->Carrinho_model->get_total_quantidade_carrinho(),
                    'total_itens_unicos' => $this->Carrinho_model->get_total_itens_carrinho(),
                    'subtotal_carrinho' => $this->Carrinho_model->get_subtotal_carrinho(),
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Não foi possível atualizar a quantidade. Verifique o estoque.',
                ];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function remover_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('produto_id', 'ID do Produto', 'required|integer');
        $this->form_validation->set_rules('variacao_id', 'ID da Variação', 'required|integer');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'Dados inválidos para remover item.',
                'errors'  => validation_errors()
            ];
        } else {
            $produto_id  = $this->input->post('produto_id');
            $variacao_id = $this->input->post('variacao_id');

            if ($this->Carrinho_model->remover_item($produto_id, $variacao_id)) {
                $response = [
                    'success'           => true,
                    'message'           => 'Item removido do carrinho.',
                    'total_itens'       => $this->Carrinho_model->get_total_quantidade_carrinho(),
                    'total_itens_unicos' => $this->Carrinho_model->get_total_itens_carrinho(),
                    'subtotal_carrinho' => $this->Carrinho_model->get_subtotal_carrinho()
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Item não encontrado no carrinho.',
                ];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function esvaziar_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->Carrinho_model->esvaziar_carrinho();
        $response = [
            'success'           => true,
            'message'           => 'Carrinho esvaziado com sucesso.',
            'total_itens'       => 0,
            'total_itens_unicos' => 0,
            'subtotal_carrinho' => 0.00
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function get_carrinho_count_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $count = $this->Carrinho_model->get_total_quantidade_carrinho();
        $response = ['count' => $count];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}