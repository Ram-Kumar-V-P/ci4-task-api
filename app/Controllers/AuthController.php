<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\JWTService;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function ping()
    {
        return respondSuccess(['message' => 'Task API up']);
    }

    public function register()
    {
        $rules = [
            'name'     => 'required|min_length[2]|max_length[120]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[128]',
        ];
        if (! $this->validate($rules)) {
            return respondFail('Validation failed', ResponseInterface::HTTP_UNPROCESSABLE_ENTITY, $this->validator->getErrors());
        }

        $input = (array)$this->request->getJSON();

        $userId = (new UserModel())->insert([
            'name'          => trim($input['name']),
            'email'         => strtolower($input['email']),
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
        ]);

        return respondSuccess(['id' => $userId], ResponseInterface::HTTP_CREATED);
    }

    public function login()
    {
        $input = (array)$this->request->getJSON();
        $email = strtolower((string)$input['email']);
        $pass  = (string)$input['password'];

        $user = (new UserModel())->byEmail($email);
        if (! $user || ! password_verify($pass, $user['password_hash'])) {
            return respondFail('Invalid credentials', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = (new JWTService())->generateToken(['id' => $user['id'], 'email' => $user['email']]);

        return respondSuccess(['token' => $token]);
    }

    public function me()
    {
        $u = service('request')->user;
        return respondSuccess([
            'id'    => $u['id'],
            'name'  => $u['name'],
            'email' => $u['email']
        ]);
    }
}
