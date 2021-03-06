<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Usuario_Acl_Tipo_Model $current_model
 *
 */
class Usuarios_Acl_Tipos extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();

        $this->template->set('page_title', 'Perfis');
        $this->template->set_breadcrumb('Perfis', base_url("$this->controller_uri/index"));

        $this->load->model('usuario_acl_tipo_model', 'current_model');
    }

	public function index($offset = 0, $parceiro_id = 0)
	{
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'List');
        $this->template->set_breadcrumb('List', base_url("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id)));

        $this->load->library('pagination');

        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id));
        $config['uri_segment'] = 4;
        $config['total_rows'] = count($this->current_model->filter_by_parceiro(emptyor($parceiro_id, $this->parceiro_id), $this->parceiro_pai_id));
        $config['per_page'] = 10;
        $this->pagination->initialize($config);

        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['parceiro_id'] = emptyor($parceiro_id, $this->parceiro_id);
        $data['parceiro_pai_id'] = $this->parceiro_pai_id;
        $data['rows'] = $this->current_model->filter_by_parceiro(emptyor($parceiro_id, $this->parceiro_id), $this->parceiro_pai_id, $config['per_page'], $offset);

        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
	}

    public function add($parceiro_id = 0)
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Add');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id)));

        if($_POST){

            if($this->current_model->validate_form()){

                $insert_id = $this->current_model->insert_form();

                if($insert_id){

                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                }else {

                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }

                redirect("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id));
            }
        }

        $data = array();
        $data['parceiro_id'] = emptyor($parceiro_id, $this->parceiro_id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] = base_url("$this->controller_uri/add/". emptyor($parceiro_id, $this->parceiro_id));

        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($id, $parceiro_id = 0)
    {
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Editar');
        $this->template->set_breadcrumb('Edit', base_url("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id)));

        $data = array();
        $data['row'] = $this->current_model->get($id);

        if(!$data['row'])
        {
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id));
        }

        if($_POST)
        {
            if($this->current_model->validate_form())
            {
                $this->current_model->update_form();

                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');
                redirect("$this->controller_uri/index/0/". emptyor($parceiro_id, $this->parceiro_id));
            }
        }

        $data['parceiro_id'] = emptyor($parceiro_id, $this->parceiro_id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}/". emptyor($parceiro_id, $this->parceiro_id));

        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function delete($id, $parceiro_id = 0)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index/0/". emptyor($parceiro_id, $this->parceiro_id));
    }

}
