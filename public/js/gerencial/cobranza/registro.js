let idioma;
let carga_ini = 1;
var tempClienteSelected = {};
var tempoNombreCliente = '';
var userNickname= '';
$(document).ready(function () {
    // list();
    listarRegistros();
    actualizarDocVentReq();
});
function list() {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'listar-registros',
        data: {},
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listarRegistros() {
    var vardataTables = funcDatatables();
        tableRequerimientos = $("#listar-registros").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        ajax: {
            url: "listar-registros",
            type: "POST"
        },
        columns: [
            {data: 'id_cobranza', name:"id_cobranza"},
            {data: 'empresa', name:"empresa"},
            {data: 'ocam', name:"ocam"},
            {data: 'cliente', name:"cliente"},
            {data: 'factura', name:"factura"},
            {data: 'uu_ee', name:"uu_ee"},
            {data: 'fuente_financ', name:"fuente_financ"},
            {data: 'oc', name:"oc"},
            {data: 'siaf', name:"siaf"},
            {data: 'fecha_emision', name:"fecha_emision"},
            {data: 'fecha_recepcion', name:"fecha_recepcion"},
            {data: 'atraso', name:"atraso"},
            {data: 'moneda', name:"moneda"},
            {data: 'importe', name:"importe"},
            {
                render: function (data, type, row) {
                    var html='';
                    var html=`<select class="" name="estado_documento">`;
                    row['estado'][1].forEach(element => {

                        html+=`<option value="${element.id_estado_doc}" ${element.id_estado_doc===row['estado'][0]?'selected':''}>${element.nombre}</option>`;
                    });
                    html+=`</select>`;
                    return (html);
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    var html='';
                    var html=`<select class="" name="area_responsable">`;
                    row['area'][1].forEach(element => {

                        html+=`<option value="${element.id_area}" ${element.id_area===row['area'][0]?'selected':''}>${element.descripcion}</option>`;
                    });
                    html+=`</select>`;
                    return (html);
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return (`<label class="label label-primary">${row['fase']}</label>`);
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return ``;
                },
                className: "text-center"
            }
        ],
        // order: [[0, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }]
    });

}
$(document).on('click','[data-action="nuevo-registro"]',function () {
    $('#modal-cobranza').modal('show');
});
$(document).on('submit','#formulario',function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
});
function ModalSearchCustomer() {
    $('#modal-buscar-cliente').modal('show');
    customerList();
}
function customerList() {
    var vardataTables = funcDatatables();
    tableRequerimientos = $("#tabla-clientes").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        ajax: {
            url: "listar-clientes",
            type: "POST"
        },
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'text-center'}
        ],
        columns: [
            {data: 'id_cliente', name:"id_cliente"},
            {data: 'nombre', name:"nombre"},
            {data: 'ruc', name:"ruc"},
        ],
        order: [[1, "asc"]],
        initComplete: function(data){
            if(tempoNombreCliente.length >0) {
                // $('#example_filter input').val(tempoNombreCliente);
                this.api().search(tempoNombreCliente).draw();
                document.querySelector("input[type='search']").focus();
                document.querySelector("input[type='search']").setSelectionRange(tempoNombreCliente.length,tempoNombreCliente.length );
                tempoNombreCliente='';
            }
        }
    });
}
$('#tabla-clientes tbody').on('click', 'tr', function(){
    if ($(this).hasClass('selected')){
        $(this).removeClass('selected');
        document.querySelector("button[id='edit_customer']").setAttribute('disabled',true)
        document.querySelector("button[id='btnAgregarCliente']").setAttribute('disabled',true)
        console.log('seleccion if');
    } else {
        $('#tablaClientes').dataTable().$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
        document.querySelector("button[id='edit_customer']").removeAttribute('disabled')
        document.querySelector("button[id='btnAgregarCliente']").removeAttribute('disabled')

        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'get-cliente/'+$(this)[0].firstChild.innerHTML,
            data: {},
            dataType: 'JSON',
            success: function(response){
                console.log(response);
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    }
    var id = $(this)[0].firstChild.innerHTML;
    var nombre = $(this)[0].childNodes[1].textContent;
    var ruc = $(this)[0].childNodes[2].innerHTML;
    tempClienteSelected = {
        id,nombre,ruc
    };
});
function ModalAddNewCustomer() {
    $('#modal-agregar-cliente').modal({show: true});
    $('[name=nuevo_ruc_dni_cliente]').val('');
    $('[name=nuevo_cliente]').val('');
}
function ModalEditCustomer(){
    $('#modal-editar-cliente').modal({show: true});
    document.querySelector("div[id='modal-editar-cliente'] input[id='edit_ubigeo_cliente']").value = tempClienteSelected.ubigeo;
    document.querySelector("div[id='modal-editar-cliente'] input[id='edit_ruc_dni_cliente']").value = tempClienteSelected.nro_documento;
    document.querySelector("div[id='modal-editar-cliente'] input[id='edit_cliente']").value = tempClienteSelected.razon_social;
    document.querySelector("div[id='modal-editar-cliente'] input[id='edit_id']").value = tempClienteSelected.id;

}
function agregarCliente(tipo){
    $('#modal-buscar-cliente').modal('hide');
    if (tipo == 'ventas') {
        document.querySelector("form[form='ventas'] input[id='cliente']").value= tempClienteSelected.nombre;
        document.querySelector("form[form='ventas'] input[id='id_cliente']").value= tempClienteSelected.id;
        document.querySelector("form[form='ventas'] input[id='ruc_dni_cliente']").value= tempClienteSelected.ruc;
    } else {
        document.querySelector("form[form='cobranza'] input[id='cliente']").value= tempClienteSelected.ruc;
        document.querySelector("form[form='cobranza'] input[id='id_cliente']").value= tempClienteSelected.id;
    }
    tempClienteSelected = {};
}
function SaveNewCustomer(){

    var data = $('[data-form="guardar-cliente"]').serialize();
    Swal.fire({
        title: '¿Está seguro de guardar?',
        text: "Se guardara como un registro nuevo",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'nuevo-cliente',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        $('#tabla-clientes').DataTable().ajax.reload();
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
        },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Éxito',
                text: "Se actualizo su clave con éxito",
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // window.location.reload();
                }
            })
        }
    })
}
$(document).on('change','[data-select="departamento-select"]',function () {
    var id_departamento = $(this).val()
        this_select = $(this).closest('div.modal-body').find('div [name="provincia"]'),
        html='';

    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'provincia/'+id_departamento,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                $.each(response.data, function (index, element) {
                    html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                });
                console.log(this_select);
                // $('[data-form="guardar-cliente"] [name="provincia"]').html(html);
                this_select.html(html);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});

