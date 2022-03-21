<?php

namespace App\Http;

class Response
{
    /**
     * Código do status HTTP
     * @var int
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do Response
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo que está sendo retornado
     * @var string
     */
    private $contentType = 'text/html';
    /**
     * Conteúdo do Response
     * @var mixed
     */
    private $content;

    /**
     * Método responsável por inicar a classe e definir os valores
     * @param int $httpCode
     * @param $content
     * @param string $contentType
     */
    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);

    }

    /**
     * Metodo responsavel por alterar o content type do response
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeaders('Content-Type', $contentType);
    }

    /**
     * Metodo responsavel por adicionar um registro no cabeçalho de response
     * @param string $key
     * @param string $value
     */
    public function addHeaders($key, $value){
        $this->headers[$key] = $value;
    }
    private function sendHeaders(){
        //Status
        http_response_code($this->httpCode);
        //Enviar os headers
        foreach ($this->headers as $key=>$value){
            header($key.': '.$value);
        }
    }

    /**
     * Metodo responsavel por enviar responsta para o usuário
     */
    public function sendResponse(){
        //Envia os headers
        $this->sendHeaders();
        //Imprime o conteudo
        switch ($this->contentType){
            case 'text/html':
                echo $this->content;
                break;
        }
    }
}