<?php

namespace App\Session\Admin;

class Login
{
    /**
     * Método responsável por iniciar a sessão
     */
    private static function init(){
        //VERIFICA SE A SESSÃO NÃO ESTA ATIVA
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }
    /**
     * Método responsável por criar o alert do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login ($obUser){
        //INICIA A SESSÃO
        self::init();

        $_SESSION['admin']['usuario'] = [
            'id'=>$obUser->id,
            'nome'=>$obUser->nome,
            'email'=>$obUser->email
        ];

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged(){
        //INICIA A SESSÃO
        self::init();

        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['admin']['usuario']['id']);
    }

    /**
     * Método responsável pelo logout do usuário
     * @return boolean
     */
    public static function logout(){
        //INICIA A SESSÃO
        self::init();
        //DESLOGA USUÁRIO
        unset($_SESSION['admin']['usuario']);
        //SUCESSO
        return true;
    }

}