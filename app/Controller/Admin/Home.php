<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page
{
    /**
     * Método responsável por renderiza a View do painel
     * @param $request
     * @retunr string
     */
    public static function getHome($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/home/index',[]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Home > GabrielWeb', $content, 'home');
    }
}