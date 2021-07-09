function anular_requerimiento(id_req){
    if(id_req > 0){
        baseUrl = 'anular-requerimiento/'+id_req;
        $.ajax({
            type: 'PUT',
            url: baseUrl,
            dataType: 'JSON',
            beforeSend: function (data) {
                var customElement = $("<div>", {
                    "css": {
                        "font-size": "24px",
                        "text-align": "center",
                        "padding": "0px",
                        "margin-top": "-400px"
                    },
                    "class": "your-custom-class",
                    "text": "Anulando requerimiento..."
                });

                $('#wrapper-okc').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    custom: customElement,
                    imageColor: "#3c8dbc"
                });
            },
            success: function(response){
                // console.log(response);
                $('#wrapper-okc').LoadingOverlay("hide", true);
                if(response.status_requerimiento ==200){
                    alert("Requerimiento Anulado. La página se recargará para que pueda volver a crear un requerimiento");
                    location.reload();
                }

                // if(response.status_requerimiento ==200 && response.status_transferencia ==200){
                //     alert("Requerimiento Anulado y se revertió la transferencia.");
                //     nuevo_req();
                // }else if((response.status_requerimiento ==200 && response.status_transferencia == 0) || (response.status_requerimiento ==200 && response.status_transferencia == 400)){
                //     alert("Requerimiento Anulado.");
                //     mostrar_requerimiento(id_req);
                // }else if(response.status_requerimiento ==400){
                //     alert("Hubo un problema, No se puede Anular el Requerimiento");
                // }else{
                //     alert("No se pudo Anular el Requerimiento.");
                // }
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#wrapper-okc').LoadingOverlay("hide", true);
                alert("Hubo un problema al anular el requerimiento. Por favor actualice la página e intente de nuevo");
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
   
}