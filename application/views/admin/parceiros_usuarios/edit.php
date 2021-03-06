<?php
if($_POST){
  $row = $_POST;
}
?>
<div class="layout-app" ng-controller="AppController">
  <div class="row row-app">
    <div class="col-md-12">

      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome();?></li>
          <li class="active"><?php echo $page_subtitle;?></li>
        </ol>
      </div>

      <div class="card">
        <div class="card-body">
          <a href="<?php echo base_url("{$current_controller_uri}/view/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
          </a>
          <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Salvar
          </a>
        </div>
      </div>

      <div class="col-separator col-unscrollable bg-none box col-separator-first">
        <div class="col-table">
          <div class="col-table-row">
            <div class="col-app col-unscrollable">
              <div class="col-app">
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                  <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                  <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                  <input type="hidden" name="parceiro_id" value="<?php echo $parceiro_id; ?>"/>
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <?php $this->load->view('admin/partials/validation_errors');?>
                          <?php $this->load->view('admin/partials/messages'); ?>
                        </div>
                      </div>
                      <div class="row innerLR">
                        <div class="col-md-6">
                          <h4>Tipo de Acesso</h4>
                          <hr>
                          <br>

                          <?php $field_name = 'usuario_acl_tipo_id';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nível de acesso *</label>
                            <div class="col-md-8">
                              <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" ng-model="UsuarioACL" ng-change="MontaTela()">
                                <option name="" value="">Selecione</option>
                                <?php
                                if( isset($row) ) {
                                  $usuario_acl_tipo_id = $row["usuario_acl_tipo_id"];
                                } else {
                                  $usuario_acl_tipo_id = "";
                                }
                                foreach($niveis as $linha) { ?>
                                <option name="" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                  <?php echo $linha['nome']; ?>
                                </option>
                                <?php }  ?>
                              </select>
                            </div>
                          </div>

                          <br>
                          <h4>Dados de acesso</h4>
                          <hr>
                          <br>

                          <?php $field_name = 'nome';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Nome/Descrição *</label>
                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>

                          <?php $field_name = 'email';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">E-mail *</label>
                            <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                          </div>

                          <?php $field_name = 'senha';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Senha *</label>
                            <div class="col-md-8">
                              <input class="form-control <?php if($new_record) echo 'required';?>" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="password"  autocomplete="off" />
                              <?php if(!$new_record) :?>
                              <p class="help-block">Deixar campo em branco caso não deseja alterar a senha.</p>
                              <?php endif;?>
                            </div>
                          </div>

                          <?php $field_name = 'token';?>
                          <div class="form-group">
                            <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Token Venda Online (gerado automaticamente)</label>
                            <div class="col-md-8">
                              <input readonly="readonly" class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" />
                            </div>
                          </div>

                          <?php $field_name = 'ativo';?>
                          <div class="form-group ">
                            <div class="radio radio-styled">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Ativo *</label>
                              <label class="radio-inline">
                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                       value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                Sim
                              </label>
                              <label class="radio-inline">
                                <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                       value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                Não
                              </label>
                            </div>
                          </div>
                          
                          <div ng-show="!MontaTelaReduzida">
                            <br>
                            <h4>Dados cadastrais</h4>
                            <hr>
                            <br>

                            <?php $field_name = 'cpf';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">CPF *</label>
                              <div class="col-md-8"><input class="form-control inputmask-cpf" placeholder="___.___.___-__" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                            </div>

                            <?php $field_name = 'colaborador_cargo_id';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Cargo *</label>
                              <div class="col-md-8">
                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                  <option name="" value="">-NENHUM-</option>
                                  <?php
                                  $departamento = '';
                                  foreach($cargos as $linha) : ?>
                                  <?php if($departamento != $linha['colaborador_departamento_id']) :?>
                                  <?php if($departamento != '') : ?>
                                  <?php endif; ?>

                                  <optgroup label="<?php echo $linha['colaborador_departamento_descricao']; ?>">

                                    <?php $departamento = $linha['colaborador_departamento_id']; endif; ?>
                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                            <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                      <?php echo $linha['descricao']; ?>
                                    </option>
                                    <?php endforeach;  ?>
                                    <?php if($departamento != '') : ?>
                                  </optgroup>
                                  <?php endif; ?>
                                </select>
                              </div>
                            </div>

                            <?php $field_name = 'data_nascimento';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Data de nascimento</label>
                              <div class="col-md-8"><input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? app_dateonly_mysql_to_mask($row[$field_name]) : set_value($field_name); ?>" /></div>
                            </div>


                            <br>
                            <h4>Dados bancários</h4>
                            <hr>
                            <br>

                            <?php
                            $field_name = 'banco_id';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Banco do colaborador</label>
                              <div class="col-md-8">
                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                  <option name="" value="">Selecione</option>
                                  <?php foreach($bancos as $linha) { ?>
                                  <option name="" value="<?php echo $linha[$field_name] ?>"
                                          <?php if(isset($row[$field_name])){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                    <?php echo $linha['nome']; ?>
                                  </option>
                                  <?php }  ?>
                                </select>
                              </div>
                            </div>

                            <?php $field_name = 'agencia';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Agência</label>
                              <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                            </div>
                            <?php $field_name = 'conta';?>
                            <div class="form-group">
                              <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Conta do banco</label>
                              <div class="col-md-8"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                            </div>
                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <a href="<?php echo base_url("{$current_controller_uri}/view/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                      </a>
                      <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  AppController.controller("AppController", ['$scope', '$http', '$filter', '$mdDialog', function ( $scope, $http, $filter, $mdDialog ) {
    $scope.UsuarioACL = "<?php echo $usuario_acl_tipo_id ?>";
    $scope.MontaTelaReduzida = false;

    $scope.MontaTela = function() {
      if( typeof $scope.UsuarioACL != typeof undefined && $scope.UsuarioACL == "3" ){
        $scope.MontaTelaReduzida = true;
      } else {
        $scope.MontaTelaReduzida = false;
      }
    }
    
    $scope.MontaTela();
  }]);
</script>
