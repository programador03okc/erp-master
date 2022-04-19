function verDocumentosAutogenerados(id_doc_com) {
    $('#docs').html('');
    $.ajax({
        type: "GET",
        url: "verDocumentosAutogenerados/" + id_doc_com,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var html = ''
            response.forEach(element => {
                html += `
                <div class="row">
                    <div class="col-md-12">
                        <span>Requerimiento: </span>
                        <label><a href="#" onClick="abrirRequerimiento(${element.id_requerimiento})" 
                        title="Ver Requerimiento">${element.codigo_req}</a></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span>Orden Compra: </span>
                        <label><a href="#" onClick="abrirOrden(${element.id_orden_compra})" 
                        title="Ver Orden de Compra">${element.codigo_oc}</a></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span>Guía Compra: </span>
                        <label><a href="#" onClick="verIngreso(${element.id_ingreso})" 
                        title="Ver Ingreso">${element.guia_com}</a></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span>Doc Compra: </span>
                        <label>${element.doc_com}</label>
                    </div>
                </div>`;
            });
            $('#docs').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function verIngreso(idIngreso) {
    if (idIngreso !== "") {
        // var id = encode5t(idIngreso);
        window.open("imprimir_ingreso/" + idIngreso);
    }
}

function abrirOrden(idOc) {
    if (idOc !== "") {
        let url = `/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${idOc}`;
        var win = window.open(url, "_blank");
        win.focus();
    }
}