<?php

namespace App\Config\Filters;

use App\Filters\JWTAuthFilter;
use CodeIgniter\Config\BaseConfig;

class JWTFilters extends BaseConfig
{
    public array $aliases = [
        'jwt' => JWTAuthFilter::class,
    ];

    public array $globals = [
        'before' => [],
        'after'  => [],
    ];
}
