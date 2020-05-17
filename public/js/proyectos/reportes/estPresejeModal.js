$(function(){
    $('#listaEstructurasPreseje tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        var raz = $(this)[0].childNodes[3].innerHTML;
        var mnd = $(this)[0].childNodes[4].innerHTML;

        console.log(id);
        $('[name=id_presup]').val(id);
        $('[name=cod_preseje]').val(cod);
        $('[name=descripcion]').val(des);
        $('[name=razon_social]').val(raz);
        listar_estructura(id, mnd);
        $('#modal-est_preseje').modal('hide');
    });
});

function listarEstructurasPreseje(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaEstructurasPreseje').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_estructuras_preseje',
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'simbolo'},
            // {'render': 
            //     function (data, type, row){
            //         return (formatNumber.decimal(row['sub_total'],'',-2));
            //     }, className: 'text-right'
            // }
        ],
        'columnDefs': [{ 'aTargets': [0,4], 'sClass': 'invisible'}],
    });
}

function estPresejeModal(){
    $('#modal-est_preseje').modal({
        show: true
    });
    listarEstructurasPreseje();
}
