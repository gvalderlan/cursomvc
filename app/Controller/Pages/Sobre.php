<?php
namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Sobre extends Page {

    /**
     * Método responsávelpor retornar o conteúdo (view) da nossa sobre
     * @return string
     */
    public static function getSobre(){
        $obOrganization = new Organization();

        $content = View::render('pages/sobre',[
            'name' => $obOrganization->name,
            'description' => $obOrganization->description,
            'site' => $obOrganization->site
        ]);

        //Retorna conteudo da página
        return parent::getPage('Sobre - GabrielWeb - Desenvolvimento de sites', $content);
    }
}