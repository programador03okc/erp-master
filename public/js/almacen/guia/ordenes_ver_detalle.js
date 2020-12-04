var iTableCounter=1;
var oInnerTable;

$('#ordenesPendientes tbody').on('click', 'td button.ver-detalle', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        //  This row is already open - close it
       row.child.hide();
       tr.removeClass('shown');
    }
    else {
       // Open this row
    //    row.child( format(iTableCounter, id) ).show();
       format(iTableCounter, id, row);
       tr.addClass('shown');
       // try datatable stuff
       oInnerTable = $('#ordenesPendientes_' + iTableCounter).dataTable({
        //    data: sections, 
           autoWidth: true, 
           deferRender: true, 
           info: false, 
           lengthChange: false, 
           ordering: false, 
           paging: false, 
           scrollX: false, 
           scrollY: false, 
           searching: false, 
           columns:[ 
            //   { data:'refCount' },
            //   { data:'section.codeRange.sNumber.sectionNumber' }, 
            //   { data:'section.title' }
            ]
       });
       iTableCounter = iTableCounter + 1;
   }
});

function format ( table_id, id, row ) {
    $.ajax({
        type: 'GET',
        url: 'detalleOrden/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            
            if (response.length > 0){
                response.forEach(function(element){
                    html+=`<tr>
                    <td style="border: none;">${(element.orden_am!==null ? element.orden_am +` <a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${element.id_oc_propia}&ImprimirCompleto=1">
                    <span class="label label-success">Ver O.E.</span></a>
                <a href="${element.url_oc_fisica}">
                    <span class="label label-warning">Ver O.F.</span></a>`:'')} 
                    </td>
                    <td style="border: none;">${element.codigo_oportunidad!==null ? element.codigo_oportunidad : ''}</td>
                    <td style="border: none;">${element.oportunidad!==null ? element.oportunidad : ''}</td>
                    <td style="border: none;">${element.nombre!==null ? element.nombre : ''}</td>
                    <td style="border: none;"><label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento(${element.id_requerimiento})">${element.codigo_req}</label> ${element.sede_req}</td>
                    <td style="border: none;">${element.codigo}</td>
                    <td style="border: none;">${element.part_number}</td>
                    <td style="border: none;">${element.descripcion}</td>
                    <td style="border: none;">${element.cantidad}</td>
                    <td style="border: none;">${element.abreviatura}</td>
                    <td style="border: none;">${formatNumber.decimal(element.precio,'',2)}</td>
                    <td style="border: none;">${formatNumber.decimal(element.subtotal,'',2)}</td>
                    </tr>`;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">Orden Elec.</th>
                        <th style="border: none;">Cod.CC</th>
                        <th style="border: none;">Oportunidad</th>
                        <th style="border: none;">Entidad</th>
                        <th style="border: none;">Cod.Req.</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Und.Med</th>
                        <th style="border: none;">Unitario</th>
                        <th style="border: none;">Total</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            }
            else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            console.log(tabla);
            row.child( tabla ).show();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}