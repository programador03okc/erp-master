var rutaListaRequerimientoModal, 
rutaMostrarRequerimiento,
rutaGuardarRequerimiento,
rutaActualizarRequerimiento,
rutaAnularRequerimiento,
rutaSedeByEmpresa,
rutaCopiarRequerimiento,
rutaTelefonosCliente,
rutaDireccionesCliente,
rutaEmailCliente,
rutaNextCodigoRequerimiento,
rutaCuentasCliente,
rutaGuardarCuentacliente,
rutaCuadroCostos,
rutaDetalleCuadroCostos,
rutaObtenerCostruirCliente,
rutaObtenerGrupoSelectItemParaCpmpra
;

var detalleItemsCC=[];
var tempDetalleItemsCC=[];
var tempDetalleItemCCSelect={};
var dataSelect = [];

let data = [];
let data_item=[];
var adjuntos=[];
var id_detalle_requerimiento=0;
var obs=false;
var gobal_observacion_requerimiento=[];


var ListOfPartidaSelected = [];
var ListOfItems = [];
var partidaSelected ={};
var idPartidaSelected=0;
var codigoPartidaSelected='';
var itemSelected ={};
var UsoDePartida =[];
var userSession =[];
var objPromociones =[];
var sustentoObj =[];
var action_requerimiento ='';

let tpOptCom  ={};

