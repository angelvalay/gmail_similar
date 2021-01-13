<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return redirect('/mails');
});

$router->group(['prefix' => 'mails'], function () use ($router) {
    $router->get('/', function () {
        return 'return mails sent';
    });

    $router->get('/{id}', function ($id) {
        return 'return content mail '. $id;
    });

    $router->get('/deleted', function () {
        return 'return mails trashed';
    });

    $router->put('/mark', function (){
        // id's array, true o false for mark as important
        return 'mark as important a set mails';
    });

    $router->patch('/{id}', function ($id){
        return 'send mail to trash '.$id;
    });

    $router->delete('/{id}', function ($id) {
        return 'delete definitely mail '.$id;
    });

    $router->post('/', function () {
        return 'send info mail';
    });
});
