<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?> do plano:  <span class="text-danger"><?php echo $produto_parceiro['produto_nome'];?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>

                        <a href="<?php echo base_url("$current_controller_uri/add_by_produto_parceiro/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>
                        <a onclick='salvarOrdem()' class="btn  btn-app btn-primary">
                            <i class="fa  fa-sort-amount-asc"></i> Salvar ordem
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>


                <!-- Widget -->
                <div class="card">

                    <!-- Widget heading -->
                            <!-- // Widget heading END -->

                    <div class="card-body">

                        <!-- Table -->
                        <input type="hidden" name="url_ordem" id="url_ordem" value="<?php echo  base_url("$current_controller_uri/set_ordem/{$produto_parceiro_id}"); ?>">

                        <table id="tabela-ordem" class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='10%'>Ordem</th>
                                <th width='25%'>Nome</th>
                                <th class="center" width='75%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php $i = 0; ?>
                            <?php foreach($rows as $row) :?>
                            <tr data-id="<?php echo $row[$primary_key];?>" data-ordem="<?php echo $i; ?>">

                                <td><?php echo $row['ordem'];?></td>
                                <td id="ppp_id_<?php echo $row[$primary_key]; ?>"><?php echo $row['nome'];?></td>
                                <td class="center">
                                    <a data-href="<?php echo base_url("{$current_controller_uri}/keyCreate/{$produto_parceiro_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary" <?php if( empty($row['enableCreateKey'])) { ?> disabled="disabled" <?php } ?> data-toggle="modal" data-target="#modal_gerar_chave" > <i class="fa fa-bolt"></i>  Gerar Chave </a>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm ink-reaction btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Configurações &nbsp; <i class="fa fa-caret-down"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <?php if( $row['precificacao_tipo_id'] == 1 || $row['precificacao_tipo_id'] == 5 || $row['precificacao_tipo_id'] == 6 ) : ?>
                                                <li><a href="<?php echo base_url("admin/cobertura_plano/index/{$row[$primary_key]}")?>"> Coberturas </a></li>
                                                <li><a href="<?php echo base_url("admin/produtos_parceiros_planos_precificacao_itens/index/{$row[$primary_key]}")?>" >Tabela de Preços </a></li>
                                                <li><a href="<?php echo base_url("admin/produtos_parceiros_planos_precificacao/edit/{$row[$primary_key]}")?>"> Precificação </a></li>
                                                <li><a href="<?php echo base_url("admin/produtos_parceiros_planos_origem_destino/edit/{$row[$primary_key]}")?>"> Origem e Destino </a></li>
                                                <li><a href="<?php echo base_url("admin/produtos_parceiros_planos_faixa_salarial/edit/{$row[$primary_key]}")?>"> Faixa Salarial </a></li>
                                            <?php endif; ?>
                                            <?php if($row['precificacao_tipo_id'] == 4) : ?>
                                                <li><a href="<?php echo base_url("admin/produtos_parceiros_planos_precificacao_servico/index/{$row[$primary_key]}")?>" >Tabela de Preços </a></li>
                                            <?php endif; ?>
                                            <?php if($row['precificacao_tipo_id'] == 2) : ?>
                                            <li><a href="<?php echo base_url("admin/cobertura_plano/index/{$row[$primary_key]}")?>"> Coberturas </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                                <?php $i++; ?>
                            <?php endforeach; echo $pagination_links?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->

                        <?php if ( empty($enableSomeCreateKey) ) { ?>
                            <div class="text-default-light">
                                <a href="#" class="btn btn-sm" disabled="disabled" > <i class="fa fa-bolt"></i>  Gerar Chave </a>
                                O botão de gerar chave é exibido disabilitado quando o usuário não possuir permissão para criar Cotação para qualquer Plano
                            </div>
                        <?php } ?>

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal_gerar_chave" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
