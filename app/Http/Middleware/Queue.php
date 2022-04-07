<?php

namespace App\Http\Middleware;

use App\Http\Response;

class Queue
{

    /**
     * Mapeamento de middleware
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento de middlewares que são carregados em todas as rotas
     * @var array
     */
    private static $default = [];
    /**
     * Fila de middlewares a ser executados
     * @var array
     */
    private $middlewares = [];

    /**
     * FUNÇÃO DE EXECUÇÃO DO CONTROLADOR
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos da função do controlador
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Método responsável por contruir a fila de middleware
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Método responsavel por definir o mapeamento da middleware
     * @param array $map
     */
    public static function setMap($map){
        self::$map = $map;
    }

    public static function setDefault($default){
        self::$default = $default;
    }
    /**
     * Método responsável por executar o próximo nível da fila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request){

        //VERIFICA SE A FILA ESTA VAZIA
        if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        //MIDDLEWARE
        $middleware = array_shift($this->middlewares);

        //VERIFICA O MAPEAMENTO
        if(!isset(self::$map[$middleware])){
            throw new \Exception("Problemas ao processar o middleware da requisição", 500);
        }

        //NEXT
        $queue = $this;
        $next = function ($request) use($queue){
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request,$next);

    }
}