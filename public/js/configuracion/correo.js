function irarriba() {
    $('html, body').animate({ scrollTop: 0 }, 300)
}


$(document).on('submit', '.formarchivo', function(e) {
    e.preventDefault()
    const nombreform = $(this).attr('id')

    if (nombreform == 'f_enviar_correo') {
        var miurl = 'enviar_correo'
        // var divresul = 'contenido_principal'

        console.log("enviar correo");
        let destinatario = document.getElementById('destinatario').value;
        let asunto = document.getElementById('asunto').value;
        let contenido_mail = document.getElementById('contenido_mail').value;
        // let file = document.getElementById('file').value;
        
        let myformData = new FormData();        
        myformData.append('destinatario', destinatario);
        myformData.append('asunto', asunto);
        myformData.append('contenido_mail', contenido_mail);
        myformData.append('file', $('#file')[0].files[0])


    console.log(...myformData);


        $.ajax({
            url: miurl,
            type: 'POST',
            // Form data
            // datos del formulario
            data: myformData,
            // necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            // mientras enviamos el archivo
            beforeSend() {
                $('.loading').removeClass('invisible');
            },
            // una vez finalizado correctamente
            success(data) {
                console.log('succces=>');
                console.log(data);
                
                $('.loading').addClass('invisible');
                $('#estado_email').html(data);
            },
            // si ha ocurrido un error
            error(data) {
                alert('ha ocurrido un error');
            },
        })

        
    }



    irarriba()
})




// $(document).on('change', '.email_archivo', function(e) {
//     const miurl = 'cargar_archivo_correo';
//     const divresul = 'texto_notificacion';

//     let data = new FormData();
//     data.append('file', $('#file')[0].files[0]);

//     $.ajax({
//         url: miurl,
//         type: 'POST',

//         // Form data
//         // datos del formulario
//         data:data,
//         // necesario para subir archivos via ajax
//         cache: false,
//         contentType: false,
//         processData: false,
//         // mientras enviamos el archivo
//         beforeSend() {
//             $('.loading').removeClass('invisible');

//         },
//         // una vez finalizado correctamente
//         success(data) {		
//             // console.log(data);
            
//             $('.loading').addClass('invisible');


//             const codigo =
//                 `<div class="mailbox-attachment-info"><a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>${ 
//                 data 
//                 }</a><span class="mailbox-attachment-size"> </span></div>`
//             $(`#${  divresul  }`).html(codigo)
//         },
//         // si ha ocurrido un error
//         error(data) {
//             $(`#${  divresul  }`).html(data)
//         },
//     })
// })
