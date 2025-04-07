<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// rutas view
$routes->get('/login', 'Home::index'); // Ruta por defecto
$routes->get('/formulario', 'Home::formulario');
$routes->get('/coberturas_view', 'Home::coberturas');
$routes->get('/reportes', 'Home::reportes');

// fin rutas view

$routes->get('/usuarios', 'Usuarios::getUsuarios');
$routes->get('/usuario/(:num)', 'Usuarios::getByIdUsuarios/$1');
$routes->post('/usuario/alta', 'Usuarios::postUsuarios');
$routes->get('/usuario/editar/(:num)', 'Usuarios::updateUsuarios/$1');
$routes->get('/usuario/borrar/(:num)', 'Usuarios::deleteUsuarios/$1');


$routes->get('/informes', 'Informes::getInformes');
$routes->get('/informe/(:num)', 'Informes::getByIdInformes/$1');
$routes->post('/informe/alta', 'Informes::postInforme');
$routes->put('/informe/editar/(:num)', 'Informes::updateInforme/$1');
$routes->get('/informe/borrar/(:num)', 'Informes::deleteInforme/$1');

$routes->get('/informe/mail', 'Informes::enviarCorreo');


$routes->get('/coberturas', 'Coberturas::getCoberturas');
$routes->get('/cobertura/(:num)', 'Coberturas::getByIdCoberturas/$1');
$routes->post('/cobertura/alta', 'Coberturas::postCobertura'); // Cambiado a POST
$routes->put('/cobertura/editar/(:num)', 'Coberturas::updateCobertura/$1'); // Cambiado a PUT
$routes->delete('/cobertura/borrar/(:num)', 'Coberturas::deleteCobertura/$1');



$routes->post('/login', 'Usuarios::login');
$routes->post('/cambio', 'Usuarios::cambiarPassword');
$routes->get('/descargar-archivo', 'Informes::descargarArchivo');
