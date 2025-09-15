<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('ping');
$routes->setAutoRoute(false);

// Root route
$routes->get('/', 'AuthController::ping');

// Auth
$routes->group('api', function ($routes) {
    $routes->group('auth', function ($routes) {
        $routes->post('register', 'AuthController::register');
        $routes->post('login', 'AuthController::login');
        $routes->get('me', 'AuthController::me', ['filter' => 'jwt']);
    });

    // Tasks & advanced features
    $routes->group('tasks', ['filter' => 'jwt'], function ($routes) {
        $routes->get('', 'TaskController::index');           // filtering + pagination + search
        $routes->post('', 'TaskController::create');
        $routes->get('(:num)', 'TaskController::show/$1');
        $routes->put('(:num)', 'TaskController::update/$1');
        $routes->delete('(:num)', 'TaskController::delete/$1');

        // Assignments (multi-user)
        $routes->post('(:num)/assignees', 'TaskController::assign/$1');
        $routes->get('(:num)/assignees', 'TaskController::assignees/$1');

        // Comments
        $routes->post('(:num)/comments', 'CommentController::create/$1');
        $routes->get('(:num)/comments', 'CommentController::index/$1');

        // Attachments
        $routes->post('(:num)/attachments', 'AttachmentController::upload/$1');
        $routes->get('(:num)/attachments', 'AttachmentController::index/$1');
        $routes->get('attachments/(:num)', 'AttachmentController::download/$1');
    });
});
