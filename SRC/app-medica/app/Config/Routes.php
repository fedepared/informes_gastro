<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// rutas view
$routes->get('/', 'Home::index'); // Ruta por defecto
$routes->get('/login', 'Home::index'); // Ruta por defecto
$routes->get('/formulario', 'Home::formulario');
$routes->get('/coberturas_view', 'Home::coberturas');
$routes->get('/reportes', 'Home::reportes');
$routes->get('/reset', 'Home::resetpass');
$routes->get('/error', 'Home::error');
// fin rutas view
$routes->get('/usuarios/verificarSesion', 'Usuarios::verificarSesion');
$routes->get('/logout', 'Usuarios::logout');
$routes->put('/cambio', 'Usuarios::cambiarPassword');
$routes->post('/login', 'Usuarios::login');
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
/* $routes->get('/informe/reenviar-informe/(:num)', 'Informes::reenviarInformePorId/$1'); */
$routes->get('/mai', 'Informes::enviarCorreoPrueba');
$routes->get('/descargar-archivo', 'Informes::descargarInformeCompleto');
$routes->get('/informe/imagenes/(:num)', 'Informes::getImagenesByInformeId/$1');
$routes->post('/informe/imagenes/update/(:num)', 'Informes::updateInformeImages/$1');
$routes->get('/informes/descargar-pdfs', 'Informes::downloadPdfsByDateRange');
$routes->get('/coberturas', 'Coberturas::getCoberturas');
$routes->get('/cobertura/(:num)', 'Coberturas::getByIdCoberturas/$1');
$routes->post('/cobertura/alta', 'Coberturas::postCobertura'); // Cambiado a POST
$routes->put('/cobertura/editar/(:num)', 'Coberturas::updateCobertura/$1'); // Cambiado a PUT
$routes->delete('/cobertura/borrar/(:num)', 'Coberturas::deleteCobertura/$1');

$routes->post('/reenviar-informe/(:num)', 'Informes::reenviarInformePorId/$1');

$routes->post('/solicitar-cambio-password', 'Usuarios::solicitarCambioPassword');
$routes->post('/verificar-codigo-cambio', 'Usuarios::verificarYActualizarPassword');
$routes->get('/informes-paginado', 'Informes::getInformesPaginado');