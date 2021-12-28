var $tablaHistorialOrdenesElaboradas;


$('#listaOcSoftlink tbody').on("click", "button.handleClickSelectOcParaVincular", (e) => {
    this.selectOcParaVincular(e.currentTarget);
});
$('#modal-lista-oc-softlink').on("change", ".handleChangeFiltroFechaVincularOcSoftlink", (e) => {
    console.log(document.querySelector("select[name='filtroEmpresa']").options[document.querySelector("select[name='filtroEmpresa']").selectedIndex].dataset.codigoEmpresa, e.currentTarget.value)
    this.mostrarOrdenesSoftlinkNoVinculadas(document.querySelector("select[name='filtroEmpresa']").options[document.querySelector("select[name='filtroEmpresa']").selectedIndex].dataset.codigoEmpresa, e.currentTarget.value);
});
$('#modal-lista-oc-softlink').on("change", ".handleChangeFiltroEmpresaVincularOcSoftlink", (e) => {
    console.log(document.querySelector("select[name='filtroEmpresa']").options[document.querySelector("select[name='filtroEmpresa']").selectedIndex].dataset.codigoEmpresa, e.currentTarget.value)
    this.mostrarOrdenesSoftlinkNoVinculadas(e.currentTarget.value, document.querySelector("input[name='filtroFecha']").value );
});



function listarOcSoftlink(e){
    $('#modal-lista-oc-softlink').modal({
        show: true,
        backdrop: 'true',
        keyboard: true
    });

    const $button = e.currentTarget;
    $button.setAttribute('disabled',true);
    let codigoEmpresa =document.querySelector("select[name='filtroEmpresa']")?document.querySelector("select[name='filtroEmpresa']").options[document.querySelector("select[name='filtroEmpresa']").selectedIndex].dataset.codigoEmpresa:'OKC';
    let fecha = document.querySelector("input[name='filtroFecha']").value !=''?document.querySelector("input[name='filtroFecha']").value:new Date().toISOString().substring(0, 10);
    console.log(codigoEmpresa,fecha);
    mostrarOrdenesSoftlinkNoVinculadas(codigoEmpresa,fecha);
}

function mostrarOrdenesSoftlinkNoVinculadas(codigoEmpresa,fecha){
        var vardataTables = funcDatatables();
        $tablaListaOcSoftlink= $('#listaOcSoftlink').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': false,
            'destroy': true,
            'ajax': {
                'url': `listarOrdenesSoftlinkNoVinculadas/${codigoEmpresa}/${fecha}`,
                'type': 'GET',
                beforeSend: data => {
    
                    $("#listaOcSoftlink").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }
    
            },
    
            'columns': [
                { 'data': 'mov_id', 'name': 'movimien.mov_id', 'visible': false },
                { 'data': 'num_docu', 'name': 'movimien.num_docu', 'className': 'text-center' },
                { 'data': 'nom_auxi', 'name': 'auxiliar.nom_auxi', 'className': 'text-left' }, 
                { 'data': 'mov_id', 'name': 'movimien.mov_id','className': 'text-center'  }
    
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
    
                        return `${row.num_docu}`;
                    }, targets: 1
                },
                {
                    'render': function (data, type, row) {
    
                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                        <button type="button" class="btn btn-xs btn-success handleClickSelectOcParaVincular" data-cod-docu="${row.cod_docu}" data-num-docu="${row.num_docu}" data-mov-id="${row.mov_id}" title="Vincular">
                            Vincular
                        </button>
                        </div></center>`;
                    }, targets: 3
                },
    
            ],
            'initComplete': function () {
    
    
    
                //Boton de busqueda
                const $filter = $('#listaOcSoftlink_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaOcSoftlink.search($input.val()).draw();
                })
                //Fin boton de busqueda
 

            //     document.querySelector("div[id='listaOcSoftlink_filter']").insertAdjacentHTML('afterbegin',`
            // <div style="position:absolute; left:0px;">
            //     <input type="date" class="form-control handleChangeFiltroFechaVincularOcSoftlink" value="${moment().format('YYYY-MM-DD')}" name="filtroFecha" style="width: 15rem;">
            //     <select class="form-control handleChangeFiltroEmpresaVincularOcSoftlink" name="filtroEmpresa" style="width: 15rem;">
            //     ${document.querySelector("select[name='selectEmpresa']").innerHTML}
            //     </select>
            // </div>
            // `);
    
            },
            "drawCallback": function( settings ) {
                // console.log(settings);
                document.querySelector("button[id='btn-relacionar-a-oc-softlink']").removeAttribute('disabled');

                //Botón de búsqueda
                $('#listaOcSoftlink_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaOcSoftlink_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaOcSoftlink").LoadingOverlay("hide", true);

            }
        });



    

}



function selectOcParaVincular(obj){
    let continuarVinculacion= false;
    let mensaje=[];

    const $button = obj;
    const movId = obj.dataset.movId;
    const ocsoftlink = `${obj.dataset.codDocu} ${obj.dataset.numDocu}`;
    const idOrden = document.querySelector("input[name='id_orden']").value;
    $button.setAttribute('disabled',true);
    $button.setAttribute('title','Vinculando..');
    $button.textContent='Vinculando..';

    if(idOrden!=null && parseInt(idOrden)>0){
        if(movId !=null && movId.length >0){
            continuarVinculacion= true;

        }else{
            continuarVinculacion= false;
            mensaje.push("El ID de la OC de softlink no se encontró, debe seleccionar una OC del listado");
        }
    }else{
        continuarVinculacion= false;
        mensaje.push("El ID de la orden no se encontró, debe cargar una orden");
    }

    if(continuarVinculacion){
        $.ajax({
            type: 'POST',
            url: 'vincular-oc-softlink',
            data:{idOrden,movId},
            dataType: 'JSON',
        }).done(function (response) {
            console.log(response);
            $button.setAttribute('title','vinculado');
            $button.textContent='Vinculo';
            $button.removeAttribute('disabled');

            if(response.tipo_estado == 'error'){
                Swal.fire({
                    title: '',
                    html:  response.mensaje,
                    icon: 'error'
                });
            }else{
                Lobibox.notify(response.tipo_estado, {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response.mensaje
                });
            }
            
            $('#modal-lista-oc-softlink').modal('hide');
    
        }).always(function (response) {

            $button.textContent='Vinculo';
            $button.removeAttribute('disabled');
    
        }).fail(function (jqXHR) {
            if(jqXHR.status == 404) 
            { 
                Swal.fire(
                    'Error 404',
                    'Lo sentimos hubo un problema con el servidor, la ruta a la que se quiere acceder para guardar no esta disponible, por favor vuelva a intentarlo más tarde.',
                    'error'
                );
            }
            console.log(jqXHR);
            Lobibox.notify('error', {
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Hubo un problema con el servidor. Por favor actualice la página e intente de nuevo.'
            });
            Swal.fire({
                title: '',
                html:  jqXHR.responseText,
                icon: 'error'
            });
            console.log('Error devuelto: ' + jqXHR.responseText);
        });
    }else{
        mensaje.push("No se realizó ninguna acción");
    }

    if(mensaje.length >0){
        Swal.fire({
            title: '',
            html:  mensaje.toString(),
            icon: 'warning'
        });
    }


}
