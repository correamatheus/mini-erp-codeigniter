<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Produto_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']); 
    }

    public function index() {
        $data['produtos'] = $this->Produto_model->get_all_products_with_details();
        $this->load->view('layout/header');
        $this->load->view('produtos/index', $data);
        $this->load->view('layout/footer');
    }

    public function new() {
        $this->load->view('layout/header');
        $this->load->view('produtos/form');
        $this->load->view('layout/footer');
    }

    public function store() {
        $this->form_validation->set_rules('nome', 'Nome do Produto', 'required|max_length[255]');
        $this->form_validation->set_rules('descricao', 'Descrição', 'max_length[1000]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => FALSE, 'message' => validation_errors()]);
            return;
        }

        $variacoes_data = $this->input->post('variacoes');
        $processed_variations = [];
        $errors = [];
         if (empty($variacoes_data)) {
            $errors[] = "Pelo menos uma variação é obrigatória.";
        } else {
            foreach ($variacoes_data as $key => $v) {
                if ($key === '__INDEX__') continue;
                if (empty($v['sku'])) { $errors[] = "SKU da variação " . ($key+1) . " é obrigatório."; }
                if (empty($v['nome_variacao'])) { $errors[] = "Nome da variação " . ($key+1) . " é obrigatório."; }
                if (!isset($v['preco']) || !is_numeric($v['preco'])) {
                    $errors[] = "Preço da variação " . ($key+1) . " deve ser um número válido.";
                } elseif ($v['preco'] < 0) {
                    $errors[] = "Preço da variação " . ($key+1) . " deve ser um número positivo.";
                }
                if (!isset($v['quantidade_estoque']) || !is_numeric($v['quantidade_estoque'])) {
                    $errors[] = "Estoque da variação " . ($key+1) . " deve ser um número válido.";
                } elseif ($v['quantidade_estoque'] < 0) {
                    $errors[] = "Estoque da variação " . ($key+1) . " deve ser um número positivo.";
                }

                if (empty($errors)) {
                    $processed_variations[] = [
                        'sku' => $v['sku'],
                        'nome_variacao' => $v['nome_variacao'],
                        'preco' => (float) $v['preco'],
                        'quantidade_estoque' => (int) $v['quantidade_estoque']
                    ];
                }
            }
        }

        if (!empty($errors)) {
            echo json_encode(['success' => FALSE, 'message' => implode('<br>', $errors)]);
            return;
        }

        $data_produto = [
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao')
        ];

        if ($this->Produto_model->save_product_with_details($data_produto, $processed_variations)) {
            echo json_encode(['success' => TRUE, 'message' => 'Produto cadastrado com sucesso!']);
        } else {
            echo json_encode(['success' => FALSE, 'message' => 'Erro ao cadastrar produto. Tente novamente.']);
        }
      
    }

    public function edit($id) {
        $data['produto'] = $this->Produto_model->get_product_by_id_with_details($id);

        if (!$data['produto']) {
            show_404();
        }

        $this->load->view('layout/header');
        $this->load->view('produtos/form', $data);
        $this->load->view('layout/footer');
    }

    public function update() {
        $product_id = $this->input->post('id');
        if (empty($product_id) || !is_numeric($product_id)) {
            echo json_encode(['success' => FALSE, 'message' => 'ID do produto inválido.']);
            return;
        }

        $this->form_validation->set_rules('nome', 'Nome do Produto', 'required|max_length[255]');
        $this->form_validation->set_rules('descricao', 'Descrição', 'max_length[1000]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => FALSE, 'message' => validation_errors()]);
            return;
        }

        $variacoes_data = $this->input->post('variacoes');
        $processed_variations = [];
        $errors = [];

        if (empty($variacoes_data)) {
            $errors[] = "Pelo menos uma variação é obrigatória para o produto.";
        } else {
            foreach ($variacoes_data as $key => $v) {
                if ($key === '__INDEX__') continue;
                if (!in_array($v['acao'], ['nova', 'atualizar', 'deletar'])) {
                     $errors[] = "Ação de variação inválida para o item " . ($key+1) . ".";
                     continue;
                }

                if ($v['acao'] !== 'deletar') {
                    if (empty($v['sku'])) { $errors[] = "SKU da variação " . ($key+1) . " é obrigatório."; }
                    if (empty($v['nome_variacao'])) { $errors[] = "Nome da variação " . ($key+1) . " é obrigatório."; }
                    if (!is_numeric($v['preco']) || $v['preco'] < 0) { $errors[] = "Preço da variação " . ($key+1) . " deve ser um número positivo."; }
                    if (!is_numeric($v['quantidade_estoque']) || $v['quantidade_estoque'] < 0) { $errors[] = "Estoque da variação " . ($key+1) . " deve ser um número positivo."; }
                }

                if (($v['acao'] === 'atualizar' || $v['acao'] === 'deletar') && (empty($v['variacao_id']) || !is_numeric($v['variacao_id']))) {
                    $errors[] = "ID da variação é obrigatório para atualização ou exclusão do item " . ($key+1) . ".";
                }

                if (empty($errors)) {
                    $processed_variations[] = [
                        'acao' => $v['acao'],
                        'variacao_id' => isset($v['variacao_id']) ? $v['variacao_id'] : null,
                        'sku' => $v['sku'] ?? null,
                        'nome_variacao' => $v['nome_variacao'] ?? null,
                        'preco' => $v['preco'] ?? null,
                        'quantidade_estoque' => $v['quantidade_estoque'] ?? null,
                    ];
                }
            }
        }

        if (!empty($errors)) {
            echo json_encode(['success' => FALSE, 'message' => implode('<br>', $errors)]);
            return;
        }

        $data_produto = [
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao')
        ];

        if ($this->Produto_model->update_product_with_details($product_id, $data_produto, $processed_variations)) {
            echo json_encode(['success' => TRUE, 'message' => 'Produto atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => FALSE, 'message' => 'Erro ao atualizar produto. Tente novamente.']);
        }
    }

}