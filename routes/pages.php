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

//Rota Dinâmica
$obRouter->get('/pagina/{idPagina}/{acao}',[
    function($idPagina, $acao){
        return new Response('200','Página '.$idPagina.' - '.$acao);
    }
]);
