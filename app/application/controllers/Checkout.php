<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('Carrinho_model');
    }

    public function index() {
        $itens_carrinho = $this->Carrinho_model->get_carrinho_itens();

        if (empty($itens_carrinho)) {
            redirect('carrinho');
        }

        $data['itens_carrinho'] = $itens_carrinho;
        $data['subtotal'] = $this->Carrinho_model->get_subtotal_carrinho();

        $this->load->view('layout/header');
        $this->load->view('checkout/index', $data); 
        $this->load->view('layout/footer');
    }

    public function buscar_cep_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('cep', 'CEP', 'required|exact_length[8]|integer');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'CEP inválido.',
                'errors' => validation_errors()
            ];
        } else {
            $cep = $this->input->post('cep');
            $url = "https://viacep.com.br/ws/{$cep}/json/";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $endereco = json_decode($result);

            if (isset($endereco->erro) && $endereco->erro) {
                $response = [
                    'success' => false,
                    'message' => 'CEP não encontrado ou inválido.',
                ];
            } else {
                $response = [
                    'success' => true,
                    'data' => [
                        'logradouro' => $endereco->logradouro,
                        'bairro'     => $endereco->bairro,
                        'localidade' => $endereco->localidade,
                        'uf'         => $endereco->uf,
                    ]
                ];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function calcular_frete_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('cep_destino', 'CEP de Destino', 'required|exact_length[8]|integer');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'success' => false,
                'message' => 'CEP de destino inválido para cálculo de frete.',
                'errors' => validation_errors()
            ];
        } else {
            $cep_destino = $this->input->post('cep_destino');
            $subtotal_carrinho = $this->Carrinho_model->get_subtotal_carrinho();

            $valor_frete = 0;
            $prazo_entrega = '5-7 dias úteis'; 

        if ($subtotal_carrinho > 200.00) { 
            $valor_frete = 0.00; 
        } elseif ($subtotal_carrinho >= 52.00 && $subtotal_carrinho <= 166.59) { 
            $valor_frete = 15.00;
        } else { 
            $valor_frete = 20.00;
        }

            $response = [
                'success' => true,
                'frete'   => $valor_frete,
                'prazo'   => $prazo_entrega,
                'subtotal' => $subtotal_carrinho,
                'total_com_frete' => $subtotal_carrinho + $valor_frete
            ];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // Futuramente, o método para finalizar o pedido será aqui: public function store() {...}
}