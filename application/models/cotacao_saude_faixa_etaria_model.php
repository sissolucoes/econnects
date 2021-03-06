<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Cotacao_Saude_Faixa_Etaria_Model extends MY_Model
{
    protected $_table = 'cotacao_saude_faixa_etaria';
    protected $primary_key = 'cotacao_saude_faixa_etaria_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    public $validate = array(
        array(
            'field' => 'cotacao_id',
            'label' => 'Cotação',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cotacao'
        ),
    );

    function filter_by_cotacao($cotacao_id){
        $this->_database->where("{$this->_table}.cotacao_id", $cotacao_id);
        return $this;
    }

    function filter_by_cotacao_auxiliar($slug, $cotacao_aux_id){
        
        $campo_id = "";
        if($slug == 'seguro_viagem')
        {
            $this->_database->join("cotacao_seguro_viagem aux", "aux.cotacao_id = {$this->_table}.cotacao_id AND aux.deletado = 0");
            $campo_id = "cotacao_seguro_viagem_id";
        }elseif($slug == 'equipamento')
        {
            $this->_database->join("cotacao_equipamento aux", "aux.cotacao_id = {$this->_table}.cotacao_id AND aux.deletado = 0");
            $campo_id = "cotacao_equipamento_id";
        }elseif( $slug == "generico" || $slug == "seguro_saude" )
        {
            $this->_database->join("cotacao_generico aux", "aux.cotacao_id = {$this->_table}.cotacao_id AND aux.deletado = 0");
            $campo_id = "cotacao_generico_id";
        }

        $this->_database->select(" TIMESTAMPDIFF(YEAR, aux.data_nascimento, NOW()) idade ");
        $this->_database->where("aux.{$campo_id}", $cotacao_aux_id);
        $this->_database->where(" TIMESTAMPDIFF(YEAR, aux.data_nascimento, NOW()) BETWEEN `cotacao_saude_faixa_etaria`.inicio AND `cotacao_saude_faixa_etaria`.fim ", "", FALSE);
        return $this;
    }

    /**
     * Truncate nos dados com validação
     * @param $data
     * @param $validation
     * @return null
     */
    public function atualiza_faixa_etaria($rows, $cotacao_id)
    {
        $this->delete_by(array('cotacao_id' => $cotacao_id));

        foreach($rows as $faixa)
        {
            $data = array();
            $data['cotacao_id'] = $cotacao_id;
            $data['inicio'] = $faixa['inicio'];
            $data['fim'] = $faixa['fim'];
            $data['quantidade'] = $faixa['quantidade'];

            if($this->validate($data))
            {
                if(!parent::insert($data, true))
                    return false;
            }
        }

        return true;
    }

    /**
     * Exibe a quantidade total de Beneficiarios
     * @param $cotacao_id
     * @return int
     */
    public function get_qtde_beneficiarios($cotacao_id)
    {
        $this->_database->select(" IFNULL(SUM(cotacao_saude_faixa_etaria.quantidade),0) as num_dependente", FALSE);
        $this->_database->where("{$this->_table}.cotacao_id", $cotacao_id);
        $result = $this->get_all(0, 0, false);
        return (!empty($result)) ? $result[0]['num_dependente'] : 0;
    }

}