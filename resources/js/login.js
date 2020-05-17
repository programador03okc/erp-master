$(function(){

    
    $("#formLogin").submit(function(e) {
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        const formData = $(this).serialize();
        const action = $(this).attr('action');
        const rols = $('[name=role]').val();
        // console.log('disabled');
        // document.getElementsByTagName('button')[0].setAttribute('disabled',true)
        if (rols > 0) {

            $.ajax({
                type: 'POST',
                url: action,
                data: formData,
                dataType: 'JSON',
                success (response){
                    if (response.success) {
                        let timerInterval;
                        Swal.fire({
                            type: 'success',
                            title: 'Bienvenido!',
                            footer: 'Redireccionando a la página principal',
                            html: 'Bienvenido al Sistema.',
                            timer: 3000,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer){
                                window.location.href = response.redirectto;
                            }
                            
                        })
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: 'No Autorizado!',
                    text: jqXHR.responseJSON.message,
                    imageUrl: 'images/guard_man.png',
                    imageWidth: 100,
                    imageHeight: 100,
                    backdrop: 'rgba(255, 0, 13, 0.3)'
                });
                document.getElementsByTagName('button')[0].removeAttribute('disabled');
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }); 
        }else{
            document.getElementsByTagName('button')[0].removeAttribute('disabled');
            Swal.fire({
                type: 'success',
                title: 'Error!',
                footer: 'El usuario no cuenta con rol de acceso',
                html: 'Acceso Restringido.',
                timer: 5000,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            })
        }
        e.preventDefault();
    });

});
