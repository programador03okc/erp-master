function modalAlmacenReserva(obj,indice){
  
    $('#modal-almacen-reserva').modal({
        show: true,
        backdrop: 'true'
    });
    document.querySelector("div[id='modal-almacen-reserva'] label[id='indice']").textContent = indice;

    construirSelectReservaAlmacen().then(function(data) {
        // console.log(data);
        let select_almacen_reserva = document.querySelector("div[id='modal-almacen-reserva'] select[id='almacen_reserva']");

        let length = select_almacen_reserva.options.length -1;
            for (i = length; i >= 0; i--) {
                select_almacen_reserva.remove(i);
            }

            let option = document.createElement("option");
            option.text = "selecciona una opción";
            option.value = 0;
            select_almacen_reserva.add(option);
        data.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_almacen;
            select_almacen_reserva.add(option);
        });

        let cantidad_item = obj.parentNode.parentNode.parentNode.parentNode.children[5].children[0].value;
        document.querySelector("div[id='modal-almacen-reserva'] input[id='cantidad_reserva']").value= cantidad_item;
    });

    }

function construirSelectReservaAlmacen(){
    return new Promise(function(resolve, reject) {

        $.ajax({
            type: 'GET',
            url: 'listar_almacenes',
            dataType: 'JSON',
            success: function(response){
                resolve(response.data)

            }, error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            alert('fail, Error al guardar');
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
}


function agregarReservaAlmacen(){
    let select_almacen_reserva = document.querySelector("div[id='modal-almacen-reserva'] select[id='almacen_reserva']").value;
    let cantidad_reserva = document.querySelector("div[id='modal-almacen-reserva'] input[id='cantidad_reserva']").value;
    let indiceSeleccionado = document.querySelector("div[id='modal-almacen-reserva'] label[id='indice']").textContent;
    
    if(indiceSeleccionado >= 0){
        if(select_almacen_reserva >0 && cantidad_reserva >0){
            data_item.forEach((element, index) => {
                if (index == indiceSeleccionado) {
                    data_item[index].id_almacen_reserva = parseInt(select_almacen_reserva);
                    data_item[index].stock_comprometido = parseInt(cantidad_reserva);
                    data_item[index].proveedor_id = null;

        
                }
            });
            alert("Item actualizado, Se asignó un proveedor al item");

            $('#modal-almacen-reserva').modal('hide');
            // console.log(data_item);
        }else{
            alert("Debe seleccionar un almacén / cantidad a reservar debe ser mayor a 0");
        }
    }else{
        alert("no se detecto un item seleccionado");
    }
}