function salvarOrdem()
{
    var i = 0;
    var a = new Array();
    $("#tabela-ordem > tbody > tr").each(function(index ) {
        a[i] = new Array();
        a[i][0] = $(this).data('id');
        a[i][1] = $(this).data('ordem');
        i++;
    });


    url = $('#url_ordem').val();
    console.log(url);

        $.ajax({
                type: "POST",
                url: url,
                cache: false,
                data: {itens: a}
            })
            .done(function( result ) {
                location.reload();
            });

}



$(document).ready(function(){

    $('#tabela-ordem').tableDnD({
        onDragClass: "drag",
    });


    $(".salvar-ordem").click(function () {
        salvarOrdem();

    });
    $(".campo_tipo").change(function () {

        url = $('#url_tipo').val() + '/' + $(".campo_tipo").val();
        console.log(url);
        document.location.href = url;
    });
});




