<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

Class Integracao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao';
    protected $primary_key = 'integracao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    private $data_template_script = array();
    private $tipo_layout="";
    private $layout_separador=";";


    //Dados
    public $validate = array(
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo de Dados   ',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'integracao_comunicacao_id',
            'label' => 'Tipo de Comunicação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade_unidade',
            'label' => 'Unidade Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade',
            'label' => 'Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade_hora',
            'label' => 'Hora Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'script_sql',
            'label' => 'SQL',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_chave',
            'label' => 'Campo Chave (Registros)',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ambiente',
            'label' => 'Ambiente',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'host',
            'label' => 'Host',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'porta',
            'label' => 'Porta',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'usuario',
            'label' => 'usuario',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'senha',
            'label' => 'senha',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'diretorio',
            'label' => 'Diretório',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'Habilitado',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_execute',
            'label' => 'Antes de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_execute',
            'label' => 'Depois de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_detail',
            'label' => 'Antes de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_detail',
            'label' => 'Depois de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'sequencia',
            'label' => 'Numero de sequencia',
            'rules' => '',
            'groups' => 'default'
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->library('parser');

        $this->data_template_script = array(
            'data_ini_mes_anterior' => date('Y-m-d', mktime(0, 0, 0, date('m')-1, 1, date('Y'))),
            'data_fim_mes_anterior' => date('Y-m-t', mktime(0, 0, 0, date('m')-1, 1, date('Y'))),
            'data_ini_mes' => date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y'))),
            'data_fim_mes' => date('Y-m-t', mktime(0, 0, 0, date('m'), 1, date('Y'))),
            'totalRegistros' => 0,
            'totalItens' => 0,
        );
    }

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'parceiro_id' => $this->input->post('parceiro_id'),
            'tipo' => $this->input->post('tipo'),
            'integracao_comunicacao_id' => $this->input->post('integracao_comunicacao_id'),
            'periodicidade_unidade' => $this->input->post('periodicidade_unidade'),
            'periodicidade' => $this->input->post('periodicidade'),
            'periodicidade_hora' => $this->input->post('periodicidade_hora'),
            'proxima_execucao' => $this->get_proxima_execucao( $this->input->post('integracao_id')),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'descricao' => $this->input->post('descricao'),
            'script_sql' => $this->input->post('script_sql'),
            'parametros' => $this->input->post('parametros'),
            'campo_chave' => $this->input->post('campo_chave'),
            'ambiente' => $this->input->post('ambiente'),
            'host' => $this->input->post('host'),
            'porta' => $this->input->post('porta'),
            'usuario' => $this->input->post('usuario'),
            'senha' => $this->input->post('senha'),
            'diretorio' => $this->input->post('diretorio'),
            'habilitado' => $this->input->post('habilitado'),
            'before_execute' => $this->input->post('before_execute'),
            'after_execute' => $this->input->post('after_execute'),
            'before_detail' => $this->input->post('before_detail'),
            'after_detail' => $this->input->post('after_detail'),
        );
        return $data;
    }

    public function get_proxima_execucao($integracao_id = 0){
        if($integracao_id == 0){
            return null;

        }else{
            $integracao = $this->get($integracao_id);
            if($integracao){
                switch ($integracao['periodicidade_unidade']) {
                    case 'I' :
                        $date = date('Y-m-d H:i:s', mktime(date('H'), date('i') + $integracao['periodicidade'], 0, date('m'), date('d'), date('Y')));
                        break;
                    case 'H' :
                        $date = date('Y-m-d H:i:s', mktime(date('H') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d'), date('Y')));
                        break;
                    case 'D' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('H') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d') + $integracao['periodicidade'], date('Y')));
                        break;
                    case 'M' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('H') + $integracao['periodicidade'], date('i'), 0, date('m') + $integracao['periodicidade'], date('d'), date('Y')));
                        break;
                    case 'Y' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('H') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d'), date('Y') + $integracao['periodicidade']));
                        break;
                    case 'C' :
                        $date = date("Y-m-d h-i-s");
                        break;
                }

                return $date;

            }else{
                return '0000-00-00 00:00:00';
            }
        }
    }

    public function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function run(){

        $integracoes = $this->filter_by_rotina_pronta()->order_by('proxima_execucao')->limit(1)->get_all();

        foreach ($integracoes as $integracao) {

            if($integracao['tipo'] == 'S'){
                $this->run_s($integracao['integracao_id']);
            }elseif($integracao['tipo'] == 'R'){
                $this->run_r($integracao['integracao_id']);
            }elseif($integracao['tipo'] == 'E'){
                $this->run_r($integracao['integracao_id']);
            }

        }
    }

    public function run_r($integracao_id){
        echo "run_r($integracao_id)\n";
        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_log_detalhe_campo_model', 'integracao_log_detalhe_campo');
        $this->load->model('integracao_layout_model', 'integracao_layout');

        $this->_database->select('integracao.*');
        $this->_database->where("integracao.integracao_id", $integracao_id);
        $this->_database->where("integracao.status", 'A');
        $this->_database->where("integracao.deletado", 0);
        $this->_database->where("integracao.habilitado", 1);
        $this->_database->where("integracao.proxima_execucao <= ", date('Y-m-d H:i:s'));

    	try
    	{
            $result = $this->get_all();
    	}
    	catch (Exception $e) 
    	{
    		echo "e=" . $e;
    	}

        if($result){
            $result = $result[0];
            $dados_integracao = array();
            $dados_integracao['status'] = 'L';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            //execute before execute
            if((!empty($result['before_execute'])) && (function_exists($result['before_execute']))){
                call_user_func($result['before_execute'], null, array('item' => $result, 'registro' => array(), 'log' => array(), 'valor' => null));
            }

            $layout_filename = $this->integracao_layout->filter_by_integracao($result['integracao_id'])
                ->filter_by_tipo('F')
                ->order_by('ordem')
                ->get_all();

            $file = (isset($layout_filename[0]['valor_padrao'])) ? $layout_filename[0]['valor_padrao'] : '';

            if(empty($file))
            {
                $file = $this->getFileName($result, $layout_filename);
            }

            $result_file = $this->getFile($result, $file);

            $result_process = [];
            if(!empty($result_file['file']) && $result['tipo_layout']!='ZIP')
            {
                $result_process = $this->processFileIntegracao($result, $result_file['file']);
            }

            $dados_integracao = array();
            $dados_integracao['proxima_execucao'] = $this->get_proxima_execucao($result['integracao_id']);
            $dados_integracao['ultima_execucao'] = date('Y-m-d H:i:s');
            $dados_integracao['status'] = 'A';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            //execute after execute
            if((!empty($result['after_execute'])) && (function_exists($result['after_execute']))){
                call_user_func($result['after_execute'], null, array('item' => $result, 'registro' => $result_file, 'log' => $result_process, 'valor' => null));
            }

        }
    }

    public function run_s($integracao_id){
        echo "run_s($integracao_id)\n";
        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');

        $this->_database->select('integracao.*');
        $this->_database->where("integracao.integracao_id", $integracao_id);
        $this->_database->where("integracao.status", 'A');
        $this->_database->where("integracao.deletado", 0);
        $this->_database->where("integracao.habilitado", 1);
        $this->_database->where("integracao.proxima_execucao <= ", date('Y-m-d H:i:s'));
        $result = $this->get_all();

        if($result){
            $result = $result[0];
            $dados_integracao = array();
            $dados_integracao['status'] = 'L';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            //execute before execute
            if((!empty($result['before_execute'])) && (function_exists($result['before_execute']))){
                call_user_func($result['before_execute'], null, array('item' => $result, 'registro' => array(), 'log' => array(), 'valor' => null));
            }

            $result_file = $this->createFileIntegracao($result);

            $filename = $result_file['file'];
            $integracao_log_status_id = 5; // Falha

            // gerou arquivo ou não havia registros para enviar
            if (!empty($filename) || $result_file['qtde_reg'] == 0){
                $integracao_log_status_id = 3; // Sucesso
            }

            // se gerou conteúdo no arquivo
            if (!empty($filename) && ( $result_file['qtde_reg'] > 0 || !empty($integracao['envia_vazio']) ) ) {
                $this->sendFile($result, $filename);
            }

            $dados_log = array();
            $dados_log['processamento_fim'] = date('Y-m-d H:i:s');
            $dados_log['nome_arquivo'] = basename($filename);
            $dados_log['quantidade_registros'] = $result_file['qtde_reg'];
            $dados_log['integracao_log_status_id'] = $integracao_log_status_id;

            $this->integracao_log->update($result_file['integracao_log_id'], $dados_log, TRUE);
            unset($dados_log['quantidade_registros']);

            $this->integracao_log_detalhe->update_by(
                array('integracao_log_id' => $result_file['integracao_log_id']), array(
                    'integracao_log_status_id' => $integracao_log_status_id
                )
            );

            $dados_integracao = array();
            $dados_integracao['proxima_execucao'] = $this->get_proxima_execucao($result['integracao_id']); 
            $dados_integracao['ultima_execucao'] = date('Y-m-d H:i:s');
            $dados_integracao['status'] = 'A';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            //execute after execute
            if((!empty($result['after_execute'])) && (function_exists($result['after_execute']))){
                call_user_func($result['after_execute'], null, array('item' => $result, 'registro' => $result_file, 'log' => $dados_log, 'valor' => null));
            }
        }
    }

    private function sendFile($integracao = array(), $file){
        try{

            switch ($integracao['integracao_comunicacao_id']){

                case 1:
                    $this->sendFileFTP($integracao, $file);
                    break;

                case 2:

                    break;
                case 3:

                    break;
            }

        }catch (Exception $e) {

        }
    }

    private function getFile($integracao = array(), $file){
        try{

            switch ($integracao['integracao_comunicacao_id']){

                case 1:
                    return $this->getFileFTP($integracao, $file);
                    break;

                case 2:

                    break;
                case 3:

                    break;

                // Criado para correções pontuais
                case 100:
                    return $this->getFileCustom($integracao);
                    break;
            }

        }catch (Exception $e) {
		
	  	echo "getFile::Exception " . print_r($e, true) . "\n";

        }
    }

    private function getFileCustom($integracao = array()){

        $this->load->model('integracao_log_model', 'integracao_log');

        $result = array(
            'file' => '',
            'fileget' => '',
        );

        $file_processar = '';
        $integs = $this->integracao_log
            ->filter_ret_CTA_custom($integracao['integracao_id']);

        foreach ($integs as $int) {
            $total = $this->integracao_log
                ->filter_by_integracao($integracao['integracao_id'])
                ->filter_by_file($int['nome_arquivo'])
                ->get_total();

            if ((int)$total == 0) {
                $file_processar = $int['nome_arquivo'];
                break;
            }
        }

        if(!empty($file_processar)){
            $fileget = $file_processar;
            $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";
            if(file_exists("{$diretorio}/{$fileget}")){
                $result = array(
                    'file' => "{$diretorio}/{$fileget}",
                    'fileget' => $fileget,
                );
            }
        }

        return $result;
    }

    private function getFileFTP($integracao = array(), $file){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->library('ftp');

        $config['hostname'] = $integracao['host'];
        $config['username'] = $integracao['usuario'];
        $config['password'] = $integracao['senha'];
        $config['port'] = $integracao['porta'];
        $config['debug']    = TRUE;

        $this->ftp->connect($config);
        $list = $this->ftp->list_files("{$integracao['diretorio']}");

        $result = array(
            'file' => '',
            'fileget' => '',
        );

        $file_processar = '';
        if($list) {
            foreach ($list as $index => $item) {
                if ( strpos($item, ".") === FALSE )
                    continue;

                $total = $this->integracao_log
                    ->filter_by_integracao($integracao['integracao_id'])
                    ->filter_by_file(basename($item))
                    ->get_total();

                if ((int)$total == 0) {
                    $file_processar = $item;
                    break;
                }
            }
        }

        if(!empty($file_processar)){
            $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";
            if(!file_exists($diretorio)){
                mkdir($diretorio, 0777, true);
            }

            $fileget = basename($file_processar);
            if($this->ftp->download($file_processar, "{$diretorio}/{$fileget}", 'binary')){
                $result = array(
                    'file' => "{$diretorio}/{$fileget}",
                    'fileget' => $fileget,
                );
            }

        }
        $this->ftp->close();
        return $result;
    }

    private function sendFileFTP($integracao = array(), $file){

        $this->load->library('ftp');

        $config['hostname'] = $integracao['host'];
        $config['username'] = $integracao['usuario'];
        $config['password'] = $integracao['senha'];
        $config['port'] = $integracao['porta'];
        $config['debug']    = TRUE;
        $filename = basename($file);
        $this->ftp->connect($config);
        $this->ftp->upload($file, "{$integracao['diretorio']}{$filename}", 'binary', 0777);
        $this->ftp->close();
    }

    private function processLine($multiplo, $layout, $registro, $integracao_log, $integracao_log_detalhe_id = null, $integracao = null) {
        $this->data_template_script['totalRegistros']++;
        if (!empty($multiplo)) $this->data_template_script['totalItens']++;

        $line = $this->getLinha($layout, $registro, $integracao_log, $integracao_log_detalhe_id, $integracao);
        if (empty($line)){
            $this->data_template_script['totalRegistros']--;
            if (!empty($multiplo)) $this->data_template_script['totalItens']--;
        }

        return $line;
    }

    private function geraCampoChave($campo_chave, $registro) {
        $result = explode("|", $campo_chave);
        $under = $campo_chave = '';

        foreach ($result as $r) {
            if (isset($registro[$r])) {
                $campo_chave .= $under.$registro[$r];
                $under = '|';
            }
        }

        return $campo_chave;
    }

    private function processRegisters($linhas, $layout_m, $registros, $integracao_log, $integracao) {
        if (!empty($layout_m)){
            foreach ($registros as $registro) {
                $integracao_log_detalhe_id = $this->integracao_log_detalhe->insLogDetalhe($integracao_log['integracao_log_id'], $this->data_template_script['totalRegistros']+1, $this->geraCampoChave($integracao['campo_chave'], $registro), null, $this->geraCampoChave($integracao['parametros'], $registro));

                foreach ($layout_m as $lm) {

                    $inserir=true;
                    $this->data_template_script['pedido_id']            = issetor($registro['pedido_id'], 0);
                    $this->data_template_script['apolice_status_id']    = issetor($registro['apolice_status_id'], 0);
                    $this->data_template_script['apolice_id']           = issetor($registro['apolice_id'], 0);
                    $this->data_template_script['apolice_endosso_id']   = issetor($registro['apolice_endosso_id'], 0);
                    $this->data_template_script['num_sequencial']       = issetor($registro['num_sequencial'], 0);

                    // caso tenha que pegar o campo do detalhe
                    if (!empty($lm['sql'])) {
                        $lm['sql'] = $this->parser->parse_string($lm['sql'], $this->data_template_script, TRUE);
                        $query = $this->_database->query($lm['sql']);
                        $reg = $query->result_array();
                        $query->next_result();
                        $inserir=false;

                        if (!empty($reg)) {
                            foreach ($reg as $r) {
                                $registro = array_merge($registro, $r);
                                $line = $this->processLine($lm['multiplo'], $lm['dados'], $registro, $integracao_log, $integracao_log_detalhe_id, $integracao);
                                if (!empty($line)) $linhas[] = $line;
                            }
                        }
                    }

                    if ($inserir) {
                        $line = $this->processLine($lm['multiplo'], $lm['dados'], $registro, $integracao_log, $integracao_log_detalhe_id, $integracao);
                        if (!empty($line)) $linhas[] = $line;
                    }

                }

            }
        }

        return $linhas;
    }

    private function createFileIntegracao($integracao = array()){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_log_detalhe_campo_model', 'integracao_log_detalhe_campo');
        $this->load->model('integracao_layout_model', 'integracao_layout');
        $this->load->model('integracao_detalhe_model', 'integracao_det');

        $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";

        $this->data_template_script['integracao_id'] = $integracao['integracao_id'];
        $this->data_template_script['parceiro_id'] = $integracao['parceiro_id'];
    	$this->tipo_layout=$integracao['tipo_layout'];
    	$this->layout_separador=$integracao['layout_separador'];

        $integracao['script_sql'] = $this->parser->parse_string($integracao['script_sql'], $this->data_template_script, TRUE);
        $query = $this->_database->query($integracao['script_sql']);
        $registros = $query->result_array();
        $query->next_result();

        $integracao_log =  $this->integracao_log->insLog($integracao['integracao_id'], count($registros));
        $arRet = ['file' => '', 'integracao_log_id' => $integracao_log['integracao_log_id'], 'qtde_reg' => count($registros)];
        $filename = '';
        // Não envia vazio && não retornou nenhum dado para ser enviado
        // if ( empty($integracao['envia_vazio']) && empty($registros) ) {
        //     return $arRet;
        // }

        //busca layout
        $query = $this->_database->query("
            SELECT il.*, id.multiplo, id.script_sql
            FROM integracao_layout il 
            INNER JOIN integracao_detalhe id ON il.integracao_detalhe_id=id.integracao_detalhe_id
            WHERE il.integracao_id = {$integracao['integracao_id']} AND il.deletado = 0 AND id.deletado = 0
            ORDER BY id.ordem, il.ordem
        ");
        $layout_all = $query->result_array();

        // monta na estrutura hierarquica
        $tipoReg="";
        $layout = $layout_m = $linhas = $lH = [];
        $qtdeAux=-1;
        $rmQtdeLine=0;
        foreach ($layout_all as $key => $item) {
            if($tipoReg != $item['tipo']) {
                $qtdeAux++;
                $layout[$qtdeAux] = [
                    'tipo' => $item['tipo'], 
                    'multiplo' => $item['multiplo'], 
                    'sql' => $item['script_sql'], 
                    'dados' => [],
                ];
                $tipoReg = $item['tipo'];
            }

            $layout[$qtdeAux]['dados'][] = $item;
        }

        // Trata o nome do arquivo
        $idxF = app_search( $layout, 'F', 'tipo' );
        if ( $idxF >= 0 )
        {
            // valida se o texto será upper, low ou automatico
            $str_upper = null;
            if ( !empty($layout[$idxF]['dados']) && isset($layout[$idxF]['dados'][0]['str_upper']) )
            {
                $str_upper = $layout[$idxF]['dados'][0]['str_upper'];
            }

            $filename = $this->getLinha($layout[$idxF]['dados'], $registros, $integracao_log, null, null, $str_upper);
            $filename = $filename[0];
            unset($layout[$idxF]);
        }

        if ( empty($filename) ) {
            return $arRet;
        }

        // Trata o header
        $idxH = app_search( $layout, 'H', 'tipo' );
        if ( $idxH >= 0 ) {
            $this->data_template_script['totalRegistros']++;
            if (!empty($multiplo)) $this->data_template_script['totalItens']++;

            $rmQtdeLine++;

            $lH = $layout[$idxH];
            unset($layout[$idxH]);
        }

        $arRet['file'] = "{$diretorio}/{$filename}";
        $arRet['dados'] = $registros;

        //gera todas as linhas
        $i=0;
        foreach ($layout as $lay) {
            $i++;
            $unicoRegistro = false;

            if ( count($layout) == $i && empty($layout_m) )
            {
                $unicoRegistro = true;
                $layout_m[] = $lay;
            }

            if ($lay['multiplo'] == 0 || $unicoRegistro) {
                $linhas = $this->processRegisters($linhas, $layout_m, $registros, $integracao_log, $integracao);
                $layout_m = [];

                if ( !$unicoRegistro )
                {
                    $line = $this->processLine($lay['multiplo'], $lay['dados'], !empty($registros) ? $registros[0] : [], null );
                    if (!empty($line)) $linhas[] = $line;
                }

            } else {
                $layout_m[] = $lay;
            }
        }

        // Trata o header
        if ( $idxH >= 0 ) {
            $header = $this->getLinha($lH['dados'], !empty($registros) ? $registros[0] : [], $integracao_log);
            $linhas = array_merge([$header], $linhas);
        }

        $idxT = app_search( $layout, 'T', 'tipo' );
        if ( $idxT >= 0 ) {
            $rmQtdeLine++;
        }

        // Nao envia vazio && (nao gerou linhas ou as linhas geradas não são de detalhes)
        if ( empty($integracao['envia_vazio']) && (empty($linhas) || count($linhas) <= $rmQtdeLine) ) {
            return $arRet;
        }

        $linhas = $this->processRegisters($linhas, $layout_m, $registros, $integracao_log, $integracao);

        if(!file_exists($diretorio)){
            mkdir($diretorio, 0777, true);
        }

        $content=$concat="";
        foreach ($linhas as $row) {
            $content.=$concat.implode("\n", $row);
            $concat = "\r\n";
        }

        $arRet['qtde_reg'] = count($linhas)-$rmQtdeLine;
        $content = iconv( mb_detect_encoding( $content ), 'Windows-1252//TRANSLIT', $content );
        file_put_contents("{$diretorio}/{$filename}", $content);

        return $arRet;
    }

    private function processFileIntegracao($integracao = array(), $file){
        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_layout_model', 'integracao_layout');

        $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";

        //busca layout
        $layout_header = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('H')
            ->order_by('ordem')
            ->get_all();

        $layout_detail = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D')
            ->order_by('ordem')
            ->get_all();

        $layout_trailler = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('T')
            ->order_by('ordem')
            ->get_all();

        $fh = fopen($file, 'r');

        $integracao_log =  $this->integracao_log->insLog($integracao['integracao_id'], count(file($file)), basename($file));

        $header = array();
        $detail = array();
        $trailler = array();
        $num_registro = 0;

        if($integracao['tipo_layout'] == 'LAYOUT') 
        {
            while (!feof($fh)) #INICIO DO WHILE NO ARQUIVO
            {
                $linhas = str_replace("'"," ",fgets($fh, 4096));

                //header
                if(substr($linhas,($layout_header[0]['inicio'])-1,$layout_header[0]['tamanho']) == $layout_header[0]['valor_padrao']){
                    foreach ($layout_header as $idxh => $item_h) {
                        $header[] = array(
                            'layout' => $item_h,
                            'valor' => substr($linhas,($item_h['inicio'])-1,$item_h['tamanho']),
                            'linha' => $linhas,
                        );
                    }
                }elseif(substr($linhas,($layout_detail[0]['inicio'])-1,$layout_detail[0]['tamanho']) == $layout_detail[0]['valor_padrao']){
                    $sub_detail = array();
                    foreach ($layout_detail as $idxd => $item_d) {
                        $sub_detail[] = array(
                            'layout' => $item_d,
                            'valor' => substr($linhas,($item_d['inicio'])-1,$item_d['tamanho']),
                            'linha' => $linhas,
                        );
                    }

                    $detail[] = $sub_detail;
                    $num_registro++;
                }elseif(substr($linhas,($layout_trailler[0]['inicio'])-1,$layout_trailler[0]['tamanho']) == $layout_trailler[0]['valor_padrao']){
                    foreach ($layout_trailler as $idxt => $item_t) {
                        $trailler[] = array(
                            'layout' => $item_t,
                            'valor' => substr($linhas,($item_t['inicio'])-1,$item_t['tamanho']),
                            'linha' => $linhas,
                        );
                    }
                }

            }

        } 
        else if($integracao['tipo_layout'] == 'CSV') 
        {
            $ignore = TRUE;
            while (($data = fgetcsv($fh, 4096, $integracao['layout_separador'])) !== FALSE)
            {
                if ($ignore) {
                    $ignore = FALSE;
                    continue;
                }
                $sub_detail = array();
                $c = 0;
                $num = count($data);
                foreach ($layout_detail as $idxd => $item_d) {
                    $sub_detail[] = array(
                        'layout' => $item_d,
                        'valor' => $data[$c],
                        'linha' => $data,
                    );
                    $c++;
                }
                $detail[] = $sub_detail;
                $num_registro++;
            }
        }

        $this->data_template_script['integracao_id'] = $integracao['integracao_id'];
        $integracao['script_sql'] = $this->parser->parse_string($integracao['script_sql'], $this->data_template_script, TRUE);
        $sql = $integracao['script_sql'];

        $data = array();
        $id_log = 0;
        $num_linha = 0;
        foreach ($detail as  $rows) {

            // add o header em cada linha
            $rows = array_merge($rows, $header);
            $rows = array_merge($rows, $trailler);
            $data_row = $ids = array();

            foreach ($rows as $index => $row) {
                $row['valor_anterior'] = $row['valor'];

                if ($row['layout']['insert'] == 1) {
                    if(function_exists($row['layout']['function'])){
                        $row['valor'] = call_user_func($row['layout']['function'], $row['layout']['formato'], array('item' => array(), 'registro' => array(), 'log' => array(), 'valor' => $row['valor']));
                    }
                    $data_row[$row['layout']['nome_banco']] = trim($row['valor']);
                }
                if ($row['layout']['campo_log'] == 1) {
                    $id_log = trim($row['valor']);

                    if(function_exists($row['layout']['function'])){

                        $id_log = call_user_func($row['layout']['function'], $row['layout']['formato'], array('item' => array(), 'registro' => array(), 'log' => array(), 'valor' => $row['valor_anterior']));
                    }

                    if (!empty($id_log)) {
                        $ids[$row['layout']['nome_banco']] = $id_log;
                    }
                }
            }


            if (!empty($ids)) {

                if (count($ids) > 1) {

                    $proc = $this->detectFileRetorno(basename($file), $ids);

                    if (!empty($proc)) $id_log = $proc['chave'];
                } else {
                  foreach ($ids as $id_)
                    $id_log = $id_;
                }

                $data_row['id_log'] = $id_log;

                $_tipo_file = $this->detectFileRetorno(basename($file), $ids);

                $data_row['tipo_arquivo'] = (!empty($_tipo_file)) ? $_tipo_file['tipo'] : '';


            }

            $data[] = $data_row;
            $num_linha++;
        }


        $num_linha = 1;
        foreach ($data as $index => $datum) {

            // gera log
            $integracao_log_detalhe_id = null;
            $integracao_log_status_id = 4;
            $msgDetCampo = [];

            if (!empty($datum['id_log'])) {
                $integracao_log_detalhe_id = $this->integracao_log_detalhe->insLogDetalhe($integracao_log['integracao_log_id'], $num_linha, $datum['id_log'], addslashes(json_encode($datum)));
            }

            //execute before detail
            if (!empty($integracao['before_detail']) ) {
                if ( function_exists($integracao['before_detail']) ) {

                    // Tratando o erro 22 - Linha ja inserida na db_cta_stage_ods 
                    // Tratando o erro 110 - Registro duplicado no arquivo de origem
                    if(!empty($datum['cod_erro']) && in_array($datum['cod_erro'], [22, 110]) && ( $datum['tipo_arquivo'] == 'CLIENTE' || $datum['tipo_arquivo'] == 'EMSCMS' || $datum['tipo_arquivo'] == 'PARCEMS' ) ) 
                    {
                        $msgDetCampo[] = ['id' => 12, 'msg' => $datum['cod_erro'] ." - ". $datum['descricao_erro'], 'slug' => "erro_retorno"];
                    } else 
                    {
                        $callFuncReturn = call_user_func($integracao['before_detail'], $integracao_log_detalhe_id, array('item' => $detail, 'registro' => $datum, 'log' => $integracao_log, 'valor' => null));

                        if ( !empty($callFuncReturn) && !empty($integracao_log_detalhe_id) ){

                            if ( empty($callFuncReturn->status) ){
                                // seta para erro
                                $integracao_log_status_id = 5; 
                                $msgDetCampo = $callFuncReturn->msg; 
                            } elseif ( $callFuncReturn->status === 2 ) {
                                // seta para ignorado
                                $integracao_log_status_id = 7;
                                $msgDetCampo = $callFuncReturn->msg;
                            }

                        }
                    }

                    if (!empty($msgDetCampo)) {
                        foreach ($msgDetCampo as $er) {
                            $ErroID = !empty($er['id']) ? $er['id'] : -1;
                            $ErroMSG = !empty($er['msg']) ? $er['msg'] : $er;
                            $ErroSLUG = !empty($er['slug']) ? $er['slug'] : "";
                            $this->integracao_log_detalhe_campo->insLogDetalheCampo($integracao_log_detalhe_id, $ErroID, $ErroMSG, $ErroSLUG);
                        }
                    }

                }
            }

            $ultimo_id = null;
            if (!empty($sql)){
                $this->_database->query($sql, $datum);
                $ultimo_id = $this->_database->insert_id();
                $this->integracao_log_detalhe->insLogDetalhe($integracao_log['integracao_log_id'], $num_linha, $ultimo_id);
            }

            $num_linha++;

            //execute before detail
            if((!empty($integracao['after_detail'])) && (function_exists($integracao['after_detail']))){
                call_user_func($integracao['after_detail'], null, array('item' => $detail, 'registro' => $datum, 'log' => $integracao_log, 'valor' => $ultimo_id));
            }

            if (!empty($integracao_log_detalhe_id) ){
                // seta para erro
                $this->integracao_log_detalhe->update_by(
                    array('integracao_log_detalhe_id' =>$integracao_log_detalhe_id),array(
                        'integracao_log_status_id' => $integracao_log_status_id
                    )
                );
            }

        }

        $dados_log = array();
        $dados_log['processamento_fim'] = date('Y-m-d H:i:s');
        $dados_log['integracao_log_status_id'] = 4;

        $this->integracao_log->update($integracao_log['integracao_log_id'], $dados_log, TRUE);

        return $integracao_log;
    }

    private function getLinha($layout, $registro = array(), $log = array(), $integracao_log_detalhe_id = null, $integracao = null, $upCase = 1){

        $result = "";
        $arResult = [];
        $arCampoChave = [];
        $integracao_log_status_id = 4;
        $v = 0;

        foreach ($layout as $ind => $item) {

            $campo = null;
            $trim = true;
            $pre_result = '';
            $qnt_valor_padrao = $item['tamanho'];

            if(strlen($item['valor_padrao']) > 0 && $item['qnt_valor_padrao'] > 0){
                $campo = '';
                $qnt_valor_padrao = $item['qnt_valor_padrao'];
                $trim = false;

                if (!empty($item['nome_banco'])){
                    if(isset($registro[$item['nome_banco']])){
                        $campo = $registro[$item['nome_banco']];
                    }elseif(isset($log[$item['nome_banco']])){
                        $campo = $log[$item['nome_banco']];
                    }
                }
                elseif (!empty($item['valor_padrao'])){
                    $campo = $item['valor_padrao'];
                }

            }elseif (!empty($item['function'])){

                if(function_exists($item['function'])){
                    $campo = call_user_func($item['function'], $item['formato'], array('item' => $item, 'registro' => $registro, 'log' => $log, 'global' => $this->data_template_script));
                }

            }elseif (!empty($item['nome_banco'])){

                if(isset($registro[$item['nome_banco']])){
                    $campo = $registro[$item['nome_banco']];
                }elseif(isset($log[$item['nome_banco']])){
                    $campo = $log[$item['nome_banco']];
                }else{
                    $campo = '';
                }

            }

            // Valida a chave da criação do log
            if ( !empty($integracao) ) {
                $key_field = explode("|", $integracao['campo_chave']);
                if ( in_array($item['nome_banco'], $key_field) ) {
                    $arCampoChave[$item['nome_banco']] = $campo;
                }
            }

            // Se for obrigatório precisa validar e retornar erro para gerar log de retorno
            if ($item['obrigatorio'] == 1 && !empty($item['nome_banco']) && empty($campo)){
                if ( !isset($registro[$item['nome_banco']]) || strlen(trim($registro[$item['nome_banco']])) == 0
                    || ( $item['campo_tipo']=='M' && !($registro[$item['nome_banco']] > 0) ) 
                    // || ( $item['campo_tipo']=='D' && !($registro[$item['nome_banco']] > 0) ) 
                ) {
                    // seta para erro
                    $integracao_log_status_id = 8;

                    // gera log do erro
                    $this->integracao_log_detalhe_campo->insLogDetalheCampo($integracao_log_detalhe_id, 1, "O campo {$item['nome']} é obrigatório", $item['nome_banco']);

                    // não gera a linha
                    continue;
                }
            }

            if (!is_null($campo))
            {
        		if($this->tipo_layout=="CSV")
        		{
                    $pre_result = trataRetorno($campo, $upCase, $trim);
        		}
        		else
        		{
                    $pre_result .= mb_str_pad(trataRetorno($campo, $upCase, $trim), $qnt_valor_padrao, isvazio($item['valor_padrao'],' '), $item['str_pad']);
        		}
            }

    	    $sep=$this->layout_separador;
    	    if($this->tipo_layout=="CSV")
    	    {
        		if($ind==count($layout)-1)
        		{
        			$sep="";
        		}
                $result .= $pre_result . $sep;
    	    }
    	    else
    	    {
                $result .= mb_substr($pre_result,0,$item['tamanho']);
    	    }
        }

        // Valida a chave da criação do log
        /*
        if ( !empty($arCampoChave) )
        {
            // verifica se mantém a mesma quantidade de campos para não perder alguma no meio do caminho
            // dessa forma, assume a inicial informada antes do processamento linha a linha
            $key_field = explode("|", $integracao['campo_chave']);
            if (count($key_field) == count($arCampoChave))
            {
                $this->integracao_log_detalhe->update_by(
                    array('integracao_log_detalhe_id' => $integracao_log_detalhe_id),array(
                        'chave' => $this->geraCampoChave($integracao['campo_chave'], $arCampoChave)
                    )
                );
            }
        }
        */

        if ($integracao_log_status_id != 4){
            $this->integracao_log_detalhe->update_by(
                array('integracao_log_detalhe_id' =>$integracao_log_detalhe_id),array(
                    'integracao_log_status_id' => $integracao_log_status_id
                )
            );
        } else {
            $arResult[] = $result;

            //execute before detail
            if((!empty($integracao['after_detail'])) && (function_exists($integracao['after_detail']))){
                call_user_func($integracao['after_detail'], $integracao_log_detalhe_id, array('item' => $layout, 'registro' => $registro, 'log' => $log));
            }
        }

        return $arResult;
    }

    function filter_by_rotina_pronta(){
        $this->_database->where('integracao.periodicidade_unidade <>', 'C');
        $this->_database->where('integracao.habilitado', 1);
        $this->_database->where('integracao.status', 'A');
        $this->_database->where('integracao.proxima_execucao > ', date('Y-m-d H:i:s'));

        return $this;
    }

    function max_seq_by_parceiro_id($parceiro_id){
        $sequencia = 0;

        $this->_database->select_max('integracao.sequencia', 'seq_max');
        $this->_database->where('integracao.parceiro_id', $parceiro_id);
        $this->_database->where('integracao.habilitado', 1);
        $this->_database->where('integracao.deletado', 0);
        $result = $this->get_all();

        if (!empty($result)) {
            $sequencia = $result[0]['seq_max'];
        }

        return $sequencia;
    }

    function update_log_sucess($file, $sinistro = false){

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS QUE AINDA NAO FORAM LIBERADOS
        $sql = "
            UPDATE integracao_log a
            INNER JOIN integracao_log_detalhe b ON a.integracao_log_id = b.integracao_log_id 
            SET b.integracao_log_status_id = 4, b.alteracao = NOW()
            WHERE a.nome_arquivo LIKE '{$file}%'
            AND a.integracao_log_status_id = 3 
            AND a.deletado = 0
            AND b.integracao_log_status_id NOT IN(4,5)
        ";
        $query = $this->_database->query($sql);

        $sql = "
            UPDATE integracao_log il
            SET il.integracao_log_status_id = IF((SELECT 1 FROM integracao_log_detalhe ild WHERE ild.integracao_log_id = il.integracao_log_id AND ild.integracao_log_status_id = 5 LIMIT 1) = 1, 5, 4)
            WHERE il.nome_arquivo LIKE '{$file}%'
                AND il.integracao_log_status_id = 3 
                AND il.deletado = 0
        ";
        $query = $this->_database->query($sql);

        if ($sinistro) {
            $sql = "
                UPDATE integracao_log a
                INNER JOIN integracao_log_detalhe b ON a.integracao_log_id = b.integracao_log_id 
                INNER JOIN sissolucoes1.sis_exp_complemento ec ON ec.id_sinistro_generali = LEFT(b.chave, LOCATE('|', b.chave)-1)
                INNER JOIN sissolucoes1.sis_exp_hist_carga ehc ON ec.id_exp = ehc.id_exp AND ehc.id_controle_arquivo_registros = b.integracao_log_detalhe_id
                LEFT JOIN sissolucoes1.sis_exp_hist_carga ehcx ON ec.id_exp = ehcx.id_exp AND ehcx.tipo_expediente = ehc.tipo_expediente AND ehcx.status = 'C'
                SET ehc.data_retorno = NOW(), ehc.`status` = 'C'
                WHERE a.nome_arquivo LIKE '{$file}%'
                AND a.deletado = 0
                AND b.integracao_log_status_id = 4
                AND ehc.`status` = 'P'
                AND IF(ehc.tipo_expediente = 'AJU', 1, ehcx.id_exp IS NULL)
            ";
            $query = $this->_database->query($sql);

            $sql = "
                UPDATE integracao_log a
                INNER JOIN integracao_log_detalhe b ON a.integracao_log_id = b.integracao_log_id 
                INNER JOIN sissolucoes1.sis_exp_complemento ec ON ec.id_sinistro_generali = LEFT(b.chave, LOCATE('|', b.chave)-1)
                INNER JOIN sissolucoes1.sis_exp_hist_carga ehc ON ec.id_exp = ehc.id_exp AND ehc.id_controle_arquivo_registros = b.integracao_log_detalhe_id
                INNER JOIN sissolucoes1.sis_exp_sinistro es ON es.id_exp = ec.id_exp
                INNER JOIN sissolucoes1.sis_exp e ON ec.id_exp = e.id_exp
                SET e.id_sinistro = ec.id_sinistro_generali, e.data_id_sinistro = NOW(), es.usado = 'S'
                WHERE a.nome_arquivo LIKE '{$file}%'
                AND a.deletado = 0
                AND b.integracao_log_status_id = 4
            ";
            $query = $this->_database->query($sql);

        }

        return true;
    }

    function update_log_fail($file, $chave, $sinistro = false){

        if ($sinistro) {
            $sql = "
                UPDATE integracao_log a
                INNER JOIN integracao_log_detalhe b ON a.integracao_log_id = b.integracao_log_id 
                INNER JOIN sissolucoes1.sis_exp_complemento ec ON ec.id_sinistro_generali = LEFT(b.chave, LOCATE('|', b.chave)-1)
                INNER JOIN sissolucoes1.sis_exp_hist_carga ehc ON ec.id_exp = ehc.id_exp AND ehc.id_controle_arquivo_registros = b.integracao_log_detalhe_id
                LEFT JOIN sissolucoes1.sis_exp_hist_carga ehcx ON ec.id_exp = ehcx.id_exp AND ehcx.tipo_expediente = ehc.tipo_expediente AND ehcx.status = 'C'
                SET ehc.data_retorno = NOW(), ehc.`status` = 'F'
                WHERE a.nome_arquivo LIKE '{$file}%'
                AND a.deletado = 0
                AND ehc.`status` = 'P'
                AND IF(ehc.tipo_expediente = 'AJU', 1, ehcx.id_exp IS NULL)
                AND b.chave LIKE '{$chave}%'
            ";
            $query = $this->_database->query($sql);
        }

        // marca o registro como erro (5) para que possa ser corrigido manualmente (6) e depois feito um novo envio (3)
        $sql = "
            UPDATE integracao_log il
            INNER JOIN integracao_log_detalhe ild ON ild.integracao_log_id = il.integracao_log_id 
            SET ild.integracao_log_status_id = 5, ild.alteracao = NOW()
            WHERE il.nome_arquivo LIKE '{$file}%'
            AND il.deletado = 0
            AND il.integracao_log_status_id = 3
            AND ild.integracao_log_status_id NOT IN(4,5)
            AND ild.chave = '{$chave}'
        ";
        $query = $this->_database->query($sql);

        return true;
    }

    public function detectFileRetorno($file, $dados = []) {
        $file = str_replace("-RT-", "-EV-", $file);
        $result_file = explode("-", $file);
        if (count($result_file) < 3)
            return null;

        $file = $result_file[0]."-".$result_file[1]."-".$result_file[2]."-";

        $tipo_file = explode(".", $result_file[0]);
        if (count($tipo_file) < 3)
            return null;

        $tipo_file = $tipo_file[2];
        $chave = '';

        if (!empty($dados)) {
            switch ($tipo_file) {
                case 'CLIENTE':
                    $chave = !empty($dados['cod_cliente']) ? (int)$dados['cod_cliente'] : '';
                    break;
                case 'PARCEMS':
                case 'EMSCMS':
                case 'LCTCMS':
                case 'COBRANCA':
                    $chave = !empty($dados['num_apolice']) ? trim($dados['num_apolice']) ."|". (int)$dados['num_sequencial'] : '';
                    break;
                case 'SINISTRO':
                    $chave = !empty($dados['cod_sinistro']) ? (int)$dados['cod_sinistro'] ."|". (int)$dados['cod_movimento'] : '';
                    break;
            }
        }

        return ['chave' => $chave, 'file' => $file, 'tipo' => $tipo_file];
    }

    function app_integracao_apolice_revert($num_apolice_custom, $cod_tpa)
    {
        $sql = "
            SELECT a.num_apolice
            FROM apolice a
            JOIN produto_parceiro_plano ppp ON a.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
            JOIN produto_parceiro pp ON ppp.produto_parceiro_id = pp.produto_parceiro_id
            WHERE
                a.num_apolice LIKE '%{$num_apolice_custom}'
                AND pp.cod_tpa = '{$cod_tpa}'
                AND a.deletado = 0;
        ";
        $query = $this->_database->query($sql);

        return ($query->row()) ? $query->result()[0]->num_apolice : FALSE; 
    }

    private function getFileName($integracao = array(), $layout = array())
    {
	switch($integracao['tipo_layout'])
	{
		case 'ZIP':
		case 'zip':
			$formato	=$layout[0]['formato'];
			$function	=$layout[0]['function'];
			if(function_exists($function))
			{
			    $ret = call_user_func($function, $formato, array('item' => $integracao, 'registro' => '', 'log' => '', 'global' => $this->data_template_script));
			}
			return $ret;
		break;
		default:
			return ''; // para novos desenvolvimentos
		break;
	}
    }

    function update_status_novomundo($id_exp, $status)
    {
	    $this->load->library("SoapCurl");
	    $SoapCurl = new SoapCurl();
	    $retorno = false;

	    try
	    {
		    switch($status)
		    {
			    case "CANCELADO":
				    $retorno = $SoapCurl->getAPI("atendimento/EncerrarExpediente", "PUT", json_encode( [ "idMotivoEncerramento" => 6, "idExpediente" => $id_exp ] ), 900);
		    		    //echo "($id_exp, $status)::" .  print_r($retorno, true) . "\n" ;
				    return $retorno;
			    break;
			    case "UTILIZADO":
				    $retorno = $SoapCurl->getAPI("atendimento/ConverteExpediente", "PUT", json_encode( [ "idMotivoConversao" => 4, "idExpediente" => $id_exp ] ), 900);
		    		    //echo "($id_exp, $status)::" .  print_r($retorno, true) . "\n" ;
				    return $retorno;
			    break;
			    default:
				    return $retorno;
			    break;
		    }
	    }
	    catch (Exception $e) 
	    {
		    echo "e=" . $e->getMessage();
		    return $retorno;
	    }
    }

}
