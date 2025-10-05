<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verify_jwt_token()
{
    $CI =& get_instance();

    try {
        $authHeader = $CI->input->get_request_header('Authorization');
        if (!$authHeader) {
            throw new Exception('Token não fornecido.');
        }

        list($token) = sscanf($authHeader, 'Bearer %s');
        if (!$token) {
            throw new Exception('Formato do token inválido.');
        }

        $key = $CI->config->item('jwt_key');
        
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        
        return (array) $decoded->data;

    } catch (Exception $e) {
        log_message('error', 'JWT Verification Error: ' . $e->getMessage());
        return false;
    }
}