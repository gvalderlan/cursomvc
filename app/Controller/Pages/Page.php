<?php
namespace App\Controller\Pages;

use App\Http\Request;
use \App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Page{

    /**
     * Método responsável por renderizar o topo da página
     * @return string
     */
    private static function getHeader(){
        return View::render('pages/header');
    }

    /**
     * Método responsável por renderizar o rodapé da página
     * @return string
     */
    private static function getFooter(){
        return View::render('pages/footer');
    }

    /**
     * Método responsavel por renderizar o layout de paginação
     * @param Request
     * @param Pagination
     * @return string
     */
    public static function getPagination($request, $obPagination){
        $pages = $obPagination->getPages();

        //VERIFICA A QUANTIDADE DE PÁGINAS
        if(count($pages) <- 1) return '';

        //LINKS
        $links = '';

        //URL ATUAL (SEM GETS);
        $url = $request->getRouter()->getCurrentUrl();

        //GET
        $queryParams = $request->getQueryParams();

        //RENDERIZA OS LINKS
        foreach ($pages as $page){
            //ALTERA PÁGINA
            $queryParams['page'] = $page['page'];

            //LINK
            $link = $url.'?'.http_build_query($queryParams);

            //VIEW
            $links .= View::render('pages/pagination/link',[
                'page'  => $page['page'],
                'link'  => $link,
                'active'=> $page['current'] ? 'active': ''
            ]);
        }

        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('pages/pagination/box',[
            'links'=> $links
        ]);
    }
    /**
     * Método responsável por retornar o conteúdo (view) da nossa página genérica
     * @return string
     */
    public static function getPage($title,$content){
        return View::render('pages/page',[
            'title' => $title,
            'header'=> self::getHeader(),
            'content' => $content,
            'footer'=> self::getFooter()
        ]);
    }
}