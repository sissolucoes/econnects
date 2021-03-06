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
      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome();?></li>
        </ol>

      </div>

      <div class="card">

        <!-- Widget heading -->
        <div class="card-body">

          <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
          </a>
          <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Salvar
          </a>
        </div>

      </div>

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
                  <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                  <!-- Widget -->
                  <div class="row">
                    <div class="col-md-6">
                      <?php $this->load->view('admin/partials/validation_errors');?>
                      <?php $this->load->view('admin/partials/messages'); ?>
                    </div>

                  </div>

                  <div class="card">


                    <?php  $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>

                    <div class="card-body tab-content">

                      <!-- Row -->
                      <div class="row innerLR">

                        <div class="relativeWrap">
                          <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                            <!-- Tabs Heading -->

                            <!-- // Tabs Heading END -->

                            <div class="widget-body">
                              <div class="tab-content">
                                <div class="col-md-12">
                                  <div class="card tabs-left style-default-light">
                                    <!-- Tab content -->
                                    <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_regra_negocio');?>
                                    <div class="card-body tab-content style-default-bright">
                                      <div id="tabGeral" class="tab-pane active widget-body-regular">

                                        <?php $field_name = 'markup';?>
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Markup(%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="markup" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>
                                        <?php $field_name = 'comissao';?>
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão(%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="comissao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>
                                        <?php $field_name = 'comissao_indicacao';?>
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão Indicação(%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="comissao_indicacao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>

                                        <?php $field_name = 'repasse_comissao';?>
                                        <div class="radio radio-styled">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse comissão *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                   value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                   value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>

                                        <?php $field_name = 'repasse_maximo';?>
                                        <div class="form-group repasse_maximo">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse Máximo(%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="repasse_maximo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>
                                        <br>
                                        <hr>
                                        <br>


                                        <?php $field_name = 'padrao_comissao';?>
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão (Padrão) (%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="padrao_comissao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>

                                        <?php $field_name = 'padrao_comissao_indicacao';?>
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Comissão Indicação (Padrão) (%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="padrao_comissao_indicacao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>

                                        <?php $field_name = 'padrao_repasse_comissao';?>
                                        <div class="radio radio-styled">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse comissão (Padrão) *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                   value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                   value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>

                                        <?php $field_name = 'padrao_repasse_maximo';?>
                                        <div class="form-group padrao_repasse_maximo">
                                          <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Repasse Máximo (Padrão) (%) *</label>
                                          <div class="col-md-4">
                                            <input ng-model="padrao_repasse_maximo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
                                          </div>
                                        </div>

                                      </div>
                                    </div>

                                  </div>
                                </div>

                              </div>
                            </div>

                          </div>
                        </div>

                      </div>
                      <!-- // Row END -->

                    </div>
                  </div>
                  <!-- // Widget END -->
                  <!-- Widget heading -->
                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">

                      <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
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
    $scope.markup = parseFloat( "<?php echo isset($row['markup']) ? $row['markup'] : '0'; ?>" );
    
    $scope.comissao = parseFloat( "<?php echo isset($row['comissao']) ? $row['comissao'] : '0'; ?>" );
    $scope.comissao_indicacao = parseFloat( "<?php echo isset($row['comissao_indicacao']) ? $row['comissao_indicacao'] : '0'; ?>" );
    $scope.repasse_maximo = parseFloat( "<?php echo isset($row['repasse_maximo']) ? $row['repasse_maximo'] : '0'; ?>" );
    
    $scope.padrao_comissao = parseFloat( "<?php echo isset($row['padrao_comissao']) ? $row['padrao_comissao'] : '0'; ?>" );
    $scope.padrao_comissao_indicacao = parseFloat( "<?php echo isset($row['padrao_comissao_indicacao']) ? $row['padrao_comissao_indicacao'] : '0'; ?>" );
    $scope.padrao_repasse_maximo = parseFloat( "<?php echo isset($row['padrao_repasse_maximo']) ? $row['padrao_repasse_maximo'] : '0'; ?>" );
    
  }]);
</script>
