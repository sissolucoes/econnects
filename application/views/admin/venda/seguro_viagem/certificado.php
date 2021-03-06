<?php
if($_POST)
    $row = $_POST;
?>

<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                    <?php if ($layout != "front") {  ?>

                    <h4 class="innerAll margin-none bg-white"><?php echo app_recurso_nome();?></h4>

                    <div class="col-separator-h"></div>

                    <div class="card">
                        <div class="card-body">
                            <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                    <div class="col-separator-h"></div>

                    <?php } ?>

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
                                    <input type="hidden" name="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>"/>
                                    <!-- Widget -->
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?php $this->load->view('admin/partials/validation_errors');?>
                                                    <?php $this->load->view('admin/partials/messages'); ?>
                                                </div>
                                            </div>
                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <!-- Column -->
                                                <div class="col-md-12">


                                                    <h2 class="text-light text-center">Emissão dos Certificados<br>
                                                        <small class="text-primary">Efetue download dos certificados</small>
                                                    </h2>

                                                    <?php $this->load->view('admin/venda/step', array('step' => 5, 'produto_parceiro_id' =>  issetor($produto_parceiro_id))); ?>

                                                    <div class="col-md-12">

                                                        <!-- Table -->
                                                        <table class="table table-hover">

                                                            <!-- Table heading -->
                                                            <thead>
                                                            <tr>
                                                                <th class="center">Bilhete</th>
                                                                <th width='65%'>Nome</th>
                                                                <th width='65%'>CPF</th>
                                                                <th class="center" width='25%'>Ações</th>
                                                            </tr>
                                                            </thead>
                                                            <!-- // Table heading END -->

                                                            <!-- Table body -->
                                                            <tbody>

                                                            <!-- Table row -->
                                                            <?php foreach($apolice as $row) :?>
                                                                <tr>

                                                                    <td class="center"><?php echo $row['num_apolice'];?></td>
                                                                    <td><?php echo $row['nome'];?></td>
                                                                    <td><?php echo (app_verifica_cpf_cnpj($row['cnpj_cpf']) == 'CPF') ? app_cpf_to_mask($row['cnpj_cpf']) : app_cnpj_to_mask($row['cnpj_cpf']); ?></td>
                                                                    <td class="center">
                                                                        <a target="_blank" href="<?php echo base_url("{$current_controller_uri}/certificado/{$row['apolice_id']}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-print"></i>  Imprimir </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach;?>
                                                            <!-- // Table row END -->

                                                            </tbody>
                                                            <!-- // Table body END -->

                                                        </table>
                                                        <!-- // Table END -->

                                                    </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->
                                            <div class="separator"></div>
                                        </div>


                                    </div>

                                    <?php if ($layout != "front") {  ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-separator-h"></div>
                                    <?php } ?>
                                    <!-- // Widget END -->
                                </form>
                                <!-- // Form END -->
                            </div>
                            <!-- // END col-app -->
                        </div>
                        <!-- // END col-app.col-unscrollable -->
                    </div>
                    <!-- // END col-table-row -->
                </div>
                <!-- // END col-table -->
            </div>
            <!-- // END col-separator.box -->
        </div>
    </div>
</div>