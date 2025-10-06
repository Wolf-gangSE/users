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

        // Limitar tentativas de login
        $max_attempts = 5;
        $lockout_time = 900; // 15 minutos

        if ($user && $user->failed_login_attempts >= $max_attempts) {
            $last_fail_time = strtotime($user->last_failed_login);
            
            if (time() - $last_fail_time < $lockout_time) {
                $this->response(['message' => 'Muitas tentativas de login. Tente novamente mais tarde.'], 429);
                return;
            }
        }

        if ($user && password_verify($data['password'], $user->password)) {
            if ($user->failed_login_attempts > 0) {
                $this->db->where('id', $user->id);
                $this->db->update('users', ['failed_login_attempts' => 0, 'last_failed_login' => null]);
            }

            $key = $this->config->item('jwt_key');
            $iat = time();
            $exp = $iat + 3600;

            $payload = [
                'iat' => $iat,
                'exp' => $exp,
                'data' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email
                ]
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            $this->response(['token' => $token], RestController::HTTP_OK);
        } else {
            if ($user) {
                $new_attempts = $user->failed_login_attempts + 1;
                $this->db->where('id', $user->id);
                $this->db->update('users', ['failed_login_attempts' => $new_attempts, 'last_failed_login' => date('Y-m-d H:i:s')]);
            }
            
            $this->response(['message' => 'Credenciais inválidas.'], RestController::HTTP_UNAUTHORIZED);
        }
    }
}