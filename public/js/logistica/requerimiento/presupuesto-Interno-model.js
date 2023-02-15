class PresupuestoInternoModel {

    constructor(token) {
        this.token = token;
    }

 

    obtenerListaDetallePrespuestoInterno(idPresupuestoIterno,mensualOAnual){

        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`obtener-detalle-presupuesto-interno/${idPresupuestoIterno}/${mensualOAnual}`,
                dataType: 'JSON',
                
                beforeSend: function (data) { 
                    $('select[name="id_presupuesto_interno"]').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                    },
                success(response) {
                    resolve(response);
                    $('select[name="id_presupuesto_interno"]').LoadingOverlay("hide", true);

                },
                error: function(err) {
                    reject(err) 
                }
                });
            });
    }

    comboPresupuestoInterno(idGrupo,idArea){

        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`combo-presupuesto-interno/${idGrupo}/${idArea}`,
                dataType: 'JSON',
                beforeSend: function (data) { 
                    $('select[name="id_presupuesto_interno"]').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                    },
                success(response) {
                    resolve(response);
                    $('select[name="id_presupuesto_interno"]').LoadingOverlay("hide", true);

                },
                error: function(err) {
                    reject(err) 
                }
                });
            });
    }


}
