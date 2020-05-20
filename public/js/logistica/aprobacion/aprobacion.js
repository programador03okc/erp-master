$(function(){
    $('#form-aprobacion').on('submit', function(){

        let selectUsuarioRol=document.getElementsByName('rol_usuario')[0];
        var idRolAprob = selectUsuarioRol.value;
        var data = selectUsuarioRol.options[selectUsuarioRol.selectedIndex];
        var dataArea = data.getAttribute('data-id-area');
        var data = $(this).serialize();
        data=data+'&id_rol='+idRolAprob+'&id_area='+dataArea;
        // console.log(data);
        
        var type = $(this).attr('type');
        var ask = confirm('¿Desea guardar este registro?');
        var msj='';
        var title='';
        var codigo_req='';
        var id_area='';
        if (type == 'aprobar'){
            codigo_req = document.querySelector("form[id='form-aprobacion']  input[name='codigo']").value;
            id_area = document.querySelector("form[id='form-aprobacion']  input[name='id_area']").value;

            url = '/logistica/aprobar_documento';
            msj = 'Aprobación grabada con éxito';
            title = 'Requerimiento Aprobado';
        }else if (type == 'denegar'){
            url = '/logistica/denegar_documento';
            msj = 'Se anuló el documento con éxito';
            title = 'Requerimiento Anulado';
        }

        if (ask == true){
            // console.log(data);
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: url,
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    $('#ListaReq').DataTable().ajax.reload();
                    if (response == 'ok') {
                        alert(msj);
                        $('#modal-aprobacion-docs').modal('hide');

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
            
                            //  url: urlBase + '/like',
                            url: '/notification',
                            type: "POST",
                            data: {
                                title:title,
                                message: "Código: "+codigo_req,
                                id_area: id_area,
                                id_rol: 0
                            },
                            success: function(result) {
                                console.log('success!');
                            }
                        });


                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });

    $('#form-obs-detalle').on('submit', function(){
        var data = $(this).serialize();       
        var ask = confirm('¿Desea guardar esta observación?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/observar_detalles',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response == 'ok') {
                        alert('Se agregó una observación al Item');
                        $('#modal-obs-motivo').modal('hide');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });

    $('#form-obs-requerimiento').on('submit', function(){
        let div = document.getElementById('obs-req-detalle');
        let codigo_req = document.querySelector("form[id='form-obs-requerimiento'] div[class='modal-footer'] input[name='codigo']").value;
        let id_area = document.querySelector("form[id='form-obs-requerimiento'] div[class='modal-footer'] input[name='id_area']").value;
        let id_requerimiento = document.querySelector("form[id='form-obs-requerimiento'] div[class='modal-footer'] input[name='id_requerimiento']").value;
        let id_doc = document.querySelector("form[id='form-obs-requerimiento'] div[class='modal-footer'] input[name='doc_req']").value;

        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar esta observación?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/observar_contenido',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response.status == 200) {
                        $('#modal-obs-req').modal('hide');
                        $('#ListaReq').DataTable().ajax.reload();
                        alert(response.mensaje);

                        
            event.preventDefault();

            // Envio um AJAX para o Laravel
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                //  url: urlBase + '/like',
                url: '/notification',
                type: "POST",
                data: {
                    title:"Requerimiento Observado",
                    message: "Código: "+codigo_req,
                    id_area: id_area,
                    id_rol: 0
                },
                success: function(result) {
                    console.log('success!');
                }
            });

                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });
});

function openModalAprob(){
    $('#modal-aprobacion-docs').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });


}

function openModalObs(req, doc, flujo){
    $('#modal-obs-req [name=doc_req]').val(doc);
    $('#modal-obs-req [name=flujo_req]').val(flujo);
    
    $.ajax({
        type: 'GET',
        url: '/logistica/observar_req/' + req + '/' + doc,
        dataType: 'JSON',
        beforeSend: function(){
            $(document.body).append('<span class="loading"><div></div></span>');
        },
        success: function(response){
            // console.log(response);
            $('.loading').remove();
            $('#obs-req-detalle').html(response.view);
            $('#modal-obs-req [name=codigo]').val(response.codigo);
            $('#modal-obs-req [name=id_requerimiento]').val(response.id_req);
            $('#modal-obs-req [name=id_area]').val(response.id_area);
            $('#modal-obs-req').modal({show: true, backdrop: 'static'});
        }
    });
    return false;
}
 