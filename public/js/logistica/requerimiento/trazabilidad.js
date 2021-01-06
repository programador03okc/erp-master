function verTrazabilidadRequerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verTrazabilidadRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            dibujarTablatrazabilidadRequerimiento(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function dibujarTablatrazabilidadRequerimiento(data){
    var vardataTables = funcDatatables();
    $('#listaTrazabilidadRequerimiento').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:10,
        paging:   false,
        searching: false,
        language: vardataTables[0],
        processing: true,
        data: data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.accion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nombre_corto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            }
        ],
    })

    let tablelistaitem = document.getElementById(
        'listaTrazabilidadRequerimiento_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}