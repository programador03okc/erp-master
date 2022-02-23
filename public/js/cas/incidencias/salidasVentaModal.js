$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaSalidasVenta tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaSalidasVenta").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }

        // var myId = $(this)[0].firstChild.innerHTML;
        // var guia = $(this)[0].childNodes[1].innerHTML;
        // var clie = $(this)[0].childNodes[2].innerHTML;
        // var creq = $(this)[0].childNodes[3].innerHTML;
        // var conc = $(this)[0].childNodes[4].innerHTML;
        // var fech = $(this)[0].childNodes[5].innerHTML;
        // var idreq = $(this)[0].childNodes[6].innerHTML;

        var data = $('#listaSalidasVenta').DataTable().row($(this)).data();

        $("[name=id_mov_alm]").val(data.id_mov_alm);
        $("[name=id_requerimiento]").val(data.id_requerimiento);
        $("[name=id_contribuyente]").val(data.id_contribuyente);
        $("[name=id_entidad]").val(data.id_entidad);
        $("[name=id_contacto]").val(data.id_contacto);
        $("[name=codigo_oportunidad]").val(data.codigo_oportunidad);

        $(".guia_venta").text(data.serie + '-' + data.numero);
        $(".cliente_razon_social").text(data.razon_social);
        $(".codigo_requerimiento").text(data.codigo_requerimiento);
        $(".concepto_requerimiento").text(data.concepto);
        $(".fecha_salida").text(formatDate(data.fecha_emision));

        $(".nombre").text(data.nombre);
        $(".cargo").text(data.cargo);
        $(".telefono").text(data.telefono);
        $(".direccion").text(data.direccion);
        $(".horario").text(data.horario);
        $(".email").text(data.email);

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
            { data: "id_mov_alm" },
            {
                data: 'numero', name: 'guia_ven.numero',
                'render': function (data, type, row) {
                    return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
                }
            },
            { data: "razon_social", name: 'adm_contri.razon_social' },
            { data: "codigo_requerimiento", name: 'alm_req.codigo' },
            { data: "concepto", name: 'alm_req.concepto' },
            {
                data: 'fecha_emision', name: 'mov_alm.fecha_emision',
                'render': function (data, type, row) {
                    return (row['fecha_emision'] !== undefined ? formatDate(row['fecha_emision']) : '');
                }
            },
            { data: "id_requerimiento", name: 'alm_req.id_requerimiento' },
        ],
        columnDefs: [{ aTargets: [0, 6], sClass: "invisible" }],
        order: [[0, "desc"]]
    });
}

function openSalidasVentaModal() {
    $("#modal-salidasVenta").modal({
        show: true
    });
    listarSalidasVenta();
}
