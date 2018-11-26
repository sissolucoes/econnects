<?php

Class Pedido_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'pedido';
  protected $primary_key = 'pedido_id';

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

  // const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
  // const FORMA_PAGAMENTO_FATURADO = 3;
  // const FORMA_PAGAMENTO_CARTAO_DEBITO = 6;
  // const FORMA_PAGAMENTO_BOLETO = 5;

  const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
  const FORMA_PAGAMENTO_TRANSF_BRADESCO = 2;
  const FORMA_PAGAMENTO_TRANSF_BB = 7;
  const FORMA_PAGAMENTO_CARTAO_DEBITO = 8;
  const FORMA_PAGAMENTO_BOLETO = 9;
  const FORMA_PAGAMENTO_FATURADO = 10;
  const FORMA_PAGAMENTO_CHECKOUT_PAGMAX = 11;
  const FORMA_PAGAMENTO_TERCEIROS = 12;


  //Dados
  public $validate = array(
    array(
      'field' => 'cotacao_id',
      'label' => 'Cotação',
      'rules' => 'required',
      'groups' => 'default',
      'foreign' => 'cotacao',
    ),
    array(
      'field' => 'produto_parceiro_pagamento_id',
      'label' => 'Parceiro Pagamento',
      'rules' => 'required',
      'groups' => 'default',
      'foreign' => 'produto_parceiro_pagamento'
    ),
    array(
      'field' => 'pedido_status_id',
      'label' => 'Status do pedido',
      'rules' => 'required',
      'groups' => 'default',
      'foreign' => 'pedido_status'
    ),
  );


  function getPedidoCarrinho($usuario_id){



    $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.codigo, pedido.codigo")
      ->select("pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id")
      ->join("cotacao", "pedido.cotacao_id = cotacao.cotacao_id", 'inner')
      ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
      ->where("pedido.pedido_status_id", 13)
      ->where("pedido.alteracao_usuario_id", $usuario_id);

    $carrinho = $this->get_all();

    /*
        $sql = "
                SELECT 
                     pedido.pedido_id, pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id
                FROM pedido
                INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
                INNER JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id
                WHERE
                    pedido.deletado = 0 AND
                    cotacao.deletado = 0 AND
                    cotacao_seguro_viagem.deletado = 0 AND
                    produto_parceiro.deletado = 0 AND 
                    pedido.pedido_status_id = 13 AND
                    pedido.alteracao_usuario_id = {$usuario_id}




        ";

        return $this->_database->query($sql)->result_array(); */
    return $carrinho;

  }

  function getPedidosByID($pedidos){


    //$pedidos =  implode(',', $pedidos);

    $this->_database->select("pedido.pedido_id, pedido.pedido_status_id, pedido_status.nome as pedido_status_nome")
      ->select("pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id")
      ->join("pedido_status", "pedido_status.pedido_status_id = pedido.pedido_status_id", 'inner')
      ->join("cotacao", "pedido.cotacao_id = cotacao.cotacao_id", 'inner')
      ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')

      ->where_in("pedido.pedido_id", $pedidos);

    $pedidos = $this->get_all();
    return ($pedidos) ? $pedidos : array();

    /*

        if($pedidos) {
            $sql = "
                    SELECT 
                         pedido.pedido_id, pedido.pedido_status_id, pedido_status.nome as pedido_status_nome, pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id
                    FROM pedido
                    INNER JOIN pedido_status ON pedido_status.pedido_status_id = pedido.pedido_status_id
                    INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
                    INNER JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                    INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id
                    WHERE
                        pedido.deletado = 0 AND
                        cotacao.deletado = 0 AND
                        cotacao_seguro_viagem.deletado = 0 AND
                        produto_parceiro.deletado = 0 AND 
                        pedido.pedido_id IN ({$pedidos}) 



            ";
            return $this->_database->query($sql)->result_array();
        }else{
            return array();
        }
*/


  }


  function getPedidoPagamentoPendente($pedido_id = 0){

    $this->_database->distinct()
      ->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
      ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
      ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
      ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
      ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
      ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
      ->join("fatura", "fatura.pedido_id = pedido.pedido_id", 'inner')
      ->join("fatura_parcela", "fatura.fatura_id = fatura_parcela.fatura_id", 'inner')
      ->where_in("pedido.pedido_status_id", array(2,3,15,4))
      ->where_in("pedido.lock", 0)
      ->where("fatura_parcela.data_vencimento <=", date('y-m-d'))
      ->where("fatura_parcela.fatura_status_id", 1);

    if($pedido_id > 0){
      $this->_database->where_in("pedido.pedido_id", $pedido_id);
    }

    $pedidos = $this->get_all();
    //exit($this->_database->last_query());

    //log_message('debug', 'BUSCANDO PEDIDOS PENDENTES QUERY - ' . $this->_database->last_query());
    return ($pedidos) ? $pedidos : array();

    /*
        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN  on 
                    INNER JOIN  on 
                    INNER JOIN  on 
                    WHERE 
                      pedido. = 2  
                      AND pedido. = 0   
            ";



        return $this->_database->query($sql)->result_array();*/

  }


  function getPedidoPagamentoPendenteDebito($pedido_id = 0){


    $this->_database->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
      ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
      ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
      ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
      ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
      ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
      ->where_in("pedido.pedido_status_id", 14)
      ->where_in("pedido.lock", 0);

    if($pedido_id > 0){
      $this->_database->where_in("pedido.pedido_id", $pedido_id);
    }

    $pedidos = $this->get_all();
    return ($pedidos) ? $pedidos : array();

    /*
        $pedido_id = (int)$pedido_id;

        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN forma_pagamento on forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id
                    INNER JOIN forma_pagamento_tipo on forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                    INNER JOIN forma_pagamento_integracao on forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id
                    WHERE 
                      pedido.pedido_status_id = 14 
                      AND pedido.lock = 0   
            ";

        if($pedido_id > 0){
            $sql .= " AND pedido.pedido_id = {$pedido_id} ";
        }

        return $this->_database->query($sql)->result_array();*/

  }

  function getPedidoCanceladoEstorno($pedido_id = 0){

    $this->_database->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
      ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
      ->select("pedido_cartao_transacao.tid, pedido_cartao_transacao.pedido_cartao_transacao_id")
      ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
      ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
      ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
      ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
      ->join("pedido_cartao", "pedido.pedido_id = pedido_cartao.pedido_id", 'inner')
      ->join("pedido_cartao_transacao", "pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id", 'inner')
      ->where_in("pedido.pedido_status_id", 5)
      ->where_in("pedido.lock", 0)
      ->where_in("pedido_cartao_transacao.result", 'OK');


    if($pedido_id > 0){
      $this->_database->where_in("pedido.pedido_id", $pedido_id);
    }

    $pedidos = $this->get_all();
    return ($pedidos) ? $pedidos : array();


    /*

        $pedido_id = (int)$pedido_id;

        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug, pedido_cartao_transacao.tid, pedido_cartao_transacao.pedido_cartao_transacao_id
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN forma_pagamento on forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id
                    INNER JOIN forma_pagamento_tipo on forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                    INNER JOIN forma_pagamento_integracao on forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id
                    INNER JOIN pedido_cartao on pedido.pedido_id = pedido_cartao.pedido_id
                    INNER JOIN pedido_cartao_transacao ON pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id
                    WHERE 
                      pedido.pedido_status_id = 5 
                      AND pedido.lock = 0   
                      AND pedido_cartao_transacao.result = 'OK'
            ";

        if($pedido_id > 0){
            $sql .= " AND pedido.pedido_id = {$pedido_id} ";
        }

        return $this->_database->query($sql)->result_array(); */

  }



  public function filterPesquisa()
  {

    $filters = $this->input->get();
    //  print_r($filters);exit;

    if($filters) {
      foreach ($filters as $key => $value)
      {
        if (!empty($value)) {
          switch ($key) {
            case "pedido_codigo":
              $this->_database->like('pedido.codigo', $value);
              break;
            case "razao_nome":
              $this->_database->like('cliente.razao_nome', $value);
              break;
            case "cnpj_cpf":
              $this->_database->like('cliente.cnpj_cpf', $value);
              break;
            case "data_nascimento":
              $this->_database->where('cliente.data_nascimento', app_dateonly_mask_to_mysql($value));
              break;
            case "pedido_status_id":
              $this->_database->where('pedido.pedido_status_id', ($value));
              break;
            case "fatura_status_id":
              $this->_database->where('fatura.fatura_status_id', ($value));
              break;

            case "inadimplencia":

              $hoje = date("Y-m-d");

              $this->_database->distinct();
              $this->_database->join("fatura_parcela", "fatura_parcela.fatura_id = fatura.fatura_id");
              $this->_database->join("fatura_status ps", "ps.fatura_status_id = fatura_parcela.fatura_status_id");
              $this->_database->where("fatura_parcela.data_vencimento < NOW()");
              $this->_database->where("ps.slug != 'faturado'");
              $this->_database->order_by("fatura_parcela.data_vencimento asc");

              break;
          }


        }
      }

    }
    return $this;
  }

  public function filterAPI($param = array())
  {


    if($param) {
      foreach ($param as $key => $value)
      {
        if (!empty($value)) {
          switch ($key) {
            case "apolice_id":
              $this->_database->where('apolice.apolice_id', $value);
              break;
            case "num_apolice":
              $this->_database->where('apolice.num_apolice', $value);
              break;
            case "documento":
              $this->_database->like('cliente.cnpj_cpf', $value);
              break;
            case "pedido_id":
              $this->_database->where('pedido.pedido_id', $value);
              break;                        
          }


        }
      }

    }
    return $this;
  }

  public function isInadimplente($pedido_id){

    $this->_database->distinct();
    $this->_database->join("fatura", "fatura.pedido_id = pedido.pedido_id");
    $this->_database->join("fatura_parcela", "fatura_parcela.fatura_id = fatura.fatura_id");
    $this->_database->join("fatura_status ps", "ps.fatura_status_id = fatura_parcela.fatura_status_id");
    $this->_database->where("fatura_parcela.data_vencimento < NOW()");
    $this->_database->where("ps.slug != 'faturado'");
    $this->_database->where("pedido.pedido_id = {$pedido_id}");
    $this->_database->order_by("fatura_parcela.data_vencimento asc");

    $total = $this->get_total();

    if($total > 0){
      return true;
    }else{
      return false;
    }



  }

  public function filterNotCarrinho()
  {

    $this->_database->where('pedido.pedido_status_id !=', 13);

    return $this;
  }
  public function filterCliente($cliente_id)
  {

    $this->_database->where('cotacao.cliente_id', $cliente_id);
    $this->_database->where_in('pedido.pedido_status_id', array(2,3,4,5,6,7,8,10,11,12,14,15));

    return $this;
  }

  public function build_faturamento( $engine = "generico" ) {
    if( $engine == "seguro_saude" ) {
      $engine = "generico";
    }
    $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id, pedido.num_parcela, pedido.valor_parcela")
      ->select("cotacao_{$engine}.*")
      ->select("cotacao.produto_parceiro_id, produto.slug")
      ->select("produto_parceiro.parceiro_id")
      ->select("apolice.*")
      ->select("apolice_{$engine}.*")
      ->select("fatura.*")

      ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
      ->join("cotacao_{$engine}", "cotacao_{$engine}.cotacao_id = cotacao.cotacao_id", 'inner')
      ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
      ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
      ->join("apolice", "apolice.pedido_id = pedido.pedido_id", 'inner')
      ->join("apolice_{$engine}", "apolice_{$engine}.apolice_id = apolice.apolice_id", 'inner')
      ->join("fatura", "fatura.pedido_id = pedido.pedido_id", 'inner');
    return $this;

  }

  function getPedidoProdutoParceiro( $pedido_id = 0 ){

    $pedidos = $this->db->query( "SELECT
                                      pedido.pedido_id, 
                                      pedido.cotacao_id, 
                                      pedido.produto_parceiro_pagamento_id,
                                      pedido.num_parcela, 
                                      pedido.valor_parcela,
                                      cotacao.produto_parceiro_id, 
                                      produto.slug,
                                      produto_parceiro.parceiro_id,
                                      produto_parceiro_apolice.template as template_apolice,
                                      CASE produto.slug 
                                        WHEN 'equipamento' THEN
                                          cotacao_equipamento.iof
                                        WHEN 'generico' THEN
                                          cotacao_generico.iof
                                        ELSE
                                          cotacao_seguro_viagem.iof
                                        END 
                                      AS iof,
                                      CASE produto.slug 
                                        WHEN 'equipamento' THEN
                                          cotacao_equipamento.premio_liquido_total
                                        WHEN 'generico' THEN
                                          cotacao_generico.premio_liquido_total
                                        ELSE
                                          cotacao_seguro_viagem.premio_liquido_total
                                        END 
                                      AS premio_liquido_total
                                  FROM
                                      pedido
                                      INNER JOIN cotacao ON ( cotacao.cotacao_id = pedido.cotacao_id )
                                      INNER JOIN  produto_parceiro ON ( cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id)
                                      INNER JOIN produto ON (produto.produto_id = produto_parceiro.produto_id)
                                      LEFT JOIN produto_parceiro_apolice ON ( produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id)
                                      LEFT JOIN cotacao_seguro_viagem ON ( cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id)
                                      LEFT JOIN cotacao_equipamento ON ( cotacao_equipamento.cotacao_id = cotacao.cotacao_id)
                                      LEFT JOIN cotacao_generico ON ( cotacao_generico.cotacao_id = cotacao.cotacao_id)
                                  WHERE
                                      pedido.pedido_id IN ($pedido_id) LIMIT 1" )->result_array();
    if( $pedidos ) {
      $pedido = $pedidos;
    } else {
      $pedido = array();
    }
    return $pedido;

    $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
      ->select("cotacao.produto_parceiro_id, produto.slug")
      ->select("produto_parceiro.parceiro_id")
      ->select("produto_parceiro_apolice.template as template_apolice")
      ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
      ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
      ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
      ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
      ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id", 'left')
      ->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left')
      ->join("cotacao_generico", "cotacao_generico.cotacao_id = cotacao.cotacao_id", 'left')
      ->where_in("pedido.pedido_id", $pedido_id);


    $pedidos = $this->get_all();
    if($pedidos){
      $pedido = $pedidos[0];
      if($pedido['slug'] == 'seguro_viagem'){
        $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
          ->select("cotacao.produto_parceiro_id, produto.slug")
          ->select("cotacao_seguro_viagem.*")
          ->select("produto_parceiro.parceiro_id")
          ->select("produto_parceiro_apolice.template as template_apolice")
          ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
          ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
          ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
          ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
          ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id", 'left')
          ->where_in("pedido.pedido_id", $pedido_id);
        $pedidos = $this->get_all();
      }elseif ($pedido['slug'] == 'equipamento'){
        $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
          ->select("cotacao_equipamento.*")
          ->select("cotacao.produto_parceiro_id, produto.slug")
          ->select("produto_parceiro.parceiro_id")
          ->select("produto_parceiro_apolice.template as template_apolice")
          ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
          ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
          ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
          ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
          ->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left')
          ->where_in("pedido.pedido_id", $pedido_id);
        $pedidos = $this->get_all();
      }elseif ( $pedido['slug'] == "generico" || $pedido['slug'] == "seguro_saude" ){
        $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
          ->select("cotacao_generico.*")
          ->select("cotacao.produto_parceiro_id, produto.slug")
          ->select("produto_parceiro.parceiro_id")
          ->select("produto_parceiro_apolice.template as template_apolice")
          ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
          ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
          ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
          ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
          ->join("cotacao_generico", "cotacao_generico.cotacao_id = cotacao.cotacao_id", 'left')
          ->where_in("pedido.pedido_id", $pedido_id);
        $pedidos = $this->get_all();
      }

      return ($pedidos) ? $pedidos : array();

    } else {
      return array();
    }
  }


  public function with_seguro_viagem(){

    $this->_database->select("cotacao_seguro_viagem.*")
      ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = pedido.cotacao_id");

    return $this;
  }

  function getPedidoPagamento($pedido_id = 0){

    $this->_database->select("pedido.num_parcela, pedido.valor_parcela, pedido.valor_total")
      ->select(", forma_pagamento_tipo.nome as tipo_pagamento,  forma_pagamento.nome as bandeira")
      ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
      ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
      ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
      ->where_in("pedido.pedido_id", $pedido_id);

    $pedidos = $this->get_all();
    return ($pedidos) ? $pedidos : array();


    /*



        $pedido_id = (int)$pedido_id;

        $sql = "
                select pedido.num_parcela, pedido.valor_parcela, pedido.valor_total, forma_pagamento_tipo.nome as tipo_pagamento,  forma_pagamento.nome as bandeira
                from pedido
                inner join produto_parceiro_pagamento ON pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                inner join forma_pagamento ON produto_parceiro_pagamento.forma_pagamento_id = forma_pagamento.forma_pagamento_id
                inner join forma_pagamento_tipo ON forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                WHERE pedido.pedido_id = {$pedido_id}
        ";

        return $this->_database->query($sql)->result_array();
*/
  }


  public function with_pedido_status(){

    return $this->with_simple_relation('pedido_status', 'pedido_status_', 'pedido_status_id', array('nome'), 'inner');
  }

  public function with_fatura(){

    $this->_database->where('fatura.deletado', 0);
    return $this->with_simple_relation('fatura', 'fatura_', 'pedido_id', array('tipo'), 'inner');

  }
  public function with_apolice(){

    $this->_database->where('apolice.deletado', 0);
    return $this->with_simple_relation('apolice', 'apolice_', 'pedido_id', array('num_apolice', 'apolice_id'), 'inner');

  }

  public function with_cotacao_cliente_contato(){
    $this->_database->select("'' as cod_cliente", false);
    $this->_database->select("cliente.razao_nome, cotacao_equipamento.equipamento_nome, equipamento_marca.nome as marca, equipamento_categoria.nome as categoria");
    $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 1 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS email");
    $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 2 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1)  AS celular");
    $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 3 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS telefone");
    $this->_database->join('cotacao', 'cotacao.cotacao_id = pedido.cotacao_id', 'inner');
    $this->_database->join('cliente', 'cliente.cliente_id = cotacao.cliente_id', 'inner');
    # $this->_database->join('base_pessoa', 'base_pessoa.documento = cliente.cnpj_cpf', 'inner');
    $this->_database->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left');
    $this->_database->join("equipamento_marca", "equipamento_marca.equipamento_marca_id = cotacao_equipamento.equipamento_marca_id", 'left');
    $this->_database->join("equipamento_categoria", "equipamento_categoria.equipamento_categoria_id = cotacao_equipamento.equipamento_categoria_id", 'left');

    return $this;
  }

  public function with_cotacao(){
    $this->_database->join('cotacao', 'cotacao.cotacao_id = pedido.cotacao_id', 'inner');
    return $this;
  }

  function filter_by_pedido($pedido_id)
  {
    $this->_database->where('pedido.pedido_id', $pedido_id);
    return $this;
  }

  function filter_by_apolice($apolice_id)
  {
    $this->_database->where('apolice.apolice_id', $apolice_id);
    return $this;
  }

  function filter_by_cotacao($cotacao_id)
  {
    $this->_database->where('pedido.cotacao_id', $cotacao_id);
    return $this;
  }

  function filter_by_upgrade()
  {
    //$this->_database->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id");
    //$this->_database->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id");
    $this->_database->join("produto_parceiro", "produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id");
    $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
    $this->_database->where("produto_parceiro_plano.passivel_upgrade = 1");
    $this->_database->where_in("pedido.pedido_status_id", array(3,8));

    return $this;
  }

  //Retorna por slug
  function get_by_id($id)
  {
    return $this->get_by($this->primary_key, $id);
  }

  function isPermiteCancelar($pedido_id){

    $this->load->model("apolice_model", "apolice");

    $result = FALSE;
    $pedido = $this->get($pedido_id);

    if($pedido){
      if(($pedido['pedido_status_id'] == 3) || ($pedido['pedido_status_id'] == 8) || ($pedido['pedido_status_id'] == 12) ) {
        $apolices = $this->apolice->getApolicePedido($pedido_id);
        if( $apolices ) {
          foreach ($apolices as $apolice) {
            $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
            $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);
            if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) < $fim_vigencia) {
              $result = TRUE;
            }
          }
        }
      }
    }
    return $result;
  }

  function isPermiteUpgrade($pedido_id){

    $this->load->model("apolice_model", "apolice");
    $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");

    $result = FALSE;
    $pedido = $this->get($pedido_id);


    if($pedido){


      if(($pedido['pedido_status_id'] == 3) || ($pedido['pedido_status_id'] == 8)) {

        $apolices = $this->apolice->getApolicePedido($pedido_id);


        if ($apolices) {

          foreach ($apolices as $apolice) {

            $plano = $this->produto_parceiro_plano->get($apolice['produto_parceiro_plano_id']);

            if($plano && $plano['passivel_upgrade'] == 1) {


              $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
              $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);

              if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) < $fim_vigencia) {
                $result = TRUE;
              }

            }
          }


        }
      }

    }

    return $result;

  }

  function criticas_cancelamento($pedido_id, $executar = false, $dados_bancarios = []){

    $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
    $this->load->model("apolice_model", "apolice");
    $this->load->model("fatura_model", "fatura");

    $result = array(
      'result' => FALSE,
      'mensagem' => '',
      'redirect' => "admin/pedido/index",
    );

    $pedido = $this->get($pedido_id);

    //varifica se existe o registro
    if(!$pedido){
      $result['mensagem'] = 'Não foi possível encontrar o pedido informado.';
      $result['redirect'] = "admin/pedido/index";
      return $result;
    }

    //varifica se é permitido cancelar
    if(!$this->isPermiteCancelar($pedido_id)){
      $result["mensagem"] = "Não foi possível efetuar o cancelamento desse Pedido/Apólice. Motivo: fora de vigência";
      $result["redirect"] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    if ( !empty($this->fatura->filterByPedido($pedido_id)->filterByTipo('ESTORNO')->filterByDeletado(0)->get_all()) ) {
      $result["mensagem"] = "Não foi possível efetuar o cancelamento desse Pedido/Apólice. Motivo: Apólice já está cancelada";
      $result["redirect"] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    //pega as configurações de cancelamento do pedido
    $produto_parceiro = $this->getPedidoProdutoParceiro($pedido_id);

    if(!$produto_parceiro){
      $result['mensagem'] = 'Não foi possível encontrar o produto relacionado a esse pedido.';
      $result['redirect'] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    $produto_parceiro = $produto_parceiro[0];
    $produto_parceiro_cancelamento = $this->cancelamento->filter_by_produto_parceiro($produto_parceiro['produto_parceiro_id'])->get_all();

    if(!$produto_parceiro_cancelamento){
      $result['mensagem'] = 'Não existe regras de cancelamento configuradas para esse produto';
      $result['redirect'] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    $produto_parceiro_cancelamento = $produto_parceiro_cancelamento[0];
    $apolices = $this->apolice->getApolicePedido($pedido_id);

    if(!$apolices){
      $result['mensagem'] = 'Apólice não encontrada';
      $result['redirect'] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    $apolice = $apolices[0];

    if( $apolice['apolice_status_id'] == 2 ) {
      $result["mensagem"] = "Não foi possível efetuar o cancelamento desse Pedido/Apólice. Motivo: Apólice já está cancelada";
      $result['redirect'] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    $vigencia = FALSE;

    //pega início e fim da vigencia
    $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
    $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);

    $inicio_vigencia = explode('-', $apolice['data_ini_vigencia']);
    $inicio_vigencia = mktime(0, 0, 0, $inicio_vigencia[1], $inicio_vigencia[2], $inicio_vigencia[0]);

    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

    if ( $hoje >= $inicio_vigencia && $hoje <= $fim_vigencia ) {
      //Já comeceu a vigencia
      if($produto_parceiro_cancelamento['seg_depois_hab'] == 0){
        //não pode executar cancelamento antes do início da vigência
        $result['mensagem'] = 'Cancelamento não permitido após o início da vigência';
        $result['redirect'] = "admin/pedido/view/{$pedido_id}";
        return $result;
      }else{
        // pode efetuar o cancelamento depois do início da vigência
        if($produto_parceiro_cancelamento['seg_depois_dias'] != 0){
          // verifica a quantidade de dias que pode executar o cancelamento antes do inicio da vigência
          $qnt_dias = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), date('d/m/Y'), 'D');
          if($qnt_dias > $produto_parceiro_cancelamento['seg_depois_dias']){
            //não pode executar cancelamento com limite de dias antes do início da vigência
            $result['mensagem'] = "Cancelamento só é permitido até {$produto_parceiro_cancelamento['seg_depois_dias']} dia(s) após o início da vigência";
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
          }
        }

        $vigencia = TRUE;
      }

    } elseif ( $hoje < $inicio_vigencia ) {
      if($produto_parceiro_cancelamento['seg_antes_hab'] == 0){
        $result['mensagem'] = 'Cancelamento não permitido antes do início da vigência';
        $result['redirect'] = "admin/pedido/view/{$pedido_id}";
        return $result;
      } else {
        if($produto_parceiro_cancelamento['seg_antes_dias'] != 0){
          $qnt_dias = app_date_get_diff_dias(date('d/m/Y'), app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), 'D');
          if($qnt_dias < $produto_parceiro_cancelamento['seg_antes_dias']){
            $result['mensagem'] = "Cancelamento só é permitido até {$produto_parceiro_cancelamento['seg_antes_dias']} dia(s) antes do início da vigência";
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
          }
        }

        $vigencia = FALSE;
      }
    } else {
      $result['mensagem'] = 'Cancelamento não permitido fora do período de vigência';
      $result['redirect'] = "admin/pedido/view/{$pedido_id}";
      return $result;
    }

    if (!empty($executar)) {

      if($produto_parceiro_cancelamento['indenizacao_hab'] == 1) {
        $valDadosBanc = $this->validaDadosBancarios($dados_bancarios);
        if (empty($valDadosBanc['status'])) {
          $result['mensagem'] = $valDadosBanc['mensagem'];
          $result['redirect'] = "admin/pedido/view/{$pedido_id}";
          return $result;
        }
      }

    }

    $result['result'] = TRUE;
    $result['mensagem'] = 'Pedido cancelado com sucesso.';
    $result['redirect'] = "admin/pedido/view/{$pedido_id}";
    $result['vigencia'] = $vigencia;
    return $result;
  }

  public function validaDadosBancarios($dados_bancarios = []) {
    $msg = [];
    $retorno = [
      'status' => false,
      'mensagem' => ''
    ];

    //Verifica se está válido
    if (empty($dados_bancarios)) {
      $msg[] = "Nenhum Dado Bancário foi enviado para realizar o Cancelamento";
    } else {

      // caso possua conta
      if (empty($dados_bancarios['nao_possuo_conta'])) {

        $this->load->model('banco_model', 'banco');

        if (empty($dados_bancarios['tipo_conta']))
          $msg[] = "O campo Tipo de conta é obrigatório. ['corrente': Conta Corrente, 'poupanca': Conta Poupança]";
        elseif (!in_array($dados_bancarios['tipo_conta'], ['corrente','poupanca']))
          $msg[] = "O campo Tipo de conta deve ter um dos seguintes valores ['corrente': Conta Corrente, 'poupanca': Conta Poupança]";

        if (!empty($dados_bancarios['conta_terceiro']) && !in_array($dados_bancarios['conta_terceiro'], ['S','T']))
          $msg[] = "O campo `Conta bancária Pertence` deve ter um dos seguintes valores ['S': Segurado, 'T': Terceiro]";

        if (empty($dados_bancarios['favo_nome']))
          $msg[] = "O campo Nome do favorecido é obrigatório";

        if (empty($dados_bancarios['favo_doc']))
          $msg[] = "O campo Documento do favorecido é obrigatório";

        if ( !app_validate_cpf_cnpj($dados_bancarios['favo_doc']) )
          $msg[] = "O campo Documento deve ser um CPF/CNPJ válido";

        if (strlen(trim($dados_bancarios['favo_bco_num'])) == 0)
          $msg[] = "O campo Código do Banco do favorecido é obrigatório";
        elseif (!is_numeric($dados_bancarios['favo_bco_num']))
          $msg[] = "O campo Código do Banco do favorecido deve ser numérico";
        elseif ( empty($this->banco->get_by( ['codigo' => $dados_bancarios['favo_bco_num']] ) ) )
          $msg[] = "O campo Código do Banco não é válido";

        if (strlen(trim($dados_bancarios['favo_bco_ag'])) == 0)
          $msg[] = "O campo Agência é obrigatório";

        if (!is_numeric($dados_bancarios['favo_bco_ag']))
          $msg[] = "O campo Agência deve ser numérico";

        if (strlen(trim($dados_bancarios['favo_bco_cc'])) == 0)
          $msg[] = "O campo Número da conta é obrigatório";

        if (!is_numeric($dados_bancarios['favo_bco_cc']))
          $msg[] = "O campo Número da conta deve ser numérico";

        if (strlen(trim($dados_bancarios['favo_bco_cc_dg'])) == 0)
          $msg[] = "O campo Dígito da conta é obrigatório";
        
        if (!is_numeric($dados_bancarios['favo_bco_cc_dg']))
          $msg[] = "O campo Dígito da conta deve ser numérico";

      }

    }

    if (empty($msg)) {
      $retorno['status'] = true;
    } else {
      $retorno['mensagem'] = $msg;
    }
    return $retorno;
  }

  function cancelamento($pedido_id, $dados_bancarios = []){

    $criticas = $this->criticas_cancelamento($pedido_id, true, $dados_bancarios);

    if (!empty($criticas['result'])) {
        // efetuar o cancelamento
        $this->executa_estorno_cancelamento($pedido_id, $criticas['vigencia'], TRUE, $dados_bancarios);
    }

    return $criticas;
  }

  function cancelamento_calculo($pedido_id){

    $result = [
      'mensagem' => '',
      'status' => false,
      'valor_estorno_total' => 0, 
    ];

    $criticas = $this->criticas_cancelamento($pedido_id);

    if (!empty($criticas['result'])) {
      // efetuar o cancelamento
      $result = $this->calcula_estorno_cancelamento($pedido_id, $criticas['vigencia']);
    } else {
      $result['mensagem'] = $criticas['mensagem'];
    }

    return $result;
  }

  function calcula_estorno_cancelamento($pedido_id, $vigente = FALSE){

    $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
    $this->load->model("apolice_model", "apolice");
    $this->load->model("fatura_model", "fatura");
    $this->load->model("apolice_equipamento_model", "apolice_equipamento");
    $this->load->model("apolice_generico_model", "apolice_generico");
    $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");
    $this->load->model('pedido_transacao_model', 'pedido_transacao');
    $this->load->model('apolice_movimentacao_model', 'movimentacao');
    $this->load->model( "produto_parceiro_model", "produto_parceiro" );


    $pedido = $this->get($pedido_id);

    //pega as configurações de cancelamento do pedido
    $produto_parceiro = $this->getPedidoProdutoParceiro($pedido_id);


    $produto_parceiro = $produto_parceiro[0];
    $produto_parceiro_cancelamento = $this->cancelamento->filter_by_produto_parceiro($produto_parceiro['produto_parceiro_id'])->get_all();
    $produto_parceiro_cancelamento = $produto_parceiro_cancelamento[0];

    $apolices = $this->apolice->getApolicePedido($pedido_id);
    $apolice = $apolices[0];
    $data_cancelamento = date('Y-m-d H:i:s');
    $valor_estorno_total = 0;
    $retorno = [];

    $produto = $this->produto_parceiro->with_produto()->get( $produto_parceiro["produto_parceiro_id"] );

    if($vigente == FALSE){
      foreach ($apolices as $apolice) {
        $valor_premio = $apolice["valor_premio_total"];
        $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento["seg_antes_calculo"], $produto_parceiro_cancelamento["seg_antes_valor"], $valor_premio);

        $dados_apolice = array();
        $dados_apolice['data_cancelamento'] = $data_cancelamento;
        $dados_apolice['valor_estorno'] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;

        if( $produto ) {
          $produto_slug = $produto["produto_slug"];
          $retorno[] = [
            'slug' => $produto_slug,
            'dados_apolice' => $dados_apolice,
            'apolices' => $apolice,
          ];
        }
      }

    }else{
      //FAZ CALCULO DO VALOR PARCIAL
      // $apolice["data_ini_vigencia"] = '2018-01-01';
      // $apolice["data_fim_vigencia"] = '2019-01-01';
      // $data_cancelamento = '2018-01-30';

      $dias_restantes = app_date_get_diff_dias(app_dateonly_mysql_to_mask($data_cancelamento), app_dateonly_mysql_to_mask($apolice["data_fim_vigencia"]), "D");
      $dias_utilizados = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice["data_ini_vigencia"]), app_dateonly_mysql_to_mask($data_cancelamento),  "D");
      $dias_total = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice["data_ini_vigencia"]), app_dateonly_mysql_to_mask($apolice["data_fim_vigencia"]),  "D");

      $porcento_nao_utilizado = 0;
      if ( !empty($produto_parceiro_cancelamento['seg_depois_dias_carencia']) && $dias_utilizados <= $produto_parceiro_cancelamento['seg_depois_dias_carencia']) {
        $porcento_nao_utilizado = 100;
        $dias_restantes = $dias_total;
      }

      if ($porcento_nao_utilizado == 0) {
        $porcento_nao_utilizado = (($dias_restantes / $dias_total) * 100);
      }

      foreach ($apolices as $apolice) {

        $valor_premio = $apolice['valor_premio_net'];
        $valor_premio = (($porcento_nao_utilizado / 100) * $valor_premio);
        $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento['seg_depois_calculo'], $produto_parceiro_cancelamento['seg_depois_valor'], $valor_premio);

        $dados_apolice = array();
        $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
        $dados_apolice['valor_estorno'] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;

        if( $produto ) {
          $produto_slug = $produto["produto_slug"];
          $retorno[] = [
            'slug' => $produto_slug,
            'dados_apolice' => $dados_apolice,
            'apolices' => $apolice,
          ];
        }

      }

    }

    return [
      'status' => (!empty($retorno)),
      'mensagem' => (!empty($retorno)) ? 'Cálculo realizado com sucesso' : 'Não foi possível realizar o cálculo para Cancelamento',
      'valor_estorno_total' => $valor_estorno_total, 
      'dias_utilizados' => (isset($dias_utilizados)) ? $dias_utilizados : '',
      'dados' => $retorno,
    ];
  }

  function executa_estorno_cancelamento($pedido_id, $vigente = FALSE, $ins_movimentacao = TRUE, $dados_bancarios = []){

    $this->load->model("apolice_model", "apolice");
    $this->load->model("apolice_cobertura_model", "apolice_cobertura");
    $this->load->model("apolice_equipamento_model", "apolice_equipamento");
    $this->load->model("apolice_generico_model", "apolice_generico");
    $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");

    $calculo = $this->calcula_estorno_cancelamento($pedido_id, $vigente);

    if (!empty($calculo['status'])) {

      foreach ($calculo['dados'] as $row) {
        $apolice = $row['apolices'];
        $dados_apolice = $row['dados_apolice'];

        switch( $row['slug'] ) {
          case "seguro_viagem":
            $this->apolice_seguro_viagem->update($apolice["apolice_seguro_viagem_id"],  $dados_apolice, TRUE);
            break;
          case "equipamento":
            $this->apolice_equipamento->update($apolice["apolice_equipamento_id"],  $dados_apolice, TRUE);
            break;
          case "generico":
          case "seguro_saude":
            $this->apolice_generico->update($apolice["apolice_generico_id"],  $dados_apolice, TRUE);
            break;
        }

        $this->apolice->update($apolice["apolice_id"], ['apolice_status_id' => 2], TRUE);

        if($ins_movimentacao) {
          $this->movimentacao->insMovimentacao('C', $apolice['apolice_id']);
        }

      }

    }

    $this->atualizarDadosBancarios($pedido_id, $dados_bancarios);
    $this->pedido_transacao->insStatus($pedido_id, 'cancelado', "PEDIDO CANCELADO COM SUCESSO");
    $this->fatura->insertFaturaEstorno($pedido_id, $calculo['valor_estorno_total']);
    $this->apolice_cobertura->geraDadosCancelamento($pedido_id, $calculo['valor_estorno_total']);
  }

  public function atualizarDadosBancarios($pedido_id, $dados_bancarios = []) {

    if (empty($dados_bancarios))
      return;

    //Resgata dados do post
    $data = array();

    if (empty($dados_bancarios['nao_possuo_conta'])) {
      $this->load->model('banco_model', 'banco');

      $data['nao_possui_conta_bancaria'] = "S";

      $data['conta_terceiro'] = empty($dados_bancarios['conta_terceiro']) ? "S" : "T";
      $data['favo_tipo']      = (strlen($dados_bancarios['favo_doc']) == 14) ? "PJ" : "PF";
      $data['tipo_conta']     = $dados_bancarios['tipo_conta'];
      $data['favo_nome']      = $dados_bancarios['favo_nome'];
      $data['favo_doc']       = $dados_bancarios['favo_doc'];
      $data['favo_bco_num']   = $dados_bancarios['favo_bco_num'];
      $data['favo_bco_cc']    = $dados_bancarios['favo_bco_cc'];
      $data['favo_bco_cc_dg'] = $dados_bancarios['favo_bco_cc_dg'];
      $data['favo_bco_ag'] = $dados_bancarios['favo_bco_ag'];

      $banco = $this->banco->get_by( ['codigo' => $data['favo_bco_num']] );

      $data['favo_bco_nome'] = $banco['nome'];
      $data['favo_bco_cc'] .= "-{$data['favo_bco_cc_dg']}";

    } else {
      $data['nao_possui_conta_bancaria'] = "N";
    }

    //Atualiza dados bancários
    $this->update($pedido_id, $data, TRUE);

  }

  function executa_extorno_upgrade($pedido_id){

    $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
    $this->load->model("apolice_model", "apolice");
    $this->load->model("fatura_model", "fatura");
    $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");
    $this->load->model("apolice_equipamento_model", "apolice_equipamento");
    $this->load->model('pedido_transacao_model', 'pedido_transacao');
    $this->load->model('apolice_movimentacao_model', 'movimentacao');

    $apolices = $this->apolice->getApolicePedido($pedido_id);
    $apolice = $apolices[0];

    $produto = $this->getPedidoProdutoParceiro($pedido_id);
    $produto = $produto[0];

    $valor_estorno_total = 0;

    foreach ($apolices as $apolice) {

      if($produto['slug'] == 'seguro_viagem') {
        $valor_premio = $apolice['valor_premio_total'];
        $valor_estorno = $valor_premio;
        $dados_apolice = array();
        $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
        $dados_apolice['valor_estorno'] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;
        $this->apolice_seguro_viagem->update($apolice['apolice_seguro_viagem_id'], $dados_apolice, TRUE);
      }elseif($produto['slug'] == 'equipamento'){
        $valor_premio = $apolice['valor_premio_total'];
        $valor_estorno = $valor_premio;
        $dados_apolice = array();
        $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
        $dados_apolice['valor_estorno'] = $valor_estorno;
        $valor_estorno_total += $valor_estorno;
        $this->apolice_seguro_viagem->update($apolice['apolice_equipamento_id'], $dados_apolice, TRUE);
      }

    }

    $this->fatura->insertFaturaEstorno($pedido_id, $valor_estorno_total);

  }

  /**
     * Retorna todos permitidos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
  public function get_all($limit = 0, $offset = 0)
  {
    //Efetua join com cotação
    $this->_database->join("cotacao as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

    $this->processa_parceiros_permitidos("cotacao_filtro.parceiro_id");

    return parent::get_all($limit, $offset);
  }

  /**
     * Retorna todos
     * @return mixed
     */
  public function get_total()
  {
    //Efetua join com cotação
    $this->_database->join("cotacao as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

    $this->processa_parceiros_permitidos("cotacao_filtro.parceiro_id");

    return parent::get_total(); // TODO: Change the autogenerated stub
  }

  /**
     * Extrai relatório de vendas
     */
  public function extrairRelatorioVendas($data_inicio = null, $data_fim = null)
  {

    $this->restrictProdutos();

    $this->_database->distinct();
    $this->_database->select("{$this->_table}.*, c.*, ps.nome as status, p.cnpj, p.nome_fantasia,
                                  pp.nome as nome_produto_parceiro, pr.nome as nome_produto, ppp.nome as plano_nome, {$this->_table}.valor_parcela, {$this->_table}.codigo, a.num_apolice, le.sigla as UF, u.nome as vendedor, cmg.valor AS comissao_parceiro ");

    $this->_database->select("IF(pr.slug = 'generico', cg.premio_liquido, IF(pr.slug = 'seguro_viagem', csv.premio_liquido, ce.premio_liquido)) as premio_liquido", FALSE);
    $this->_database->select("IF(pr.slug = 'generico', cg.premio_liquido_total, IF(pr.slug = 'seguro_viagem', csv.premio_liquido_total, ce.premio_liquido_total)) as premio_liquido_total", FALSE);
    $this->_database->select("IF(pr.slug = 'generico', cg.comissao_corretor, IF(pr.slug = 'seguro_viagem', csv.comissao_corretor, ce.comissao_corretor)) as comissao_corretor", FALSE);
    $this->_database->select("IF(pr.slug = 'generico', cg.nota_fiscal_valor, IF(pr.slug = 'seguro_viagem', csv.nota_fiscal_valor, ce.nota_fiscal_valor)) as nota_fiscal_valor", FALSE);

    $this->_database->select("IF(pr.slug = 'generico', cg.nome, IF(pr.slug = 'seguro_viagem', csv.nome, ce.nome)) as segurado", FALSE);
    $this->_database->select("IF(pr.slug = 'generico', cg.cnpj_cpf, IF(pr.slug = 'seguro_viagem', csv.cnpj_cpf, ce.cnpj_cpf)) as documento", FALSE);

    $this->_database->from($this->_table);

    $this->_database->join("apolice a", "a.pedido_id = {$this->_table}.pedido_id", "inner");
    $this->_database->join("cotacao c", "c.cotacao_id = {$this->_table}.cotacao_id", "inner");
    $this->_database->join("cotacao_status cs", "cs.cotacao_status_id = c.cotacao_status_id", "inner");
    $this->_database->join("pedido_status ps", "ps.pedido_status_id = {$this->_table}.pedido_status_id", "inner");
    $this->_database->join("parceiro p", "p.parceiro_id = c.parceiro_id", "inner");
    $this->_database->join("produto_parceiro pp", "pp.produto_parceiro_id = c.produto_parceiro_id", "inner");
    $this->_database->join("produto pr", "pr.produto_id = pp.produto_id", "inner");
    $this->_database->join("cotacao_seguro_viagem csv", "csv.cotacao_id = {$this->_table}.cotacao_id and csv.deletado = 0", "left");
    $this->_database->join("cotacao_equipamento ce", "ce.cotacao_id = {$this->_table}.cotacao_id and ce.deletado = 0", "left");
    $this->_database->join("cotacao_generico cg", "cg.cotacao_id = {$this->_table}.cotacao_id and cg.deletado = 0", "left");
    $this->_database->join("produto_parceiro_plano ppp", "ppp.produto_parceiro_plano_id = IF(pr.slug = 'generico', cg.produto_parceiro_plano_id, IF(pr.slug = 'seguro_viagem', csv.produto_parceiro_plano_id, ce.produto_parceiro_plano_id))", "inner");
    $this->_database->join("localidade_estado le", "le.localidade_estado_id = p.localidade_estado_id", "left");
    $this->_database->join("usuario u", "u.usuario_id = c.usuario_cotacao_id", "left");
    $this->_database->join("comissao_gerada cmg", "cmg.pedido_id = {$this->_table}.pedido_id AND cmg.parceiro_id = p.parceiro_id", "left");
    
    // colaborador só visualiza os próprios pedidos
    if ( $this->session->userdata('usuario_acl_tipo_id') == 2 ) {
      $this->_database->where("c.usuario_cotacao_id", $this->session->userdata('usuario_id'));
    }

    if(isset($data_inicio) && !empty($data_inicio))
        $this->_database->where("status_data >= '". app_date_only_numbers_to_mysql($data_inicio) ."'");
    if(isset($data_fim) && !empty($data_fim))
        $this->_database->where("status_data <= '". app_date_only_numbers_to_mysql($data_fim, FALSE) ."'");

    $this->_database->where("cs.slug = 'finalizada'");
    $query = $this->_database->get();
    $resp = [];

    if($query->num_rows() > 0)
    {
      $resp = $query->result_array();
    }
    return $resp;

  }

    private function restrictProdutos($retorno = false){
        $return = '';
        if (!empty($this->session->userdata('parceiro_id'))) {

            $this->load->model('produto_parceiro_model', 'produto_parceiro');
            $produtos = $this->produto_parceiro->getProdutosByParceiro($this->session->userdata('parceiro_id'));
            $newArray = array();

            if (!empty($produtos)) {
                foreach ($produtos as $entry) {
                    $newArray[] = $entry['produto_parceiro_id'];
                }
                $produto_parceiro_ids = implode(',', $newArray);
                $return = "pp.produto_parceiro_id IN($produto_parceiro_ids)";
                if (!$retorno) $this->_database->where($return);
                $return = " AND ". $return;
            }
        }

        return $return;
    }

  /**
     * Extrai relatório de Mapa de Repasse Analitico
     */
  public function extrairRelatorioMapaRepasseAnalitico($data_inicio = null, $data_fim = null)
  {

    $this->restrictProdutos();

    $this->_database->distinct();
    $this->_database->select("{$this->_table}.*, c.*, ps.nome as status, p.cnpj, p.nome_fantasia,
                                  pp.nome as nome_produto_parceiro, pr.nome as nome_produto, ppp.nome as plano_nome, {$this->_table}.valor_parcela, {$this->_table}.codigo, a.num_apolice, le.sigla as UF, u.nome as vendedor ");
    $this->_database->select("IF(pp.cod_tpa = '007', 'LASA NOVOS', 'LASA USADOS') AS operacao
      , pp.produto_parceiro_id
      , parc.nome as grupo
      , {$this->_table}.pedido_id
      , DATE_FORMAT({$this->_table}.status_data, '%d/%m/%Y') AS data_emissao
      , DATE_FORMAT(ae.data_ini_vigencia, '%d/%m/%Y') AS ini_vigencia
      , DATE_FORMAT(ae.data_fim_vigencia, '%d/%m/%Y') AS fim_vigencia
      , CONCAT(p.codigo_sucursal, '71', pp.cod_tpa, LPAD(substr(a.num_apolice, 8, LENGTH(a.num_apolice) ),8,'0')) AS num_apolice
      , cli.razao_nome AS segurado_nome
      , cli.cnpj_cpf AS documento
      , ec.nome as equipamento
      , em.nome as marca
      , ae.equipamento_nome as modelo
      , ae.imei
      , pp.nome as nome_produto_parceiro
      , ae.nota_fiscal_valor as importancia_segurada
      , IF(a.apolice_status_id = 2, CONCAT(p.codigo_sucursal, '71', LPAD(1,7,'0')), '0') AS num_endosso
      , DATE_FORMAT({$this->_table}.status_data, '%b/%y') AS vigencia_parcela
      , '1|1' as parcela
      , 'PAGO' as status_parcela
      , DATE_FORMAT(ae.data_cancelamento, '%d/%m/%Y') AS data_cancelamento

      , ae.valor_premio_total as valor_parcela
      , ae.valor_premio_total as PremioBruto 
      , ae.valor_premio_net AS PremioLiquido

      , (
          SELECT FORMAT(ac.valor + ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
          FROM apolice_cobertura ac 
          INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
          INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
          WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 39
          LIMIT 1
      ) AS PB_RF
      , (
          SELECT ac.valor
          FROM apolice_cobertura ac 
          INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
          INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
          WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 39
          LIMIT 1
      ) AS PL_RF
      , (
          SELECT FORMAT(ac.valor + ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
          FROM apolice_cobertura ac 
          INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
          INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
          WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 71
          LIMIT 1
      ) AS PB_QA
      , (
          SELECT ac.valor
          FROM apolice_cobertura ac 
          INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
          INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
          WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 71
          LIMIT 1
      ) AS PL_QA

      , (
          SELECT FORMAT(cmg.valor, 2)
          FROM comissao_gerada cmg
          INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
          WHERE cmg.pedido_id = {$this->_table}.pedido_id AND parc_com.parceiro_tipo_id = 3
          LIMIT 1
      ) AS pro_labore
      , (
          SELECT FORMAT(cmg.valor, 2)
          FROM comissao_gerada cmg
          INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
          WHERE cmg.pedido_id = {$this->_table}.pedido_id AND parc_com.parceiro_tipo_id = 2
          LIMIT 1
      ) AS valor_comissao
      
    ", FALSE);

    $this->_database->from($this->_table);
    $this->_database->join("pedido_status ps", "ps.pedido_status_id = {$this->_table}.pedido_status_id", "inner");
    $this->_database->join("apolice a", "a.pedido_id = {$this->_table}.pedido_id", "inner");
    $this->_database->join("cotacao c", "c.cotacao_id = {$this->_table}.cotacao_id", "inner");
    $this->_database->join("cotacao_status cs", "cs.cotacao_status_id = c.cotacao_status_id", "inner");
    $this->_database->join("cotacao_equipamento ce", "ce.cotacao_id = {$this->_table}.cotacao_id and ce.deletado = 0", "inner");
    $this->_database->join("produto_parceiro pp", "pp.produto_parceiro_id = c.produto_parceiro_id", "inner");
    $this->_database->join("parceiro p", "p.parceiro_id = pp.parceiro_id", "inner");
    $this->_database->join("parceiro parc", "parc.parceiro_id = a.parceiro_id", "inner");
    $this->_database->join("produto pr", "pr.produto_id = pp.produto_id", "inner");
    $this->_database->join("apolice_equipamento ae", "ae.apolice_id = a.apolice_id and ae.deletado = 0", "inner");
    $this->_database->join("cliente cli", "cli.cliente_id = c.cliente_id", "inner");
    $this->_database->join("equipamento_categoria ec", "ec.equipamento_categoria_id = ae.equipamento_categoria_id", "inner");
    $this->_database->join("equipamento_marca em", "em.equipamento_marca_id = ae.equipamento_marca_id", "inner");
    $this->_database->join("produto_parceiro_plano ppp", "ppp.produto_parceiro_plano_id = ce.produto_parceiro_plano_id", "inner");
    $this->_database->join("localidade_estado le", "le.localidade_estado_id = p.localidade_estado_id", "left");
    $this->_database->join("usuario u", "u.usuario_id = c.usuario_cotacao_id", "left");
    
    // colaborador só visualiza os próprios pedidos
    if ( $this->session->userdata('usuario_acl_tipo_id') == 2 ) {
      $this->_database->where("c.usuario_cotacao_id", $this->session->userdata('usuario_id'));
    }

    if(isset($data_inicio) && !empty($data_inicio))
        $this->_database->where("status_data >= '". app_date_only_numbers_to_mysql($data_inicio) ."'");
    if(isset($data_fim) && !empty($data_fim))
        $this->_database->where("status_data <= '". app_date_only_numbers_to_mysql($data_fim, FALSE) ."'");

    $this->_database->where("parc.slug IN('lojasamericanas')");
    $this->_database->where("cs.slug = 'finalizada'");
    $query = $this->_database->get();
    $resp = [];

    if($query->num_rows() > 0)
    {
        $resp = $query->result_array();
    }
    return $resp;

  }

    /**
    * Extrai relatório de Mapa de Repasse Sintetico
    */
    public function extrairRelatorioMapaRepasseSintetico($data_inicio = null, $data_fim = null)
    {

        $where = $this->restrictProdutos(true);

        // colaborador só visualiza os próprios pedidos
        if ( $this->session->userdata('usuario_acl_tipo_id') == 2 ) {
          $where .= " AND c.usuario_cotacao_id = {$this->session->userdata('usuario_id')}";
        }

        if(isset($data_inicio) && !empty($data_inicio))
            $where .= " AND status_data >= '". app_date_only_numbers_to_mysql($data_inicio) ."'";
        if(isset($data_fim) && !empty($data_fim))
            $where .= " AND status_data <= '". app_date_only_numbers_to_mysql($data_fim, FALSE) ."'";

        $query = $this->_database->query("
            SELECT cod_tpa, 
                SUM(IF(PB_RF IS NOT NULL, 1, 0)) AS quantidade_RF,
                SUM(IFNULL(IOF_RF,0)) AS IOF_RF, 
                SUM(IFNULL(PL_RF,0)) AS PL_RF, 
                SUM(IFNULL(PB_RF,0)) AS PB_RF, 
                SUM(IFNULL(pro_labore_RF,0)) AS pro_labore_RF, 
                SUM(IFNULL(valor_comissao_RF,0)) AS valor_comissao_RF, 
                SUM(IF(PB_QA IS NOT NULL, 1, 0)) AS quantidade_QA,
                SUM(IFNULL(PB_QA,0)) AS PB_QA, 
                SUM(IFNULL(IOF_QA,0)) AS IOF_QA, 
                SUM(IFNULL(PL_QA,0)) AS PL_QA, 
                SUM(IFNULL(pro_labore_QA,0)) AS pro_labore_QA, 
                SUM(IFNULL(valor_comissao_QA,0)) AS valor_comissao_QA
            FROM (
                SELECT 
                    pp.cod_tpa,
                    pedido.pedido_id,
                    (
                        SELECT FORMAT(ac.valor + ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 39
                        LIMIT 1
                    ) AS PB_RF, (
                        SELECT FORMAT(ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 39
                        LIMIT 1
                    ) AS IOF_RF, (
                        SELECT ac.valor
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 39
                        LIMIT 1
                    ) AS PL_RF, (
                        SELECT FORMAT(cmg.comissao / 100 * ac.valor, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        INNER JOIN comissao_gerada cmg ON cmg.pedido_id = ac.pedido_id
                        INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
                        WHERE cmg.pedido_id = pedido.pedido_id AND cp.cobertura_id = 39 AND parc_com.parceiro_tipo_id = 3
                        LIMIT 1
                    ) AS pro_labore_RF, (
                        SELECT FORMAT(cmg.comissao / 100 * ac.valor, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        INNER JOIN comissao_gerada cmg ON cmg.pedido_id = ac.pedido_id
                        INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
                        WHERE cmg.pedido_id = pedido.pedido_id AND cp.cobertura_id = 39 AND parc_com.parceiro_tipo_id = 2
                        LIMIT 1
                    ) AS valor_comissao_RF, (
                        SELECT FORMAT(ac.valor + ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 71
                        LIMIT 1
                    ) AS PB_QA, (
                        SELECT FORMAT(ac.valor / ae.valor_premio_net * ae.pro_labore, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 71
                        LIMIT 1
                    ) AS IOF_QA, (
                        SELECT ac.valor
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        WHERE ac.apolice_id = a.apolice_id AND cp.cobertura_id = 71
                        LIMIT 1
                    ) AS PL_QA, (
                        SELECT FORMAT(cmg.comissao / 100 * ac.valor, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        INNER JOIN comissao_gerada cmg ON cmg.pedido_id = ac.pedido_id
                        INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
                        WHERE cmg.pedido_id = pedido.pedido_id AND cp.cobertura_id = 71 AND parc_com.parceiro_tipo_id = 3
                        LIMIT 1
                    ) AS pro_labore_QA, (
                        SELECT FORMAT(cmg.comissao / 100 * ac.valor, 2)
                        FROM apolice_cobertura ac 
                        INNER JOIN cobertura_plano cp on ac.cobertura_plano_id = cp.cobertura_plano_id
                        INNER JOIN cobertura cb on cb.cobertura_id = cp.cobertura_id
                        INNER JOIN comissao_gerada cmg ON cmg.pedido_id = ac.pedido_id
                        INNER JOIN parceiro parc_com ON parc_com.parceiro_id = cmg.parceiro_id
                        WHERE cmg.pedido_id = pedido.pedido_id AND cp.cobertura_id = 71 AND parc_com.parceiro_tipo_id = 2
                        LIMIT 1
                    ) AS valor_comissao_QA
                FROM `pedido`
                INNER JOIN `pedido_status` ps ON `ps`.`pedido_status_id` = `pedido`.`pedido_status_id`
                INNER JOIN `apolice` a ON `a`.`pedido_id` = `pedido`.`pedido_id`
                INNER JOIN `cotacao` c ON `c`.`cotacao_id` = `pedido`.`cotacao_id`
                INNER JOIN `cotacao_status` cs ON `cs`.`cotacao_status_id` = `c`.`cotacao_status_id`
                INNER JOIN `cotacao_equipamento` ce ON `ce`.`cotacao_id` = `pedido`.`cotacao_id` and ce.deletado = 0
                INNER JOIN `produto_parceiro` pp ON `pp`.`produto_parceiro_id` = `c`.`produto_parceiro_id`
                INNER JOIN `parceiro` p ON `p`.`parceiro_id` = `pp`.`parceiro_id`
                INNER JOIN `parceiro` parc ON `parc`.`parceiro_id` = `a`.`parceiro_id`
                INNER JOIN `produto` pr ON `pr`.`produto_id` = `pp`.`produto_id`
                INNER JOIN `apolice_equipamento` ae ON `ae`.`apolice_id` = `a`.`apolice_id` and ae.deletado = 0
                INNER JOIN `cliente` cli ON cli.cliente_id = c.cliente_id
                INNER JOIN `equipamento_categoria` ec ON `ec`.`equipamento_categoria_id` = `ae`.`equipamento_categoria_id`
                INNER JOIN `equipamento_marca` em ON `em`.`equipamento_marca_id` = `ae`.`equipamento_marca_id`
                INNER JOIN `produto_parceiro_plano` ppp ON `ppp`.`produto_parceiro_plano_id` = `ce`.`produto_parceiro_plano_id`
                LEFT JOIN `localidade_estado` le ON `le`.`localidade_estado_id` = `p`.`localidade_estado_id`
                LEFT JOIN `usuario` u ON `u`.`usuario_id` = `c`.`usuario_cotacao_id`
                WHERE `parc`.`slug` IN('lojasamericanas')
                    AND `cs`.`slug` = 'finalizada'
                    {$where}
                ORDER BY pp.cod_tpa
            ) AS x
            GROUP BY cod_tpa
    ");

    $resp = [];

    if($query->num_rows() > 0)
    {
      $resp = $query->result_array();
    }
    return $resp;

  }

  /**
     * Muda status do pedido
     * @param $id_pedido
     * @param $status
     * @return bool
     */
  public function mudaStatus($id_pedido, $status)
  {
    $this->load->model("pedido_status_model", "pedido_status");

    $pedidoStatus = $this->pedido_status->get_by(array(
      'slug' => $status
    ));

    if($pedidoStatus)
    {
      $retorno = $this->update($id_pedido, array(
        'pedido_status_id' => $pedidoStatus['pedido_status_id']
      ), true);

      if($retorno)
        return true;
    }
    return false;
  }


  /**
     * Insere pedido
     * @param $dados
     * @return mixed
     */
  public function insertPedido($dados) {

    //Carrega models
    $this->load->model("fatura_model", "fatura");
    $this->load->model("fatura_parcela_model", "fatura_parcela");
    $this->load->model("pedido_codigo_model", "pedido_codigo");
    $this->load->model("pedido_cartao_model", "pedido_cartao");
    $this->load->model("pedido_transacao_model", "pedido_transacao");
    $this->load->model("cotacao_generico_model", "cotacao_generico");
    $this->load->model("cotacao_seguro_viagem_model", "cotacao_seguro_viagem");
    $this->load->model("cotacao_equipamento_model", "cotacao_equipamento");
    $this->load->model("cotacao_model", "cotacao");
    $this->load->model("produto_parceiro_pagamento_model", "produto_pagamento");

    $this->load->model("forma_pagamento_model", "forma_pagamento");

    //se é debito ou credito
    if($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_CARTAO_CREDITO ) {
      $item = $this->produto_pagamento->get_by_id($dados["bandeira"]);
    }elseif($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_CARTAO_DEBITO ) {
      $item = $this->produto_pagamento->get_by_id($dados["bandeira"]);
    }elseif($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_FATURADO ) {
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_FATURADO)->limit(1)->get_all();
      $item = $item[0];
    }elseif($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_TERCEIROS ) {
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_TERCEIROS)->limit(1)->get_all();
      $item = $item[0];
    }elseif ($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_BOLETO ) {
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_BOLETO)->limit(1)->get_all();
      $item = $item[0];
    }

    $cotacao = $this->cotacao->get_cotacao_produto($dados["cotacao_id"]);
    switch ($cotacao["produto_slug"]) {
      case "seguro_viagem":
        $valor_total = $this->cotacao_seguro_viagem->getValorTotal($dados["cotacao_id"]);
        break;
      case "equipamento":
        $valor_total = $this->cotacao_equipamento->getValorTotal($dados["cotacao_id"]);
        break;
      default:
        $valor_total = $this->cotacao_generico->getValorTotal($dados["cotacao_id"]);
        break;
    }

    //Se for um upgrade
    /*
        if((int)$cotacao['cotacao_upgrade_id'] > 0)
        {
            $upgrade_id = (int)$cotacao['cotacao_upgrade_id'];
            $upgrade = $this->cotacao->get($upgrade_id);

            $pedido_upgrade = $this->pedido->get_by(array('cotacao_id' => $upgrade['cotacao_id']));

            $valor_total = $valor_total + $pedido_upgrade['valor_total'];
        } */

    $parcelamento = array();
    if( isset( $item['parcelamento_maximo'] ) ) {
      for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
        if($i <= $item['parcelamento_maximo_sem_juros']) {
          $parcelamento[$i] = $valor_total/$i;
        }else{
          $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
          // $valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
          $parcelamento[$i] = $valor;
        }
      }
    }


    if( isset( $dados["bandeira"] ) && $dados["bandeira"] != "" ) {
      $dados_bandeira = "_" . $dados['bandeira'];
    } else {
      $dados_bandeira = "_" . $item["produto_parceiro_pagamento_id"];
    }

    if( !isset( $dados["parcelamento{$dados_bandeira}"] ) && isset( $dados["num_parcela"] ) ) {
      $dados["parcelamento{$dados_bandeira}"] = $dados["num_parcela"];
    }

    if( $dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_CARTAO_DEBITO && intval( $dados["num_parcela"] ) != 1 ) {
      die( 
        json_encode( 
          array( 
            "success" => false, 
            "cotacao_id" => $dados["cotacao_id"], 
            "produto_parceiro_id" => $dados["produto_parceiro_id"], 
            "forma_pagamento_id" => $dados["forma_pagamento_id"], 
            "erros" => "Número de parcelas inválido para esta forma de pagamento"
          ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES 
        ) 
      );
    }
    //die( json_encode( $dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );    

    $dados_pedido = array();
    $dados_pedido["cotacao_id"] = $dados["cotacao_id"];
    $dados_pedido["produto_parceiro_pagamento_id"] = isset( $item["produto_parceiro_pagamento_id"] ) ? $item["produto_parceiro_pagamento_id"] : "";
    $dados_pedido["pedido_status_id"] = 1;
    $dados_pedido["codigo"] = $this->pedido_codigo->get_codigo_pedido_formatado("BE");
    $dados_pedido["status_data"] = date("Y-m-d H:i:s");
    $dados_pedido["valor_total"] = $valor_total;
    $dados_pedido["num_parcela"] = $dados["parcelamento{$dados_bandeira}"];
    $dados_pedido["valor_parcela"] = $parcelamento[$dados["parcelamento{$dados_bandeira}"]];
    $dados_pedido["alteracao_usuario_id"] = $this->session->userdata("usuario_id");

    //die( json_encode( $dados_pedido, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

    $pedido_id = $this->insert( $dados_pedido, true );
    $this->pedido_transacao->insStatus( $pedido_id, "criado" );

    unset( $dados["parcelamento{$dados_bandeira}"] );
    $dados_bandeira = $dados["bandeira"];
    unset( $dados["bandeira"] );

    $this->fatura->insFaturaParcelas( $pedido_id, $dados["cotacao_id"], 1, $valor_total, $dados_pedido["num_parcela"], $dados_pedido["valor_parcela"], $dados["produto_parceiro_id"] );
    $faturas = $this->fatura->filterByPedido($pedido_id)->get_all();
    if( $faturas ) {
      $faturas = $faturas[0];
      $fatura_id = $faturas["fatura_id"];
      $fatura_parcela = $this->fatura_parcela->filterByFatura( $fatura_id )->get_all();
      if( $fatura_parcela ) {
        $fatura_parcela = $fatura_parcela[0];
        $dados["fatura_parcela_id"] = $fatura_parcela["fatura_parcela_id"];
      }
    }

    //die( json_encode( $dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

    $dados["bandeira"] = $dados_bandeira;
    $this->insDadosPagamento( $dados, $pedido_id );

    $this->cotacao->update( $dados["cotacao_id"], array( "cotacao_status_id" => 2 ), true );
    //ob_clean();
    return $pedido_id;

  }

  public function updatePedido($pedido_id, $dados){


    $this->load->model('fatura_model', 'fatura');
    $this->load->model('fatura_parcela_model', 'fatura_parcela');

    $this->load->model('pedido_codigo_model', 'pedido_codigo');
    $this->load->model('pedido_cartao_model', 'pedido_cartao');
    $this->load->model('pedido_transacao_model', 'pedido_transacao');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
    $this->load->model('forma_pagamento_model', 'forma_pagamento');



    $this->load->model('produto_parceiro_capitalizacao_model', 'parceiro_capitalizacao');
    $this->load->model('capitalizacao_model', 'capitalizacao');
    $this->load->model('capitalizacao_serie_titulo_model', 'titulo');



    //  $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
    if($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_CREDITO) {
      $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
    }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_DEBITO){
      $item = $this->produto_pagamento->get_by_id($dados['bandeira_debito']);
    }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_FATURADO){
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_FATURADO)->limit(1)->get_all();
      $item = $item[0];
    }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_TERCEIROS){
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_TERCEIROS)->limit(1)->get_all();
      $item = $item[0];
    }elseif ($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_BOLETO){
      $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_BOLETO)->limit(1)->get_all();
      $item = $item[0];
    }

    $cotacao = $this->cotacao->get_cotacao_produto($dados['cotacao_id']);
    switch ($cotacao['produto_slug']) {
      case 'seguro_viagem':
        $valor_total = $this->cotacao_seguro_viagem->getValorTotal($dados['cotacao_id']);
        break;
      case 'equipamento':
        $valor_total = $this->cotacao_equipamento->getValorTotal($dados['cotacao_id']);
        break;
      default:
        $valor_total = $this->cotacao_generico->getValorTotal($dados['cotacao_id']);
        break;
    }


    $item['juros_parcela'] = (isset($item['juros_parcela'])) ? $item['juros_parcela'] : 0;
    $item['parcelamento_maximo'] = (isset($item['parcelamento_maximo'])) ? $item['parcelamento_maximo'] : 1;
    $item['parcelamento_maximo_sem_juros'] = (isset($item['parcelamento_maximo_sem_juros'])) ? $item['parcelamento_maximo_sem_juros'] : 1;
    $parcelamento = array();
    for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
      if($i <= $item['parcelamento_maximo_sem_juros']) {
        $parcelamento[$i] = $valor_total/$i;
      }else{
        $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
        //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
        $parcelamento[$i] = $valor;
      }
    }

    if( $dados['bandeira'] != "" ) {
      $dados_bandeira = "_" . $dados['bandeira'];
    } else {
      $dados_bandeira = "";
    }
    $dados_pedido = array();
    $dados_pedido['produto_parceiro_pagamento_id'] = $item['produto_parceiro_pagamento_id'];
    $dados_pedido['valor_total'] = $valor_total;
    $dados_pedido['num_parcela'] = $dados["parcelamento{$dados_bandeira}"];
    $dados_pedido['valor_parcela'] = $parcelamento[$dados["parcelamento{$dados_bandeira}"]];
    $dados_pedido['alteracao_usuario_id'] = $this->session->userdata('usuario_id');

    $this->update($pedido_id,  $dados_pedido, TRUE);
    $this->pedido_transacao->insStatus($pedido_id, 'alteracao');


    //altera o status da cotação
    $this->cotacao->update($dados['cotacao_id'], array('cotacao_status_id' => 2), TRUE);

    //insere faturamento



    $this->fatura->deleteFaturamento($pedido_id);
    $this->fatura->insFaturaParcelas($pedido_id, $dados['cotacao_id'], 1, $valor_total, $dados_pedido['num_parcela'],
                                     $dados_pedido['valor_parcela'], $cotacao['produto_parceiro_id']);

    return $pedido_id;

  }

  public function insDadosPagamento($dados, $pedido_id){

    $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
    $this->load->model('forma_pagamento_model', 'forma_pagamento');
    $this->load->model('pedido_cartao_model', 'pedido_cartao');
    $this->load->model('pedido_boleto_model', 'pedido_boleto');
    $this->load->model('pedido_transacao_model', 'pedido_transacao');

    $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
    if( $item ) {
      $forma_pagamento = $this->forma_pagamento->get_by_id($dados['forma_pagamento_id']);
    } else {
      $forma_pagamento = $this->forma_pagamento->get_by_id($item['forma_pagamento_id']);
    }

    if($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_CREDITO){

      $this->load->library('encrypt');

      $dados_cartao = array();
      $dados_cartao['pedido_id'] = $pedido_id;
      $dados_cartao['numero'] = $this->encrypt->encode(app_retorna_numeros($dados['numero']));
      $dt = explode('/', $dados['validade']);
      $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
      $dados_cartao['validade'] = $this->encrypt->encode(date('Ym',$date));
      $dados_cartao['nome'] = $this->encrypt->encode($dados['nome_cartao']);
      $dados_cartao['codigo'] = $this->encrypt->encode($dados['codigo']);
      $dados_cartao['bandeira'] = $this->encrypt->encode($forma_pagamento['slug']);
      $dados_cartao['bandeira_cartao'] = $this->encrypt->encode($dados['bandeira_cartao']);

      //Se possuir, insere
      if($this->input->post("dia_vencimento"))
        $dados_cartao['dia_vencimento'] =  $this->encrypt->encode($dados['dia_vencimento']);

      $dados_cartao['processado'] = 0;
      //
      $this->pedido_cartao->update_by(
        array('pedido_id' =>$pedido_id),array(
          'ativo' => 0
        )
      );


      $this->pedido_cartao->insert($dados_cartao, true);
      $this->pedido_transacao->insStatus($pedido_id, "Aguardando_pagamento");

    }elseif($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_CARTAO_DEBITO){

      $this->load->library("encrypt");

      $dados_cartao = array();
      $dados_cartao["pedido_id"] = $pedido_id;
      $dados_cartao["numero"] = $this->encrypt->encode(app_retorna_numeros($dados["numero"]));
      $dt = explode("/", $dados["validade"]);
      $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
      $dados_cartao["validade"] = $this->encrypt->encode(date("Ym",$date));
      $dados_cartao["nome"] = $this->encrypt->encode($dados["nome_cartao"]);
      $dados_cartao["codigo"] = $this->encrypt->encode($dados["codigo"]);
      $dados_cartao["bandeira"] = $this->encrypt->encode($forma_pagamento["slug"]);
      $dados_cartao["bandeira_cartao"] = $this->encrypt->encode($dados["bandeira_cartao"]);


      $dados_cartao["processado"] = 0;
      $this->pedido_cartao->update_by(
        array("pedido_id" => $pedido_id),
        array("ativo" => 0)
      );

      $this->pedido_cartao->insert($dados_cartao, true);
      $this->pedido_transacao->insStatus($pedido_id, "Aguardando_pagamento");
    }elseif($dados["forma_pagamento_tipo_id"] == self::FORMA_PAGAMENTO_BOLETO){

      if( isset( $dados["cotacao_id"] ) ) {
        unset( $dados["cotacao_id"] );
      }

      if( isset( $dados["produto_parceiro_id"] ) ) {
        unset( $dados["produto_parceiro_id"] );
      }

      if( isset( $dados["pedido_id"] ) ) {
        unset( $dados["pedido_id"] );
      }

      if( isset( $dados["forma_pagamento_id"] ) ) {
        unset( $dados["forma_pagamento_id"] );
      }

      if( isset( $dados["forma_pagamento_tipo_id"] ) ) {
        unset( $dados["forma_pagamento_tipo_id"] );
      }

      if( isset( $dados["num_parcela"] ) ) {
        unset( $dados["num_parcela"] );
      }

      $dados_boleto = $dados;

      $dados_boleto["processado"] = 0;
      $this->pedido_boleto->insert( $dados_boleto, true );
      $this->pedido_transacao->insStatus( $pedido_id, "Aguardando_pagamento" );
    }

  }
}














