<?php
if($_POST)
    $row = $_POST;
?>

<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$cliente_id}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <!-- col-separator.box -->
            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                    <!-- Widget -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <h4 class="text-primary"><?php echo $page_subtitle;?></h4>
                                        </div>
                                        <!-- // Widget heading END -->

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
                                                <div class="col-md-6">
                                                    <?php $field_name = 'nome';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome *</label>
                                                        <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'cliente_contato_nivel_relacionamento_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Relacionamento *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php 
                                                                
                                                                foreach($nivel_relacionamento as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>" 
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['descricao']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select> 
                                                        </div>
                                                    </div>

                                                    <?php if($tipo_cliente == 'CO'):?>

                                                        <?php $field_name = 'cliente_contato_departamento_id';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Departamento</label>
                                                            <div class="col-md-8">
                                                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <option name="" value="">Selecione</option>
                                                                    <?php

                                                                    foreach($departamentos as $linha) { ?>
                                                                        <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                            <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                            <?php echo $linha['nome']; ?>
                                                                        </option>
                                                                    <?php }  ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <?php $field_name = 'cliente_contato_cargo_id';?>
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Cargo</label>
                                                            <div class="col-md-8">
                                                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                    <option name="" value="">Selecione</option>
                                                                    <?php

                                                                    foreach($cargos as $linha) { ?>
                                                                        <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                            <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                            <?php echo $linha['nome']; ?>
                                                                        </option>
                                                                    <?php }  ?>
                                                                </select>
                                                            </div>
                                                        </div>


                                                    <?php endif;?>

                                                    <?php $field_name = 'contato_tipo_id';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de contato *</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control tipo_contato" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                <option name="" value="">Selecione</option>
                                                                <?php

                                                                foreach($contato_tipo as $linha) { ?>
                                                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                        <?php echo $linha['nome']; ?>
                                                                    </option>
                                                                <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <?php $field_name = 'contato';?>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Contato *</label>
                                                        <div class="col-md-8"><input class="form-control contato" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                    </div>

                                                    <?php $field_name = 'decisor';?>
                                                        <div class="form-group">
                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Decisor *</label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                Sim
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="<?php echo $field_name; ?>" class="required styled"
                                                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                Não
                                                            </label>
                                                        </div>
                                                </div>
                                                <!-- // Column END -->
                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$cliente_id}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                <i class="fa fa-edit"></i> Salvar
                                            </a>
                                        </div>

                                    </div>
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