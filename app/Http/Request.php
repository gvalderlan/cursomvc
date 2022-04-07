<?php

namespace App\Http;

class Request
{
    /**
     * Metodo HTTp da reuisição
     * @var string
     */
    private $httpMethod;
    /**
     * URI da página
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];

    /**
     * Váriaveis recebidas via ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Cabçalho da requisição
     * @var array
     */
    private $headers = [];

    /**
     * Instância do router
     * @var $router
     */
    private $router;

    public function __construct($router)
    {
        $this->router      = $router;
        $this->queryParams = $_GET ?? [];
        $this->postVars    = $_POST ?? [];
        $this->headers     = getallheaders();
        $this->httpMethod  = $_SERVER['REQUEST_METHOD'] ?? [];
        $this->setUri();
    }

    /**
     * Método responsavel por definir a Uri
     */
    private function setUri(){
        //URI completa com GETS
        $this->uri = $_SERVER['REQUEST_URI'] ?? [];

        //Remove GETS URI
        $xUri = explode('?', $this->uri);
        $this->uri = $xUri[0];
    }

    /**
     * Método responsavel por retornar a instancia de router
     * @return Router
     */
    public function getRouter(){
        return$this->router;
    }
    /**
     * Método responsável por retornar o método HTTP da requisição
     * @return string
     */
    public function getHttpMethod(){
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição
     * @return string
     */
    public function getUri(){
        return $this->uri;
    }

    /**
     * Método responsável por retornar os headers da requisição
     * @return array
     */
    public function getHeaders(){
        return $this->headers;
    }

    /**
     * Método responsável por retornar os parametros da URL da requisição
     * @return string
     */
    public function getQueryParams(){
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar as variaveis POST da requisição
     * @return string
     */
    public function getPostVars(){
        return $this->postVars;
    }
}