<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros_Planos
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */

require_once APPPATH . 'controllers/admin/api.php';

class Produtos_Parceiros_Planos extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro / Planos");
        $this->template->set_breadcrumb("Produtos / Parceiro / Planos", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_plano_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('precificacao_tipo_model', 'precificacao_tipo');
        $this->load->model('comissao_tipo_model', 'comissao_tipo');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('capitalizacao_model', 'capitalizacao');

        $this->parceiro_id = $this->session->userdata('parceiro_id');
    }

    public function set_ordem($produto_parceiro_id = 0)
    {
        if(isset($_POST['itens']))
        {
            $i = 1;
            foreach ($_POST['itens'] as $item)
            {
                $data_ordem = array();
                $data_ordem['ordem'] = $i;
                $this->current_model->update($item[0], $data_ordem, TRUE);
                $i++;
            }
            $this->session->set_flashdata('succ_msg', 'A ordem foi salva corretamente.');
        }
        else
        {
            $this->session->set_flashdata('fail_msg', 'Não possuem registros para salvar a ordem.');
            exit('0');
        }
        exit('1');
    }

    public function view_by_produto_parceiro($produto_parceiro_id , $offset = 0)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        $this->template->js(app_assets_url('core/js/jquery.tablednd.js', 'admin'));
        $this->template->js(app_assets_url('modulos/produtos_parceiros_planos/base.js', 'admin'));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Planos");
        $this->template->set_breadcrumb("Planos", base_url("$this->controller_uri/view_by_produto_parceiro/{$produto_parceiro_id}"));

        $produto_parceiro = $this->produto_parceiro->with_produto()->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/view_by_produto_parceiro/{$produto_parceiro_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_total();
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();

        $data['rows'] = $this->current_model->coreSelectPlanosProduto()
            ->limit($config['per_page'], $offset)
            ->order_by('produto_parceiro_plano.ordem', 'asc')
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;

        $enableSomeCreateKey = true;
        foreach ($data['rows'] as $key => $r)
		{
			$planos = $this->current_model->PlanosHabilitados($this->parceiro_id, $produto_parceiro_id, $r['slug_plano']);
			$data['rows'][$key]['enableCreateKey'] = (!empty($planos));

			if (empty($planos)) $enableSomeCreateKey = false;
        }

        $data['enableSomeCreateKey'] = $enableSomeCreateKey;
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view_by_produto_parceiro", $data );
    }

    public function add_by_produto_parceiro($produto_parceiro_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos");
        $this->template->set_breadcrumb("Produtos", base_url("$this->controller_uri/index"));


        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Insere form
                $insert_id = $this->current_model->insert_form();
                if($insert_id)
                {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("{$this->controller_uri}/view_by_produto_parceiro/{$produto_parceiro_id}");
            }
        }

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;
        $data['precificacao_tipo'] = $this->precificacao_tipo->get_all();
        $data['capitalizacao'] = $this->capitalizacao->with_capitalizacao_tipo()->with_produto_parceiro_capitalizacao($produto_parceiro['produto_parceiro_id'])->get_all();
        $data['comissao_tipo'] = $this->comissao_tipo->get_all();
        $data['moeda'] = $this->moeda->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Plano");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        $produto_parceiro =  $this->produto_parceiro->get($data['row']['produto_parceiro_id']);

        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {
                //Realiza update
                $this->current_model->update_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("{$this->controller_uri}/view_by_produto_parceiro/{$produto_parceiro['produto_parceiro_id']}");
            }
        }

        $data['produto_parceiro_id'] = $produto_parceiro['produto_parceiro_id'];
        $data['produto_parceiro'] = $produto_parceiro;
        $data['precificacao_tipo'] = $this->precificacao_tipo->get_all();
        $data['capitalizacao'] = $this->capitalizacao->with_capitalizacao_tipo()->with_produto_parceiro_capitalizacao($produto_parceiro['produto_parceiro_id'])->get_all();
        $data['comissao_tipo'] = $this->comissao_tipo->get_all();
        $data['moeda'] = $this->moeda->get_all();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function delete($id)
    {
        $row = $this->current_model->get($id);
        if(!$row){
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');

        redirect("{$this->controller_uri}/view_by_produto_parceiro/{$row['produto_parceiro_id']}");
    }

    public function keyCreate($produto_parceiro_id, $id)
    {
        $row = $this->current_model->get($id);
        if(!$row){
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");
        }



        //Gera as cotações
        /*
        case 'cotacao':

            $this->load->model( "produto_parceiro_model", "produto_parceiro" );
            $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );

            $produtos = $this->produto_parceiro->get_produtos_venda_admin_parceiros( $this->parceiro_id, $parametros['produto_slug'] );
            if(!empty($produtos))
            {
                foreach ($produtos as $prod) {

                    // Separando o produto do parceiro
                    $r = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro($prod['produto_parceiro_id'])->filter_by_slug($parametros["plano_slug"])->get_all();

                    if(!empty($r)){
                        $this->produto_parceiro_id = $prod['produto_parceiro_id'];
                        $this->parceiro_id_pai     = $prod['parceiro_id'];
                        $this->produto_parceiro_plano_id = $r[0]['produto_parceiro_plano_id'];
                    }

                }

                if (empty($this->produto_parceiro_id)) {
                    die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o Plano"),JSON_UNESCAPED_UNICODE));
                }
            }
            else 
            {
                die(json_encode(array("status"=>false,"message"=>"Não foi possível identificar o Produto"),JSON_UNESCAPED_UNICODE));
            }
            */

            // Campos da cotação
            $arrOptions = [
                "produto_parceiro_id"       => $produto_parceiro_id,
                "produto_parceiro_plano_id" => $id,
            ];

            $url = base_url() ."api/cotacao";
            $obj = new Api();
            $r = $obj->execute($url, 'POST', json_encode($arrOptions));

            print_pre($r);

            if(!empty($r))
            {
                // pegando o ID da cotação
                $retorno = convert_objeto_to_array($r);
                if( !empty($retorno->{"status"}) )
                {
                    $cotacao_id = $retorno->{"cotacao_id"};

                    // Chamando o Calculo da cotação
                    // $this->etapas('calculocotacao');
                } 
                else 
                {
                    $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                    $this->session->set_flashdata('fail_msg', $msg);
                }
            }
            else
            {
            	$this->session->set_flashdata('fail_msg', "Não foi possível criar a cotação");
            }
/*
            break;

        case 'calculocotacao':

            // Validar o valor passado se diferente alertar e abortar
            $url = base_url() ."api/cotacao/calculo";
            $fields = [
                'cotacao_id' => $this->cotacao_id,
                'coberturas' => emptyor($this->campos_estrutura['coberturas'], []),
            ];

            $obj = new Api();
            $r = $obj->execute($url, 'POST', json_encode($fields));

            if(!empty($r))
            {
                $retorno = convert_objeto_to_array($r);
                if( !empty($retorno->{"status"}) )
                {
                    // Validação valores  
                    if(!empty($this->valor_premio_bruto) && $this->valor_premio_bruto != $retorno->{"premio_liquido_total"}){
                        die(json_encode(array("status"=>false,"message"=>"O valor do prêmio {$this->valor_premio_bruto} informado diferente do valor calculado ".$retorno->{"premio_liquido_total"}, "cotacao_id" => $this->cotacao_id),JSON_UNESCAPED_UNICODE));
                    }

                    $retorno->{"cotacao_id"} = $this->cotacao_id;
                    $this->etapas('contratarcotacao',$retorno);
                }
                else
                {
                    $msg = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
                    die(json_encode(array("status"=>false, "message"=>$msg, "cotacao_id" => $this->cotacao_id),JSON_UNESCAPED_UNICODE));
                }
            }
            else
            {
                die(json_encode(array("status"=>false,"message"=>"Não foi possível retornar os dados do cálculo da cotação"),JSON_UNESCAPED_UNICODE));
            }
            break;
*/
        $this->session->set_flashdata('succ_msg', 'Chaves geradas com sucesso.');

        redirect("{$this->controller_uri}/view_by_produto_parceiro/{$row['produto_parceiro_id']}");
    }

}
