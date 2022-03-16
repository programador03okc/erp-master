$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaSalidasVenta tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaSalidasVenta").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }

        var data = $('#listaSalidasVenta').DataTable().row($(this)).data();
        console.log(data);

        // $("[name=id_mov_alm]").val(data.id_mov_alm);
        // $("[name=id_guia_ven]").val(data.id_guia_ven);
        $("[name=id_requerimiento]").val(data.id_requerimiento ?? 0);
        $("[name=id_contribuyente]").val(data.id_contribuyente ?? null);
        $("[name=id_empresa]").val(data.id_empresa);
        $("[name=id_entidad]").val(data.id_entidad);
        $("[name=id_contacto]").val(data.id_contacto);
        $("[name=codigo_oportunidad]").val(data.codigo_oportunidad);

        $("[name=cliente_razon_social]").val(data.razon_social);
        $("[name=nro_orden]").val(data.nro_orden);
        $(".codigo_oportunidad").text(data.codigo_oportunidad);
        $(".fecha_registro").text(formatDate(fecha_actual()));

        $("[name=nombre_contacto]").val(data.nombre);
        $("[name=cargo_contacto]").val(data.cargo);
        $("[name=telefono_contacto]").val(data.telefono);
        $("[name=direccion_contacto]").val(data.direccion);
        $(".horario_contacto").text(data.horario);
        $(".email_contacto").text(data.email);

        $("#modal-salidasVenta").modal("hide");
    });
});

function listarSalidasVenta() {
    var vardataTables = funcDatatables();

    $("#listaSalidasVenta").dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        bDestroy: true,
        ajax: "listarSalidasVenta",
        columns: [
            { data: "id" },
            // {
            //     data: 'numero', name: 'guia_ven.numero',
            //     'render': function (data, type, row) {
            //         return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
            //     }
            // },
            { data: "nro_orden", name: 'oc_propias_view.nro_orden' },
            { data: "codigo_oportunidad", name: 'oportunidades.codigo_oportunidad' },
            { data: "razon_social", name: 'adm_contri.razon_social' },
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[0, "desc"]]
    });
}

function openSalidasVentaModal() {
    $("#modal-salidasVenta").modal({
        show: true
    });
    listarSalidasVenta();
}
