<?php
if($_POST)
    $row = $_POST;
?>
<div class="col-md-6 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">

    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
        <option name="" value="">Selecione</option>
        <?php

        foreach($list[$field_name] as $linha) { ?>
            <option name="" value="<?php echo $linha['localidade_id'] ?>"
                <?php if(isset($row[$field_name] ) &&  $row[$field_name] == $linha['localidade_id']) {echo " selected ";} else {echo set_select($field_name, $linha['localidade_id'] );}  ?>>
                <?php echo $linha['nome']; ?>
            </option>
        <?php }  ?>
    </select>
    <?php echo app_get_form_error($field_name); ?>
    <label class=" control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
</div>
