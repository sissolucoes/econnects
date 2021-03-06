<?php
class Fatura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'fatura';
    protected $primary_key = 'fatura_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    //Dados
    public $validate = array(

    );

    public function with_fatura_status($fields = array('nome'))
    {
        $this->with_simple_relation('fatura_status', 'fatura_status_', 'fatura_status_id', $fields);
        return $this;
    }

    public function with_pedido($fields = array('codigo'))
    {
        $this->with_simple_relation('pedido', 'pedido_', 'pedido_id', $fields);
        return $this;
    }

    public function filterByPedido($pedido_id)
    {
        $this->_database->where("fatura.pedido_id", $pedido_id);
        return $this;
    }

    public function filterByTipo($tipo)
    {
        $this->_database->where("fatura.tipo", $tipo);
        return $this;
    }

    public function filterByDeletado($deletado)
    {
        $this->_database->where("fatura.deletado", $deletado);
        return $this;
    }

    public function insertFaturaEstorno($pedido_id, $valor_total)
    {

        $this->load->model('fatura_parcela_model', 'fatura_parcela');

        $dados_faturamento                       = array();
        $dados_faturamento['tipo']               = 'ESTORNO';
        $dados_faturamento['fatura_status_id']   = 1;
        $dados_faturamento['pedido_id']          = $pedido_id;
        $dados_faturamento['valor_total']        = (-1) * ($valor_total);
        $dados_faturamento['num_parcela']        = 1;
        $dados_faturamento['valor_parcela']      = (-1) * ($valor_total);
        $dados_faturamento['data_processamento'] = date('Y-m-d H:i:s');

        $fatura_id = $this->insert($dados_faturamento, true);

        $dados_parcelamento                       = array();
        $dados_parcelamento['fatura_status_id']   = 1;
        $dados_parcelamento['fatura_id']          = $fatura_id;
        $dados_parcelamento['num_parcela']        = 1;
        $dados_parcelamento['valor']              = (-1) * ($valor_total);
        $dados_parcelamento['data_vencimento']    = date('Y-m-d');
        $dados_parcelamento['data_processamento'] = date('Y-m-d H:i:s');

        $this->fatura_parcela->insert($dados_parcelamento, true);

    }

    public function pagamentoCompletoEfetuado($fatura_parcela_id)
    {
        $this->load->model('fatura_parcela_model', 'fatura_parcela');

        $fatura_parcela = $this->fatura_parcela->get($fatura_parcela_id);

        $dados_fatura                     = array();
        $dados_fatura['fatura_status_id'] = 2;
        $this->update($fatura_parcela['fatura_id'], $dados_fatura, true);
        $dados_parcela                     = array();
        $dados_parcela['data_pagamento']   = date('Y-m-d H:i:s');
        $dados_parcela['fatura_status_id'] = 2;
        $this->fatura_parcela->update($fatura_parcela_id, $dados_parcela, true);
    }

    public function insFaturaParcelas($pedido_id, $cotacao_id, $fatura_status_id, $valor_total, $num_parcela, $valor_parcela, $produto_parceiro_id)
    {

        $dados_faturamento                       = array();
        $dados_faturamento['fatura_status_id']   = $fatura_status_id;
        $dados_faturamento['pedido_id']          = $pedido_id;
        $dados_faturamento['valor_total']        = $valor_total;
        $dados_faturamento['num_parcela']        = $num_parcela;
        $dados_faturamento['valor_parcela']      = $valor_parcela;
        $dados_faturamento['data_processamento'] = date('Y-m-d H:i:s');

        $fatura_id = $this->insert($dados_faturamento, TRUE);
        $this->insFaturaParcelamentoDet($fatura_id, $cotacao_id, $produto_parceiro_id);

    }

    public function insFaturaParcelamentoDet($fatura_id, $cotacao_id, $produto_parceiro_id, $num_parcela = 1, $valor = 0, $dt_vencimento = NULL, $dt_pagamento = NULL){

        if ( empty($dt_vencimento) )
        {
            $dt_vencimento = date('Y-m-d', mktime(0,0,0, date('m'), date('d'), date('Y') ));
        }

        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('cotacao_model', 'cotacao');

        $insereItem = true;
        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
        $configuracao = $this->produto_parceiro_configuracao
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        if ($configuracao) {
            $configuracao = $configuracao[0];
            if ($configuracao['pagamento_tipo'] == 'RECORRENTE') {

                if($cotacao['produto_slug'] == 'seguro_viagem'){
                    $vigencia = array(
                        'inicio_vigencia' => $cotacao['data_saida'],
                        'fim_vigencia' =>  $cotacao['data_retorno'],
                        'dias' => $cotacao['qnt_dias']
                    );
                }elseif($cotacao['produto_slug'] == 'equipamento') {
                    $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($cotacao['produto_parceiro_plano_id'], $cotacao['nota_fiscal_data']);
                }elseif($cotacao['produto_slug'] == 'generico') {
                    $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($cotacao['produto_parceiro_plano_id'], date('Y-m-d'));
                }

                switch ($configuracao['pagamento_periodicidade_unidade']) {
                    case 'DIA':
                        $qnt_parcelas = round($vigencia['dias'] / $configuracao['pagamento_periodicidade']);
                        $dia          = 0;
                        for ($i = 1; $i <= $qnt_parcelas; $i++) {
                            $insereItem = false;
                            $dt_vencimento                            = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $dia, date('Y')));
                            $dados_parcelamento                       = array();
                            $dados_parcelamento['fatura_status_id']   = 1;
                            $dados_parcelamento['fatura_id']          = $fatura_id;
                            $dados_parcelamento['num_parcela']        = $i;
                            $dados_parcelamento['valor']              = $cotacao['premio_liquido_total'];
                            $dados_parcelamento['data_vencimento']    = $dt_vencimento;
                            $dados_parcelamento['data_processamento'] = date('Y-m-d H:i:s');
                            $dia += $configuracao['pagamento_periodicidade'];
                            $this->fatura_parcela->insert($dados_parcelamento, true);
                        }
                        break;
                    case 'MES':
                        $qnt_parcelas = round($vigencia['dias'] / ($configuracao['pagamento_periodicidade'] * 30));
                        $mes          = 0;
                        for ($i = 1; $i <= $qnt_parcelas; $i++) {
                            $insereItem = false;
                            $dt_vencimento                            = date('Y-m-d', mktime(0, 0, 0, date('m') + $mes, date('d'), date('Y')));
                            $dados_parcelamento                       = array();
                            $dados_parcelamento['fatura_status_id']   = 1;
                            $dados_parcelamento['fatura_id']          = $fatura_id;
                            $dados_parcelamento['num_parcela']        = $i;
                            $dados_parcelamento['valor']              = $cotacao['premio_liquido_total'];
                            $dados_parcelamento['data_vencimento']    = $dt_vencimento;
                            $dados_parcelamento['data_processamento'] = date('Y-m-d H:i:s');
                            $mes += $configuracao['pagamento_periodicidade'];
                            $this->fatura_parcela->insert($dados_parcelamento, true);
                        }
                        break;
                        break;
                    case 'ANO':
                        $qnt_parcelas = round($vigencia['dias'] / ($configuracao['pagamento_periodicidade'] * 365));
                        $ano          = 0;
                        for ($i = 1; $i <= $qnt_parcelas; $i++) {
                            $insereItem = false;
                            $dt_vencimento                            = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + $ano));
                            $dados_parcelamento                       = array();
                            $dados_parcelamento['fatura_status_id']   = 1;
                            $dados_parcelamento['fatura_id']          = $fatura_id;
                            $dados_parcelamento['num_parcela']        = $i;
                            $dados_parcelamento['valor']              = $cotacao['premio_liquido_total'];
                            $dados_parcelamento['data_vencimento']    = $dt_vencimento;
                            $dados_parcelamento['data_processamento'] = date('Y-m-d H:i:s');
                            $ano += $configuracao['pagamento_periodicidade'];
                            $this->fatura_parcela->insert($dados_parcelamento, true);
                        }
                        break;
                }
            }
        }

        if ($insereItem)
        {
            $dados_parcelamento                       = array();
            $dados_parcelamento['fatura_status_id']   = 1;
            $dados_parcelamento['fatura_id']          = $fatura_id;
            $dados_parcelamento['num_parcela']        = $num_parcela;
            $dados_parcelamento['valor']              = emptyor($valor, $cotacao['premio_liquido_total']);
            $dados_parcelamento['data_vencimento']    = $dt_vencimento;
            $dados_parcelamento['data_pagamento']     = $dt_pagamento;
            $dados_parcelamento['data_processamento'] = date('Y-m-d H:i:s');
            $this->fatura_parcela->insert($dados_parcelamento, TRUE);
        }

    }

    public function estornoCompletoEfetuado($pedido_id)
    {

        $this->load->model('fatura_parcela_model', 'parcela');

        //'FATURAMENTO','ESTORNO'
        $faturas = $this->filterByPedido($pedido_id)->filterByTipo('ESTORNO')->get_all();

        foreach ($faturas as $fatura) {
            $parcelas = $this->parcela->filterByFatura($fatura['fatura_id'])->get_all();

            $dados_fatura                     = array();
            $dados_fatura['fatura_status_id'] = 5;
            $dados_fatura['data_estorno']     = date('Y-m-d H:i:s');
            $this->update($fatura['fatura_id'], $dados_fatura, true);

            foreach ($parcelas as $parcela) {
                $dados_parcela                     = array();
                $dados_parcela['data_estorno']     = date('Y-m-d H:i:s');
                $dados_parcela['fatura_status_id'] = 5;
                $this->parcela->update($parcela['fatura_parcela_id'], $dados_parcela, true);
            }

        }

    }

    public function deleteFaturamento($pedido_id)
    {

        $this->load->model('fatura_parcela_model', 'parcela');

        $faturas = $this->filterByPedido($pedido_id)->get_all();

        foreach ($faturas as $fatura) {
            $parcelas = $this->parcela->filterByFatura($fatura['fatura_id'])->get_all();
            foreach ($parcelas as $parcela) {
                $this->parcela->delete($parcela['fatura_parcela_id']);
            }
            $this->delete($fatura['fatura_id']);

        }

    }

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data = array(

        );
        return $data;
    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

}
