<?php

namespace App\Controller\Admin;

use \App\Model\Entity\User;
use \App\Utils\View;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page
{
    /**
     * Método responsável por retornar a renderização da página de alert
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request,$errorMessage = null)
    {
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage):'';

        //Conteúdo da página de LOGIN
        $content = View::render('admin/login',[
            'status'=> $status
        ]);
        //RETORNA APÁGINA COMPLETA
        return parent::getPage('Login > GabrielWeb',$content);

    }

    /**
     * Método responsável por definir o alert do usuário
     * @param Request $request
     */
    public static function setLogin($request){
        //POSTVARS
        $postVars = $request->getPostVars();
        $email = $postVars['email']??'';
        $senha = $postVars['senha']??'';

        //BUSCA USUARIO PELO EMAIL
        $obUser = User::getUserByEmail($email);
        if(!$obUser instanceof User){
            return self::getLogin($request, 'E-mail ou senha inválidos!');
        }

        //VERIFICA A SENHA DO USUÁRIO
        if(!password_verify($senha,$obUser->senha)){
            return self::getLogin($request, 'E-mail ou senha inválidos!');
        }

        //CRIA A SESSÃO DE LOGIN
        SessionAdminLogin::login($obUser);
        //REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
        $request->getRouter()->redirect('/admin');
    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     */
    public static function setLogout($request){

        //DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::logout();
        //REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/admin/login');
    }
}