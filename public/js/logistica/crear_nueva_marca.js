function agregar_nueva_marca(){
    $('#modal-crear-nueva-marca').modal({
        show: true
    });

    let selectCategoriaModalCrearProducto = document.querySelector("form[id='form-crear-nuevo-producto'] select[name='id_categoria']").value;
    if(selectCategoriaModalCrearProducto > 0){
        document.querySelector("form[id='form-crear-nueva-marca'] select[name='id_categoria']").value = selectCategoriaModalCrearProducto;
    }
}


function guardar_nueva_marca(){
    let data ={
        'id_categoria': document.querySelector("form[id='form-crear-nueva-marca'] select[name='id_categoria']").value,
        'descripcion': document.querySelector("form[id='form-crear-nueva-marca'] input[name='nombre_marca']").value
    } 
    if(data.id_categoria == '' || data.id_categoria==null){
        alert("debe seleccionar una categoría");
    }else{
        $.ajax({
            type: 'POST',
            url: '/almacen/catalogos/sub-categorias/guardar-marca',
            data: data,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response.status != 200){
                    alert(response.msj);
                } else {
                    $('#modal-crear-nueva-marca').modal('hide');
                    alert(response.msj);
                    limpiarSelectMarca();
                    llenarSelectMarca(response.data);

                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }

    function limpiarSelectMarca(){
        let selectElement = document.querySelector("form[id='form-crear-nuevo-producto'] select[name='id_subcategoria']");
        if(selectElement !=null){
            while (selectElement.options.length > 0) {                
                selectElement.remove(0);
            }    
        }
    }
    function llenarSelectMarca(array){
        let selectSubCategoria = document.querySelector("form[id='form-crear-nuevo-producto'] select[name='id_subcategoria']");

        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_subcategoria;
            selectSubCategoria.add(option);
        });
    }

}