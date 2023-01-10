$(document).ready(function () {
    lista();
});
function vistaCrear() {
    window.location.href = "crear";
}
function lista() {
    var vardataTables = funcDatatables();
    var tableRequerimientos = $("#lista-presupuesto-interno").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        lengthChange: false,
        dom: vardataTables[1],
        buttons:[
            {
                text: '<i class="fas fa-plus"></i> Nuevo presupuesto',
                attr: {
                    id: 'btn-nuevo',
                    href:'crear',
                },
                action: () => {
                    // vistaCrear();
                    window.open('crear');
                },
                className: 'btn-default btn-sm'
            }

        ],
        ajax: {
            url: "lista-presupuesto-interno",
            type: "POST",
            data:{
                // filtros
            },
            beforeSend: data => {
                $("#lista-presupuesto-interno").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        columns: [
            {data: 'id_presupuesto_interno', name:"id_presupuesto_interno"},
            {data: 'codigo', name:"codigo"},
            {data: 'descripcion', name:"descripcion"},
            {data: 'fecha_registro', name:"fecha_registro"},
            {data: 'id_grupo', name:"id_grupo"},
            {data: 'estado', name:"estado"},
            {
                render: function (data, type, row) {
                    html='';
                        html+='<button type="button" class="btn btn-warning btn-flat botonList editar-registro" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Editar" data-original-title="Editar"><i class="fas fa-edit"></i></button>';

                        html+='<button type="button" class="btn btn-danger btn-flat botonList eliminar" data-id="'+row['id_presupuesto_interno']+'" title="Eliminar"><i class="fas fa-trash"></i></button>';

                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        order: [[1, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        "drawCallback": function (settings) {

            $("#lista-presupuesto-interno").LoadingOverlay("hide", true);
        }
    });
}
