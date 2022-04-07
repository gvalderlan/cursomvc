<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router
{
    /**
     * URL completa do projeto (raiz)
     * @var string
     */

    private $url = '';
    /**
     * Prefixo de todas as rotas
     * @var string
     */

    private $prefix = '';

    /**
     * Indice de rotas
     * @var array
     */
    private $routes = [];

    /**
     * Instancia de request
     * @var Request
     */
    private $request;

    /**
     * Método responsavel por iniciar a classe
     * @param string $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url     = $url;
        $this->setPrefix();
    }

    /**
     * Metodo responsavel por fefinir o prefixo das rotas
     */
    private function setPrefix(){
        //Informações da url atual
        $parseUrl = parse_url($this->url);

        //Define o prefixo
        $this->prefix = $parseUrl['path'] ?? '';

    }

    /**
     * Metodo responsavel por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array $params
     */
    private function addRoute($method,$route,$params=[]){
        //Validação dos parâmetros
        foreach ($params as $key=>$value){
            if($value instanceof Closure){
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];

        //Váriaves da rota
        $params['variables'] = [];

        //Padrão de validação das váriaveis das rotas
        $patternVariable = '/{(.*?)}/';
            if(preg_match_all($patternVariable, $route, $matches)){
                $route = preg_replace($patternVariable, '(.*?)', $route);
                $params['variables'] = $matches[1];
            }

        //Padrão de validação da URL
        $patternRoute = '/^'.str_replace('/', '\/',$route).'$/';

        //Adiciona a rota na classe
        $this->routes[$patternRoute][$method] = $params;
    }
    /**
     * Método responsavel por definir uma rota de GET
     * @param string $route
     * @param array $params
     */
    public  function get($route,$params = []){
        return $this->addRoute('GET',$route,$params);
    }

    public  function post($route,$params = []){
        return $this->addRoute('POST',$route,$params);
    }

    public  function put($route,$params = []){
        return $this->addRoute('PUT',$route,$params);
    }

    public  function delete($route,$params = []){
        return $this->addRoute('DELETE',$route,$params);
    }
    /**
     * Metodo responsável por retonar a URL desconsiderando o prefixo
     * @return string
     */
    private function getUri(){
        //URI da Request
        $uri = $this->request->getUri();

        //Fatia a URI com o prefixo
        $xUri = strlen($this->prefix)? explode($this->prefix,$uri):[$uri];

        //Retorna a URI sem prefixo
        return end($xUri);
    }

    /**
     * Método responsável por retornar os dados da rota atual
     * @return array
     */
    private function getRoute(){
        //URI
        $uri = $this->getUri();

        //Method
        $httpMethod = $this->request->getHttpMethod();

        //Valida as rotas
        foreach ($this->routes as $patternRoute=>$methods){

            //Verifica se a URI bate com o padrão
            if(preg_match($patternRoute,$uri,$matches)){

                //Verificar o método
                if(isset($methods[$httpMethod])){
                   //Remove a primeira posição
                    unset($matches[0]);

                    //Variaveis processadas
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    //Retorno dos parametros da rota
                    return $methods[$httpMethod];
                }

                //Método não permitido definido
                throw new Exception("Método não é permitido", 405);
            }
        }
        throw new Exception("URL não encontrada", 404);
    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run(){
        try {
            //Obtém a rota atual
            $route = $this->getRoute();

            //verifica o controlador
            if(!isset($route['controller'])){
                throw new Exception("A URL não pode ser processada", 500);
            }

            //Argumentos da função
            $args = [];

            //Reflection
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name]??'';
            }

            //RETORNA A EXCUÇÃO DA FILA DE MIDDLEWARES
            return (new MiddlewareQueue($route['middlewares'],$route['controller'],$args))->next($this->request);

        }catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage());
        }
    }

    /**
     * METODO RESPONSAVEL POR RETORNAR A URL ATUAL
     * @return string
     */
    public function getCurrentUrl(){
        return $this->url.$this->getUri();
    }

    /**
     * Método responsavel por redirecionar a URL
     * @param string $route
     */
    public function redirect($route){
        $url = $this->url.$route;

        //EXECUTA O REDIRECT
        header('location: '.$url);
        exit();
    }

}