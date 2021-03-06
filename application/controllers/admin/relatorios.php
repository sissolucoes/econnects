<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Relatorios
 */
class Relatorios extends Admin_Controller
{
    /**
     * Relatório de vendas
     */
    public function index()
    {

        //Carrega React e Orb (relatórios)
        $this->loadLibraries();
        $this->template->js(app_assets_url("modulos/relatorios/vendas/venda.js", "admin"));

        //Dados para template
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }


    public function mapa_repasse_dinamico()
    {

        //Carrega React e Orb (relatórios)
        $this->loadLibraries();
        $this->template->js(app_assets_url("modulos/relatorios/vendas/mapa_repasse.js", "admin"));

        //Dados para template
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }

    public function processamento_venda()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(2);
        $data['src'] = $this->controller_uri;
        $data['layout'] = 'processamento_venda';
        $data['title'] = 'Relatório de Processamento de Vendas';
        $data['columns'] = [
            'DATA PROCESSAMENTO',
            'ARQUIVO',
            'STATUS',
            'STATUS PROCESSAMENTO',
            'RESULTADO DO PROCESSAMENTO (CRITICA)',
            'DETALHE DO PROCESSAMENTO',
            'CODIGO DA TRANSAÇÃO',
            'MOVIMENTO',
            'DATA DO MOVIMENTO',
            'APOLICE',
            'VIGENCIA (mes)',
            'NOME',
            'CPF',
            'SEXO',
            'ENDEREÇO',
            'TELEFONE',
            'COD LOJA',
            'COD VENDEDOR',
            'COD PRODUTO SAP',
            'EAN',
            'MARCA',
            'EQUIPAMENTO',
            'VALOR NF',
            'DATA NF',
            'NRO NF',
            'PREMIO BRUTO',
            'VALOR CALCULADO',
            'PREMIO LIQUIDO',
            'FORMA DE PAGAMENTO',
            'NRO PARCELA',
        ];

        if ($_POST) {
            $result = $this->getRelatorioProcVenda(FALSE);

            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $VALOR_NF = !empty($row['VALOR_NF']) ? app_format_currency($row['VALOR_NF'], true) : '';
                    $row['RESULTADO_PROCESSAMENTO'] = $this->trataHTML($row['RESULTADO_PROCESSAMENTO']);
                    $row['DETALHE_PROCESSAMENTO'] = $this->trataHTML($row['DETALHE_PROCESSAMENTO']);
                    $rows[] = [
                        app_date_mysql_to_mask($row['DATA_PROCESSAMENTO'], 'd/m/Y'),
                        $row['ARQUIVO'],
                        $row['STATUS'],
                        $row['STATUS_PROCESSAMENTO'],
                        $row['RESULTADO_PROCESSAMENTO'],
                        $row['DETALHE_PROCESSAMENTO'],
                        $row['CODIGO_TRANSACAO'],
                        $row['MOVIMENTO'],
                        $row['DATA_MOVIMENTO'],
                        "'".$row['APOLICE'],
                        $row['VIGENCIA'],
                        $row['NOME'],
                        $row['CPF'],
                        $row['SEXO'],
                        $row['ENDERECO'],
                        $row['TELEFONE'],
                        $row['COD_LOJA'],
                        $row['COD_VENDEDOR'],
                        $row['COD_PRODUTO_SAP'],
                        $row['EAN'],
                        $row['MARCA'],
                        $row['EQUIPAMENTO'],
                        $VALOR_NF,
                        app_date_mysql_to_mask($row['DATA_NF'], 'd/m/Y'),
                        $row['NRO_NF'],
                        app_format_currency($row['PREMIO_BRUTO'], true),
                        app_format_currency($row['VALOR_CALCULADO'], true),
                        app_format_currency($row['PREMIO_LIQUIDO'], true),
                        $row['FORMA_PAGAMENTO'],
                        $row['NRO_PARCELA'],
                    ];
                }
                //echo "<pre>";
                //print_r($rows);

