function obtenerArchivosMgcp(id, tipo) {
    console.log("id:" + id + "tipo: " + tipo);
    $.ajax({
        type: "POST",
        url: "obtenerArchivosOc",
        data: { id: id, tipo: tipo },
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            $("#modal-archivos_oc_mgcp").modal({
                show: true
            });
            $("#lista_archivos_oc_mgcp").html(response.archivos);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
