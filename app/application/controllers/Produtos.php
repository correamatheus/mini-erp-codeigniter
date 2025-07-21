<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Produto_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    public function index() {
        $data['produtos'] = $this->Produto_model->listar();
        $this->load->view('layout/header');
        $this->load->view('produtos/index', $data);
        $this->load->view('layout/footer');
    }

    public function novo() {
        $this->load->view('layout/header');
        $this->load->view('produtos/form');
        $this->load->view('layout/footer');
    }

    public function salvar() {
        $data = $this->input->post();
        $this->Produto_model->salvar($data);
        redirect('produtos');
    }

    public function editar($id) {
        $data['produto'] = $this->Produto_model->buscar_por_id($id);
        $this->load->view('layout/header');
        $this->load->view('produtos/form', $data);
        $this->load->view('layout/footer');
    }

    public function atualizar() {
        $id = $this->input->post('id');
        $data = $this->input->post();
        unset($data['id']);
        $this->Produto_model->atualizar($id, $data);
        redirect('produtos');
    }

    public function deletar($id) {
        $this->Produto_model->deletar($id);
        redirect('produtos');
    }
}