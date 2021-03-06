<?php
Class Forma_Pagamento_Tipo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'forma_pagamento_tipo';
    protected $primary_key = 'forma_pagamento_tipo_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'forma_pagamento_integracao_id',
            'label' => 'Tipo de Integração',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'forma_pagamento_integracao_id' => $this->input->post('forma_pagamento_integracao_id')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


}
