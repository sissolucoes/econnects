<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tbody>
        <tr>            
            <td class="table-cell-field"><b>Descri&ccedil;&atilde;o</b></td>
            <td class="table-cell-field"><b>LMI</b></td>
            <td class="table-cell-field"><b>Franquia</b></td>
            <td class="table-cell-field"><b>Carência</b></td>            
            <td class="table-cell-field"><b>Prêmio Bruto</b></td>
            <td class="table-cell-field"><b>IOF</b></td>
            <td class="table-cell-field td-last"><b>Prêmio Liquido</b></td>
        </tr>
        <?php foreach ($coberturas_all as $i => $cobertura): ?>        
            <tr>
                <td class="table-cell-field"><b><?= $cobertura['cobertura_nome']; ?></b></td>
                <td class="table-cell-field">R$ <?= app_format_currency($cobertura['importancia_segurada']); ?></td>            
                <td class="table-cell-field"><?= isempty($cobertura['franquia'], 'Não Há'); ?></td>
                <td class="table-cell-field"><?= isempty($cobertura['carencia'], 'Não Há'); ?></td>
                <td class="table-cell-field">R$ <?= app_format_currency($cobertura['premio_liquido_total']); ?></td>            
                <td class="table-cell-field">R$ <?= app_format_currency($cobertura['iof']); ?></td>            
                <td class="table-cell-field td-last">R$ <?= app_format_currency($cobertura['premio_liquido']); ?></td>                            
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

