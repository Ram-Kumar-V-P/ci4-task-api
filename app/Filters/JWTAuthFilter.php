<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Libraries\JWTService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return respondFail('Missing or invalid Authorization header', ResponseInterface::HTTP_UNAUTHORIZED);
        }
        $jwt = substr($header, 7);

        try {
            $payload = (new JWTService())->decode($jwt);
            $userId  = $payload->data->id ?? null;
            if (! $userId) {
                return respondFail('Invalid token payload', ResponseInterface::HTTP_UNAUTHORIZED);
            }
            $user = (new UserModel())->find($userId);
            if (! $user) {
                return respondFail('User not found', ResponseInterface::HTTP_UNAUTHORIZED);
            }
            // Attach auth user to request (service container)
            service('request')->user = $user;
        } catch (\Throwable $e) {
            return respondFail('Token verification failed', ResponseInterface::HTTP_UNAUTHORIZED, ['token' => $e->getMessage()]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
