<?php if ($layout != "front") { ?>
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
        <a class="pull-right btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>
    </div>
</div>

<?php } ?>

<div class="card">
    <div class="card-body">
        <!-- Form -->
        <form class="form" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $primary_key ?>" id="<?php echo $primary_key ?>" value="<?php if (isset($produto_parceiro_id)) echo $produto_parceiro_id; ?>"/>
            <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
            <input type="hidden" id="url_busca_cliente"  name="url_busca_cliente" value="<?php echo base_url("{$current_controller_uri}/get_cliente"); ?>"/>
            <input type="hidden" id="lista_id"  name="lista_id" value="<?php echo emptyor($lista_id, 1); ?>"/>

            <div class="row">
                <div class="col-md-6">
                    <?php // $this->load->view('admin/partials/validation_errors');?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>
            </div>

            <!-- Column -->
            <div class="col-md-12">

                <h2 class="text-light text-center"><?php echo app_produto_traducao('Dados iniciais da Cotação', $produto_parceiro_id); ?><br><small class="text-primary"><?php echo app_produto_traducao('Informe os dados pessoais para iniciar a cotação', $produto_parceiro_id); ?></small></h2>

                <?php $this->load->view('admin/venda/step', array('step' => 1 ,'produto_parceiro_id' =>  issetor($produto_parceiro_id))); ?>

                <?php $this->load->view('admin/campos_sistema/lista_campos'); ?>

            </div>

        </form>
    </div>
</div>

<!-- // Widget END -->
<div class="card">
    <div class="card-body">
        <?php if ($layout == "base") { ?>
            <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>

            <a href="<?php echo base_url("admin/clientes/view/")?>" class="btn  btn-app btn-primary btn-detalhes disabled ls-modal">
                <i class="fa fa-chain"></i> Detalhes do Cliente
            </a>
        <?php } ?>

        <a class="pull-right btn btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-arrow-right"></i> Próximo
        </a>

    </div>
</div>

<div id="detalhe-cliente" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Detalhes do Cliente</h4>
            </div>
            <div class="modal-body">
                <p>Carregando...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>