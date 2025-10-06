<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Users extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->model('user');
        $this->load->helper('auth');

        $http_verb = $this->request->method;

        $ci_method = $this->router->fetch_method();

        $is_public = ($ci_method === 'index' && $http_verb === 'post');

        if (!$is_public) {
            $this->auth_user_data = verify_jwt_token();

            if ($this->auth_user_data === false) {
                $this->response(['message' => 'Acesso não autorizado.'], RestController::HTTP_UNAUTHORIZED);
                exit();
            }
        }
    }

    // Método GET para listar usuários
    public function index_get($id = 0)
    {
        if(!empty($id)){
            $data = $this->user->get_user($id);
        } else {
            $data = $this->user->get_all_users();
        }

        if ($data) {
            $this->response($data, RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Nenhum usuário encontrado.'], RestController::HTTP_NOT_FOUND);
        }
    }

    // Método POST para criar um usuário
    public function index_post()
    {
        $data = $this->post();

        $this->form_validation->set_data($data);

        // Validação de entrada
        $this->form_validation->set_rules('name', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Senha', 'required|min_length[8]');
        
        if ($this->form_validation->run() == FALSE) {
            $this->response(['errors' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
        } else {
            $data = [
                'name' => $this->post('name'),
                'email' => $this->post('email'),
                'password' => password_hash($this->post('password'), PASSWORD_BCRYPT)
            ];
            $insert_id = $this->user->insert_user($data);
            
            if ($insert_id) {
                unset($data['password']);
                $data['id'] = $insert_id;
                $this->response($data, RestController::HTTP_CREATED);
            } else {
                $this->response(['message' => 'Erro ao criar usuário.'], RestController::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // Método PUT para atualizar um usuário
    public function index_put($id)
    {
        $data = $this->put();

        if (empty($data)) {
            $this->response(['message' => 'Nenhum dado para atualizar.'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        $this->form_validation->set_data($data);

        if (array_key_exists('name', $data)) {
            $this->form_validation->set_rules('name', 'Nome', 'trim');
        }
        if (array_key_exists('email', $data)) {
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email|unique_email_update[users.id]');
        }
        if (array_key_exists('password', $data)) {
            $this->form_validation->set_rules('password', 'Senha', 'min_length[8]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->response(['errors' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if ($this->user->update_user($id, $data)) {
            $this->response(['message' => 'Usuário atualizado com sucesso.'], RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Erro ao atualizar usuário.'], RestController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Método DELETE para deletar um usuário
    public function index_delete($id)
    {
        $user = $this->user->get_user($id);

        if (!$user) {
            $this->response(['message' => 'Usuário não encontrado.'], RestController::HTTP_NOT_FOUND);
            return;
        }

        if ($this->user->delete_user($id)) {
            $this->response(['message' => 'Usuário deletado com sucesso.'], RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Erro ao deletar usuário.'], RestController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}