<?php
namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Depoimentos as EntityDepo;
use \WilliamCosta\DatabaseManager\Pagination;

class Depoimentos extends Page {

    /**
     * Métodos responsavel por obter a rendereizalção dos itens de depoimentos
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getDepoimentosItens($request,&$obPagination){
        $itens = '';

        //Quantidade total de registros
        $quantidadeTotal = EntityDepo::getDepoimentos(null,null, null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 3);

        //Resultados da página
        $results = EntityDepo::getDepoimentos(null,'id DESC', $obPagination->getLimit());

        //Renderiza o item
        while ($obDepoimento = $results->fetchObject(EntityDepo::class)){
            $itens .= View::render('pages/depoimento/item',[
                'nome'     => $obDepoimento->nome,
                'mensagem' => $obDepoimento->mensagem,
                'data'     => date('d/m/Y H:i:s', strtotime($obDepoimento->data))
            ]);
        }

        return $itens;
    }

    /**
     * Método responsávelpor retornar o conteúdo (view) da nossa home
     * @package Request
     * @return string
     */
    public static function getDepoimentos($request){

        $content = View::render('pages/depoimentos',[
            'itens'=> self::getDepoimentosItens($request,$obPagination),
            'pagination' => parent::getPagination($request,$obPagination)
        ]);

        //Retorna conteudo da página
        return parent::getPage('Depoimentos - GabrielWeb - Desenvolvimento de sites', $content);
    }

    /**
     * Método responsável por cadastrar um depoimento
     * @param Request $request
     * @return string
     */
    public static function insereDepoimento($request){
        //Dados do POST
        $postVars = $request->getPostVars();

        //Nova instancia de depoimento
        $obDepoimento = new EntityDepo();
        $obDepoimento->nome = $postVars['nome'];
        $obDepoimento->mensagem = $postVars['mensagem'];
        $obDepoimento->cadastrar();

        //Retorna a pagina de listagem de depoimentos
        return self::getDepoimentos($request);
    }
}