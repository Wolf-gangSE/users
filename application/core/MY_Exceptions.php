<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

    public function __construct()
    {
        parent::__construct();
    }

    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        $this->output_json_error($status_code, $heading, $message);
    }

    public function show_exception($exception)
    {
        $status_code = 500;
        $message = $exception->getMessage();
        
        if (empty($message)) {
            $message = 'Uncaught Exception of type ' . get_class($exception);
        }

        $this->output_json_error($status_code, 'Uncaught Exception', $message, $exception->getFile(), $exception->getLine());
    }

    public function show_php_error($severity, $message, $filepath, $line)
    {
        $status_code = 500;
        $heading = 'A PHP Error was encountered';

        $this->output_json_error($status_code, $heading, $message, $filepath, $line);
    }

    private function output_json_error($status_code, $heading, $message, $filepath = null, $line = null)
    {
        $status_code = is_numeric($status_code) && $status_code > 100 ? (int) $status_code : 500;
        
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status_code);

        if (ENVIRONMENT === 'development') {
            $response = [
                'status'  => 'error',
                'error'   => $heading,
                'message' => is_array($message) ? implode("\n", $message) : strip_tags($message),
                'debug'   => [
                    'file' => str_replace(FCPATH, '', $filepath),
                    'line' => $line
                ]
            ];
        } else {
            $response = [
                'status'  => 'error',
                'message' => 'Ocorreu um erro inesperado no servidor. Por favor, tente novamente mais tarde.'
            ];
            log_message('error', 'Heading: ' . $heading . ' Message: ' . (is_array($message) ? implode("\n", $message) : $message) . ' File: ' . $filepath . ' Line: ' . $line);
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit(1);
    }
}