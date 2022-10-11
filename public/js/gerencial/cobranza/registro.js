let idioma;
let carga_ini = 1;
var tempClienteSelected = {};
var tempoNombreCliente = '';
var userNickname= '';
$(document).ready(function () {
    // list();
    listarRegistros();
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
        order: [[0, "desc"]],
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
$(document).on('change','.buscar-registro',function () {
    const cdp = $(this).val();
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'buscar-cdp/'+cdp,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                console.log(response);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
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
                console.log(response);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
