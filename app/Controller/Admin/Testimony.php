<?php

namespace App\Controller\Admin;

use App\Model\Entity\Depoimentos as EntityTestimony;
use \App\Utils\View;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{

    /**
     * Métodos responsavel por obter a rendereizalção dos itens de depoimentos
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItems($request,&$obPagination){
        $itens = '';

        //Quantidade total de registros
        $quantidadeTotal = EntityTestimony::getDepoimentos(null,null, null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        //Resultados da página
        $results = EntityTestimony::getDepoimentos(null,'id DESC', $obPagination->getLimit());

        //Renderiza o item
        while ($obDepoimento = $results->fetchObject(EntityTestimony::class)){
            $itens .= View::render('admin/modules/testimonies/item',[
                'id'     => $obDepoimento->id,
                'nome'     => $obDepoimento->nome,
                'mensagem' => $obDepoimento->mensagem,
                'data'     => date('d/m/Y H:i:s', strtotime($obDepoimento->data))
            ]);
        }

        return $itens;
    }
    /**
     * Método responsável por renderiza a View de listagem de depoimentos
     * @param $request
     * @retunr string
     */
    public static function getTestimonies($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/testimonies/index',[
            'itens'     => self::getTestimonyItems($request,$obPagination),
            'pagination'=> parent::getPagination($request, $obPagination),
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Depoimentos > GabrielWeb', $content, 'testimonies');
    }

    /**
     * Método responsável por retornar o fomulário de cadastro de um novo depoimentos
     * @param $request
     * @return string
     */
    public static function getNewTestimony($request){
        //CONTEUDO DO FORMULARIO
        $content = View::render('admin/modules/testimonies/form',[
            'title'=>'Cadastrar depoimento',
            'nome'=>'',
            'mensagem'=>'',
            'status'=>''
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar Depoimentos > GabrielWeb', $content, 'testimonies');
    }

    /**
     * Método responsável por cadastrar o depoimento no banco
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request)
    {
        //POST vARS
        $postVars = $request->getPostVars();

        //NOVA INSTANCIA DE DEPOISMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome']??'';
        $obTestimony->mensagem = $postVars['mensagem']??'';

        $obTestimony->cadastrar();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    public static function getStatus($request){
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']){
            case 'created':
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento excluído com sucesso!');
                break;

        }
    }

    /**
     * Método responsável por retornar o fomulário de edição de um novo depoimentos
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request, $id){
        //OBTEM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }


        $content = View::render('admin/modules/testimonies/form',[
            'title'   =>'Editar depoimento',
            'nome'    =>$obTestimony->nome,
            'mensagem'=>$obTestimony->mensagem,
            'status'  => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar Depoimentos > GabrielWeb', $content, 'testimonies');
    }

    /**
     * Método responsável por gravar a atualização de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditTestimony($request, $id){
        //OBTEM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA INSTANCIA
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
        $obTestimony->atualizar();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
    }

    /**
     * Método responsável por retornar o fomulário de edição de um novo depoimentos
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request, $id){
        //OBTEM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }


        $content = View::render('admin/modules/testimonies/delete',[
            'title'   =>'Excluir depoimento',
            'nome'    =>$obTestimony->nome,
            'mensagem'=>$obTestimony->mensagem
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Excluir Depoimento > GabrielWeb', $content, 'testimonies');
    }

    /**
     * Método responsável por deletar um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteTestimony($request, $id){
        //OBTEM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //EXCLUIR O DEPOIMENTO
        $obTestimony->excluir();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }
}