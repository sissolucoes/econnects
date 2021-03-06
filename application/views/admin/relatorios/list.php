
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>

<!-- Widget -->
<div class="card">

    <div class="card-head style-primary">
        <header>Relatório de Vendas</header>
    </div>

    <div class="card-body">

        <p>Selecione uma data inicial e final para resgatar os registros. Arreste os campos para as linhas, colunas ou dados.</p>

        <div class="row">
            <div class="col-md-12">
                <?php $field_name = "data_inicio"; $field_label = "Data inicial: " ?>
                <div class="col-md-3 col-sm-4 form-group">
                    <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                    <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_inicio ?>" />
                </div>

                <?php $field_name = "data_fim"; $field_label = "Data final: " ?>
                <div class="col-md-3 col-sm-4 form-group">
                    <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
                    <input class="form-control inputmask-date" placeholder="__/__/____" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?= $data_fim ?>" />
                </div>

                <div class="col-md-2 col-sm-4">
                    <button id="btnFiltro" class="btn btn-primary btnFiltrarResultadoRelatorios"><i class="fa fa-search"> </i>  Filtrar dados</button>
                    <input type="hidden" name="action" id="action" value="<?= issetor($action, '') ?>">
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="relatorio_container"></div>
            </div>
        </div>




    </div>
</div>
<div id="processando" style="display:none; width: 100%; height: 100%; text-align: center; position: absolute; left: 0px; top: 0px; z-index: 1000; background-color: #CCC; opacity: 0.95;" aria-hidden="true">
    <div style="color: #191A1A; position: relative; font-size: 192px; top: 30%; z-index: 999"><i class="fa fa-gear fa-spin" aria-hidden="true"></i></div>
</div>