$(document).on('change','[data-select="provincia-select"]',function () {
    var id_provincia = $(this).val(),
        this_select = $(this).closest('div.modal-body').find('div [name="distrito"]'),
        html='';

    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'distrito/'+id_provincia,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                $.each(response.data, function (index, element) {
                    html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                });
                this_select.html(html);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
// busca por factura
$(document).on('change','.buscar-factura',function () {
    const factura = $(this).val();
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'buscar-factura/'+factura,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                $('#formulario .modal-body select[name="moneda"]').removeAttr('selected');
                $('#formulario .modal-body select[name="moneda"] option[value="'+response.data.moneda+'"]').attr('selected','true');
                $('#formulario .modal-body input[name="importe"]').val(response.data.total_a_pagar)
                $('#formulario .modal-body input[name="plazo_credito"]').val(response.data.credito_dias)
                $('#formulario .modal-body input[name="fecha_emi"]').val(response.data.fecha_emision)
                console.log(response);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
function searchSource(){
    $('#modal-fue-fin').modal({show: true, backdrop: 'static'});
    $('#modal-fue-fin').on('shown.bs.modal', function(){
        $('[name=fuente]').select();
    });
}
function fuenteFinan(value){
    $('#rubro').empty();
    $('#rubro').append('<option value="" disabled selected>Elija una opción</option>');
    var opcion;
    if (value == 1){
        opcion = '<option value="00">RECURSOS ORDINARIOS</option>';
    }else if(value == 2){
        opcion = '<option value="09">RECURSOS DIRECTAMENTE RECAUDADOS</option>';
    }else if(value == 3){
        opcion = '<option value="19">RECURSOS POR OPERACIONES OFICIALES DE CREDITO</option>';
    }else if(value == 4){
        opcion = '<option value="13">DONACIONES Y TRANSFERENCIAS</option>';
    }else if(value == 5){
        opcion = '<option value="04">CONTRIBUCIONES A FONDOS</option><option value="07">FONDO DE COMPENSACION MUNICIPAL</option><option value="08">IMPUESTOS MUNICIPALES</option><option value="18">CANON Y SOBRECANON, REGALIAS, RENTA DE ADUANAS Y PARTICIPACIONES</option>';
    }
    $('#rubro').append(opcion);
}
function selectSource(){
    var fuente = $('#fuente').val();
    var rubro = $('#rubro').val();
    var text = fuente.concat('-', rubro);
    $('#ff').val(text);
    $('#modal-fue-fin').modal('hide');
}
$('#formulario').on('submit', function(){
    var data = $(this).serialize();
    var form = $(this).attr('form');
    var type = $(this).attr('type');
    var page = 'formulario';
    // var ask = confirm('¿Desea guardar este registro?');

    var url;
    var msj;

    Swal.fire({
        title: 'Guardar',
        text: "¿Esta seguro de guardar este registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            $.ajax({
                type: 'POST',
                url: 'guardar-registro-cobranza',
                dataType: 'JSON',
                data:data,
                success: function(response){
                    if (response.status===200) {
                        Swal.fire(
                          'Éxito',
                          'Se elimino con éxito.',
                          'success'
                        ).then((result) => {
                            $('#listaUsuarios').DataTable().ajax.reload();
                        })
                    }else{
                        Swal.fire(
                            'Error',
                            'No se pudo eliminar.',
                            'error'
                        );
                    }
                    //
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
          },
          allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
    })
});
function actualizarDocVentReq() {
    $.ajax({
        type: 'GET',
        url: 'actualizar-ven-doc-req',
        dataType: 'JSON',
        data:{},
        success: function(response){

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
$(document).on('click','[data-action="actualizar"]',function () {
    actualizarDocVentReq();
});
$(document).on('click','.modal-lista-procesadas',function () {
    const input = $(this).closest('div').find('input').val();
    const action = $(this).closest('div').find('input').data('action');

    if (input) {
        $('#lista-procesadas').modal('show');
        $('#lista-procesadas .btn-seleccionar').attr('disabled','true');
        listarRegistrosProcesadas(input, action);
    }

});
function listarRegistrosProcesadas(input, action) {

    var vardataTables = funcDatatables();


    $("#lista-ventas-procesadas").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        destroy: true,
        pageLength: 20,
        lengthChange: false,
        serverSide: true,
        ajax: {
            url: 'buscar-registro/'+input+'/'+action,
            type: "GET"
        },
        columns: [
            { data: "id_requerimiento_logistico", name:"requerimiento_logistico_view.id_requerimiento_logistico"
             },
            { data: "nro_orden", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['nro_orden']+'')
                }
            },
            { data: "codigo_oportunidad", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['codigo_oportunidad']+'')
                }
            },
            {
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['serie']+'-'+row['numero']);
                },
                className: "text-center selecionar",
            },
            { data: "fecha_emision", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['fecha_emision']+'')
                }
            },

        ],
        order: [[1, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }]
    });
}
$(document).on('click','.selecionar',function () {
    const id_requerimiento = $(this).find('input').val();
    if (id_requerimiento) {
        $('#lista-procesadas .btn-seleccionar').removeAttr('disabled');
        $('#lista-procesadas .btn-seleccionar').attr('data-id',id_requerimiento);
    }

});
$(document).on('click','#lista-procesadas .btn-seleccionar',function () {
    const id_requerimiento = $(this).data('id');
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'seleccionar-registro/'+id_requerimiento,
        data: {},
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.status===200) {
                $('#lista-procesadas').modal('hide');
                $('#formulario .modal-body select[name="moneda"]').removeAttr('selected');
                $('#formulario .modal-body select[name="moneda"] option[value="'+response.data.id_moneda+'"]').attr('selected','true');

                $('#formulario .modal-body input[name="importe"]').val(response.data.total_a_pagar)
                $('#formulario .modal-body input[name="plazo_credito"]').val(response.data.credito_dias)
                $('#formulario .modal-body input[name="fecha_emi"]').val(response.data.fecha_emision)
                $('#formulario .modal-body input[name="oc"]').val(response.data.nro_orden)
                $('#formulario .modal-body input[name="cdp"]').val(response.data.codigo_oportunidad)

                $('#formulario .modal-body input[name="id_cliente"]').val(response.data.id_cliente)
                $('#formulario .modal-body input[name="cliente"]').val(response.data.razon_social)

            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
