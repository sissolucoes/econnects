<?php
Class Equipamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'equipamento';
    protected $primary_key = 'equipamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');
    //Dados
    public $validate = array(
        array(
            'field' => 'equipamento_marca_id',
            'label' => 'Marca',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'equipamento_marca'
        ),
        array(
            'field' => 'equipamento_categoria_id',
            'label' => 'Categoria',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'equipamento_categoria'
        ), /*
        array(
            'field' => 'equipamento_sub_categoria_id',
            'label' => 'Sub Categoria',
            'rules' => '',
            'groups' => 'default',
            'foreign' => 'equipamento_categoria',
            'foreign_key' => 'equipamento_categoria_id',
            'foreign_join' => 'left'
        ), */
        array(
            'field' => 'ean',
            'label' => 'EAN',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tags',
            'label' => 'TAGS',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'marca',
            'label' => 'Marca',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'skus',
            'label' => 'SKUS',
            'rules' => '',
            'groups' => 'default'
        )
    );

    public function match($equipamento)
    {
        $equipamento_tratado = $this->trata_string_match($equipamento);
        $equip = $this->_database->query("
            SELECT MATCH(beel.name) against('{$equipamento_tratado}' IN BOOLEAN MODE) as indice, beel.idEquipamento AS equipamento_id, beel.idMarca AS equipamento_marca_id, beel.category AS equipamento_categoria_id, beel.name AS nome, beel.ean
            FROM Equipamentos beel
            JOIN Equipamentos_Marcas beM ON beel.idMarca = beM.idEquipamentos_Marcas
            JOIN Equipamentos_Linhas beL ON beel.category = beL.idEquipamentos_Linhas
            WHERE MATCH(beel.name) AGAINST('{$equipamento_tratado}' IN BOOLEAN MODE) > 0
            ORDER BY 1 DESC
            LIMIT 1
        ");
        $row = null;
        if ($equip){
            $row = $equip->result();
            $row = $row[0];
        }

        return $row;
    }

    public function trata_string_match($string){
        if(empty($string)) return $string;

        $string = trim($string);
        $string = str_replace(' e ', ' ', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('(', '', $string);
        $string = str_replace(')', '', $string);
        $string = str_replace(' -', ' ', $string);
        $string = str_replace('- ', ' ', $string);
        $string = str_replace('+', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('>', '', $string);
        $string = str_replace('<', '', $string);
        $string = str_replace('~', '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace( "CELULAR", "", strtoupper( $string ) );
        $string = str_replace( "CEL", "", strtoupper( $string ) );
        $string = str_replace( "  ", " ", strtoupper( $string ) );
        $string = str_replace( " ", "* ", strtoupper( $string ) );

        //$string = preg_replace('/\s+/', '$1* ', $string);
        $string = preg_replace('/\s+[\W|\w]\s+/', '$1', ' '.$string.' ');

        $string = trim($string);
        $string .= (isset($string) && substr($string, -1) != "*") ? "*" : "";

        return $string;
    }
}
