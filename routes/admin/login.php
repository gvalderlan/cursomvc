<?php
use \App\Http\Response;
use \App\Controller\Admin;

//Rota login
$obRouter->get('/admin/login',[
    'middlewares'=>[
        'required-admin-logout'
    ],
    function($request){
        return new Response('200',Admin\Login::getLogin($request));
    }
]);

//Rota login (post)
$obRouter->post('/admin/login',[
    'middlewares'=>[
        'required-admin-logout'
    ],
    function($request){
        return new Response('200',Admin\Login::setLogin($request));
    }
]);

//Rota de logout
$obRouter->get('/admin/logout',[
    'middlewares'=>[
        'required-admin-login'
    ],
    function($request){
        return new Response('200',Admin\Login::setLogout($request));
    }
]);
