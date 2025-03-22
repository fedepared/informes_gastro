<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index'); // Ruta por defecto

$routes->get('/coberturas', 'Coberturas::getCoberturas');
$routes->get('/cobertura/(:num)', 'Coberturas::getByIdCoberturas/$1');
$routes->get('/cobertura/alta', 'Coberturas::postCobertura');
$routes->get('/cobertura/editar/(:num)', 'Coberturas::updateCobertura/$1');
$routes->get('/cobertura/borrar/(:num)', 'Coberturas::deleteCobertura/$1');


