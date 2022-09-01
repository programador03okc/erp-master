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
    // $.ajax({
    //     type: 'POST',
    //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //     url: 'listar-registros',
    //     data: {},
    //     dataType: 'JSON',
    //     beforeSend: function(){
    //     },
    //     success: function(response){

    //         $('#listar-registros').dataTable({
    //             // 'dom': 'Bfrtip',
    //             // 'lengthMenu': [[5, 20, 40, 60, -1 ], ['5', '20', '40', '60', 'Todos' ]],
    //             'pageLength': 10,
    //             'language' : vardataTables[0],
    //             'bDestroy': true,
    //             'serverSide': false,
    //             'data': response,
    //             'columns' : [
    //                 {data: 'id_cobranza'},
    //                 {data: 'empresa'},
    //                 {data: 'ocam'},
    //                 {data: 'cliente'},
    //                 {data: 'factura'},
    //                 {data: 'uu_ee'},
    //                 {data: 'fuente_financ'},
    //                 {data: 'oc'},
    //                 {data: 'siaf'},
    //                 {data: 'fecha_emision'},
    //                 {data: 'fecha_recepcion'},
    //                 {data: 'atraso'},
    //                 {data: 'moneda'},
    //                 {data: 'importe'},
    //                 {data: 'estado'},
    //                 {data: 'area'},
    //                 // {data: 'fase'},
    //                 // {data: 'tramite'},
    //                 {
    //                     render: function (data, type, row) {
    //                         return `---`;
    //                     },
    //                     className: "text-center"
    //                 }
    //             ],
    //             columnDefs: [{ aTargets: [0], sClass: "invisible" }],
    //             order: [[0, "desc"]],
    //             // 'order': [
    //             //     [10, 'asc'], [9, 'asc']
    //             // ],
    //             // initComplete: function(data){
    //             //     selectFilter();
    //             // }
    //         });
    //     }
    // }).fail( function(jqXHR, textStatus, errorThrown) {
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
    // var vardataTables = funcDatatables();
    //     // console.time();

        tableRequerimientos = $("#listar-registros").DataTable({
        // dom: vardataTables[1],
        // buttons: [],
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        // lengthChange: false,
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
            // {data: 'estado', name:"estado"},
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
            // {data: 'fase', name:"fase"},
            {
                render: function (data, type, row) {
                    return (`<label class="label label-primary">${row['fase']}</label>`);
                },
                className: "text-center"
            },
            // {data: 'id_tipo_tramite', name:"id_tipo_tramite"},
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
