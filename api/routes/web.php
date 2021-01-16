<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return redirect('/mails/inbox');
});

$router->group(['prefix' => 'mails'], function () use ($router) {

    $router->get('/inbox', 'MailsController@getInbox');

    $router->get('/sent','MailsController@getSentEmails');

    $router->get('/trash', 'MailsController@getTrash');

    $router->get('/{id:[0-9]+}', 'MailsController@show');

    $router->post('/', 'MailsController@store');

    $router->put('/trash', 'MailsController@sentToTrash');

    $router->put('/mark', 'MailsController@markAsImportant');

    $router->put('/delete', 'MailsController@delete');

    $router->put('/read','MailsController@markAsRead');

});
