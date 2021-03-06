<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Produtos_Parceiros
 *
 * @property Produto_Parceiro_Model $current_model
 *
 */
class Produtos_Parceiros_Configuracao_Comissao extends Admin_Controller
{



    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Produtos / Parceiros / Configurações / Regra de Negócios / Comissão");
        $this->template->set_breadcrumb("PProdutos / Parceiros / Configurações / Regra de Negócios / Comissão", base_url("$this->controller_uri/edit"));

        //Carrega modelos
        $this->load->model('produto_parceiro_configuracao_model', 'current_model');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');


    }



    public function edit($produto_parceiro_id) //Função que edita registro
    {

        $this->template->js(app_assets_url('modulos/produtos_parceiros_configuracao/base.js', 'admin'));

                //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos / Parceiros / Configurações / Regra de Negócios / Comissão");
        $this->template->set_breadcrumb('Produtos / Parceiros / Configurações / Regra de Negócios / Comissão', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $row = $this->current_model->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($row) > 0){
            $data['row'] = $row[0];
        }else{
            $data['row'] = NULL;
        }

        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$produto_parceiro_id}");


        $produto_parceiro =  $this->produto_parceiro->get($produto_parceiro_id);


        if(!$produto_parceiro){
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("admin/parceiros/index");

        }


        //Verifica se registro existe
        if(!$data['row'])
        {
            $data['row'] = array();
            $data['row']['parceiro_configuracao_id'] = 0;
            $data['row']['repasse_comissao'] = 0;
            $data['row']['padrao_repasse_comissao'] = 0;
            $data['row']['repasse_maximo'] = '000,000';
            $data['row']['padrao_repasse_maximo'] = '000,000';
            $data['new_record'] = '1';
        }else{
            $data['new_record'] = '0';
        }

        $data['produto_parceiro'] = $produto_parceiro;


        //Caso post
        if($_POST)
        {

            //check_markup_relacionamento
            if($this->current_model->validate_form('comissao')) //Valida form
            {

                if($this->input->post('new_record') == '1'){
                    $this->current_model->insert_config('comissao');
                }else {
                    //Realiza update
                    $this->current_model->update_config('comissao');
                }



                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("admin/produtos_parceiros_configuracao_comissao/edit/{$produto_parceiro['produto_parceiro_id']}");
            }
        }


        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['produto_parceiro'] = $produto_parceiro;




        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }


    public function check_markup_relacionamento($markup)
    {

        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $markup = app_unformat_currency($markup);
        $this->load->library('form_validation');


        $produto_parceiro_id = $this->input->post('produto_parceiro_id');

        $soma = $this->relacionamento->get_todas_comissoes($produto_parceiro_id);

        $this->form_validation->set_message('check_markup_relacionamento', 'O Valor do campo markup deve ser inferior ou igual as somas de todas as comissões dos parceiros relacionados para esse produto. Soma total: ' . app_format_currency($soma, false, 2));

        if($soma > $markup){
            return false;
        }else{
            return true;
        }


    }

    public function check_tipo_calculo($tipo_calculo)
    {

        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('produto_parceiro_desconto_model', 'produto_parceiro_desconto');

        $produto_parceiro_id = $this->input->post('produto_parceiro_id');

        $result = TRUE;
        if($tipo_calculo != 2){
           if($this->relacionamento->is_desconto_produto_habilitado($produto_parceiro_id) === TRUE){
               $result = FALSE;
           }

            if($this->produto_parceiro_desconto->is_desconto_habilitado($produto_parceiro_id) === TRUE){
                $result = FALSE;
            }


        }
        



        if($result === FALSE){
            $this->form_validation->set_message('check_tipo_calculo', 'O Tipo de Cálculo selecionado não aceita desconto condiciona, favor desabilite o desconto condicional.');
        }

        return $result;


    }


    public function check_repasse_maximo($repasse)
    {

        $this->load->library('form_validation');

        $this->form_validation->set_message('check_repasse_maximo', 'O Campo Repasse máximo deve ser inferior ou igual do que o campo Comissão');

        $comissao = $this->input->post('comissao');


        $repasse = app_unformat_currency($repasse);
        $comissao = app_unformat_currency($comissao);


        if($repasse > $comissao){
            return false;

        }else{
            return true;

        }

    }

}
