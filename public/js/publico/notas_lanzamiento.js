$(function() {
    //get_notas_lanzamiento()
})

function get_notas_lanzamiento(url) {
    $.ajax({
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        url: url,
        dataType: 'JSON',
        success: function(response) {
            if (response.length > 0) {
                fill_text_notas_lanzamiento(response)
            }
        },
    })
}

function fill_text_notas_lanzamiento(data) {
    let notaLanzamiento = document.getElementsByName('text_nota_lanzamiento')[0]
    let html = ''
    data.forEach(element => {
        html +=
            '<h4>' +
            element.titulo +
            '.</h4><p>' +
            element.descripcion +
            ' <small>(' +
            element.fecha_detalle_nota_lanzamiento +
            ')</small></p>'
    })
    notaLanzamiento.innerHTML = html
}