function inicializar( _rutaLista,
    _rutaMostrarRequerimiento,
    _rutaGuardarRequerimiento,
    _rutaActualizarRequerimiento,
    _rutaAnularRequerimiento,
    _rutaSedeByEmpresa,
    _rutaCopiarRequerimiento,
    _rutaTelefonosCliente,
    _rutaDireccionesCliente,
    _rutaEmailCliente,
    _rutaNextCodigoRequerimiento,
    _rutaCuentasCliente,
    _rutaGuardarCuentacliente,
    _rutaCuadroCostos,
    _rutaDetalleCuadroCostos,
    _rutaObtenerCostruirCliente,
    _rutaObtenerGrupoSelectItemParaCpmpra
    ) {
    rutaListaRequerimientoModal = _rutaLista;
    rutaMostrarRequerimiento = _rutaMostrarRequerimiento;
    rutaGuardarRequerimiento = _rutaGuardarRequerimiento;
    rutaActualizarRequerimiento = _rutaActualizarRequerimiento;
    rutaAnularRequerimiento = _rutaAnularRequerimiento;
    rutaSedeByEmpresa = _rutaSedeByEmpresa;
    rutaCopiarRequerimiento = _rutaCopiarRequerimiento;
    rutaTelefonosCliente = _rutaTelefonosCliente;
    rutaDireccionesCliente = _rutaDireccionesCliente;
    rutaEmailCliente = _rutaEmailCliente;
    rutaNextCodigoRequerimiento = _rutaNextCodigoRequerimiento;
    rutaCuentasCliente = _rutaCuentasCliente;
    rutaGuardarCuentacliente = _rutaGuardarCuentacliente;
    rutaCuadroCostos = _rutaCuadroCostos;
    rutaDetalleCuadroCostos = _rutaDetalleCuadroCostos;
    rutaObtenerCostruirCliente = _rutaObtenerCostruirCliente;
    rutaObtenerGrupoSelectItemParaCpmpra = _rutaObtenerGrupoSelectItemParaCpmpra;

    listar_almacenes();

            let selectTipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            createOptionTipoCliente(selectTipoRequerimiento);
            
            getNexCodigoRequerimiento(selectTipoRequerimiento);

            var id_requerimiento = localStorage.getItem("id_requerimiento");

            if (id_requerimiento !== null){
                mostrar_requerimiento(id_requerimiento);
                verTrazabilidadRequerimiento(id_requerimiento);
                localStorage.removeItem("id_requerimiento");
                changeStateButton('historial');
                vista_extendida();

            }
            var ordenP_Cuadroc = JSON.parse(sessionStorage.getItem('ordenP_Cuadroc'));
            var justificacion_generar_requerimiento = JSON.parse(sessionStorage.getItem('justificacion_generar_requerimiento'));
            if(ordenP_Cuadroc !== null && ordenP_Cuadroc.hasOwnProperty('tipo_cuadro') && ordenP_Cuadroc.hasOwnProperty('id_cc')){
                vista_extendida();
                // console.log(ordenP_Cuadroc);
                if(justificacion_generar_requerimiento != null){
                    if(ordenP_Cuadroc.id_cc == justificacion_generar_requerimiento.id_cc){
                        document.querySelector("input[name='justificacion_generar_requerimiento']").value=justificacion_generar_requerimiento.contenido;
                    }
                }

                let btnVinculoAcrivoCC= `<span class="text-info" id="text-info-cc-vinculado" > (vinculado a un CC) <span class="badge label-danger" onClick="eliminarVinculoCC();" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
                document.querySelector("section[class='content-header']").children[0].innerHTML+=btnVinculoAcrivoCC;
                getDataCuadroCostos(ordenP_Cuadroc);
                document.querySelector("input[name='fecha_entrega']").setAttribute('disabled',true);
                document.querySelector("div[id='input-group-monto'] h5").textContent ='Monto OC';
                mostrarTipoForm('MGCP');

            }else{
                // console.log('no se encontro cuadro de costos, variable de sesión ordenP_Cuadroc vacia');
                document.querySelector("fieldset[id='group-detalle-cuadro-costos']").setAttribute('hidden',true);

            }

            //variable de lista requerimientos - btn editar requerimiento
            var idReq = localStorage.getItem('id_req');
            if (idReq != null){
                mostrar_requerimiento(idReq);
                verTrazabilidadRequerimiento(idReq);
                // localStorage.clear();
                localStorage.removeItem("id_req");
                changeStateButton('historial');
                vista_extendida();
            }
            var today = new Date();

            $('[name=periodo]').val(today.getFullYear());
}

$(function(){
    $.ajax({
        type: 'GET',
        url: '/session-rol-aprob',
        data: data,
        success: function(response){
            // console.log(response); 
            userSession=response;
            document.getElementsByName('id_usuario_session')[0].value= response.id_usuario;
        }
    });

     


    resizeSide();
    
    $("#form-requerimiento").submit(function(e) {
        e.preventDefault();
    });

    // $('#form-obs-sustento').on('submit', function(){
    //     var data = $(this).serialize();
    //     var ask = confirm('¿Desea guardar el sustento?');
    //     if (ask == true){
    //         $.ajax({
    //             type: 'POST',
    //             // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             url: '/logistica/guardar_sustento',
    //             data: data,
    //             beforeSend: function(){
    //                 $(document.body).append('<span class="loading"><div></div></span>');
    //             },
    //             success: function(response){
    //                 // console.log(response);
                    
    //                 $('.loading').remove();
    //                 if (response.status == 'ok') {
    //                     alert('Se agregó sustento al Requerimiento');
    //                     mostrar_requerimiento(response.data);
    //                     $('#modal-sustento').modal('hide');
    //                 }else {
    //                     alert('No se puedo Guardar sustento al requerimiento');
    //                     $('#modal-sustento').modal('hide');
    //                 }
    //             }
    //         });
    //         return false;
    //     }else{
    //         return false;
    //     }
    // });
    changeOptComercialSelect(); // label's title of option comercial 


    $('#listaTelefonosCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var tel = $(this)[0].firstChild.innerHTML;
        $('[name=telefono_cliente]').val(tel);    
        $('#modal-telefonos-cliente').modal('hide');
    });
    $('#listaEmailCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var email = $(this)[0].firstChild.innerHTML;
        $('[name=email_cliente]').val(email);    
        $('#modal-Email-cliente').modal('hide');
    });
    $('#listaDireccionesCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var dir = $(this)[0].firstChild.innerHTML;
        $('[name=direccion_entrega]').val(dir);    
        $('#modal-direcciones-cliente').modal('hide');
    });
    $('#listaCuentasCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id_cuenta = $(this)[0].firstChild.innerHTML;
        var banco = $(this)[0].childNodes[1].innerHTML;
        var tipo_cuenta = $(this)[0].childNodes[2].innerHTML;
        var nro_cuenta = $(this)[0].childNodes[3].innerHTML;
        var nro_cuenta_interbancaria = $(this)[0].childNodes[4].innerHTML;
        // var moneda = $(this)[0].childNodes[5].innerHTML;

        $('[name=id_cuenta]').val(id_cuenta);    
        $('[name=banco]').val(banco);    
        $('[name=tipo_cuenta]').val(tipo_cuenta);    
        $('[name=nro_cuenta]').val(nro_cuenta);    
        $('[name=cci]').val(nro_cuenta_interbancaria);    
        $('#modal-cuentas-cliente').modal('hide');
    });

        /* Seleccionar valor del DataTable */
        $('#listaRequerimiento tbody').on('click', 'tr', function(){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('#listaRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var idTr = $(this)[0].firstChild.innerHTML;
            $('.modal-footer #id_requerimiento').text(idTr);
            
        });
    
    
        $('#checkViewTodos').on('click',function(){
            if(document.getElementById('checkViewTodos').checked){
                listarRequerimiento('SHOW_ALL');
            }else{
                listarRequerimiento('ONLY_ACTIVOS');
            }
        });

     /* Seleccionar valor del DataTable */
     $('#listaItems tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        var codigo = $(this)[0].children[4].innerHTML;
        var partNum = $(this)[0].children[5].innerHTML;
        var categoria = $(this)[0].children[6].innerHTML;
        var subcategoria = $(this)[0].children[7].innerHTML;
        var descri = $(this)[0].children[8].innerHTML;
        var unidad = $(this)[0].children[9].innerHTML;
        var id_unidad = $(this)[0].children[10].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #part_number').text(partNum);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
        $('.modal-footer #unidad_medida').text(unidad);
        $('.modal-footer #id_unidad_medida').text(id_unidad);
        $('.modal-footer #categoria').text(categoria);
        $('.modal-footer #subcategoria').text(subcategoria);
    });
});


function changeOptComercialSelect(){
    let optCom =getActualOptComercial();
    if(document.getElementById('title-option-comercial') != null){
        document.getElementById('title-option-comercial').textContent = 'Código '+optCom.texto;
        switch (optCom.id) {
            case '1':
                document.getElementsByName('codigo_occ')[0].setAttribute('maxlength', '14');
                document.getElementsByName('codigo_occ')[0].setAttribute('placeholder', 'OKC0000-0000000');
                
                break;
                case '2':
                    document.getElementsByName('codigo_occ')[0].setAttribute('maxlength', '11');
                    document.getElementsByName('codigo_occ')[0].setAttribute('placeholder', 'OKC00-00000');
    
                break;
            default:
                break;
        }
    }
}


function getActualOptComercial(){
    if(document.getElementsByName('tpOptCom')[0] != undefined){
        let selection = document.getElementsByName('tpOptCom')[0].options.selectedIndex;
 
        tpOptCom.texto = document.getElementsByName('tpOptCom')[0].options[selection].textContent;
        tpOptCom.id  = document.getElementsByName('tpOptCom')[0].value;
        // console.log(tpOptCom);
    
        return tpOptCom;
    }

}