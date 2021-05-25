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

        let cantidad_item_input = obj.parentNode.parentNode.parentNode.parentNode.children[5].children[0];
        let cantidad_item_td = obj.parentNode.parentNode.parentNode.parentNode.children[5];

        let cantidad_item = cantidad_item_input?cantidad_item_input.value:cantidad_item_td.textContent;
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
    let select_almacen_reserva = document.querySelector("div[id='modal-almacen-reserva'] select[id='almacen_reserva']");
    let select_almacen_reserva_id = select_almacen_reserva.value;
    let select_almacen_reserva_text= select_almacen_reserva.options[select_almacen_reserva.selectedIndex].textContent;
    let cantidad_reserva = document.querySelector("div[id='modal-almacen-reserva'] input[id='cantidad_reserva']").value;
    let indiceSeleccionado = document.querySelector("div[id='modal-almacen-reserva'] label[id='indice']").textContent;
    
    if(indiceSeleccionado >= 0){
        if(select_almacen_reserva_id >0 && cantidad_reserva >0){
            data_item.forEach((element, index) => {
                if (index == indiceSeleccionado) {
                    data_item[index].id_almacen_reserva = parseInt(select_almacen_reserva_id);
                    data_item[index].almacen_reserva = select_almacen_reserva_text;
                    data_item[index].stock_comprometido = parseInt(cantidad_reserva);
                    data_item[index].proveedor_id = null;
                    data_item[index].proveedor_razon_social = null;

        
                }
            });
            componerTdItemDetalleRequerimiento();
            alert("Producto actualizado vinculado al "+select_almacen_reserva_text);

            $('#modal-almacen-reserva').modal('hide');
            // console.log(data_item);
        }else{
            alert("Debe seleccionar un almacén / cantidad a reservar debe ser mayor a 0");
        }
    }else{
        alert("no se detecto un item seleccionado");
    }
}

function quitarReservaAlmacen(){
    let indiceSeleccionado = document.querySelector("div[id='modal-almacen-reserva'] label[id='indice']").textContent;
    data_item.forEach((element, index) => {
        if (index == indiceSeleccionado) {
            data_item[index].id_almacen_reserva = null;
            data_item[index].almacen_reserva = null;
            data_item[index].stock_comprometido = null;
            data_item[index].proveedor_id = null;
            data_item[index].proveedor_razon_social = null;


        }
    });
    componerTdItemDetalleRequerimiento();

    $('#modal-almacen-reserva').modal('hide');

}