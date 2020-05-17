$(function(){
    $('#listaProyecto tbody').on("click","tr", function(){
        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
        var id = $(this)[0].firstChild.innerHTML;
        if (id !== null && id !== undefined){
            if (formName == 'preseje'){
                var rspta = confirm('¿Esta seguro que desea guardar un nuevo Presupuesto de Ejecución con el Proyecto seleccionado?');
                if (rspta){
                    generar_preseje(id);
                }
            } 
            else if (formName == 'residente'){
                var cod = $(this)[0].childNodes[1].innerHTML;
                var des = $(this)[0].childNodes[2].innerHTML;
                var raz = $(this)[0].childNodes[3].innerHTML;
                var sim = $(this)[0].childNodes[4].innerHTML;
                var imp = $(this)[0].childNodes[5].innerHTML;
                var mnd = $(this)[0].childNodes[6].innerHTML;
                // var mnd = $(this)[0].childNodes[7].innerHTML;
                console.log(id);
                $('[name=id_proyecto]').val(id);
                $('[name=codigo]').val(cod);
                $('[name=descripcion]').val(des);
                $('[name=razon_social]').val(raz);
                $('[name=simbolo]').val(sim);
                $('[name=importe]').val(imp);
                // $('[name=cod_preseje]').val(eje);
                $('[name=moneda]').val(mnd);
            }
        }
        $('#modal-proyecto').modal('hide');
    });
});
function listarProyectos(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaProyecto').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_proyectos',
        'columns': [
            {'data': 'id_proyecto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'simbolo'},
            {'data': 'importe'},
            // {'data': 'cod_preseje'},
            {'data': 'moneda'},
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
}
function proyectoModal(){
    $('#modal-proyecto').modal({
        show: true
    });
    clearDataTable();
    listarProyectos();
}
