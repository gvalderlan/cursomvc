<?php
use \App\Http\Response;
use \App\Controller\Pages;

//Rota home
$obRouter->get('/',[
    function(){
        return new Response('200',Pages\Home::getHome());
    }
]);

//Rota Sobre
$obRouter->get('/sobre',[
    function(){
        return new Response('200',Pages\Sobre::getSobre());
    }
]);

//Rota Depoimentos
$obRouter->get('/depoimentos',[
    function($request){
        return new Response('200',Pages\Depoimentos::getDepoimentos($request));
    }
]);

//Rota Depoimentos insert
$obRouter->post('/depoimentos',[
    function($request){
        return new Response('200',Pages\Depoimentos::insereDepoimento($request));
    }
]);

//Rota Dinâmica
$obRouter->get('/pagina/{idPagina}/{acao}',[
    function($idPagina, $acao){
        return new Response('200','Página '.$idPagina.' - '.$acao);
    }
]);
