$(function(){
    $('#listaProyectoContrato tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        var nro = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;
        var raz = $(this)[0].childNodes[4].innerHTML;
        var sim = $(this)[0].childNodes[5].innerHTML;
        var imp = $(this)[0].childNodes[6].innerHTML;
        var cod = $(this)[0].childNodes[7].innerHTML;
        console.log(id);
        $('[name=id_contrato]').val(id);
        $('[name=nro_contrato]').val(nro);
        $('[name=descripcion]').val(des);
        $('[name=razon_social]').val(raz);
        $('[name=simbolo]').val(sim);
        $('[name=importe]').val(imp);
        $('[name=cod_preseje]').val(cod);
        $('#modal-proyecto_contrato').modal('hide');
    });
});
function listarContratos(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaProyectoContrato').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_proyectos_contratos',
        'columns': [
            {'data': 'id_contrato'},
            {'data': 'nro_contrato'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_contrato']));
                }
            },
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'simbolo'},
            {'data': 'importe'},
            {'data': 'cod_preseje'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function contratoModal(){
    $('#modal-proyecto_contrato').modal({
        show: true
    });
    listarContratos();
}
