<?php
if($_POST){
    $row = $_POST;
}

?>
<div class="row" id="pagamento-credito">
    <?php if(isset($forma['pagamento'])) : ?>
        <input type="hidden" name="bandeira" value="<?php echo $forma['pagamento'][0]['produto_parceiro_pagamento_id']; ?>">
    <?php endif; ?>
    <div class="col-md-6">

        <div class="form-group<?php echo (app_is_form_error('numero')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('nome_cartao')) ? ' has-error' : ''; ?>">

            <div class="col-md-6">
                <h5>Número do Cartão</h5>
                <input class="form-control" placeholder="Número do Cartão" id="numero" name="numero" type="text" value="<?php echo isset($row['numero']) ? $row['numero'] : set_value('numero'); ?>" />
                <?php echo app_get_form_error('numero'); ?>
            </div>

            <div class="col-md-6">
                <h5>Nome (Como no cartão)</h5>
                <input placeholder="Nome (Como no cartão)" class="form-control" id="nome_cartao" name="nome_cartao" type="text" value="<?php echo isset($row['nome_cartao']) ? $row['nome_cartao'] : set_value('nome_cartao'); ?>" />
                <?php echo app_get_form_error('nome_cartao'); ?>
            </div>
        </div>

        <div class="form-group<?php echo (app_is_form_error('validade')) ? ' has-error' : ''; ?><?php echo (app_is_form_error('codigo')) ? ' has-error' : ''; ?>">
            <div class="col-md-6">
                <h5>Validade (MM/AAAA)</h5>
                <input class="form-control" placeholder="Validade (MM/AAAA)" id="validade" name="validade" type="text" value="<?php echo isset($row['validade']) ? $row['validade'] : set_value('validade'); ?>" />
                <?php echo app_get_form_error('validade'); ?>
            </div>
            <div class="col-md-6">
                <h5>Código</h5>
                <input placeholder="Código" class="form-control" id="codigo" name="codigo" type="text" value="<?php echo isset($row['codigo']) ? $row['codigo'] : set_value('codigo'); ?>" />
                <?php echo app_get_form_error('codigo'); ?>
            </div>
        </div>

        <div class="form-group<?php echo (app_is_form_error('dia_vencimento')) ? ' has-error' : ''; ?>">


            <?php if(($produto_parceiro_configuracao['pagamento_tipo'] == 'RECORRENTE') && ($produto_parceiro_configuracao['pagmaneto_cobranca'] == 'VENCIMENTO_CARTAO ') ) { ?>
                <div class="col-md-6">
                    <h5>Dia do vencimento</h5>
                    <input placeholder="Dia do vencimento" class="form-control" id="dia_vencimento" name="dia_vencimento" type="number" value="<?php echo isset($row['dia_vencimento']) ? $row['dia_vencimento'] : set_value('dia_vencimento'); ?>" />
                    <?php echo app_get_form_error('dia_vencimento'); ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="col-md-6">

        <div class="form-group">

            <div class="col-md-12">
                <div class="card-wrapper"></div>
            </div>
        </div>

    </div>

    <div class="col-md-12">
        <?php $hd = "";  ?>
        <?php foreach ($forma['pagamento'] as $bandeira) : ?>
            <?php $field_name = "parcelamento_{$bandeira['produto_parceiro_pagamento_id']}";?>
            <div <?php echo $hd; ?> class="form-group parcelamento parcelamento_<?php echo $bandeira['produto_parceiro_pagamento_id']; ?>">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parcelamento *</label>
                <div class="col-md-4">
                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                        <?php foreach($bandeira['parcelamento'] as $parcela => $linha) : ?>
                            <option name="" value="<?php echo $parcela; ?>"
                                <?php if(isset($row[$field_name])){if($row[$field_name] == $parcela) {echo " selected ";};}; ?> >
                                <?php echo $linha; ?>
                            </option>
                        <?php endforeach;  ?>
                    </select>
                </div>
            </div>
            <?php $hd = 'style="display: none;"';  ?>
        <?php endforeach; ?>
    </div>

</div>