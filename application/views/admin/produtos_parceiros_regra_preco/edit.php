<?php
if($_POST)
  $row = $_POST;
?>
<div class="layout-app" ng-controller="AppController">
  <!-- row -->
  <div class="row row-app">
    <!-- col -->
    <div class="col-md-12">
      <!-- col-separator.box -->
      <div class="col-separator col-unscrollable bg-none box col-separator-first">
        <div class="section-header">
          <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?></li>
            <li class="active"><?php echo $page_subtitle;?></li>
          </ol>

        </div>

        <div class="card">

          <!-- Widget heading -->
          <div class="card-body">
            <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
              <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
              <i class="fa fa-edit"></i> Salvar
            </a>
          </div>

        </div>
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
                  <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
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


                          <?php $field_name = 'regra_preco_id';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Regra *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <?php
                                foreach($regras as $linha) { ?>
                                <option name="" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <?php $field_name = 'parametros';?>
                          <div class="form-group">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parametros</label>
                            <div class="col-md-8">
                              <input <?php if( strtoupper($row["regra_preco_id"])==2){ echo 'ui-number-mask'; }?> ng-model="parametros" class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                            </div>
                          </div>



                        </div>
                        <!-- // Column END -->


                      </div>
                      <!-- // Row END -->

                    </div>
                  </div>
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                      <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
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
<script>
  AppController.controller("AppController", ["$scope", "$sce", "$http", "$filter", "$timeout", "$interval", function ( $scope, $sce, $http, $filter, $timeout, $interval ) {
    $scope.parametros = parseFloat( "<?php echo isset($row['parametros']) ? $row['parametros'] : '0'; ?>" );
  }]);
</script>
