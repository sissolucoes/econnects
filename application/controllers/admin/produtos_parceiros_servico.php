<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Produtos_Parceiros_Servico extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiro / Serviços ");
        $this->template->set_breadcrumb("Produtos / Parceiro / Serviços", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_servico_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        
        $this->load->model("servico_model", "servico");
        $this->load->model("servico_tipo_model", "servico_tipo");
    }

    public function index($produto_parceiro_id , $offset = 0)
    {
        $this->auth->check_permission('view', 'produto_parceiro_servico', 'admin/poroduto_parceiro/');

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Serviços");
        $this->template->set_breadcrumb("Serviços", base_url("$this->controller_uri/index/{$produto_parceiro_id}"));

        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        $rows = $this->current_model
            ->with_foreign()
            ->get_many_by(array(
            'produto_parceiro_servico.produto_parceiro_id' => $produto_parceiro_id
        ));

        $data = array();
        $data['servicos'] = $this->servico->with_foreign()->with_prod_parc_serv($produto_parceiro_id)->get_all();

        $data['rows'] = $rows;
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['parceiro_id'] = $produto_parceiro['parceiro_id'];
        $data['primary_key'] = $this->current_model->primary_key();

        if($_POST){
            $this->current_model->delete_by(array(
                'produto_parceiro_id' => $produto_parceiro_id
            ));
            if($this->input->post('selec_row')){
                foreach ($this->input->post('selec_row') as $item) {
                    $dados_produtos = array();
                    $dados_produtos['produto_parceiro_id'] = $produto_parceiro_id;
                    $dados_produtos['servico_id'] = $item;
                    $dados_produtos['param'] = $this->input->post('param_'. $item ); #21291171
                    $this->current_model->insert($dados_produtos, TRUE);
                    //Mensagem de sucesso
                }

            }
            $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

            //Redireciona para index
            redirect("$this->controller_uri/index/{$produto_parceiro_id}");
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function index2($produto_parceiro_id , $offset = 0)
    {
        $this->auth->check_permission('view', 'produto_parceiro_regra_preco', 'admin/poroduto_parceiro/');

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Regra de Preços");
        $this->template->set_breadcrumb("Regra de Preços", base_url("$this->controller_uri/index/{$produto_parceiro_id}"));

        $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

        //Verifica se registro existe
        if(!$produto_parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }


        $rows = $this->current_model
            ->with_foreign()
            ->get_many_by(array(
                'produto_parceiro.produto_parceiro_id' => $produto_parceiro_id
            ));

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $rows;
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['parceiro_id'] = $produto_parceiro['parceiro_id'];
        $data['primary_key'] = $this->current_model->primary_key();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_id){

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar comunicação de parceiro");
        $this->template->set_breadcrumb("Comunicação de parceiro", base_url("$this->controller_uri/index"));

        //Verifica se registro existe
        if(!$produto_parceiro_id)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '1';
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        
        $data['comunicacoes_eventos'] = $this->comunicacao_evento->get_all();
        $data['comunicacoes_templates'] = $this->comunicacao_template->get_all();


        //Caso post
        if($_POST)
        {

            if ($this->current_model->validate_form())
            {
                $insert_id = $this->current_model->insert_form();
                if($insert_id)
                {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.' ); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.'. validation_errors());
                }
                //Redirecionamento
                redirect("$this->controller_uri/index/{$produto_parceiro_id}");
            }

        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );

    }

    public function edit($produto_parceiro_id, $id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Regra de Preços");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        $row =  $this->current_model->get($id);

        //Verifica se registro existe
        if(!$row)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/$produto_parceiro_id/{$id}");
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['comunicacoes_eventos'] = $this->comunicacao_evento->get_all();
        $data['comunicacoes_templates'] = $this->comunicacao_template->get_all();

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
                redirect("$this->controller_uri/index/{$produto_parceiro_id}");
            }
        }



        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public  function delete($produto_parceiro_id, $id)
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

        redirect("$this->controller_uri/index/{$produto_parceiro_id}");
    }


}
