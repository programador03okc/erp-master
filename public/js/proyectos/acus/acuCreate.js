function open_acu_create(){
    $('#modal-acu_create').modal({
        show: true
    });
    $('[name=id_cu]').val('');
    $('[name=id_categoria]').val('');
    $('[name=cu_descripcion]').val('');
    $('[name=observacion]').val('');
}
function edit_acu_create(data){
    $('#modal-acu_create').modal({
        show: true
    });
    $('[name=id_cu]').val(data.id_cu);
    $('[name=id_categoria]').val(data.id_categoria);
    $('[name=cu_descripcion]').val(data.descripcion);
    $('[name=observacion]').val(data.observacion);
}
$(function(){
    $("#form-acu_create").on("submit", function(){
        var data = $(this).serialize();
        console.log(data);
        var id = $('[name=id_cu]').val();
        var url = '';
        var msj = '';
        if (id !== ''){
            url = 'update_cu';
            msj = 'Nombre del Costo Unitario actualizado con éxito!';
        } else {
            url = 'guardar_cu';
            msj = 'Nombre del Costo Unitario creado con éxito!';
        }
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['id_cu'] > 0){
                    alert(msj);
                    $('#modal-acu_create').modal('hide');
                    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
                    if (formName =='cu'){
                        $('#listaCu').DataTable().ajax.reload();
                    } else if (formName =='presint' || formName =='preseje'){
                        $('[name=id_cu]').val(response['cu'].id_cu);
                        $('[name=cod_acu]').val(response['cu'].codigo);
                        $('[name=des_acu]').val(response['cu'].descripcion);
                    }
                } else {
                    alert('Ya existe dicha Descripción!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        return false;
    });
});