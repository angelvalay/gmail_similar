<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return redirect('/mails');
});

$router->group(['prefix' => 'mails'], function () use ($router) {

    $router->get('/', 'MailsController@getAll');

    $router->get('/{id:[0-9]+}', 'MailsController@show');

    $router->post('/', 'MailsController@store');

    $router->get('/trash', 'MailsController@getTrash');

    $router->put('/mark', 'MailsController@markAsImportant');

    $router->delete('/{id:[0-9]+}', 'MailsController@sentToTrash');

});
