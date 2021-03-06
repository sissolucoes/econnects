<?php
Class Capitalizacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao';
    protected $primary_key = 'capitalizacao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    //Dados
    public $validate = array(
        array(
            'field' => 'capitalizacao_tipo_id',
            'label' => 'Tipo de Capitalização',
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
            'field' => 'data_inicio',
            'label' => 'Início Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_fim',
            'label' => 'Fim Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'titulo_randomico',
            'label' => 'Título randômico',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qtde_titulos_por_compra',
            'label' => 'Quantidade de Títulos por compra',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_minimo_participacao',
            'label' => 'Valor Mínimo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_custo_titulo',
            'label' => 'Custo do título',
            'rules' => 'required_if[tipo_custo,0,Custo do título]',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_sorteio',
            'label' => 'Valor Sorteio',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ativo',
            'label' => 'Ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'serie',
            'label' => 'Série',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'capitalizacao_sorteio_id',
            'label' => 'Tipo de sorteio',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo_qnt_sorteio',
            'label' => 'Tipo de Qtde. de Sorteios',
            'rules' => "requerid",
            'groups' => 'default'
        ),
        array(
            'field' => 'qnt_sorteio',
            'label' => 'Quantidade Sorteios',
            'rules' => "required_if[tipo_qnt_sorteio,0,Quantidade de Sorteios]",
            'groups' => 'default'
        ),
        array(
            'field' => 'num_remessa',
            'label' => 'Número Seqüencial Remessa',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo_custo',
            'label' => 'Tipo de Custo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'dia_corte',
            'label' => 'Dia de Corte',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'responsavel_num_sorte',
            'label' => 'Responsável por Gerar Número da Sorte',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'responsavel_num_sorte_distribuicao',
            'label' => 'Responsável pela Distribuição do Número da Sorte',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_primeiro_sorteio',
            'label' => 'Primeiro Sorteio',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_interno',
            'label' => 'Código Interno do Produto',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'capitalizacao_tipo_id' => $this->input->post('capitalizacao_tipo_id'),
            'capitalizacao_sorteio_id' => $this->input->post('capitalizacao_sorteio_id'),
            'tipo_qnt_sorteio' => $this->input->post('tipo_qnt_sorteio'),
            'qnt_sorteio' => $this->input->post('qnt_sorteio'),
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'codigo_interno' => $this->input->post('codigo_interno'),
            'data_inicio' => app_dateonly_mask_to_mysql($this->input->post('data_inicio')),
            'data_fim' => app_dateonly_mask_to_mysql($this->input->post('data_fim')),
            'titulo_randomico' => $this->input->post('titulo_randomico'),
            'qtde_titulos_por_compra' => $this->input->post('qtde_titulos_por_compra'),
            'valor_minimo_participacao' => app_unformat_currency($this->input->post('valor_minimo_participacao')),
            'valor_custo_titulo' => str_replace(",",".",str_replace( "_", "", $this->input->post('valor_custo_titulo'))), // Voltei para o original | Alterado para Quero Quero
            'valor_sorteio' => app_unformat_currency($this->input->post('valor_sorteio')),
            'num_remessa' => $this->input->post('num_remessa'),
            'ativo' => $this->input->post('ativo'),
            'serie' => $this->input->post('serie'),
            'tipo_custo' => $this->input->post('tipo_custo'),
            'dia_corte' => $this->input->post('dia_corte'),
            'responsavel_num_sorte' => $this->input->post('responsavel_num_sorte'),
            'responsavel_num_sorte_distribuicao' => $this->input->post('responsavel_num_sorte_distribuicao'),
            'data_primeiro_sorteio' => app_dateonly_mask_to_mysql($this->input->post('data_primeiro_sorteio')),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    //Agrega tipo de capitalização
    function with_capitalizacao_tipo($fields = array('nome'))
    {
        $this->with_simple_relation('capitalizacao_tipo', 'capitalizacao_tipo_', 'capitalizacao_tipo_id', $fields );
        return $this;
    }

    function with_produto_parceiro_capitalizacao($produto_parceiro_id)
    {
        $this->_database->join("produto_parceiro_capitalizacao", "produto_parceiro_capitalizacao.capitalizacao_id = {$this->_table}.capitalizacao_id");
        $this->_database->where("produto_parceiro_capitalizacao.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("produto_parceiro_capitalizacao.deletado", 0);
        return $this;
    }

    function getTituloNaoUtilizado($capitalizacao_id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "
            SELECT capitalizacao_serie_titulo.capitalizacao_serie_titulo_id, capitalizacao_serie_titulo.numero, capitalizacao_serie_titulo.num_lote
            from capitalizacao
            inner JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            inner JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id
            WHERE 
                capitalizacao.capitalizacao_id = {$capitalizacao_id}
                AND capitalizacao.responsavel_num_sorte != 1 #PARCEIRO ENVIA O NÚMERO
                AND capitalizacao_serie.ativo = 1
                AND capitalizacao_serie.deletado = 0
                AND capitalizacao_serie_titulo.utilizado = 0
                AND capitalizacao_serie_titulo.ativo = 1 
                AND capitalizacao_serie.data_inicio < '{$date}'
                AND capitalizacao_serie.data_fim > '{$date}'
            ORDER BY IF(capitalizacao.titulo_randomico = 1, rand(), capitalizacao_serie_titulo.numero)
            LIMIT 1;
        ";

        return $this->_database->query($sql)->result_array();
    }

    function get_titulos_pedido($pedido_id){
        $sql = "
            SELECT *
            FROM capitalizacao
            INNER JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            INNER JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id
            WHERE capitalizacao_serie.ativo = 1
                AND capitalizacao_serie.deletado = 0
                AND capitalizacao_serie_titulo.ativo = 1 
                AND capitalizacao_serie_titulo.pedido_id = {$pedido_id}
            ORDER BY capitalizacao_serie_titulo.data_compra 
        ";

        return $this->_database->query($sql)->result_array();
    }

    public function getDadosSerie($capitalizacao_id, $numero_sorte, $num_proposta_capitalizacao = null)
    {
        $sqlAuxTitulo = '';
        if ( !empty($num_proposta_capitalizacao) )
            $sqlAuxTitulo .= " AND capitalizacao_serie_titulo.num_lote = '{$num_proposta_capitalizacao}' ";

        $date = date('Y-m-d H:i:s');
        $sql = "
            SELECT capitalizacao.*, capitalizacao_serie.*, capitalizacao_serie_titulo.capitalizacao_serie_titulo_id
            FROM capitalizacao
            INNER JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            LEFT JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id 
                AND capitalizacao_serie_titulo.numero = '{$numero_sorte}'
                {$sqlAuxTitulo}
                AND capitalizacao_serie_titulo.utilizado = 0
                AND capitalizacao_serie_titulo.ativo = 1 
            WHERE capitalizacao.capitalizacao_id = {$capitalizacao_id}
            AND capitalizacao_serie.ativo = 1
            AND capitalizacao_serie.deletado = 0
            AND capitalizacao_serie.data_inicio < '{$date}'
            AND capitalizacao_serie.data_fim > '{$date}'
            AND '{$numero_sorte}' BETWEEN CAST(capitalizacao_serie.numero_inicio as SIGNED) AND CAST(capitalizacao_serie.numero_fim as SIGNED)
        ";

        $result = $this->_database->query($sql)->result_array();
        return ($result) ? $result[0] : [];

    }

    public function numSorteRange($capitalizacao_id, $numero_sorte){

        $date = date('Y-m-d H:i:s');
        $sql = "
            SELECT *
            FROM capitalizacao
            INNER JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            WHERE capitalizacao.capitalizacao_id = {$capitalizacao_id}
            AND capitalizacao_serie.ativo = 1
            AND capitalizacao_serie.deletado = 0
            AND capitalizacao_serie.data_inicio < '{$date}'
            AND capitalizacao_serie.data_fim > '{$date}'
            AND '{$numero_sorte}' BETWEEN CAST(capitalizacao_serie.numero_inicio as SIGNED) AND CAST(capitalizacao_serie.numero_fim as SIGNED)
            LIMIT 1;
        ";

        $result = $this->_database->query($sql)->result_array();
        return ($result) ? $result[0] : [];

    }

    public function numSorteUtilizado($capitalizacao_id, $numero_sorte, $num_proposta_capitalizacao = null)
    {
        $sqlAux = '';
        if ( !empty($num_proposta_capitalizacao) )
            $sqlAux .= " AND capitalizacao_serie_titulo.num_lote = '{$num_proposta_capitalizacao}' ";

        $date = date('Y-m-d H:i:s');
        $sql = "
            SELECT *
            FROM capitalizacao
            INNER JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            INNER JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id 
            WHERE capitalizacao.capitalizacao_id = {$capitalizacao_id}
                AND capitalizacao_serie.ativo = 1
                AND capitalizacao_serie.deletado = 0
                AND capitalizacao_serie_titulo.deletado = 0
                AND capitalizacao_serie_titulo.utilizado = 0 #retirado temporariamente devido à Pompeia que reutiliza o número (devia ser 1)
                AND capitalizacao_serie_titulo.ativo = 1 
                AND capitalizacao_serie.data_inicio < '{$date}'
                AND capitalizacao_serie.data_fim > '{$date}'
                AND capitalizacao_serie_titulo.numero = '{$numero_sorte}'
                {$sqlAux}
            LIMIT 1;
        ";

        $result = $this->_database->query($sql)->result_array();
        return ($result) ? $result[0] : [];

    }

    public function validaNumeroSorte($cotacao_id)
    {
        $this->load->model('produto_parceiro_capitalizacao_model', 'produto_parceiro_capitalizacao');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('cotacao_model', 'cotacao');

        $result['status'] = true;
        $result['message'] = 'OK';
        $capitalizacoes = [];

        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);

        $plano_capitalizacao = $this->produto_parceiro_plano
            ->with_capitalizacao()
            ->filter_by_capitalizacao_ativa()
            ->get_by_id($cotacao['produto_parceiro_plano_id']);

        if ( !empty($plano_capitalizacao) )
        {
            // get_by_id retorna apenas 1 registro
            $capitalizacoes[] = $plano_capitalizacao;
        } 
        else
        {
            //verifica se tem capitalização configurado
            $parceiro_capitalizacao = $this->produto_parceiro_capitalizacao->with_capitalizacao()
                ->filter_by_produto_parceiro($cotacao['produto_parceiro_id'])
                ->filter_by_capitalizacao_ativa()
                ->get_all();

            //capitalização
            if ( !empty($parceiro_capitalizacao) )
            {
                // get_all retornar vários registros
                $capitalizacoes = $parceiro_capitalizacao;
            }
        }

        if ( empty($capitalizacoes) )
        {
            $result['message'] = "Produto/Plano não está configurado para aceitar Número da Sorte";
            return $result;
        }

        foreach ($capitalizacoes as $index => $item)
        {
            // Parceiro é o responsável pela distribuição do número da sorte
            if ( $item['capitalizacao_responsavel_num_sorte_distribuicao'] == 1 )
            {
                // verifica se possui capitalizacao nas coberturas
                if ( !$this->cotacao->tem_capitalizacao($cotacao_id) )
                {
                    $result['message'] = "A cotação não possui cobertura de Capitalização";

                } else
                {
                    if ( !isset($cotacao["numero_sorte"]) || ( empty($cotacao["numero_sorte"]) && strlen($cotacao["numero_sorte"]) == 0) )
                    {
                        $result['status'] = false;
                        $result['message'] = 'O Número da Sorte não foi informado';

                    // validar se está dentro da range
                    } elseif ( !$this->numSorteRange($item['capitalizacao_id'], $cotacao["numero_sorte"]) )
                    {
                        $result['status'] = false;
                        $result['message'] = 'Número da Sorte fora do Range aceito';

                    } elseif ( $this->numSorteUtilizado($item['capitalizacao_id'], $cotacao["numero_sorte"], $cotacao["num_proposta_capitalizacao"]) )
                    {
                        $result['status'] = false;
                        $result['message'] = 'Número da Sorte já utilizado';
                    }
                }

            } else
            {
                // Se não é gerado pelo parceiro não pode ser recebido
                if ( !empty($cotacao["numero_sorte"]) )
                {
                    $result['status'] = false;
                    $result['message'] = 'O Número da Sorte não deve ser gerado pelo parceiro';
                } else
                {
                    $result['message'] = "Parceiro não é o responsável por enviar o Número da Sorte";
                }
            }
        }

        return $result;
    }

    function filter_by_codigo_interno($codigo_interno){
        $this->_database->where("{$this->_table}.codigo_interno", $codigo_interno);
        return $this;
    }

}
