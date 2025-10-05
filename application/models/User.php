<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

    private $table = 'users';

    public function get_all_users()
    {
        $this->db->select('id, name, email, created_at');
        return $this->db->get($this->table)->result();
    }

    public function get_user($id)
    {
        $this->db->select('id, name, email, created_at');
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert_user($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}