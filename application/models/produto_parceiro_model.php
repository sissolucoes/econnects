<?php
Class Produto_Parceiro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro';
    protected $primary_key = 'produto_parceiro_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_susep',
            'label' => 'Código SUSEP',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seguradora_id',
            'label' => 'Seguradora',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'venda_agrupada',
            'label' => 'Venda Agrupada',
            'rules' => 'required',
            'groups' => 'default'
        ),
         array( 
            'field' => 'slug_produto', 
            'label' => 'Slug', 
            'rules' => 'required', 
            'groups' => 'default' 
        ), 
        array(
            'field' => 'cod_tpa',
            'label' => 'Código TPA',
            'groups' => 'default'
        ),
        array(
            'field' => 'cod_sucursal',
            'label' => 'Código Sucursal',
            'groups' => 'default'
        ),
        array(
            'field' => 'cod_ramo',
            'label' => 'Código Ramo',
            'groups' => 'default'
        ),
        array(
            'field' => 'lista_id',
            'label' => 'Tabela de Equipamentos',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome'              => $this->input->post('nome'),
            'codigo_susep'      => $this->input->post('codigo_susep'),
            'produto_id'        => $this->input->post('produto_id'),
            'parceiro_id'       => $this->input->post('parceiro_id'),
            'seguradora_id'     => $this->input->post('seguradora_id'),
            'venda_agrupada'    => $this->input->post('venda_agrupada'),
            'slug_produto'      => $this->input->post('slug_produto'),
            'cod_tpa'           => $this->input->post('cod_tpa'),
            'cod_sucursal'      => isempty($this->input->post('cod_sucursal'), null),
            'cod_ramo'          => isempty($this->input->post('cod_ramo'), null),
            'lista_id'          => $this->input->post('lista_id'),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_implantacao_staus(){
        $this->_database->select("implantacao_status.nome as implantacao_status_nome, implantacao_status.implantacao_status_id, ppi.criacao as data_implantacao_status");
        $this->_database->join("implantacao_status", "implantacao_status.implantacao_status_id = {$this->_table}.implantacao_status_id", "left");
        $this->_database->join("produto_parceiro_implantacao ppi", "ppi.produto_parceiro_implantacao_id = (SELECT MAX(produto_parceiro_implantacao_id) FROM produto_parceiro_implantacao WHERE produto_parceiro_id = {$this->_table}.produto_parceiro_id)", "left", FALSE);
        return $this;
    }

    function filter_produto_texto($produto){
        $this->_database->like('produto_parceiro.nome', $produto);
        return $this;
    }

    function filter_implantacao_status_id($implantacao_status_id){
        $this->_database->where('produto_parceiro.implantacao_status_id', $implantacao_status_id);
        return $this;
    }

    function with_produto(){
        $this->with_simple_relation('produto', 'produto_', 'produto_id', array('nome', 'produto_ramo_id', 'slug'));
        return $this;
    }

    function with_produto_parceiro_configuracao(){
        $this->with_simple_relation('produto_parceiro_configuracao', 'produto_parceiro_configuracao_', 'produto_parceiro_id', array('pagamento_tipo', 'pagamento_periodicidade_unidade', 'pagamento_periodicidade', 'pagmaneto_cobranca', 'pagmaneto_cobranca_dia', 'pagamento_teimosinha'));
        return $this;
    }

    function with_parceiro(){
        $this->with_simple_relation('parceiro', 'parceiro_', 'parceiro_id', array('nome'));
        return $this;
    }

    function with_produto_parceiro_plano(){
        $this->with_simple_relation('produto_parceiro_plano', 'produto_parceiro_plano_', 'produto_parceiro_plano_id', array('nome', 'slug_plano', 'codigo_operadora'));
        return $this;
    }

    function getDadosToBilhete( $produto_parceiro_plano_id ){
        $this->_database->select("{$this->_table}.cod_tpa, {$this->_table}.cod_ramo, IFNULL({$this->_table}.cod_sucursal, parceiro.codigo_sucursal) AS cod_sucursal", FALSE);
        $this->_database->select('produto_parceiro_plano.codigo_operadora AS cod_produto');
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
        $this->_database->join("parceiro", "parceiro.parceiro_id = produto_parceiro.parceiro_id");
        $this->_database->where("produto_parceiro_plano.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        // remove o * da sql
        return $this->get_all(0,0,true,false);
    }

    function get_produtos_venda_admin( $parceiro_id = null, $produto_id = null, $produto_parceiro_id = null ){

        $this->_database->select($this->_table.'.produto_parceiro_id');
        $this->_database->select($this->_table.'.parceiro_id');
        $this->_database->select($this->_table.'.produto_id');
        $this->_database->select($this->_table.'.nome as nome_prod_parc');
        $this->_database->select('produto.slug, produto.nome');
        $this->_database->select('parceiro.nome as parceiro_nome');
        $this->_database->select('parceiro.nome_fantasia as parceiro_nome_fantasia');
        $this->_database->select('produto_parceiro_configuracao.venda_carrinho_compras, produto_parceiro_configuracao.venda_multiplo_cartao, '.$this->_table.'.slug_produto');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table.'.deletado', 0);
        if( !is_null( $parceiro_id ) ) {
            $this->_database->where($this->_table.'.parceiro_id', $parceiro_id );
        }
        if( !is_null( $produto_parceiro_id ) ) {
            $this->_database->where($this->_table.'.produto_parceiro_id', $produto_parceiro_id );
        }
        if( !is_null( $produto_id ) ) {
            $this->_database->where($this->_table.'.produto_id', $produto_id );
        }
        $this->_database->where('produto_parceiro_configuracao.deletado', 0);
        $this->_database->where('produto_parceiro_configuracao.venda_habilitada_admin', 1);
        $this->_database->where('produto.deletado', 0);
        $this->_database->join('produto', 'produto.produto_id = '.$this->_table.'.produto_id', 'inner');
        $this->_database->join('produto_parceiro_configuracao', $this->_table. '.produto_parceiro_id = produto_parceiro_configuracao.produto_parceiro_id', 'inner');
        $this->_database->join('parceiro', $this->_table. '.parceiro_id = parceiro.parceiro_id', 'inner');
        $this->_database->order_by('produto.nome', 'ASC');
        $this->_database->order_by($this->_table.'.nome', 'ASC');

        $query = $this->_database->get();

        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }

    function get_produtos_venda_admin_parceiros($parceiro_id, $slug_produto = null, $produto_parceiro_id = null)
    {
        if( !is_null( $slug_produto ) ) {
            $this->_database->where($this->_table.'.slug_produto', $slug_produto);
        }

        if( !empty( $produto_parceiro_id ) ) {
            $this->_database->where($this->_table.'.produto_parceiro_id', $produto_parceiro_id);
        }

        $this->_database->select($this->_table.'.produto_parceiro_id, '.$this->_table.'.parceiro_id, '.$this->_table.'.produto_id, '.$this->_table.'.nome as nome_prod_parc, pr.slug, pr.nome, p.nome as parceiro_nome, p.nome_fantasia as parceiro_nome_fantasia, ppc.venda_carrinho_compras,  ppc.venda_multiplo_cartao, '.$this->_table.'.slug_produto');

        $this->_database->join('parceiro p', 'p.parceiro_id = '.$this->_table.'.parceiro_id ', 'inner');
        $this->_database->join('parceiro_relacionamento_produto prp', 'prp.produto_parceiro_id = '.$this->_table.'.produto_parceiro_id', 'inner');
        $this->_database->join('produto pr', 'pr.produto_id = '.$this->_table.'.produto_id', 'inner');
        $this->_database->join('produto_parceiro_configuracao ppc', 'ppc.produto_parceiro_id = '.$this->_table.'.produto_parceiro_id', 'inner');

        $this->_database->where('prp.deletado', 0);
        $this->_database->where('pr.deletado', 0);
        $this->_database->where('ppc.deletado', 0);
        $this->_database->where('ppc.venda_habilitada_admin', 1);
        $this->_database->where('prp.parceiro_id', $parceiro_id);
        $this->_database->order_by('pr.nome', 'ASC');
        $this->_database->order_by($this->_table.'.nome', 'ASC');
        return $this->get_all(0,0,false,false);
    }

    public function getProdutosByParceiro($parceiro_id, $produto_id = null, $onlyEnable = true, $slug_produto = null)
    {
        $produtos = $this->get_produtos_venda_admin( $parceiro_id, $produto_id );
        $relacionamento = $this->get_produtos_venda_admin_parceiros( $parceiro_id, null, $produto_id );

        if (!empty($produtos) || !empty($relacionamento)) {

        	$result = array_merge_recursive_distinct('produto_parceiro_id', $produtos, $relacionamento);

            // compara os produtos com os que tem acesso
            if ($onlyEnable) {
                // $result = array_intersect_key($result, $this->getProdutosHabilitados($parceiro_id));

                $produtosHabilitados = $this->getProdutosHabilitados($parceiro_id, null, $slug_produto);
                $ret = [];
                foreach ($result as $rs) {
                    foreach ($produtosHabilitados as $prod) {
                        if ( $rs['produto_parceiro_id'] == $prod['produto_parceiro_id'] ) {
                            $ret[] = $rs;
                        }
                    }
                }

                $result = [];
                if (!empty($ret)) {
                    $result = $ret;
                }
            }

            return $result;
        } else {
            return null;
        }
    }
    public function getProdutosHabilitados($parceiro_id = null, $produto_parceiro_id = null, $slug_produto = null)
    {
        $where = '';
        if ( !empty($parceiro_id) )
        {
            if (is_array($parceiro_id))
            {
                $implode = implode($parceiro_id, ",");
                $where .= " AND h.parceiro_id IN({$implode}) ";
            } else {
                $where .= " AND h.parceiro_id = $parceiro_id ";
            }
        }

        if ( !empty($produto_parceiro_id) )
        {
            $where .= " AND h.produto_parceiro_id = $produto_parceiro_id ";
        }

        if ( !empty($slug_produto) )
        {
            $where .= " AND produto_parceiro.slug_produto = '{$slug_produto}' ";
        }

        // print_r("
        //     SELECT h.parceiro_id, h.produto_parceiro_id, parceiro.nome FROM (
        //         SELECT parceiro_id, produto_parceiro_id FROM parceiro_produto where deletado = 0 
        //         UNION
        //         SELECT DISTINCT parceiro_plano.parceiro_id, produto_parceiro_plano.produto_parceiro_id 
        //         FROM produto_parceiro_plano 
        //         INNER JOIN parceiro_plano ON produto_parceiro_plano.produto_parceiro_plano_id = parceiro_plano.produto_parceiro_plano_id 
        //         WHERE parceiro_plano.deletado = 0 AND produto_parceiro_plano.deletado = 0 
        //     ) AS h
        //     INNER JOIN produto_parceiro ON h.produto_parceiro_id = produto_parceiro.produto_parceiro_id
        //     INNER JOIN parceiro ON h.parceiro_id = parceiro.parceiro_id
        //     WHERE produto_parceiro.deletado = 0 
        //         {$where}
        //     ");die();
        $query = $this->db->query( "
            SELECT h.parceiro_id, h.produto_parceiro_id, parceiro.nome_fantasia AS nome FROM (
                SELECT parceiro_id, produto_parceiro_id FROM parceiro_produto where deletado = 0 
                UNION
                SELECT DISTINCT parceiro_plano.parceiro_id, produto_parceiro_plano.produto_parceiro_id 
                FROM produto_parceiro_plano 
                INNER JOIN parceiro_plano ON produto_parceiro_plano.produto_parceiro_plano_id = parceiro_plano.produto_parceiro_plano_id 
                WHERE parceiro_plano.deletado = 0 AND produto_parceiro_plano.deletado = 0 
            ) AS h
            INNER JOIN produto_parceiro ON h.produto_parceiro_id = produto_parceiro.produto_parceiro_id
            INNER JOIN parceiro ON h.parceiro_id = parceiro.parceiro_id
            WHERE produto_parceiro.deletado = 0 AND parceiro.deletado = 0
                {$where}
            "
        );

        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }

    public function getPlanosHabilitados($parceiro_id = null, $produto_parceiro_id = null, $produto_parceiro_plano_id = null)
    {
        $where = '';
        if ( !empty($parceiro_id) )
        {
            $where .= " AND h.parceiro_id = $parceiro_id ";
        }

        if ( !empty($produto_parceiro_id) )
        {
            $where .= " AND produto_parceiro_plano.produto_parceiro_id = $produto_parceiro_id ";
        }

        if ( !empty($produto_parceiro_id) )
        {
            $where .= " AND h.produto_parceiro_plano_id = $produto_parceiro_plano_id ";
        }

        $query = $this->db->query( "
            SELECT h.parceiro_id, h.produto_parceiro_plano_id, parceiro.nome_fantasia FROM (
                SELECT parceiro_id, produto_parceiro_plano_id FROM parceiro_plano where deletado = 0
                UNION
                SELECT parceiro_produto.parceiro_id, produto_parceiro_plano.produto_parceiro_plano_id
                FROM parceiro_produto 
                INNER JOIN produto_parceiro_plano ON produto_parceiro_plano.produto_parceiro_id = parceiro_produto.produto_parceiro_id 
                WHERE parceiro_produto.deletado = 0 AND produto_parceiro_plano.deletado = 0
            ) AS h
            INNER JOIN produto_parceiro_plano ON h.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id
            INNER JOIN parceiro ON h.parceiro_id = parceiro.parceiro_id
            WHERE produto_parceiro_plano.deletado = 0 AND parceiro.deletado = 0
                {$where}
            "
        );

        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }

    function filter_by_parceiro($parceiro_id){
        $this->_database->where('parceiro_id', $parceiro_id);
        return $this;
    }

    function filter_by_produto_parceiro($produto_parceiro_id){
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        return $this;
    }

    function filter_by_produto_parceiro_plano($produto_parceiro_plano_id){
        $this->_database->where('produto_parceiro_plano.produto_parceiro_plano_id', $produto_parceiro_plano_id);
        return $this;
    }

    function filter_by_slug($slug){
        $this->_database->where('slug_produto', $slug);
        return $this;
    }

    public function get_all($limit = 0, $offset = 0, $processa = true, $viewAll = true) {
        if($processa) {
            $parceiro_id = $this->session->userdata('parceiro_id');
            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_all($limit, $offset, $viewAll);
    }

    public function get_total($processa = true) {
        if($processa) {
            //Efetua join com cotação
            //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");
            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_total(); // TODO: Change the autogenerated stub
    }

}
