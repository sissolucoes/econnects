<?php
if ($_POST)
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
          <li class="active"><?php echo app_recurso_nome(); ?></li>
        </ol>
      </div>

      <div class="card">
        <div class="card-body">
          <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$parceiro_id}") ?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
          </a>
          <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Salvar
          </a>
        </div>

      </div>
      <div class="col-separator col-unscrollable bg-none box col-separator-first">

        <div class="col-table">


          <!-- col-table-row -->
          <div class="col-table-row">

            <!-- col-app -->
            <div class="col-app col-unscrollable">

              <!-- col-app -->
              <div class="col-app">

                <!-- Form -->
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                  <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>" />
                  <input type="hidden" name="new_record" value="<?php echo $new_record; ?>" />
                  <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>" />
                  <!-- Widget -->
                  <div class="row">
                    <div class="col-md-6">
                      <?php $this->load->view('admin/partials/validation_errors'); ?>
                      <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                  </div>

                  <div class="card">

                    <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao'); ?>

                    <!-- // Widget heading END -->

                    <div class="card-body">
                      <!-- Row -->
                      <div class="row innerLR">

                        <div class="relativeWrap">
                          <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                            <!-- Tabs Heading -->

                            <!-- // Tabs Heading END -->

                            <div class="widget-body">
                              <div class="col-md-12">
                                <div class="card tabs-left style-default-light">
                                  <!-- Tab content -->
                                  <?php $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_regra_negocio'); ?>
                                  <div class="card-body tab-content style-default-bright">

                                    <div class="tab-content">

                                      <!-- Tab content -->
                                      <div id="tabCancelamento" class="tab-pane active widget-body-regular">
                                        <h4>Cancelamento</h4>
                                        <hr>
                                        <br>
                                        <?php $field_name = 'cancel_via_admin'; ?>
                                        <div class="form-group">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Cancelar Via Admin *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if ((isset($row[$field_name]) && $row[$field_name] == '0') || empty($row[$field_name])) echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>
                                        <hr>
                                        <?php $field_name = 'indenizacao_hab'; ?>
                                        <div class="form-group">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Reembolso *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>

                                        <hr>

                                        <?php $field_name = 'seg_antes_hab'; ?>
                                        <div class="form-group">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Antes do início da vigência *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>
                                        <?php $field_name = 'seg_antes_dias'; ?>
                                        <div class="form-group antes_habilitado">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Quantidade máxima de dias</label>
                                          <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                        </div>
                                        <?php $field_name = 'seg_antes_calculo'; ?>
                                        <div class="form-group antes_habilitado">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Forma de cálculo da Penalidade *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="PORCENTAGEM" <?php if (isset($row[$field_name]) && $row[$field_name] == 'PORCENTAGEM') echo 'checked="checked"'; ?> />
                                            Porcentagem (%)
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="MONETARIO" <?php if (isset($row[$field_name]) && $row[$field_name] == 'MONETARIO') echo 'checked="checked"'; ?> />
                                            Monetário (R$)
                                          </label>
                                        </div>
                                        <?php $field_name = 'seg_antes_valor'; ?>
                                        <div class="form-group antes_habilitado">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Valor</label>
                                          <div class="col-md-2">
                                            <input ng-model="seg_antes_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" />
                                          </div>
                                        </div>
                                        <hr>
                                        <?php $field_name = 'seg_depois_hab';
                                        $seg_depois_hab = "";
                                        if (isset($row)) {
                                          $seg_depois_hab = $row["seg_depois_hab"];
                                        } ?>
                                        <div class="form-group">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Depois do início da vigência *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input ng-model="seg_depois_hab" type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label ng-model="seg_depois_hab" class="radio-inline radio-styled radio-primary">
                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>
                                        <fieldset ng-show="seg_depois_hab=='1'">
                                          <?php $field_name = 'seg_depois_dias'; ?>
                                          <div class="form-group">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Quantidade máxima de dias</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>

                                          <?php $field_name = 'seg_depois_dias_carencia'; ?>
                                          <div class="form-group">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Carência de dias p/ utilização do cálculo</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>

                                          <?php $field_name = 'calculo_tipo'; ?>
                                          <div class="form-group">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Forma de Cálculo *</label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="T" <?php if (isset($row[$field_name]) && $row[$field_name] == 'T') echo 'checked="checked"'; ?> />
                                              Tabela prazo curto
                                            </label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="P" <?php if (isset($row[$field_name]) && $row[$field_name] == 'P') echo 'checked="checked"'; ?> />
                                              Pro-rata
                                            </label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="E" <?php if (isset($row[$field_name]) && $row[$field_name] == 'E') echo 'checked="checked"'; ?> />
                                              Específico
                                            </label>

                                          </div>

                                          <fieldset class="cancelamento_especial">
                                            <?php $field_name = 'seg_depois_calculo'; ?>
                                            <div class="form-group">
                                              <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Forma de cálculo da Penalidade *</label>
                                              <label class="radio-inline radio-styled radio-primary">
                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="PORCENTAGEM" <?php if (isset($row[$field_name]) && $row[$field_name] == 'PORCENTAGEM') echo 'checked="checked"'; ?> />
                                                Porcentagem (%)
                                              </label>
                                              <label class="radio-inline radio-styled radio-primary">
                                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="MONETARIO" <?php if (isset($row[$field_name]) && $row[$field_name] == 'MONETARIO') echo 'checked="checked"'; ?> />
                                                Monetário (R$)
                                              </label>
                                            </div>
                                            <?php $field_name = 'seg_depois_valor'; ?>
                                            <div class="form-group">
                                              <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Valor</label>
                                              <div class="col-md-2">
                                                <input ng-model="seg_depois_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" />
                                              </div>
                                            </div>
                                          </fieldset>
                                        </fieldset>
                                        <hr>
                                        <?php $field_name = 'inad_hab';
                                        $inad_hab = "";
                                        if (isset($row)) {
                                          $inad_hab = $row["inad_hab"];
                                        } ?>
                                        <div class="form-group">
                                          <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Por inadimplência *</label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input ng-model="inad_hab" type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                            Sim
                                          </label>
                                          <label class="radio-inline radio-styled radio-primary">
                                            <input ng-model="inad_hab" type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                            Não
                                          </label>
                                        </div>
                                        <fieldset ng-show="inad_hab=='1'">
                                          <?php $field_name = 'inad_max_dias'; ?>
                                          <div class="form-group inadimplencia_habilitado">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Quantidade máxima de dias em aberto</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>
                                          <?php $field_name = 'inad_max_parcela'; ?>
                                          <div class="form-group inadimplencia_habilitado">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Quantidade máxima de parcelas em aberto</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>

                                          <?php $field_name = 'inad_reativacao_hab'; ?>
                                          <div class="form-group">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Permite reativar *</label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                              Sim
                                            </label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                              Não
                                            </label>
                                          </div>
                                          <?php $field_name = 'inad_reativacao_max_dias'; ?>
                                          <div class="form-group inadimplencia_reativacao">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">No máximo quantos dias depois de cancelado</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>

                                          <?php $field_name = 'inad_reativacao_max_parcela'; ?>
                                          <div class="form-group inadimplencia_reativacao">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">No máximo quantas parcelas depois de cancelado</label>
                                            <div class="col-md-2"><input class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : $codigo; ?>" /></div>
                                          </div>



                                          <?php $field_name = 'inad_reativacao_calculo'; ?>
                                          <div class="form-group inadimplencia_reativacao">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Forma de cálculo da Penalidade *</label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="PORCENTAGEM" <?php if (isset($row[$field_name]) && $row[$field_name] == 'PORCENTAGEM') echo 'checked="checked"'; ?> />
                                              Porcentagem (%)
                                            </label>
                                            <label class="radio-inline radio-styled radio-primary">
                                              <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled" value="MONETARIO" <?php if (isset($row[$field_name]) && $row[$field_name] == 'MONETARIO') echo 'checked="checked"'; ?> />
                                              Monetário (R$)
                                            </label>
                                          </div>
                                          <?php $field_name = 'inad_reativacao_valor'; ?>
                                          <div class="form-group inadimplencia_reativacao">
                                            <label class="col-md-2 control-label" for="<?php echo $field_name; ?>">Valor</label>
                                            <div class="col-md-2">
                                              <input ng-model="inad_reativacao_valor" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" />
                                            </div>
                                          </div>
                                        </fieldset>
                                      </div>
                                      <!-- End Tab content -->

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

                  <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                      <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$parceiro_id}") ?>" class="btn  btn-app btn-primary">
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
  AppController.controller("AppController", ['$scope', '$http', '$filter', '$mdDialog', function($scope, $http, $filter, $mdDialog) {
    $scope.seg_depois_hab = "<?php echo $seg_depois_hab; ?>";
    $scope.inad_hab = "<?php echo $inad_hab; ?>";

    $scope.seg_antes_valor = parseFloat("<?php echo isset($row['seg_antes_valor']) ? $row['seg_antes_valor'] : '0'; ?>");
    $scope.seg_depois_valor = parseFloat("<?php echo isset($row['seg_depois_valor']) ? $row['seg_depois_valor'] : '0'; ?>");
    $scope.inad_reativacao_valor = parseFloat("<?php echo isset($row['inad_reativacao_valor']) ? $row['inad_reativacao_valor'] : '0'; ?>");
  }]);
</script>