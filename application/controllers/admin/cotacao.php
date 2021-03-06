<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Cotacao_Model $current_model
 *
 */
class Cotacao extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Cotações");
        $this->template->set_breadcrumb("Cotações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('cotacao_model', 'current_model');
    }

    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Cotações");
        $this->template->set_breadcrumb("Cotações", base_url("$this->controller_uri/index"));

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->with_clientes_contatos()
            ->filterFromInput()
            ->with_produto_parceiro()
            ->filterByStatus(1)
            ->get_total();

        $config['per_page'] = 10;

        $this->pagination->initialize($config);
        
        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model
            ->filterByStatus(1)
            ->filterFromInput()
            ->limit($config['per_page'], $offset)
            ->with_clientes_contatos()
            ->with_produto_parceiro()
            ->get_all();

        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function view($id)
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Detalhes da Cotação");
        $this->template->set_breadcrumb('Cotação', base_url("$this->controller_uri/view/{$id}"));

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->with_produto_parceiro()->get($id);
        $data['produto'] = $this->current_model->get_cotacao_produto($id);

        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

       // print_r($data['seguro_viagem']);exit;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/view/{$id}");
        
        //Verifica se registro existe
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }

    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }

}
