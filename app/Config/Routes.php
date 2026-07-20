<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Client\LoginController::form');
$routes->get('/home', 'Home::index');

// --- Opérateur (protégé par auth) ---
$routes->get('/admin/login', 'Admin\AuthController::form');
$routes->post('/admin/login', 'Admin\AuthController::login');
$routes->get('/admin/logout', 'Admin\AuthController::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->post('prefixes/store', 'Admin\PrefixeController::store');
    $routes->post('prefixes/toggle/(:num)', 'Admin\PrefixeController::toggle/$1');

    $routes->get('baremes', 'Admin\BaremeController::index');
    $routes->post('baremes/store', 'Admin\BaremeController::store');
    $routes->post('baremes/update/(:num)', 'Admin\BaremeController::update/$1');
    $routes->post('baremes/delete/(:num)', 'Admin\BaremeController::delete/$1');

    $routes->get('gains', 'Admin\GainController::index');
    $routes->get('comptes', 'Admin\CompteController::index');
    $routes->get('operateurs', 'Admin\OperateurExterneController::index');
    $routes->post('operateurs/store', 'Admin\OperateurExterneController::store');
    $routes->post('operateurs/update/(:num)', 'Admin\OperateurExterneController::update/$1');
    $routes->post('operateurs/delete/(:num)', 'Admin\OperateurExterneController::delete/$1');
    $routes->get('montants-a-envoyer', 'Admin\MontantsAEnvoyerController::index');
});

// --- Client ---
$routes->get('/client/login', 'Client\LoginController::form');
$routes->post('/client/login', 'Client\LoginController::login');
$routes->get('/client/logout', 'Client\LoginController::logout');

$routes->group('client', ['filter' => 'client_auth'], function ($routes) {
    $routes->get('accueil', 'Client\CompteController::index');
    $routes->get('depot', 'Client\OperationController::formDepot');
    $routes->post('depot/valider', 'Client\OperationController::depot');
    $routes->get('retrait', 'Client\OperationController::formRetrait');
    $routes->post('retrait/valider', 'Client\OperationController::retrait');
    $routes->get('transfert', 'Client\OperationController::formTransfert');
    $routes->post('transfert/valider', 'Client\OperationController::transfert');
    $routes->get('historique', 'Client\OperationController::historique');
});
