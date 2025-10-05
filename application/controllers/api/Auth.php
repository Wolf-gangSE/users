<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Auth extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->model('User');
    }

    public function login_post()
    {
        $this->load->library('form_validation');

        $data = $this->post();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->response(['errors' => $this->form_validation->error_array()], RestController::HTTP_BAD_REQUEST);
            return;
        }

        $user = $this->db->get_where('users', ['email' => $data['email']])->row();

        if ($user && password_verify($data['password'], $user->password)) {
            $key = $this->config->item('jwt_key');
            $iat = time();
            $exp = $iat + 3600;

            $payload = [
                'iat' => $iat,
                'exp' => $exp,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            $this->response(['token' => $token], RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Credenciais inválidas.'], RestController::HTTP_UNAUTHORIZED);
        }
    }
}