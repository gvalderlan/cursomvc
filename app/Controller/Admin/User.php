<?php

namespace App\Controller\Admin;

use App\Model\Entity\User as EntityUser;
use \App\Utils\View;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{

    /**
     * Métodos responsavel por obter a rendereizalção dos itens de usuários
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request,&$obPagination){
        //USUÁRIOS
        $itens = '';

        //Quantidade total de registros
        $quantidadeTotal = EntityUser::getUsers(null,null, null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        //Resultados da página
        $results = EntityUser::getUsers(null,'id DESC', $obPagination->getLimit());

        //Renderiza o item
        while ($obUser = $results->fetchObject(EntityUser::class)){
            $itens .= View::render('admin/modules/users/item',[
                'id'     => $obUser->id,
                'nome'   => $obUser->nome,
                'email'  => $obUser->email
            ]);
        }

        return $itens;
    }
    /**
     * Método responsável por renderiza a View de listagem de usuários
     * @param $request
     * @retunr string
     */
    public static function getUsers($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/users/index',[
            'itens'     => self::getUserItems($request,$obPagination),
            'pagination'=> parent::getPagination($request, $obPagination),
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Usuários > GabrielWeb', $content, 'users');
    }

    /**
     * Método responsável por retornar o fomulário de cadastro de um novo depoimentos
     * @param $request
     * @return string
     */
    public static function getNewUser($request){
        //CONTEUDO DO FORMULARIO
        $content = View::render('admin/modules/users/form',[
            'title' =>'Cadastrar usuário',
            'nome'  =>'',
            'email' =>'',
            'status'=> self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar usuário > GabrielWeb', $content, 'users');
    }

    /**
     * Método responsável por cadastrar um usuario no banco
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email']??'';
        $nome = $postVars['nome']??'';
        $senha = $postVars['senha']??'';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);
        if($obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        //NOVA INSTANCIA DE USUARIO
        $obUser = new EntityUser;
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);

        $obUser->cadastrar();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
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
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluído com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O E-mail digitado já está sendo utilizado!');
                break;

        }
    }

    /**
     * Método responsável por retornar o fomulário de edição de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id){
        //OBTEM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form',[
            'title'   =>'Editar usuário',
            'nome'    =>$obUser->nome,
            'email'   =>$obUser->email,
            'senha'   =>'',
            'status'  => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar Usuário > GabrielWeb', $content, 'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request, $id){
        //OBTEM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email']??'';
        $nome = $postVars['nome']??'';
        $senha = $postVars['senha']??'';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $id){
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        //ATUALIZA INSTANCIA
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }

    /**
     * Método responsável por retornar o fomulário de edição de um novo usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id){
        //OBTEM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }


        $content = View::render('admin/modules/users/delete',[
            'title'   =>'Excluir usuário',
            'nome'    =>$obUser->nome,
            'email'=>$obUser->email
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Excluir Usuário > GabrielWeb', $content, 'users');
    }

    /**
     * Método responsável por deletar um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request, $id){
        //OBTEM O USUARIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //EXCLUIR O USUÁRIO
        $obUser->excluir();

        //REDIRECIONA O USUARIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}