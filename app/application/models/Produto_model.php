<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Produto_model extends CI_Model {
    public function listar() {
        return $this->db->get('produtos')->result();
    }

    public function salvar($data) {
        return $this->db->insert('produtos', $data);
    }
    
    public function buscar_por_id($id) {
        return $this->db->get_where('produtos', ['id' => $id])->row();
    }

    public function atualizar($id, $data) {
        return $this->db->where('id', $id)->update('produtos', $data);
    }

    public function deletar($id) {
        return $this->db->where('id', $id)->delete('produtos');
    }
}