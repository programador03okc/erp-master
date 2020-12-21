const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,      
    maximumFractionDigits: 2,
 });

$("#listaPartidas tbody").on('click', ".ver-detalle", function(){
    var id = $(this).data('id');
    
    if ($('#'+id).css('display') == 'none'){
        $('#'+id).show();
        if ($('#'+id+' td table tbody').length == 1){
            mostrarRequerimientosDetalle(id);
        }
    } else {
        $('#'+id).hide();
    }

});

function mostrarRequerimientosDetalle(id){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: "mostrarRequerimientosDetalle/"+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                var html = '';
                var total = 0;
                response.forEach(element => {
                    total += parseFloat(element.precio_referencial);
                    html += `<tr>
                        <td width="70px">${element.codigo}</td>
                        <td>${element.concepto}</td>
                        <td>${element.descripcion_adicional}</td>
                        <td width="50px" style="text-align:right;">${formatter.format(parseFloat(element.precio_referencial))}</td>
                        </tr>`;
                });
                html += `<tr>
                    <td colSpan="2"></td>
                    <td style="font-size: 14px;"><strong>Total Consumido</strong></td>
                    <td style="font-size: 14px; text-align:right;"><strong>${formatter.format(total)}</strong></td>
                </tr>`
                $('#'+id+' td table tbody').html(html);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}