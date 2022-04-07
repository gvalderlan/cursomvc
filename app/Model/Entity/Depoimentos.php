<?php

namespace App\Model\Entity;

use App\Controller\Admin\Testimony;
use \WilliamCosta\DatabaseManager\Database;

class Depoimentos
{
    /**
     * @var $id
     * @var $nome
     * @var $mensagem
     * @var $data
     */
    public $id;
    public $nome;
    public $mensagem;
    public $data;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        $this->data = date('Y-m-d H:i:s');

        //Insere o depoimento no BD
        $this->id = (new Database('depoimentos'))->insert([
                'nome'     => $this->nome,
                'mensagem' => $this->mensagem,
                'data'     => $this->data
        ]);

        return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instancia atual
     * @return boolean
     */
    public function atualizar(){

        //Insere o depoimento no BD
        return (new Database('depoimentos'))->update('id = '.$this->id,[
            'nome'     => $this->nome,
            'mensagem' => $this->mensagem
        ]);

    }

    /**
     * Método responsável por atualizar os dados do banco com a instancia atual
     * @return boolean
     */
    public function excluir(){

        //Exclui o depoimento no BD
        return (new Database('depoimentos'))->delete('id = '.$this->id);

    }

    /**
     * Método responsável por retornar um depoimento com base no seu id
     * @param integer $id
     * @return Testimony
     */
    public static function getTestimonyById($id){
        return self::getDepoimentos('id ='.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retonar os depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getDepoimentos($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('depoimentos'))->select($where,$order,$limit,$fields);
    }
}