<?php
Class Cotacao_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_cobertura';
    protected $primary_key = 'cotacao_cobertura_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    //Dados
    public $validate = array(

    );

    function filterByID($cotacao_id){
        $this->_database->where("cotacao_id", $cotacao_id);
        $this->_database->where("deletado", 0);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function deleteByCotacao($cotacao_id){
        $this->db->where('cotacao_id', $cotacao_id);
        $this->db->delete($this->_table);
    }

    public function geraCotacaoCobertura($cotacao_id, $produto_parceiro_id, $produto_parceiro_plano_id = null, $importancia_segurada = null, $premio_liquido = null) {

        $coberturas = $this->plano_cobertura->with_prod_parc($produto_parceiro_id, $produto_parceiro_plano_id)->get_all();
        $importancia_segurada = floatval( $importancia_segurada );

        // limpa os dados
        $this->deleteByCotacao($cotacao_id);

        for( $i = 0; $i < sizeof( $coberturas ); $i++ ) {
            $cobertura = $coberturas[$i];
            $cobertura_plano_id = $cobertura["cobertura_plano_id"];
            $percentagem = $valor_cobertura = $valor_config = 0;

            switch ($cobertura["mostrar"]) {
                case 'importancia_segurada':
                    $percentagem = $valor_config = floatval($cobertura["porcentagem"]);
                    $valor_cobertura = ( $importancia_segurada * $percentagem ) / 100;
                    break;
                case 'preco':
                    $valor_cobertura = $valor_config = floatval($cobertura["preco"]);
                    break;
                case 'descricao':
                    $percentagem = $valor_config = floatval($cobertura["porcentagem"]);
                    if ($percentagem==0) {
                        $percentagem = $valor_config = 100;
                    }
                    $valor_cobertura = ( $premio_liquido * $percentagem ) / 100;
                    break;
            }

            $dados['cotacao_id'] = $cotacao_id;
            $dados['cobertura_plano_id'] = $cobertura_plano_id;
            $dados['valor'] = round($valor_cobertura, 2);
            $dados['iof'] = (empty($cobertura["usar_iof"])) ? 0 : $cobertura["iof"];
            $dados['mostrar'] = $cobertura["mostrar"];
            $dados['valor_config'] = $valor_config;
            $dados['criacao'] = date("Y-m-d H:i:s");
            $this->insert($dados, TRUE);

            $coberturas[$i]["valor_cobertura"] = $valor_cobertura;
        }

        return $coberturas;
    }

    /**
     * Retorna todos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function get_all($limit = 0, $offset = 0, $processa = true)
    {
        return parent::get_all($limit, $offset);
    }

}