                //$this->exportExcel($data['columns'], $rows, 'CSV');
                $this->exportCSV($data['columns'], $rows, 'Relatório de Processamento de Vendas');
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
            $data['representante'] = $this->input->get_post('representante');
        }

        $data['combo'] = $this->getParceiro('vendas-canc');

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/{$data['action']}", $data);
    }

    /**
     * Retorna resultado
     */
    public function getRelatorioProcVenda($ajax = TRUE)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        // $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');
        $id_parceiro = $this->input->get_post('representante');

        $resultado['data'] = $this->pedido->extrairRelatorioProcessamentoVendas($data_inicio, $data_fim, $id_parceiro);
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    public function vendas1()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 01 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Desc. do Produto',
            'Seguro Contratado',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'),
                        $row['segurado'],
                        $row['documento'],
                        $row['plano_nome'],
                        $row['nome_produto_parceiro'],
                        app_format_currency($row['nota_fiscal_valor'], true),
                        app_format_currency($row['premio_liquido_total'], true),
                        $row['num_apolice'],
                    ];
                }
                //$this->exportExcel($data['columns'], $rows);
                $this->exportCSV($data['columns'], $rows, 'Relatório 01 de Vendas');
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas1", $data);
    }

    public function vendas4()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 04 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Vendedor',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'),
                        $row['segurado'],
                        $row['documento'],
                        $row['plano_nome'],
                        $row['nome_produto_parceiro'],
                        app_format_currency($row['nota_fiscal_valor'], true),
                        app_format_currency($row['premio_liquido_total'], true),
                        $row['num_apolice'],
                        $row['nome_fantasia'],
                        $row['cnpj'],
                        $row['UF'],
                        $row['vendedor'],
                    ];
                }
                //$this->exportExcel($data['columns'], $rows);
                $this->exportCSV($data['columns'], $rows, 'Relatório 04 de Vendas');
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas4", $data);
    }

    public function vendas5()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 05 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Valor a Receber',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'),
                        $row['segurado'],
                        $row['documento'],
                        $row['plano_nome'],
                        $row['nome_produto_parceiro'],
                        app_format_currency($row['nota_fiscal_valor'], true),
                        app_format_currency($row['premio_liquido_total'], true),
                        $row['num_apolice'],
                        $row['nome_fantasia'],
                        $row['cnpj'],
                        $row['UF'],
                        app_format_currency($row['comissao_parceiro'], true),
                    ];
                }
                //$this->exportExcel($data['columns'], $rows);
                $this->exportCSV($data['columns'], $rows, 'Relatório 05 de Vendas');
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas5", $data);
    }

    public function vendas6()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 06 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Representante de Seguros',
            'Cobertura Comercializada',
            'Número do Bilhete (PARCEIRO)',
            'Número do Bilhete (GBS)',
            'Codigo da Loja',
            'Data de Emissão',
            'Inicio de Vigencia',
            'Fim de Vigencia',
            'Data Cancelamento',
            'CPF do Segurado',
            'Data de Nascimento',
            'Nome do Segurado',
            'Cidade',
            'Estado',
            'CEP',
            'Logradouro',
            'Tipo de Equipamento',
            'Marca',
            'Modelo (Descrição do Equipamento)',
            'LMI',
            'Prêmio Líquido',
            'Prêmio Bruto',
            'Valor Restituído'
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];
            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'),
                        $row['nome_fantasia'],
                        $row['plano_nome'],
                        $row['num_apolice'],
                        $row['num_apolice_cliente'],
                        $row['cod_loja'],
                        $row['data_pedido'],
                        $row['ini_vigencia'],
                        $row['fim_vigencia'],
                        '',
                        $row['documento'],
                        $row['data_nascimento'],
                        $row['segurado'],
                        $row['endereco_cidade'],
                        $row['endereco_estado'],
                        $row['endereco_cep'],
                        $row['endereco_logradouro'],
                        $row['tipo_equipamento'],
                        $row['marca'],
                        $row['modelo'],
                        app_format_currency($row['nota_fiscal_valor'], true),
                        app_format_currency($row['premio_liquido'], true),
                        app_format_currency($row['premio_liquido_total'], true),
                        ''
                    ];
                }
                //$this->exportExcel($data['columns'], $rows);
                $this->exportCSV($data['columns'], $rows, 'Relatório 06 de Vendas');
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas6", $data);
    }


    public function _mapa_repasse_lasa()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(3);
        $data['src'] = $this->controller_uri;
        $data['title'] = 'Relatório de Mapa de Repasse';
        $data['layout'] = 'mapa_analitico';
        $data['columns'] = [
            'Operacao',
            'Grupo',
            'Data da Venda',
            'Inicio Vigencia',
            'Fim Vigencia',
            'Num Bilhete',
            'Segurado',
            'Documento',
            'Equipamento',
            'Marca',
            'Modelo',
            'IMEI',
            'Produto',
            'Importancia Segurada',
            'Num Endosso',
            'Vigencia Parcela',
            'Parcela',
            'Status Parcela',
            'Data Cancelamento',
            'Valor Parcela',
            'Premio Bruto Roubo Furto',
            'Premio Liquido Roubo Furto',
            'Premio Bruto Quebra',
            'Premio Liquido Quebra',
            'Pro Labore',
            'Comissao Corretagem',

        ];

        if ($_POST) {
            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
            }
            $result = $this->getMapaRepasse(FALSE, $data['layout']);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {
                $this->exportExcelMapaRepasse($data['columns'], $data['result']);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "{$this->controller_uri}/{$data['action']}", $data);
    }

    public function mapa_repasse()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(3);
        $data['src'] = $this->controller_uri;
        $data['title'] = 'Relatório de Mapa de Repasse';
        $data['layout'] = 'mapa_sintetico';
        $data['columns'] = [
            'Plano',
            'Representante',
            'Cobertura',
            'Tipo Movimento (Emissão ou Cancelamento',
            'Data do Movimento',
            'Inicio Vigencia',
            'Fim Vigencia',
            'Num Bilhete',
            'Nome',
            'CPF',
            'Equipamento',
            'Marca',
            'Modelo',
            'IMEI',
            'Produto',
            'Importancia Segurada',
            'Forma Pagto',
            'Num Endosso',
            'Mês Parcela',
            'Parcela',
            'Status Parcela',
            'Data processamento Cliente/SIS',
            // 'Data retorno SIS/Cliente',
            // 'Data processamento SIS/Seguradora',
            // 'Data processamento Seguradora/SIS',
            'Data Cancelamento',
            'Valor Parcela',
            'Premio Bruto',
            'Premio Liquido',
            'Comissao Representante',
            'Comissao Corretagem',

        ];
        // Bandeira utilizada para exibir os resultados
        $data['flag'] = FALSE;
        if ($_POST) {

            $data['title'] = "Relatório de Mapa de Repasse - ( " . $_POST['nomerepresentante'] . " )";

            $data['id_parceiro'] = $_POST['representante'];
            $data['slug'] = $_POST['slug'];

            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
                $data['flag'] = TRUE;
            }
            $result = $this->getMapaRepasse(FALSE, $data['layout'], $data['id_parceiro'], $data['slug']);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                if ($_POST['layout'] == "mapa_analitico") {
                    //$this->exportExcelMapaRepasse($data['columns'], $data['result']);
                    $this->exportCSVMapaRepasse($data['columns'], $data['result']);
                } else if ($_POST['layout'] == "mapa_sintetico") {
                    $this->exportExcelMapaRepasseSintetico($data['columns'], $data['result']);
                }
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');

            $data['id_parceiro'] = $_POST['representante'];
            $data['slug'] = $_POST['slug'];
        }

        $data['combo'] = $this->getParceiro();

        //Carrega template
        $this->template->load("admin/layouts/base", "{$this->controller_uri}/{$data['action']}", $data);
    }


    /* Retorno os dados para combo */
    public function getParceiro($withFileIntegration_bySlugGroup = null)
    {

        $this->load->model('pedido_model', 'pedido');
        return $this->pedido->getRepresentantes($withFileIntegration_bySlugGroup);
    }

    /**
     * Retorna resultado
     */
    public function getRelatorio($ajax = TRUE)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        // $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        $resultado['data'] = $this->pedido->extrairRelatorioVendas($data_inicio, $data_fim);
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    /**
     * Retorna resultado
     */
    public function getMapaRepasse($ajax = TRUE, $layout, $parceiro, $slug)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if ($layout == 'mapa_analitico') {

            $resultado['data'] = $this->pedido->extrairRelatorioMapaRepasseAnalitico($data_inicio, $data_fim, $parceiro, $slug);
        } else {

            // 'Sinténtico'
            $resultado['data'] = $this->preparaMapaRepasse($this->pedido->extrairRelatorioMapaRepasseSintetico($data_inicio, $data_fim, $parceiro, $slug));
        }
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    private function preparaMapaRepasse($result)
    {
        if (empty($result)) {
            return [];
        }

        $planos = [];

        foreach ($result as $k => $v) {
            $planos[$k] = $v['planos'];
        }

        $ret = [];
        $V_quantidade = 0;
        $V_IOF = 0;
        $V_PL = 0;
        $V_PB = 0;
        $V_pro_labore = 0;
        $V_valor_comissao = 0;

        $C_quantidade = 0;
        $C_IOF = 0;
        $C_PL = 0;
        $C_PB = 0;
        $C_pro_labore = 0;
        $C_valor_comissao = 0;

        $T_quantidade = 0;
        $T_IOF = 0;
        $T_PL = 0;
        $T_PB = 0;
        $T_pro_labore = 0;
        $T_valor_comissao = 0;

        foreach ($planos as $key => $desc) {
            $find = false;
            foreach ($result as $row) {
                if ($row['planos'] == $desc) {
                    $row['desc'] = $desc;
                    $ret[] = $row;

                    $V_quantidade += $row['V_quantidade'];
                    $V_IOF += $row['V_IOF'];
                    $V_PL += $row['V_PL'];
                    $V_PB += $row['V_PB'];
                    $V_pro_labore += $row['V_pro_labore'];
                    $V_valor_comissao += $row['V_valor_comissao'];

                    $C_quantidade += $row['C_quantidade'];
                    $C_IOF += $row['C_IOF'];
                    $C_PL += $row['C_PL'];
                    $C_PB += $row['C_PB'];
                    $C_pro_labore += $row['C_pro_labore'];
                    $C_valor_comissao += $row['C_valor_comissao'];

                    $T_quantidade += $V_quantidade + $C_quantidade;
                    $T_IOF += $V_IOF + $C_IOF;
                    $T_PL += $V_PL + $C_PL;
                    $T_PB += $V_PB + $C_PB;
                    $T_pro_labore += $V_pro_labore + $C_pro_labore;
                    $T_valor_comissao += $V_valor_comissao + $C_valor_comissao;
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                $ret[] = [
                    'desc' => $desc,
                    'V_quantidade' => 0,
                    'V_IOF' => 0,
                    'V_PL' => 0,
                    'V_PB' => 0,
                    'V_pro_labore' => 0,
                    'V_valor_comissao' => 0,

                    'C_quantidade' => 0,
                    'C_IOF' => 0,
                    'C_PL' => 0,
                    'C_PB' => 0,
                    'C_pro_labore' => 0,
                    'C_valor_comissao' => 0,

                    'T_quantidade' => 0,
                    'T_IOF' => 0,
                    'T_PL' => 0,
                    'T_PB' => 0,
                    'T_pro_labore' => 0,
                    'T_valor_comissao' => 0,
                ];
            }
        }

        $ret[] = [
            'desc' => 'TOTAL',
            'V_quantidade' => $V_quantidade,
            'V_IOF' => $V_IOF,
            'V_PL' => $V_PL,
            'V_PB' => $V_PB,
            'V_pro_labore' => $V_pro_labore,
            'V_valor_comissao' => $V_valor_comissao,
            'C_quantidade' => $C_quantidade,
            'C_IOF' => $C_IOF,
            'C_PL' => $C_PL,
            'C_PB' => $C_PB,
            'C_pro_labore' => $C_pro_labore,
            'C_valor_comissao' => $C_valor_comissao,
            'T_quantidade' => $T_quantidade,
            'T_IOF' => $T_IOF,
            'T_PL' => $T_PL,
            'T_PB' => $T_PB,
            'T_pro_labore' => $T_pro_labore,
            'T_valor_comissao' => $T_valor_comissao,
        ];

        return $ret;
    }

    private function _preparaMapaRepasse($result)
    {
        if (empty($result)) {
            return [];
        }

        $tpas = [
            '007' => 'NOVOS',
            '010' => 'USADOS'
        ];

        $ret = [];
        $V_quantidade_RF = 0;
        $V_IOF_RF = 0;
        $V_PL_RF = 0;
        $V_PB_RF = 0;
        $V_pro_labore_RF = 0;
        $V_valor_comissao_RF = 0;
        $V_quantidade_QA = 0;
        $V_PB_QA = 0;
        $V_IOF_QA = 0;
        $V_PL_QA = 0;
        $V_pro_labore_QA = 0;
        $V_valor_comissao_QA = 0;

        $C_quantidade_RF = 0;
        $C_IOF_RF = 0;
        $C_PL_RF = 0;
        $C_PB_RF = 0;
        $C_pro_labore_RF = 0;
        $C_valor_comissao_RF = 0;
        $C_quantidade_QA = 0;
        $C_PB_QA = 0;
        $C_IOF_QA = 0;
        $C_PL_QA = 0;
        $C_pro_labore_QA = 0;
        $C_valor_comissao_QA = 0;

        $T_quantidade_RF = 0;
        $T_IOF_RF = 0;
        $T_PL_RF = 0;
        $T_PB_RF = 0;
        $T_pro_labore_RF = 0;
        $T_valor_comissao_RF = 0;
        $T_quantidade_QA = 0;
        $T_PB_QA = 0;
        $T_IOF_QA = 0;
        $T_PL_QA = 0;
        $T_pro_labore_QA = 0;
        $T_valor_comissao_QA = 0;

        foreach ($tpas as $tpa => $desc) {
            $find = false;
            foreach ($result as $row) {
                if ($row['cod_tpa'] == $tpa) {
                    $row['desc'] = $desc;
                    $ret[] = $row;

                    $V_quantidade_RF += $row['V_quantidade_RF'];
                    $V_IOF_RF += $row['V_IOF_RF'];
                    $V_PL_RF += $row['V_PL_RF'];
                    $V_PB_RF += $row['V_PB_RF'];
                    $V_pro_labore_RF += $row['V_pro_labore_RF'];
                    $V_valor_comissao_RF += $row['V_valor_comissao_RF'];
                    $V_quantidade_QA += $row['V_quantidade_QA'];
                    $V_PB_QA += $row['V_PB_QA'];
                    $V_IOF_QA += $row['V_IOF_QA'];
                    $V_PL_QA += $row['V_PL_QA'];
                    $V_pro_labore_QA += $row['V_pro_labore_QA'];
                    $V_valor_comissao_QA += $row['V_valor_comissao_QA'];

                    $C_quantidade_RF += $row['C_quantidade_RF'];
                    $C_IOF_RF += $row['C_IOF_RF'];
                    $C_PL_RF += $row['C_PL_RF'];
                    $C_PB_RF += $row['C_PB_RF'];
                    $C_pro_labore_RF += $row['C_pro_labore_RF'];
                    $C_valor_comissao_RF += $row['C_valor_comissao_RF'];
                    $C_quantidade_QA += $row['C_quantidade_QA'];
                    $C_PB_QA += $row['C_PB_QA'];
                    $C_IOF_QA += $row['C_IOF_QA'];
                    $C_PL_QA += $row['C_PL_QA'];
                    $C_pro_labore_QA += $row['C_pro_labore_QA'];
                    $C_valor_comissao_QA += $row['C_valor_comissao_QA'];

                    $T_quantidade_RF += $V_quantidade_RF + $C_quantidade_RF;
                    $T_IOF_RF += $V_IOF_RF + $C_IOF_RF;
                    $T_PL_RF += $V_PL_RF + $C_PL_RF;
                    $T_PB_RF += $V_PB_RF + $C_PB_RF;
                    $T_pro_labore_RF += $V_pro_labore_RF + $C_pro_labore_RF;
                    $T_valor_comissao_RF += $V_valor_comissao_RF + $C_valor_comissao_RF;
                    $T_quantidade_QA += $V_quantidade_QA + $C_quantidade_QA;
                    $T_PB_QA += $V_PB_QA + $C_PB_QA;
                    $T_IOF_QA += $V_IOF_QA + $C_IOF_QA;
                    $T_PL_QA += $V_PL_QA + $C_PL_QA;
                    $T_pro_labore_QA += $V_pro_labore_QA + $C_pro_labore_QA;
                    $T_valor_comissao_QA += $V_valor_comissao_QA + $C_valor_comissao_QA;
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                $ret[] = [
                    'desc' => $desc,
                    'V_quantidade_RF' => 0,
                    'V_IOF_RF' => 0,
                    'V_PL_RF' => 0,
                    'V_PB_RF' => 0,
                    'V_pro_labore_RF' => 0,
                    'V_valor_comissao_RF' => 0,
                    'V_quantidade_QA' => 0,
                    'V_PB_QA' => 0,
                    'V_IOF_QA' => 0,
                    'V_PL_QA' => 0,
                    'V_pro_labore_QA' => 0,
                    'V_valor_comissao_QA' => 0,

                    'C_quantidade_RF' => 0,
                    'C_IOF_RF' => 0,
                    'C_PL_RF' => 0,
                    'C_PB_RF' => 0,
                    'C_pro_labore_RF' => 0,
                    'C_valor_comissao_RF' => 0,
                    'C_quantidade_QA' => 0,
                    'C_PB_QA' => 0,
                    'C_IOF_QA' => 0,
                    'C_PL_QA' => 0,
                    'C_pro_labore_QA' => 0,
                    'C_valor_comissao_QA' => 0,

                    'T_quantidade_RF' => 0,
                    'T_IOF_RF' => 0,
                    'T_PL_RF' => 0,
                    'T_PB_RF' => 0,
                    'T_pro_labore_RF' => 0,
                    'T_valor_comissao_RF' => 0,
                    'T_quantidade_QA' => 0,
                    'T_PB_QA' => 0,
                    'T_IOF_QA' => 0,
                    'T_PL_QA' => 0,
                    'T_pro_labore_QA' => 0,
                    'T_valor_comissao_QA' => 0,
                ];
            }
        }

        $ret[] = [
            'desc' => 'TOTAL',
            'V_quantidade_RF' => $V_quantidade_RF,
            'V_IOF_RF' => $V_IOF_RF,
            'V_PL_RF' => $V_PL_RF,
            'V_PB_RF' => $V_PB_RF,
            'V_pro_labore_RF' => $V_pro_labore_RF,
            'V_valor_comissao_RF' => $V_valor_comissao_RF,
            'V_quantidade_QA' => $V_quantidade_QA,
            'V_PB_QA' => $V_PB_QA,
            'V_IOF_QA' => $V_IOF_QA,
            'V_PL_QA' => $V_PL_QA,
            'V_pro_labore_QA' => $V_pro_labore_QA,
            'V_valor_comissao_QA' => $V_valor_comissao_QA,
            'C_quantidade_RF' => $C_quantidade_RF,
            'C_IOF_RF' => $C_IOF_RF,
            'C_PL_RF' => $C_PL_RF,
            'C_PB_RF' => $C_PB_RF,
            'C_pro_labore_RF' => $C_pro_labore_RF,
            'C_valor_comissao_RF' => $C_valor_comissao_RF,
            'C_quantidade_QA' => $C_quantidade_QA,
            'C_PB_QA' => $C_PB_QA,
            'C_IOF_QA' => $C_IOF_QA,
            'C_PL_QA' => $C_PL_QA,
            'C_pro_labore_QA' => $C_pro_labore_QA,
            'C_valor_comissao_QA' => $C_valor_comissao_QA,
            'T_quantidade_RF' => $T_quantidade_RF,
            'T_IOF_RF' => $T_IOF_RF,
            'T_PL_RF' => $T_PL_RF,
            'T_PB_RF' => $T_PB_RF,
            'T_pro_labore_RF' => $T_pro_labore_RF,
            'T_valor_comissao_RF' => $T_valor_comissao_RF,
            'T_quantidade_QA' => $T_quantidade_QA,
            'T_PB_QA' => $T_PB_QA,
            'T_IOF_QA' => $T_IOF_QA,
            'T_PL_QA' => $T_PL_QA,
            'T_pro_labore_QA' => $T_pro_labore_QA,
            'T_valor_comissao_QA' => $T_valor_comissao_QA,
        ];

        return $ret;
    }

    public function exportCSV($columns, $rows = [], $nomeArq)
    {
        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: no-cache");
        header("Cache: no-cahce");
        $filename = app_assets_dir('arquivos', 'uploads') . "relatorio_exp_" . date("Y-m-d_H-i-s", time()) . ".csv";
        $fp = fopen($filename, "a+");
        $linha = '';
        $linhaheader = $nomeArq;
        fwrite($fp, $linhaheader . "\n");
        $linhaheader = '';
        // Cria as colunas
        foreach ($columns as $column) {
            $linhaheader .= $column . ";";
        }
        fwrite($fp, $linhaheader . "\n");
        // Cria as Linhas
        foreach ($rows as $row) {
            $contC = 0;
            foreach ($columns as $column) {
                $linha .= $row[$contC] . ";";
                $contC++;
            }
            fwrite($fp, $linha . "\n");
            $linha = '';
        }
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: attachment; filename=' . basename($filename));
        readfile($filename);
        unlink($filename);
        exit();
    }

    public function exportExcel2($columns, $rows = [], $formato = 'XLS')
    {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $contC = 0;
        $contR = 1;

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $letters = array('A');
        $current = 'A';
        while ($current != 'ZZ') {
            $letters[] = ++$current;
        }

        // Cria as colunas
        foreach ($columns as $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $column);
            $contC++;
        }

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:' . $letters[count($columns) - 1] . '1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        //print_pre ($rows);
        // Cria as Linhas
        foreach ($rows as $row) {
            $contR++;
            $contC = 0;

            foreach ($columns as $column) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $row[$contC]);
                $contC++;
            }
        }

        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', app_assets_dir('temp', 'uploads'). basename(__FILE__)));

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        if ($formato == 'CSV') {
            header('Content-Disposition: attachment;filename="relatorio.csv"');
        } else {
            header('Content-Disposition: attachment;filename="relatorio.xls"');
        }
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        if ($formato == 'CSV') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        }
        $objWriter->save('php://output');
        exit;
    }

    public function exportExcel($columns, $rows = [], $formato = 'XLS')
    {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();
        //print_pre ($rows);

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A3:C3')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#0aa89e')
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
            )
        );

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, "CONSOLIDADO");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 3, "%");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 3, "TOTAL");

        //TITULO DIAS
        $row = 3;
        $col = 3;
        foreach ($rows['dias'] as $data) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data['dia']);
            $col++;
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, "VENDAS");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, "X%");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, emptyor($rows['vendas']['total_venda'], 0));

        //TOTAL VENDAS POR DIA
        $row = 5;
        $col = 3;
        foreach ($rows['dias'] as $data) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, emptyor($rows['vendas']['totais_dia'][$data['dia_format']], 0));
            $col++;
        }

        //TOTAL VENDAS POR PLANO
        $row = 6; //ULTIMO VALOR DE LINHA INSERIDO MANUALMENTE 
        foreach ($rows['planos'] as $plano) {
            $col = 3;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $plano['nome']);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, emptyor($plano['percentual'], 0));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $plano['qtde']);

            foreach ($rows['dias'] as $data) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, emptyor($rows['data']['vendas'][$plano['produto_parceiro_plano_id']][$data['dia_format']], 0));
                $col++;
            }
            $row++;
        }

        //<!-- GRUPOS / STATUS -->
        $descricao_grupo = '';
        foreach ($rows['grupos'] as $grupo) {
            $col = 3;

            // valida se deve fazer a quebra do grupo
            if ($descricao_grupo != $grupo['descricao_grupo']) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, " ");
                $row++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $grupo['descricao_grupo']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, emptyor($rows['grupos_totais'][$grupo['cliente_evolucao_status_grupo_id']]['percentual'], 0) . '%');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, emptyor($rows['grupos_totais'][$grupo['cliente_evolucao_status_grupo_id']]['valor'], 0));

                foreach ($rows['dias'] as $data) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, emptyor($rows['grupos_totais'][$grupo['cliente_evolucao_status_grupo_id']][$data['dia_format']], 0));
                    $col++;
                }
                $row++;
                $descricao_grupo = $grupo['descricao_grupo'];
            }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $grupo['descricao']);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, emptyor($grupo['percentual'], 0) . '%');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, emptyor($grupo['qtde']));

            $col = 3;
            foreach ($rows['dias'] as $data) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, emptyor($rows['data']['mailing'][$grupo['produto_parceiro_cliente_status_id']][$data['dia_format']], 0));
                $col++;
            }
            $row++;
        }



        //print_pre ($rows);
        // Cria as Linhas
        //foreach ($rows as $row) {
        // $contR++;
        // $contC = 0;


        //foreach ($columns as $column) {
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $row[$contC]);
        //$contC++;
        //}

        //}

        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', app_assets_dir('temp', 'uploads'). basename(__FILE__)));

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        if ($formato == 'CSV') {
            header('Content-Disposition: attachment;filename="relatorio.csv"');
        } else {
            header('Content-Disposition: attachment;filename="relatorio.xls"');
        }
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        if ($formato == 'CSV') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        }
        $objWriter->save('php://output');
        exit;
    }

    public function exportCSVMapaRepasse($columns, $rows = [])
    {
        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: no-cache");
        header("Cache: no-cahce");
        $filename = app_assets_dir('arquivos', 'uploads') . "rel_mapa_repasse" . date("Y-m-d_H-i-s", time()) . ".csv";
        $fp = fopen($filename, "a+");
        $linha = '';
        $linhaheader = 'Relatório de Mapa de Repasse';
        fwrite($fp, $linhaheader . "\n");
        $linhaheader  = '';
        $linhaheader .= 'Plano' . ";";
        $linhaheader .= 'Representante' . ";";
        $linhaheader .= 'Cobertura' . ";";
        $linhaheader .= 'Tipo Movimento (Emissão ou Cancelamento' . ";";
        $linhaheader .= 'Data do Movimento' . ";";
        $linhaheader .= 'Inicio Vigencia' . ";";
        $linhaheader .= 'Fim Vigencia' . ";";
        $linhaheader .= 'Num Bilhete' . ";";
        $linhaheader .= 'Nome' . ";";
        $linhaheader .= 'CPF' . ";";
        $linhaheader .= 'Equipamento' . ";";
        $linhaheader .= 'Marca' . ";";
        $linhaheader .= 'Modelo' . ";";
        $linhaheader .= 'IMEI' . ";";
        $linhaheader .= 'Produto' . ";";
        $linhaheader .= 'Importancia Segurada' . ";";
        $linhaheader .= 'Forma Pagto' . ";";
        $linhaheader .= 'Num Endosso' . ";";
        $linhaheader .= 'Mês Parcela' . ";";
        $linhaheader .= 'Parcela' . ";";
        $linhaheader .= 'Status Parcela' . ";";
        $linhaheader .= 'Data processamento Cliente/SIS' . ";";
        $linhaheader .= 'Data Cancelamento' . ";";
        $linhaheader .= 'Valor Parcela' . ";";
        $linhaheader .= 'Premio Bruto' . ";";
        $linhaheader .= 'Premio Liquido' . ";";
        $linhaheader .= 'Comissao Representante' . ";";
        $linhaheader .= 'Comissao Corretagem' . ";";
        fwrite($fp, $linhaheader . "\n");
        //Gera as linhas dos registros
        foreach ($rows as $row) {
            $linha .= $row['plano_nome'] . ';';
            $linha .= $row['representante'] . ';';
            $linha .= $row['cobertura'] . ';';
            $linha .= $row['venda_cancelamento'] . ';';
            $linha .= $row['data_emissao'] . ';';
            $linha .= $row['ini_vigencia'] . ';';
            $linha .= $row['fim_vigencia'] . ';';
            $linha .= $row['num_apolice'] . ';';
            $linha .= $row['segurado_nome'] . ';';
            $linha .= $row['documento'] . ';';
            $linha .= $row['equipamento'] . ';';
            $linha .= $row['marca'] . ';';
            $linha .= $row['modelo'] . ';';
            $linha .= $row['imei'] . ';';
            $linha .= $row['nome_produto_parceiro'] . ';';
            $linha .= $row['importancia_segurada'] . ';';
            $linha .= $row['forma_pagto'] . ';';
            $linha .= $row['num_endosso'] . ';';
            $linha .= $row['vigencia_parcela'] . ';';
            $linha .= $row['parcela'] . ';';
            $linha .= $row['status_parcela'] . ';';
            $linha .= $row['data_processamento_cli_sis'] . ';';
            $linha .= $row['data_cancelamento'] . ';';
            $linha .= $row['valor_parcela'] . ';';
            $linha .= $row['PB'] . ';';
            $linha .= $row['PL'] . ';';
            $linha .= $row['pro_labore'] . ';';
            $linha .= $row['valor_comissao'] . ';';
            fwrite($fp, $linha . "\n");
            $linha = '';
        }
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: attachment; filename=' . basename($filename));
        readfile($filename);
        unlink($filename);
        exit();
    }

    public function exportExcelMapaRepasse($columns, $rows = [])
    {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $contC = 0;
        $contR = 1;

        $styleCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleCenterVertic = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Relatório de Mapa de Repasse');
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:AE1')->applyFromArray($styleCenter);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Plano');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', 'Representante');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', 'Cobertura');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', 'Tipo Movimento (Emissão ou Cancelamento');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', 'Data do Movimento');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', 'Inicio Vigencia');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', 'Fim Vigencia');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', 'Num Bilhete');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', 'Nome');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', 'CPF');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', 'Equipamento');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L3', 'Marca');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M3', 'Modelo');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N3', 'IMEI');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O3', 'Produto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P3', 'Importancia Segurada');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q3', 'Forma Pagto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R3', 'Num Endosso');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S3', 'Mês Parcela');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T3', 'Parcela');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U3', 'Status Parcela');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V3', 'Data processamento Cliente/SIS');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('W3', 'Data Cancelamento');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('X3', 'Valor Parcela');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y3', 'Premio Bruto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z3', 'Premio Liquido');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA3', 'Comissao Representante');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB3', 'Comissao Corretagem');


        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:AC1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:AC1')->applyFromArray($styleCenter);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:AC1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:AC1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A3:AC3')->getFont()->setBold(true);

        $contR = 4;
        // Cria as Linhas

        foreach ($rows as $row) {
            $contRFim = $contR + 1;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $contR, $row['plano_nome']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, $row['representante']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, $row['cobertura']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, $row['venda_cancelamento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, $row['data_emissao']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, $row['ini_vigencia']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, $row['fim_vigencia']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, $row['num_apolice']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, $row['segurado_nome']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, $row['documento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, $row['equipamento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $contR, $row['marca']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $contR, $row['modelo']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $contR, $row['imei']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $contR, $row['nome_produto_parceiro']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $contR, $row['importancia_segurada']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $contR, $row['forma_pagto']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $contR, $row['num_endosso']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $contR, $row['vigencia_parcela']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $contR, $row['parcela']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $contR, $row['status_parcela']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $contR, $row['data_processamento_cli_sis']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $contR, $row['data_cancelamento']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $contR, $row['valor_parcela']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $contR, $row['PB']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z' . $contR, $row['PL']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA' . $contR, $row['pro_labore']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $contR, $row['valor_comissao']);
            $contR++;
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="relatorio.xls"'); <<<***Exporta para CSV por causa do volume de dados***>>>
        header('Content-Disposition: attachment;filename="relatorio.csv"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); <<<***Exporta para CSV por causa do volume de dados***>>>
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save('php://output');
        exit;
    }

    public function exportExcelMapaRepasseSintetico($columns, $rows = [])
    {


        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $contC = 0;
        $contR = 1;

        $styleCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleCenterVertic = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K2')->applyFromArray($styleCenter);


        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '');
        // $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C1:E1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'VENDAS');
        // $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F1:H1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'CANCELAMENTOS');
        // $objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:K1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C1:E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('C1:E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A65536')->getFont()->setBold(true);

        $contR = 2;
        // Cria as Linhas
        foreach ($rows as $row) {

            $contRFim = $contR + 5;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleCenter);

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contR . ':A' . $contRFim)->getStyle('A' . $contR . ':A' . $contR)->applyFromArray($styleCenterVertic);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $contR, $row['desc']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Quantidade de Registros');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, $row['V_quantidade']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, $row['C_quantidade']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, $row['V_quantidade'] - $row['C_quantidade']);
            $contR++;


            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Prêmio Bruto');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_PB'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['C_PB'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_PB'] - $row['C_PB'], true));
            $contR++;


            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'IOF');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_IOF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['C_IOF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_IOF'] - $row['C_IOF'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Prêmio Líquido');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_PL'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['C_PL'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_PL'] - $row['C_PL'], true));
            $contR++;


            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Comissão Representante');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_pro_labore'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['C_pro_labore'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_pro_labore'] - $row['C_pro_labore'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Comissão de Corretagem');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_valor_comissao'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['C_valor_comissao'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_valor_comissao'] - $row['C_valor_comissao'], true));
            $contR++;
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="relatorio.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function _exportExcelMapaRepasse($columns, $rows = [])
    {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $contC = 0;
        $contR = 1;

        $styleCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleCenterVertic = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K2')->applyFromArray($styleCenter);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C1:E1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'VENDAS');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F1:H1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'CANCELAMENTOS');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:K1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', 'Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', 'TOTAL');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', 'Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', 'Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', 'TOTAL');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', 'Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', 'Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', 'TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        $contR = 3;
        // Cria as Linhas
        foreach ($rows as $row) {
            $contRFim = $contR + 5;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':H' . $contR)->applyFromArray($styleCenter);

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contR . ':A' . $contRFim)->getStyle('A' . $contR . ':A' . $contR)->applyFromArray($styleCenterVertic);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $contR)->getAlignment()->setTextRotation(90);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $contR, $row['desc']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Quantidade de Registros');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, $row['V_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, $row['V_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, $row['V_quantidade_RF'] + $row['V_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, $row['C_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, $row['C_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, $row['C_quantidade_RF'] + $row['C_quantidade_QA']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, $row['V_quantidade_RF'] + $row['C_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, $row['V_quantidade_QA'] + $row['C_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, $row['V_quantidade_RF'] + $row['C_quantidade_RF'] + $row['V_quantidade_QA'] + $row['C_quantidade_QA']);
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Prêmio Bruto');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['V_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_PB_RF'] + $row['V_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, app_format_currency($row['C_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, app_format_currency($row['C_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, app_format_currency($row['C_PB_RF'] + $row['C_PB_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, app_format_currency($row['V_PB_RF'] + $row['C_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, app_format_currency($row['V_PB_QA'] + $row['C_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, app_format_currency($row['V_PB_RF'] + $row['C_PB_RF'] + $row['V_PB_QA'] + $row['C_PB_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'IOF');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['V_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_IOF_RF'] + $row['V_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, app_format_currency($row['C_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, app_format_currency($row['C_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, app_format_currency($row['C_IOF_RF'] + $row['C_IOF_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, app_format_currency($row['V_IOF_RF'] + $row['C_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, app_format_currency($row['V_IOF_QA'] + $row['C_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, app_format_currency($row['V_IOF_RF'] + $row['C_IOF_RF'] + $row['V_IOF_QA'] + $row['C_IOF_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Prêmio Líquido');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['V_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_PL_RF'] + $row['V_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, app_format_currency($row['C_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, app_format_currency($row['C_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, app_format_currency($row['C_PL_RF'] + $row['C_PL_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, app_format_currency($row['V_PL_RF'] + $row['C_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, app_format_currency($row['V_PL_QA'] + $row['C_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, app_format_currency($row['V_PL_RF'] + $row['C_PL_RF'] + $row['V_PL_QA'] + $row['C_PL_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Pró-labore LASA');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['V_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_pro_labore_RF'] + $row['V_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, app_format_currency($row['C_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, app_format_currency($row['C_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, app_format_currency($row['C_pro_labore_RF'] + $row['C_pro_labore_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, app_format_currency($row['V_pro_labore_RF'] + $row['C_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, app_format_currency($row['V_pro_labore_QA'] + $row['C_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, app_format_currency($row['V_pro_labore_RF'] + $row['C_pro_labore_RF'] + $row['V_pro_labore_QA'] + $row['C_pro_labore_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $contR . ':E' . $contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $contR, 'Comissão de Corretagem');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $contR, app_format_currency($row['V_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $contR, app_format_currency($row['V_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['V_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $contR, app_format_currency($row['C_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $contR, app_format_currency($row['C_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $contR, app_format_currency($row['C_valor_comissao_RF'] + $row['C_valor_comissao_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['C_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $contR, app_format_currency($row['V_valor_comissao_QA'] + $row['C_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['C_valor_comissao_RF'] + $row['V_valor_comissao_QA'] + $row['C_valor_comissao_QA'], true));
            $contR++;
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="relatorio.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    private function loadLibraries()
    {
        //Carrega React e Orb (relatórios)
        $this->template->js(app_assets_url("core/js/react.js", "admin"));
        $this->template->js(app_assets_url("core/js/orb.min.js", "admin"));
        $this->template->js(app_assets_url("modulos/relatorios/vendas/core.js", "admin"));
        $this->template->js(app_assets_url("modulos/relatorios/vendas/jquery.blockUI.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));

        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));
        $this->template->css(app_assets_url("core/css/orb.min.css", "admin"));
    }

    private function getRelatorioVendaDireta($parceiro_id, $produto_parceiro_id, $data_inicio, $data_fim)
    {
        $this->load->model("pedido_model", "pedido");
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_cliente_status_model', 'produto_parceiro_cliente_status');

        //Dados via GET
        $qtdeDias = app_date_get_diff_dias($data_inicio, $data_fim, 'D');
        $resultado = [
            'status' => false,
            'dias' => [],
            'grupos_totais' => [],
        ];

        $resultado['planos'] = $this->produto_parceiro_plano
            ->wtih_plano_habilitado($parceiro_id)
            ->order_by('nome')
            ->get_many_by(array('produto_parceiro_id' => $produto_parceiro_id));

        $resultado['grupos'] = $this->produto_parceiro_cliente_status
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->order_by('descricao_grupo, descricao')
            ->get_all();

        for ($i = 0; $i <= $qtdeDias; $i++) {
            $strtotime = strtotime(app_dateonly_mask_to_mysql($data_inicio) . ' + ' . $i . ' days');
            $dia = date('d/m/Y', $strtotime);
            $dia_format = date('Ymd', $strtotime);
            $resultado['dias'][] = ['dia' => $dia, 'dia_format' => $dia_format];

            // inicia os contadores das vendas diarias por plano
            foreach ($resultado['planos'] as $key => $value) {
                $resultado['data']['vendas'][$value['produto_parceiro_plano_id']][$dia_format] = 0;
                $resultado['planos'][$key]['qtde'] = 0;
                $resultado['planos'][$key]['percentual'] = 0;
            }

            // inicia os contadores dos grupos/status diarios
            foreach ($resultado['grupos'] as $key => $value) {
                $resultado['data']['mailing'][$value['produto_parceiro_cliente_status_id']][$dia_format] = 0;
                // $resultado['data']['mailing'][$value['produto_parceiro_cliente_status_id']]['valor'] = 0;
                $resultado['grupos'][$key]['qtde'] = 0;
                $resultado['grupos'][$key]['percentual'] = 0;
                $resultado['grupos_totais'][$value['cliente_evolucao_status_grupo_id']] = ['valor' => 0, 'percentual' => 0, 'cliente_evolucao_status_grupo_id' => $value['cliente_evolucao_status_grupo_id']];
            }
        }

        // print_pre($resultado['grupos_totais']);

        $resultado['mailing'] = $this->pedido->getRelatorioVendaDireta($data_inicio, $data_fim, $produto_parceiro_id);
        $resultado['vendas'] = $this->pedido->extrairRelatorioVendasDiario($data_inicio, $data_fim, $produto_parceiro_id);

        if (!empty($resultado['mailing'])) {
            $totalGroup = 0;
            foreach ($resultado['mailing'] as $mailing) {

                $resultado['data']['mailing'][$mailing['produto_parceiro_cliente_status_id']][$mailing['data_format']] += $mailing['qtde'];

                $indexGroup = app_search($resultado['grupos'], $mailing['produto_parceiro_cliente_status_id'], 'produto_parceiro_cliente_status_id');
                if ($indexGroup >= 0) {
                    $resultado['grupos'][$indexGroup]['qtde'] += $mailing['qtde'];
                    $resultado['grupos_totais'][$mailing['cliente_evolucao_status_grupo_id']]['valor'] += $mailing['qtde'];

                    if (!isset($resultado['grupos_totais'][$mailing['cliente_evolucao_status_grupo_id']][$mailing['data_format']])) {
                        $resultado['grupos_totais'][$mailing['cliente_evolucao_status_grupo_id']][$mailing['data_format']] = 0;
                    }

                    //Total por dia
                    $resultado['grupos_totais'][$mailing['cliente_evolucao_status_grupo_id']][$mailing['data_format']] += $mailing['qtde'];

                    $totalGroup += $mailing['qtde'];
                }

                $totalGroup += $mailing['qtde'];
            }

            // foreach ($resultado['mailing'] as $mailing)
            // {
            //     $resultado['data']['mailing'][$mailing['produto_parceiro_cliente_status_id']]['valor'] += $mailing['qtde'];
            // }

            foreach ($resultado['grupos_totais'] as $key => $value) {
                $resultado['grupos_totais'][$key]['percentual'] = $value['valor'] / $totalGroup * 100;
            }

            //Calcula o percentual de cada linha de um grupo
            foreach ($resultado['grupos'] as $key => $value) {
                foreach ($resultado['grupos_totais'] as $key2 => $value) {
                    if ($resultado['grupos'][$key]['cliente_evolucao_status_grupo_id'] == $resultado['grupos_totais'][$key2]['cliente_evolucao_status_grupo_id']) {
                        $resultado['grupos'][$key]['percentual'] = $resultado['grupos'][$key]['qtde'] / $resultado['grupos_totais'][$key2]['valor'] * 100;
                    }
                }
            }
        }

        if (!empty($resultado['vendas'])) {
            $totalPlan = 0;
            foreach ($resultado['vendas'] as $venda) {
                if (!isset($resultado['vendas']['totais_dia'][$venda['data_format']])) {
                    $resultado['vendas']['totais_dia'][$venda['data_format']] = 0;
                }

                $resultado['vendas']['totais_dia'][$venda['data_format']] += $venda['qtde'];
                $resultado['data']['vendas'][$venda['produto_parceiro_plano_id']][$venda['data_format']] += $venda['qtde'];
                $indexPlan = app_search($resultado['planos'], $venda['produto_parceiro_plano_id'], 'produto_parceiro_plano_id');
                if ($indexPlan >= 0) {
                    $resultado['planos'][$indexPlan]['qtde'] += $venda['qtde'];
                }
                $totalPlan += $venda['qtde'];
            }

            $resultado['vendas']['total_venda'] = $totalPlan;

            foreach ($resultado['planos'] as $key => $value) {
                $resultado['planos'][$key]['percentual'] = $resultado['planos'][$key]['qtde'] / $totalPlan * 100;
            }
        }

        $resultado['status'] = true;
        return $resultado;
    }

    public function vendaDireta()
    {
        $this->load->model('produto_parceiro_model', 'produto_parceiro');

        $parceiro_id = $this->session->userdata('parceiro_id');

        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y", strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 04 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Vendedor',
        ];
        $data['combo'] = $this->produto_parceiro->getProdutosByParceiro($parceiro_id);

        if ($_POST) {

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');

            if (empty($_POST['produto_parceiro_id'])) {
                $this->session->set_flashdata('fail_msg', 'Informe o Produto.');
                redirect("$this->controller_uri/vendaDireta");
            }

            if (empty($data['data_inicio'])) {
                $this->session->set_flashdata('fail_msg', 'Informe a Data Inicial.');
                redirect("$this->controller_uri/vendaDireta");
            }

            if (empty($data['data_fim'])) {
                $this->session->set_flashdata('fail_msg', 'Informe a Data Final.');
                redirect("$this->controller_uri/vendaDireta");
            }

            $produto_parceiro_id = $_POST['produto_parceiro_id'];
            $result = $this->getRelatorioVendaDireta($parceiro_id, $produto_parceiro_id, $data['data_inicio'], $data['data_fim']);
            // print_pre($result);
            $data['result']['data'] = $result['data'];
            $data['result']['dias'] = $result['dias'];
            $data['result']['planos'] = $result['planos'];
            $data['result']['grupos'] = $result['grupos'];
            $data['result']['grupos_totais'] = $result['grupos_totais'];
            $data['result']['vendas'] = $result['vendas'];



            if (!empty($_POST['btnExcel'])) {
                $rows = [];
                foreach ($data['result']['grupos_totais'] as $row) {
                    $rows[] = [
                        //app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['valor'],
                        $row['percentual'],
                        $row['cliente_evolucao_status_grupo_id'],
                        //app_format_currency($row['nota_fiscal_valor'], true), 
                        //app_format_currency($row['premio_liquido_total'], true), 

                    ];
                }

                $this->exportExcel($data['columns'], $data['result']);
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas_direta", $data);
    }

    public function list_to_html($data, $header = false)
    {
        $html = "";
        if ($data) {
            $td = 'td';
            if ($header) {
                $td = 'th';
            }

            if (isset($data[0][1]) && is_array($data[0][1])) {
                $data = $data[0];
            }
            foreach ($data as $row) {
                $html .= "<tr>";
                if (is_array($row)) {
                    foreach ($row as $col) {
                        $html .= "<" . $td . ">" . $col . "</" . $td . ">";
                    }
                }
                $html .= "</tr>";
            }
        }

        return $html;
    }

    public function htmlInfoIcon($data)
    {
        return "<div class='fs-3 mb-3 icon_info' title='" . $data['title'] . "'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-info-circle' viewBox='0 0 16 16'>
                        <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'></path>
                        <path d='M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z'></path>
                    </svg>
                    <div class='info_data' style='background-color: rgb(101 158 216);color: rgb(230 229 229);text-align: center;width: auto;display: none;'>" . $data['title'] . "</div>
                </div>";
    }



    public function htmlLabel($data)
    {
        return "<label class='control-label' for='" . $data['for'] . "'>" . $data['description'] . "</label>";
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     *   $data = [id='',name='', option=[value='',description='']]
     * @return string
     */
    public function htmlSelectBox($data)
    {
        $id = isset($data['id']) ? $data['id'] : '';
        $name = isset($data['name']) ? $data['name'] : '';
        $option = isset($data['option']) ? $data['option'] : [];
        $label = isset($data['label']) ? $this->htmlLabel($data['label']) : '';

        $html = $label;
        $html .= "<select data-filter=true name=" . $name . " id=" . $id . ">";
        for ($i = 0; $i < count($option); $i++) {
            $html .= "<option value=" . $option[$i][0] . ">" . $option[$i][1] . "</option>";
        }
        $html .= "</select>";
        return $html;
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     *   $data = [id='',name='', option=[value='',description='']]
     * @return string
     */
    public function htmlInput($data)
    {
        $id = isset($data['id']) ? $data['id'] : '';
        $name = isset($data['name']) ? $data['name'] : '';
        $type = isset($data['type']) ? $data['type'] : 'text';
        $value = isset($data['value']) ? $data['value'] : '';
        $placeholder = isset($data['placeholder']) ? $data['placeholder'] : '';
        $class = isset($data['class']) ? $data['class'] : '';
        $label = isset($data['label']) ? $this->htmlLabel($data['label']) : '';
        $js  = isset($data['js']) ? $data['js'] : '';
        $extra = '';
        if ($type == 'date') {
            $type = 'text';
            $class .= " ";
            $placeholder = !isset($placeholder) ? $placeholder : '__/____';
            $extra .= " maxlength='7' ";
        }
        $defaultClass = 'form-control';
        if (($type == 'button') || ($type == 'input')) {
            $defaultClass = "btn btn-primary";
        }
        $html = $label;
        $html .= "<input data-filter=true class=' " . $defaultClass . $class . "' placeholder='" . $placeholder . "' id='" . $id . "' name='" . $name . "' type='" . $type . "' value='" . $value . "' " . $js . " " . $extra . "/>";
        return $html;
    }

    public function getHtmlElement($data)
    {
        $html = '';
        switch (array_keys($data)[0]) {
            case 'input':
                $html .= $this->htmlInput($data['input']);
                break;
            case 'selectbox':
                $html .= $this->htmlSelectBox($data['selectbox']);
                break;
            case 'icone_info':
                $html .= $this->htmlInfoIcon($data['icone_info']);
                break;
        }
        return $html;
    }

    public function filter_html($filters)
    {
        $html = "";
        for ($i = 0; $i < count($filters); $i++) {
            $html .= "<div class='col-md-3   '>";
            $html .= $this->getHtmlElement($filters[$i]);
            $html .= "</div>";
        }

        return $html;
    }

    public function infoIconeHTML($title = '')
    {
        return [
            'icone_info' => [
                'title' => $title,
            ]
        ];
    }

    public function periodoFieldHTML()
    {
        return [
            'input' => [
                'type' => 'date',
                'name' => 'periodo',
                'id' => 'periodo',
                'label' => ['for' => 'periodo', 'description' => 'Selecione o Periodo'],
            ]
        ];
    }

    public function operacaoFieldHTML()
    {
        return [
            'selectbox' => [
                'style' => 'inline',
                'name' => 'operacao',
                'id' => 'operacao',
                'label' => ['for' => 'operacao', 'description' => 'Operação'],
                'option' => [
                    ['', 'valor'],
                    ['valor1', 'descricao1'],
                    ['valor2', 'descricao2'],
                ]
            ]
        ];
    }

    public function getReportButtonHTML($reportControllerMethod)
    {
        return [
            'input' => [
                'type' => 'button',
                'name' => 'consulte',
                'id' => 'consulte',
                'value' => 'buscar',
                'js' => "onclick='getReportBody(\"" . $reportControllerMethod . "\")'",
            ]
        ];
    }

    public function slaCapitalizacao()
    {
        $this->load->model("Integracao_Comunicacao_Model", "comunicacao_model");

        if (!$_POST) {
            //Carrega React e Orb (relatórios)
            $this->loadLibraries();
            $data = [];
            $header = $this->comunicacao_model->getDataReport('slaCapitalizacao', '', true);
            $data['tbody'] = $this->list_to_html([$header], true);
            $data['title'] = "Relatório SLA de Capitalização";
            $filters = [
                $this->periodoFieldHTML(),
                $this->infoIconeHTML('Periodo De algo'),
                $this->getReportButtonHTML('/'.$this->controller_uri.'/'.$this->uri->segment(3)),
            ];
            $data['filters'] = $this->filter_html($filters);
            //Carrega template
            $this->template->load("admin/layouts/base", "$this->controller_uri/default_report_template.php", $data);
        } else {

            $filters = $_POST;
            $data = array();
            $data[] = $this->comunicacao_model->getDataReport(
                'slaCapitalizacao',
                [
                    'periodo' => $_POST['periodo']
                ],
                false
            );
            echo $this->list_to_html($data);
        }
    }

    public function slaEmissaoCancelamento()
    {
        $this->load->model("Integracao_Comunicacao_Model", "comunicacao_model");

        if (!$_POST) {
            //Carrega React e Orb (relatórios)
            $this->loadLibraries();
            $data = [];
            $header = $this->comunicacao_model->getDataReport('slaEmissaoCancelamento', '', true);
            $data['tbody'] = $this->list_to_html([$header], true);
            $data['title'] = "Relatório SLA de Emissão e Cancelamento";
            $filters = [
                $this->periodoFieldHTML(),
                $this->getReportButtonHTML('/'.$this->controller_uri.'/'.$this->uri->segment(3)),
            ];
            $data['filters'] = $this->filter_html($filters);
            //Carrega template
            $this->template->load("admin/layouts/base", "$this->controller_uri/default_report_template.php", $data);
        } else {

            $filters = $_POST;
            $data = array();
            $data[] = $this->comunicacao_model->getDataReport(
                'slaEmissaoCancelamento',
                [
                    'periodo' => $_POST['periodo']
                ],
                false
            );
            echo $this->list_to_html($data);
        }
    }

    public function slaEmissaoCancelamentoRejeicao()
    {
        $this->load->model("Integracao_Comunicacao_Model", "comunicacao_model");

        if (!$_POST) {
            //Carrega React e Orb (relatórios)
            $this->loadLibraries();
            $data = [];
            $header = $this->comunicacao_model->getDataReport('slaEmissaoCancelamentoRejeicao', '', true);
            $data['tbody'] = $this->list_to_html([$header], true);
            $data['title'] = "Relatório SLA Emissão Cancelamento e Rejeição Bilhete";
            $filters = [
                $this->periodoFieldHTML(),
                $this->getReportButtonHTML('/'.$this->controller_uri.'/'.$this->uri->segment(3)),
            ];
            $data['filters'] = $this->filter_html($filters);
            //Carrega template
            $this->template->load("admin/layouts/base", "$this->controller_uri/default_report_template.php", $data);
        } else {

            $filters = $_POST;
            $data = array();
            $data[] = $this->comunicacao_model->getDataReport(
                'slaEmissaoCancelamentoRejeicao',
                [
                    'periodo' => $_POST['periodo']
                ],
                false
            );
            echo $this->list_to_html($data);
        }
    }

    public function sla_emissao_e_cancelamento_rejeicao()
    {
        $this->load->model("Integracao_Comunicacao_Model", "comunicacao_model");
        if (!$_POST) {
            //Carrega React e Orb (relatórios)
            $this->loadLibraries();
            $data = [];
            $header = $this->comunicacao_model->getDataReport('slaEmissaoCancelamentoRejeicao', '', true);
            $data['tbody'] = $this->list_to_html([$header], true);
            $data['title'] = "Relatório SLA de Emissão e Cancelamento Rejeição";
            $filters = [
                $this->periodoFieldHTML(),
                $this->getReportButtonHTML('/'.$this->controller_uri.'/'.$this->uri->segment(3)),
            ];
            $data['filters'] = $this->filter_html($filters);
            //Carrega template
            $this->template->load("admin/layouts/base", "$this->controller_uri/default_report_template.php", $data);
        } else {

            $filters = $_POST;
            $data = array();
            $data[] = $this->comunicacao_model->getDataReport(
                'slaEmissaoCancelamentoRejeicao',
                [
                    'periodo' => $_POST['periodo']
                ],
                false
            );
            echo $this->list_to_html($data);
        }
    }

    public function slaBaixaComissao()
    {
        $this->load->model("Integracao_Comunicacao_Model", "comunicacao_model");
        if (!$_POST) {
            //Carrega React e Orb (relatórios)
            $this->loadLibraries();
            $data = [];
            $header = $this->comunicacao_model->getDataReport('slaBaixaComissao', '', true);
            $data['tbody'] = $this->list_to_html([$header], true);
            $data['title'] = "Relatório SLA Baixa Comissão";
            $filters = [
                $this->periodoFieldHTML(),
                $this->getReportButtonHTML('/'.$this->controller_uri.'/'.$this->uri->segment(3)),
            ];
            $data['filters'] = $this->filter_html($filters);
            //Carrega template
            $this->template->load("admin/layouts/base", "$this->controller_uri/default_report_template.php", $data);
        } else {

            $filters = $_POST;
            $data = array();
            $data[] = $this->comunicacao_model->getDataReport(
                'slaBaixaComissao',
                [
                    'periodo' => $_POST['periodo']
                ],
                false
            );
            echo $this->list_to_html($data);
        }
    }

    private function trataHTML($text)
    {
        if (empty($text)) {
            return '';
        }

        if ( strpos($text, '<html') !== false )
        {
            $text = 'Erro de Processamento';
        }

        return $text;
    }
}
