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
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <!-- Widget -->
                <div class="card">

                    <div class="card-body">


                        <!-- Table -->
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="center">ID</th>
                                    <th style="width: 40%">Nome</th>
                                    <th style="width: 40%">Cidade</th>
                                    <th class="center" style="width: 20%">Ações</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach($rows as $row) :?>
                                <tr>
                                    <td class="center"><?php echo $row[$primary_key];?></td>
                                    <td><?php echo $row['nome'];?></td>
                                    <td><?php echo $this->cidades->getNome($row['localidade_cidade_id']);?></td>
                                    <td class="center">
                                        <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                        <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                        <!-- // Table END -->
                        <?php echo $pagination_links;?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>