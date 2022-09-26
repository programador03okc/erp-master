
function mostrarIncidencias() {
    $("#listaIncidenciasDevolucion tbody").html('');
    var row = '';

    incidencias.forEach(sel => {
        if (sel.estado == 1) {
            row += `<tr>
                <td style="text-align:center">${sel.codigo}</td>
                <td style="text-align:center">${sel.fecha_reporte}</td>
                <td style="text-align:center">${sel.razon_social ?? ''}</td>
                <td style="text-align:center">${sel.nombre_corto ?? ''}</td>
                <td style="text-align:center">${sel.estado_descripcion}</td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton delete" data-id="${sel.id_incidencia}"
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
                </td>
            </tr>`;
        }
    })
    $("#listaIncidenciasDevolucion tbody").html(row);
}

// Delete row on delete button click
$('#listaIncidenciasDevolucion tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular ésta salida?");

    if (anula) {
        let id_incidencia = $(this).data('id');

        if (id_incidencia !== '') {
            let item = incidencias.find(element => {
                return element.id_incidencia == id_incidencia;
            });
            if (item.id == 0) {
                let index = incidencias.findIndex(function (item, i) {
                    return item.id_incidencia == id_incidencia;
                });
                incidencias.splice(index, 1);
            } else {
                item.estado = 7;
            }
            console.log(incidencias);
        }
        $(this).parents("tr").remove();
        mostrarIncidencias();
    }
});