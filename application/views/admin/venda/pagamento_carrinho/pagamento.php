<?php
if($_POST){
    $row = $_POST;
}

?>
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome(); ?></li>
    </ol>
</div>


<div class="card">
    <div class="card-body">

        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Efetuar Pagamento
        </a>
    </div>
</div>

<div class="card">

    <!-- col-app -->
    <div class="card-body">

            <!-- Form -->
        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($carrossel['produto_parceiro_id'])) echo $carrossel['produto_parceiro_id']; ?>"/>
            <!-- Widget -->


            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors'); ?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th width="40%">PEDIDO</th>
                            <th width="40%">PRODUTO</th>
                            <th width="20%">VALOR</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $valor_total = 0; ?>
                        <?php foreach ($pedidos as $pedido) : ?>
                            <tr>
                                <td><?php echo $pedido['codigo']; ?></td>
                                <td><?php echo $pedido['nome']; ?></td>
                                <td><?php  echo app_format_currency($pedido['valor_total'], false, 2 ); ?></td>
                            </tr>
                            <?php $valor_total += $pedido['valor_total']; ?>

                        <?php endforeach; ?>
                        <tr>
                            <td class="text-right" colspan="2"><strong>TOTAL: </strong></td>
                            <td><?php  echo app_format_currency($valor_total, false, 2 ); ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>

                <!-- Collapsible Widgets -->
            <div class="row">
                <div class="col-md-12">

                    <h4>Formas de Pagamento</h4>


                    <div class="panel-group" id="accordion1">
                    <?php $in = " in"; ?>
                    <?php $expanded = " expanded"; ?>
                    <?php $select = " checked"; ?>

                        <?php foreach ($forma_pagamento as $index => $forma) : ?>
                            <!-- Accordion Item -->
                            <div class="card panel<?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo ' expanded';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $expanded;}} ?>">
                                <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>">
                                    <header>
                                        <div class="radio radio-styled">
                                            <label>
                                                <input type="radio" name="forma_pagamento_tipo_id" value="<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>" <?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo 'checked="checked"';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $select;}} ?> >
                                                <span><?php  echo $forma['tipo']['nome'];  ?></span>
                                            </label>
                                        </div>
                                    </header>
                                    <div class="tools">
                                        <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div id="accordion1-<?php echo $forma['tipo']['forma_pagamento_tipo_id']; ?>" class="collapse <?php if(isset($row['forma_pagamento_tipo_id'])){if($row['forma_pagamento_tipo_id'] == $forma['tipo']['forma_pagamento_tipo_id']) {echo ' in';}} else {if(!isset($row['forma_pagamento_tipo_id'])){ echo $in;}} ?>">
                                    <div class="panel-body">
                                        <?php $this->load->view('admin/venda/pagamento_carrinho/pagamento/'. $forma['tipo']['slug'], array('forma' => $forma));?>
                                    </div>
                                </div>
                            </div>

                            <?php $in = ""; $expanded = ""; $select = "";?>
                        <?php endforeach;  ?>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-body">

        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Efetuar Pagamento
        </a>
    </div>
</div>