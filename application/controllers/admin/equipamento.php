<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 *
 * @property Localidade_Países $current_model
 *
 */
class Equipamento extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Equipamento");
        $this->template->set_breadcrumb("Equipamento", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('equipamento_model', 'current_model');
    }


    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service_categorias($categoria_id = 0, $nivel = 1)
    {
        $this->load->model("equipamento_categoria_model", "equipamento_categoria");
        $filter             = $this->input->get_post("q");
        $marca_id           = $this->input->get_post("marca_id");
        $categoria_pai_id   = (!empty($categoria_pai_id)) ? $categoria_pai_id : $this->input->get_post("categoria_pai_id");
        $page               = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;
        $limit              = 30;

        //Se houver categoria
        if( !empty($categoria_id) )
        {
            $itens = $this->equipamento_categoria
                ->with_foreign()
                ->get($categoria_id);

            $itens['id'] = $categoria_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));

            return;
        }

        //Retorna tudo
        $data = $this->equipamento_categoria;
        $data->limit($limit, $limit*($page-1));

        if($filter)
        {
            $data->_database->or_where('(vw_Equipamentos_Linhas.nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('vw_Equipamentos_Linhas.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data->with_foreign();

        if (!empty($nivel)) {
            if ($nivel == 1) {
                $data = $data->filter_by_nviel(1);
            } else {
                $data = $data->with_sub_categoria($categoria_pai_id, $marca_id);
            }
        }

        $data = $data->get_all();

        $total = $this->equipamento_categoria;

        if($filter)
        {
              $total->_database->or_where('(vw_Equipamentos_Linhas.nome LIKE "%'.$filter.'%"', NULL, FALSE);
              $total->_database->or_where('vw_Equipamentos_Linhas.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $total = $total->with_foreign();

        if (!empty($nivel)) {
            if ($nivel == 1) {
                $total = $total->filter_by_nviel(1);
            } else {
                $total = $total->with_sub_categoria($categoria_pai_id, $marca_id);
            }
        }

        $total = $total->get_total("DISTINCT vw_Equipamentos_Linhas.equipamento_categoria_id");

        foreach ($data as $index => $item)
        {
            $data[$index]['id'] = $item['equipamento_categoria_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }


    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service_marcas($marca_id = 0, $categoria_id = 0)
    {
        $this->load->model("equipamento_marca_model", "equipamento_marca");

        $filter = $this->input->get_post("q");
        $categoria_id = (!empty($categoria_id)) ? $categoria_id : $this->input->get_post("categoria_id");
        $page = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;
        $limit = 30;

        //Se houver categoria
        if($marca_id > 0)
        {
            $itens = $this->equipamento_marca
                ->with_foreign()
                ->get($marca_id);

            $itens['id'] = $marca_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));

            return;
        }

        //Retorna tudo
        $total = $this->equipamento_marca;

        if (!empty($categoria_id))
        {
            $total->_database->where("vw_Equipamentos_Marcas.equipamento_marca_id IN(SELECT DISTINCT equipamento_marca_id FROM vw_Equipamentos WHERE deletado = 0 AND equipamento_categoria_id = {$categoria_id})", NULL, FALSE);
        }

        if($filter) {
            $total->_database->where('(vw_Equipamentos_Marcas.nome LIKE "%' . $filter . '%"', NULL, FALSE);
            $total->_database->or_where('vw_Equipamentos_Marcas.descricao LIKE "%' . $filter . '%")', NULL, FALSE);
        }

        $total = $total
            ->with_foreign()
            ->get_total();

        $data = $this->equipamento_marca;

        $data->limit($limit, $limit*($page-1));

        if (!empty($categoria_id))
        {
            $data->_database->where("vw_Equipamentos_Marcas.equipamento_marca_id IN(SELECT DISTINCT equipamento_marca_id FROM vw_Equipamentos WHERE deletado = 0 AND equipamento_categoria_id = {$categoria_id})", NULL, FALSE);
        }

        if($filter)
        {
            $data->_database->where('(vw_Equipamentos_Marcas.nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('vw_Equipamentos_Marcas.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data
            ->with_foreign()
            ->get_all();

        foreach ($data as $index => $item)
        {
            $data[$index]['id'] = $item['equipamento_marca_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }


    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service($equipamento_id = 0, $marca_id = 0, $categoria_id = 0)
    {
        $json = array();

        if (empty($equipamento_id)){
            if (isset($_POST['0'])) {

                $data = $this->current_model->with_foreign()->whith_multiples_ids($_POST)->get_all();
                foreach ($data as $index => $item) {
                    $data[$index]['id'] = $item['equipamento_id'];
                }

                // $itens['id'] = $equipamento_id;
                $json['total_count'] = count($data);
                $json['incomplete_results'] = FALSE;
                $json['items'] = $data;

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($json));
                return;
            }

        }

        if($equipamento_id > 0){
            $itens = $this->current_model->with_foreign()->get($equipamento_id);
            $itens['id'] = $equipamento_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));
            return;
        }

        $filter             = $this->input->get_post("q");
        $marca_id           = (!empty($marca_id)) ? $marca_id : $this->input->get_post("marca_id");
        $categoria_id       = (!empty($categoria_id)) ? $categoria_id : $this->input->get_post("categoria_id");
        $sub_categoria_id   = $this->input->get_post("sub_categoria_id");
        $limit              = 30;
        $page               = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;


        //Retorna tudo
        $data = $this->current_model;

        $data->limit($limit, $limit*($page-1));

        if (!empty($marca_id))
        {
            $data->_database->where("equipamento_marca_id = {$marca_id}", NULL, FALSE);
        }

        if (!empty($categoria_id))
        {
            $data->_database->where("equipamento_categoria_id = {$categoria_id}", NULL, FALSE);
        }

        if (!empty($sub_categoria_id))
        {
            $data->_database->where("equipamento_sub_categoria_id = {$sub_categoria_id}", NULL, FALSE);
        }

        if($filter)
        {
            $data->_database->where('(ean LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('descricao LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('tags LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data->with_foreign()->get_all();

        $total = $this->current_model;

        if (!empty($marca_id))
        {
            $total->_database->where("equipamento_marca_id = {$marca_id}", NULL, FALSE);
        }

        if (!empty($categoria_id))
        {
            $total->_database->where("equipamento_categoria_id = {$categoria_id}", NULL, FALSE);
        }

        if (!empty($sub_categoria_id))
        {
            $total->_database->where("equipamento_sub_categoria_id = {$sub_categoria_id}", NULL, FALSE);
        }

        if($filter)
        {
            $total->_database->where('(ean LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('descricao LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('tags LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $total = $total->with_foreign()->get_total();

        foreach ($data as $index => $item) {
            $data[$index]['id'] = $item['equipamento_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    public function index($offset = 0) { 
      	//Função padrão (load)
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Tipo de Campo");
        $this->template->set_breadcrumb("Tipo de Campo", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add() //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Tipo de Campo");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");

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
                redirect("$this->controller_uri/index");
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Tipo de Campo");
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
                redirect("$this->controller_uri/index");
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}

