function listarAcusPartidas(tp){
    var vardataTables = funcDatatables();
    var tabla = $('#listaAcuPartida').DataTable({
        'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'buttons': [
            {
                text: "Crear A.C.U.",
                className: 'btn btn-warning',
                action: function(){
                    open_acu_partida_create(undefined);
                }
            }
        ],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_acus_sin_presup',
        'columns': [
            {'data': 'id_cu_partida'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'rendimiento'},
            {'data': 'total'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'initComplete': function () {
            $('#listaAcu_filter label input').focus();
        }
    });
    $('#listaAcuPartida tbody').on("click","tr", function(){
        var data = tabla.row($(this)).data();
        console.log(data);
        console.log(tp);
        if (data !== undefined){
            if (tp=='cd'){
                var filas = document.querySelectorAll('#listaAcusCD tbody tr');
                var existe = false;
                filas.forEach(function(e){
                    var colum = e.querySelectorAll('td');
                    if (colum[0].innerText == data.codigo){
                        existe = true;
                    }
                });
                console.log(existe);
                if (!existe){
                    var mnd = $('[name=moneda]').val();
                    console.log('moneda: '+mnd);
                    var precio = 0;
                    if (mnd !== '1'){
                        var tpc = $('[name=tipo_cambio]').val();
                        console.log('tc: '+tpc);
                        precio = parseFloat(data.total) * parseFloat(tpc);
                    } else {
                        precio = parseFloat(data.total);
                    }
                    console.log(data);
                    $('[name=id_cu_partida_cd]').val(data.id_cu_partida);
                    $('[name=cod_cu]').val(data.codigo);
                    $('[name=des_cu]').val(data.descripcion);
                    $('[name=precio_unitario]').val(precio);
                    $('[name=unid_medida]').val(data.abreviatura);
                    $('[name=id_unid_medida]').val(data.unid_medida);
                    $('[name=cantidad]').focus();
                    $('#modal-acu_partida').modal('hide');
                } else {
                    alert('El A.C.U. seleccionado ya existe en la Lista!');
                }
            } 
            else if (tp=='ci'){
                $('[name=id_cu_ci]').val(data.id_cu_partida);
                $('[name=cod_acu_ci]').val(data.codigo);
                $('[name=des_acu_ci]').val(data.descripcion);
                $('[name=precio_unitario_ci]').val(data.total);
                $('[name=unid_medida_ci]').val(data.unid_medida);
                $('#modal-acu_partida').modal('hide');
            } 
            else if (tp=='gg'){
                $('[name=id_cu_gg]').val(data.id_cu_partida);
                $('[name=cod_acu_gg]').val(data.codigo);
                $('[name=des_acu_gg]').val(data.descripcion);
                $('[name=precio_unitario_gg]').val(data.total);
                $('[name=unid_medida_gg]').val(data.unid_medida);
                $('#modal-acu_partida').modal('hide');
            }
        }
    });
}
function acuPartidaModal(tp){
    var id = $('[name=id_presupuesto]').val();
    if (id !== '' && id !== null){
        $('#modal-acu_partida').modal({
            show: true
        });
        console.log('tp: '+tp);
        // $("#listaAcuPartida").dataTable().fnDestroy();
        clearDataTable();
        listarAcusPartidas(tp);
    } else {
        alert('Debe seleccionar un Presupuesto');
    }
    
}
