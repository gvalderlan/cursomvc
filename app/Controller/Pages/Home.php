<?php
namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page {

    /**
     * Método responsávelpor retornar o conteúdo (view) da nossa home
     * @return string
     */
    public static function getHome(){
        $obOrganization = new Organization();

        $content = View::render('pages/home',[
            'name' => $obOrganization->name,
            'description' => $obOrganization->description,
            'site'=> $obOrganization->site
        ]);

        //Retorna conteudo da página
        return parent::getPage('GabrielWeb - Desenvolvimento de sites', $content);
    }
}