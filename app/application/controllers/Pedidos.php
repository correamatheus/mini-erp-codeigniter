<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('Carrinho_model');
        $this->load->model('Pedido_model'); 
    }

    public function store() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('nome_cliente', 'Nome Completo', 'required|min_length[3]');
        $this->form_validation->set_rules('email_cliente', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('telefone_cliente', 'Telefone', 'min_length[8]'); // Opcional
        $this->form_validation->set_rules('cep', 'CEP', 'required|exact_length[9]'); // Formato com hífen, ex: 00000-000
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required');
        $this->form_validation->set_rules('localidade', 'Cidade', 'required');
        $this->form_validation->set_rules('uf', 'Estado', 'required|exact_length[2]');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'Por favor, preencha todos os campos obrigatórios corretamente.',
                'errors' => validation_errors(' ', ' ')
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $itens_carrinho = $this->Carrinho_model->get_carrinho_itens();
        if (empty($itens_carrinho)) {
            $response = [
                'success' => false,
                'message' => 'Seu carrinho está vazio. Por favor, adicione itens antes de finalizar o pedido.'
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $dados_form = $this->input->post();
        $dados_form['cep'] = str_replace('-', '', $dados_form['cep']);

        $subtotal_carrinho = $this->Carrinho_model->get_subtotal_carrinho();
        $valor_frete = 0;
        if ($subtotal_carrinho >= 200.00) {
            $valor_frete = 0.00;
        } elseif ($subtotal_carrinho >= 100.00) {
            $valor_frete = 15.00;
        } else {
            $valor_frete = 25.00;
        }

       
        $hash_id_pedido = $this->Pedido_model->finalizar_pedido($dados_form, $itens_carrinho, $valor_frete);

        if ($hash_id_pedido) {
            $response = [
                'success' => true,
                'message' => 'Pedido finalizado com sucesso! Seu número de pedido é: ' . $hash_id_pedido,
                'hash_id' => $hash_id_pedido
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Não foi possível finalizar o pedido. Estoque insuficiente ou erro interno. Tente novamente.'
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function sucesso($hash_id = null) {
        if ($hash_id === null) {
            redirect('produtos'); 
        }

        $data['pedido'] = $this->Pedido_model->get_pedido_by_hash($hash_id);

        if (!$data['pedido']) {
            show_404(); 
        }

        $data['itens_pedido'] = $this->Pedido_model->get_pedido_itens($data['pedido']->id);

        $this->load->view('layout/header');
        $this->load->view('pedidos/sucesso', $data);
        $this->load->view('layout/footer');
    }

}