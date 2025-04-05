<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('songs', 'Songs::index');
$routes->get('songs/create', 'Songs::create');
$routes->get('songs/create/(:num)', 'Songs::create/$1');
$routes->post('songs/save', 'Songs::save');
$routes->get('songs/edit/(:num)', 'Songs::edit/$1');
$routes->post('songs/update/(:num)', 'Songs::update/$1');
$routes->delete('songs/delete/(:num)', 'Songs::delete/$1');
$routes->post('songs/preview', 'Songs::preview');
$routes->get('songs/preview/(:num)', 'Songs::preview/$1');

// Auth Routes
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
