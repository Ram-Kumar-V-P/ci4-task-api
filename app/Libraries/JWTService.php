<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private string $secret;
    private int $ttlMinutes;

    public function __construct()
    {
        $this->secret     = getenv('JWT_SECRET');
        $this->ttlMinutes = (int) getenv('JWT_TTL_MINUTES') ?: 120;
    }

    public function generateToken(array $claims): string
    {
        $now   = time();
        $exp   = $now + ($this->ttlMinutes * 60);
        $token = [
            'iss' => 'ci4-task-api',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'data'=> $claims
        ];
        return JWT::encode($token, $this->secret, 'HS256');
    }

    public function decode(string $jwt): ?object
    {
        return JWT::decode($jwt, new Key($this->secret, 'HS256'));
    }
}
