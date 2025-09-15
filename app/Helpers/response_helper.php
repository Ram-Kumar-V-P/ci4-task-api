<?php

use CodeIgniter\HTTP\ResponseInterface;

if (! function_exists('respondSuccess')) {
    function respondSuccess($data, int $code = ResponseInterface::HTTP_OK) {
        return service('response')->setStatusCode($code)->setJSON([
            'status'  => 'success',
            'data'    => $data
        ]);
    }
}

if (! function_exists('respondFail')) {
    function respondFail(string $message, int $code = ResponseInterface::HTTP_BAD_REQUEST, array $errors = []) {
        return service('response')->setStatusCode($code)->setJSON([
            'status'  => 'fail',
            'message' => $message,
            'errors'  => $errors,
        ]);
    }
}

if (! function_exists('respondError')) {
    function respondError(string $message, int $code = ResponseInterface::HTTP_INTERNAL_SERVER_ERROR) {
        return service('response')->setStatusCode($code)->setJSON([
            'status'  => 'error',
            'message' => $message
        ]);
    }
}
