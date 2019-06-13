<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apolice extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
        header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-type" );
        header( "Content-Type: application/json");

        $method = $_SERVER["REQUEST_METHOD"];
        if( $method == "OPTIONS" ) {
            die();
        }

        $this->load->database('default');

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('apolice_status_model', 'apolice_status');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model("fatura_model", "fatura");
        $this->load->model("fatura_parcela_model", "fatura_parcela");

        $this->load->helper("api_helper");
    }

    private function checkKey() {
        if( !isset( $_SERVER["HTTP_APIKEY"] ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
        }

        $this->api_key = $_SERVER["HTTP_APIKEY"];
        $this->load->model( "usuario_webservice_model", "webservice" );

        $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
        if( !sizeof( $webservice ) ) {
            die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
        }

        $this->usuario_id = $webservice["usuario_id"];
        $this->parceiro_id = $webservice["parceiro_id"];
        return $webservice;
    }

    public function consultaBase() {
        die( json_encode( $this->retornaApolices($_POST), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function consultaBaseProduto() {
        die( json_encode( $this->retornaProdutoApolices($_POST), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function retornaApolices($GET = []) {
        $pedidos = $this->filtraPedidos($GET);

        if (!empty($pedidos['status'])) {

            $pedidos = $pedidos['pedidos']->get_all();
            if($pedidos) {
                $resposta = [];

                foreach ($pedidos as $pedido) {
                    //Monta resposta da apólice
                    $apolice = $this->apolice->getApolicePedido( $pedido["pedido_id"] );
                    $apolice[0]["inadimplente"] = ($this->pedido->isInadimplente( $pedido["pedido_id"] ) === false ) ? 0 : 1;

                    $faturas = $this->fatura->filterByPedido($pedido["pedido_id"])
                    ->with_fatura_status()
                    ->with_pedido()
                    ->order_by("data_processamento")
                    ->get_all();

                    foreach ($faturas as $index => $fatura) {
                        $faturas[$index]["parcelas"] = $this->fatura_parcela->with_fatura_status()
                            ->filterByFatura($fatura["fatura_id"])
                            ->order_by("num_parcela")
                            ->get_all();
                    }

                    $resposta[] = array(
                        "apolice" => api_retira_timestamps($apolice),
                        "faturas" => api_retira_timestamps($faturas),
                        "pedido" => api_retira_timestamps($pedido),
                    );
                }

                return array("status" => true, "dados" => $resposta);
            } else {
                return array( "status" => false, "message" => "Não foi possível localizar a apólice com os parâmetros informados" );
            }

        } else {
            return $pedidos;
        }
    }

    public function retornaProdutoApolices($GET = []) {
        $pedidos = $this->filtraPedidos($GET);

        if (!empty($pedidos['status'])) {
            $pedidos = $pedidos['pedidos']->group_by('produto.produto_id, produto.nome')->get_all();
            return array("status" => true, "dados" => api_retira_timestamps($pedidos));
        } else {
            return $pedidos;
        }
    }

    private function filtraPedidos($GET = []) {
        $apolice_id = null;
        if( isset( $GET["apolice_id"] ) ) {
            $apolice_id = $GET["apolice_id"];
        } 

        $num_apolice = null;
        if( isset( $GET["num_apolice"] ) ) {
            $num_apolice = $GET["num_apolice"];
        }

        $documento = null;
        if( isset( $GET["documento"] ) ) {
            $documento = $GET["documento"];
        }

        $pedido_id = null;
        if( isset( $GET["pedido_id"] ) ) {
            $pedido_id = $GET["pedido_id"];
        }

        $parceiro_id = null;
        if( isset( $GET["parceiro_id"] ) ) {
            $parceiro_id = $GET["parceiro_id"];
        }

        $produto_id = null;
        if( isset( $GET["produto_id"] ) ) {
            $produto_id = $GET["produto_id"];
        }

        $retorno = null;
        $params = array();
        $params["apolice_id"] = $apolice_id;
        $params["num_apolice"] = $num_apolice;
        $params["documento"] = $documento;
        $params["pedido_id"] = $pedido_id;
        $params["parceiro_id"] = $parceiro_id;
        $params["produto_id"] = $produto_id;

        if($apolice_id || $num_apolice || $documento || $pedido_id ) {
            $pedidos = $this->pedido
            ->with_pedido_status()
            ->with_cotacao_cliente_contato()
            ->with_apolice()
            ->with_produto_parceiro()
            // ->with_fatura()
            ->filterNotCarrinho()
            ->filterAPI($params);            

            $retorno = array("status" => true, "pedidos" => $pedidos);
        } else {
            $retorno = array( "status" => false, "message" => "Parâmetros inválidos" );
        }

        return $retorno;
    }

    public function index() {

        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
            die( json_encode( $this->retornaApolices($GET), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {
            if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "PUT" ) {
                $PUT = json_decode( file_get_contents( "php://input" ), true );
                if( !isset( $PUT["apolice_id"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
                }
                if( !isset( $PUT["num_apolice"] ) ) {
                    die( json_encode( array( "status" => false, "message" => "Campo num_apolice é obrigatório" ) ) );
                }
                $apolice_id = $PUT["apolice_id"];
                $num_apolice = $PUT["num_apolice"];

                $this->update( $apolice_id, $num_apolice );
            } else {
                die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
            }
        }

    }

    private function update( $apolice_id, $num_apolice ) {
        $this->db->query("UPDATE apolice SET num_apolice='$num_apolice' WHERE apolice_id=$apolice_id" );
        $result = $this->db->query("SELECT * FROM apolice WHERE apolice_id=$apolice_id" )->result_array();
        die( json_encode( array( "status" => (bool)sizeof($result), "apolice" => $result ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function validarDadosEntrada()
    {
        if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            $POST = json_decode( file_get_contents( "php://input" ), true );
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $apolice_id = null;
        if( isset( $POST["apolice_id"] ) ) {
            $apolice_id = $POST["apolice_id"];
            $params["apolice_id"] = $apolice_id;
        } else {
            die( json_encode( array( "status" => false, "message" => "Campo apolice_id é obrigatório" ) ) );
        }

        $this->load->model("pedido_model", "pedido");

        $pedido = $this->pedido->with_apolice()->filter_by_apolice($apolice_id)->get_all();

        if(!$pedido) {
            die( json_encode( array( "status" => false, "message" => "Apólice não encontrada" ) ) );
        }
        

        return [ 'dados' => $POST, 'pedido_id' => $pedido[0]["pedido_id"] ];
    }

    public function cancelar() {
        
        $this->checkKey();

        $validacao = $this->validarDadosEntrada();
        $pedido_id = $validacao['pedido_id'];
        $dados_bancarios = !empty($validacao['dados']['dados_bancarios']) ? $validacao['dados']['dados_bancarios'] : [];
        $define_date = !empty($validacao['dados']["define_date"]) ? $validacao['dados']["define_date"] : date("Y-m-d H:i:s") ;

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento( $pedido_id, $dados_bancarios, $define_date);

        if( isset( $produto_parceiro_cancelamento["result"] ) && $produto_parceiro_cancelamento["result"] == false ) {
            die( json_encode( array( "status" => false, "message" => $produto_parceiro_cancelamento["mensagem"] ) ) );
        } else {
            die( json_encode( array( "status" => true, "message" => "Apólice cancelada com sucesso" ) ) );
        }

    }

    public function calculoCancelar() {
        $this->checkKey();

        $validacao = $this->validarDadosEntrada();


        $pedido_id = $validacao['pedido_id'];
        $define_date = !empty($validacao['dados']["define_date"]) ? $validacao['dados']["define_date"] : date("Y-m-d H:i:s") ;

        //pega as configurações de cancelamento do pedido
        $produto_parceiro_cancelamento = $this->pedido->cancelamento_calculo( $pedido_id , $define_date );

        die( json_encode( $produto_parceiro_cancelamento ) );
    }

    public function getDocumentos()
    {
        $this->checkKey();

        if( empty( $_GET["plano_slug"] ) ) {
            die( json_encode( array( "status" => false, "message" => "O Atributo plano_slug é obrigatório" ) ) );
        }

        $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
        $this->load->model( "produto_parceiro_plano_tipo_documento_model", "produto_parceiro_plano_tipo_documento" );

        $planos = $this->produto_parceiro_plano
            ->wtih_plano_habilitado($this->parceiro_id)
            ->filter_by_slug($_GET["plano_slug"])
            ->get_all_select();
        if( empty( $planos ) ) {
            die( json_encode( array( "status" => false, "message" => "Nenhum plano encontrado com o slug informado" ) ) );
        }

        $produto_parceiro_plano_id = $planos[0]['produto_parceiro_plano_id'];

        $docs = $this->produto_parceiro_plano_tipo_documento
            ->filter_by_plano_slug($_GET["plano_slug"])
            ->with_tipo_documento()
            ->get_all_select();
        if( empty( $docs ) ) {
            die( json_encode( array( "status" => false, "message" => "Nenhum documento encontrado" ) ) );
        }

        die( json_encode( array( "status" => true, "message" => "OK" , "documents" => $docs) ) );
    }

    public function sendDocumentos()
    {
        $this->checkKey();

        $payload = json_decode( file_get_contents( "php://input" ), false );

        if( empty( $payload->{"apolice_id"} ) ) {
            die( json_encode( array( "status" => false, "message" => "O atributo 'apolice_id' é um campo obrigatório" ) ) );
        } else {
            $apolice_id = $payload->{"apolice_id"};
        }

        if( empty( $payload->{"itens"} ) ) {
            die( json_encode( array( "status" => false, "message" => "O atributo 'itens' é um campo obrigatório" ) ) );
        }

        // Models
        $this->load->model( "apolice_model", "apolice" );
        $this->load->model( "apolice_documento_model", "docs" );
        $this->load->model( "produto_parceiro_plano_tipo_documento_model", "prod_parc_plano_doc" );

        $apolice = $this->apolice->get($apolice_id);
        if( empty( $apolice ) ) {
            die( json_encode( array( "status" => false, "message" => "Apólice não encontrada (#$apolice_id)" ) ) );
        }

        $produto_parceiro_plano_id = $apolice['produto_parceiro_plano_id'];

        $itens = [];
        $cont=0;

        foreach ($payload->{"itens"} as $item) {

            if( empty( $item->{"tipo_documento_id"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'tipo_documento_id' é um campo obrigatório" ) ) );
            }

            if( empty( $item->{"extension"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'extension' é um campo obrigatório" ) ) );
            }

            if( empty( $item->{"file"} ) ) {
                die( json_encode( array( "status" => false, "message" => "O atributo 'file' é um campo obrigatório (base64)" ) ) );
            }

            $produto_parceiro_plano_tipo_documento = $this->prod_parc_plano_doc->filter_by_plano_id($produto_parceiro_plano_id)->filter_by_tipo_doc_id($item->{"tipo_documento_id"})->get_all();
            if( empty( $produto_parceiro_plano_tipo_documento ) ) {
                die( json_encode( array( "status" => false, "message" => "Tipo de Documento não habilitado para este Plano" ) ) );
            }

            $produto_parceiro_plano_tipo_documento_id = $produto_parceiro_plano_tipo_documento[0]['produto_parceiro_plano_tipo_documento_id'];

            $itens[$cont] = (array) $item;
            $itens[$cont]['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
            $itens[$cont]['produto_parceiro_plano_tipo_documento_id'] = $produto_parceiro_plano_tipo_documento_id;

            $cont++;
        }

        // inativa todos os documentos inseridos
        $this->docs->disableDoc($apolice_id);

        $cont = 0;
        foreach ($itens as $item) {

            #$tipo_arquivo = pathinfo($arquivo, PATHINFO_EXTENSION);
            $produto_parceiro_plano_tipo_documento_id = $item["produto_parceiro_plano_tipo_documento_id"];
            $name = "{$apolice_id}_{$produto_parceiro_plano_tipo_documento_id}_". date('Ymd_His') ."_". rand(0,100) .".". $item["extension"];

            $apolice_documento_id = $this->docs->uploadFile($name, $apolice_id, $produto_parceiro_plano_tipo_documento_id, $item["file"]);
            $itens[$cont]['apolice_documento_id'] = $apolice_documento_id;
            unset($itens[$cont]['file']);
        }

        die( json_encode( array( "status" => true, "message" => "OK" , "documents" => $itens) ) );
    }

}
