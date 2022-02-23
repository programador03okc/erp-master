$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-incidencia").hide();
    $(".edit-incidencia").show();

    var id_incidencia = localStorage.getItem("id_incidencia");

    if (id_incidencia !== null && id_incidencia !== undefined) {
        // mostrarFicha(id_incidencia);
        localStorage.removeItem("id_incidencia");
    }
});

function openContacto() {
    var id_requerimiento = $("[name=id_requerimiento]").val();
    var id_contribuyente = $("[name=id_contribuyente]").val();
    var id_entidad = $("[name=id_entidad]").val();
    var id_contacto = $("[name=id_contacto]").val();
    var codigo = $("[name=codigo_oportunidad]").val() + ' - ' + $(".codigo_requerimiento").text();

    openDespachoContactoIncidencia(id_requerimiento, id_contribuyente, id_entidad, id_contacto, codigo);
}

$(".nueva-incidencia").on('click', function () {

    $(".edition").removeAttr("disabled");
    $(".guardar-incidencia").show();
    $(".nueva-incidencia").hide();
    $(".anular-incidencia").hide();
    $(".edit-incidencia").hide();
    $(".buscar-incidencia").hide();
    $("[name=modo]").val("edicion");

    $(".limpiarIncidencia").val("");
    $("[name=id_incidencia]").val("");

    // $("#mostrar_checks").hide();
    // $("#marcar_checks").show();

});