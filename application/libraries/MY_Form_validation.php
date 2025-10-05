<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

    protected $CI;

    public function __construct($rules = array())
    {
        parent::__construct($rules);
        $this->CI =& get_instance();
    }

    /**
     * Regra de validação para verificar se o e-mail é único durante uma atualização.
     *
     * @param string $email O e-mail enviado na requisição.
     * @param string $params Parâmetros da regra: 'tabela.coluna_id'
     * @return bool
     */
    public function unique_email_update($email, $params)
    {
        list($table, $id_column) = explode('.', $params, 2);

        $id_value = $this->CI->uri->segment(3);

        $this->CI->db->where($id_column . ' !=', $id_value);
        $this->CI->db->where('email', $email);
        $query = $this->CI->db->get($table);

        if ($query->num_rows() > 0) {
            $this->set_message('unique_email_update', 'O campo {field} já está cadastrado.');
            return FALSE;
        }

        return TRUE;
    }
}