<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">


            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("$current_controller_uri/add")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>
                        <a href="<?php echo base_url("admin/parceiros_relacionamento_produtos/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Relacionamentos
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <?php $this->load->view('admin/parceiros/search_form')?>
                <!-- Widget -->
                <div class="card">

                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width="30%">Nome Fantasia</th>
                                <th width="20%" >Tipo</th>
                                <th class="center" width="50%">Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>
                                <td><?php echo $row['nome_fantasia'];?></td>
                                <td><?php echo $row['parceiro_tipo_nome'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm ink-reaction btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Configurações &nbsp; <i class="fa fa-caret-down"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="<?php echo base_url("admin/parceiros_usuarios/view/{$row[$primary_key]}")?>"> Usuários </a></li>
                                            <li><a href="<?php echo base_url("admin/usuarios_acl_tipos/index/0/{$row[$primary_key]}")?>"> Grupo de Acessos </a></li>
                                            <li><a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$row[$primary_key]}")?>"> Produtos </a></li>
                                            <li><a href="<?php echo base_url("admin/parceiros_contatos/view/{$row[$primary_key]}")?>"> Contatos </a></li>
                                            <?php if(verifica_permissao("parceiros_cobranca", "view")) { ?>
                                                <li><a href="<?php echo base_url("admin/parceiros_cobranca/view/{$row[$primary_key]}")?>"> Cobrança </a></li>
                                            <?php } ?>
                                            <li><a href="<?php echo base_url("admin/parceiros_planos/edit/{$row[$primary_key]}")?>"> Planos Habilitados </a></li>
                                        </ul>
                                    </div>

                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                            <?php endforeach; echo $pagination_links?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>