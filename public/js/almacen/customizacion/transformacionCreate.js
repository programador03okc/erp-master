$(".add-new-servicio").on('click',function(){
    $(this).attr("disabled", "disabled");

    var cant = $("#listaServiciosDirectos tbody tr").length;
    var index = 0;
    if (cant > 0){
        index = $("#listaServiciosDirectos tbody tr:last-child")[0].id;
    }
    console.log();
    var row = `<tr id="${parseInt(index)+1}">
        <td><input type="text" class="form-control" name="descripcion" id="descripcion"></td>
        <td><input type="number" class="form-control" name="total" id="total"></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaServiciosDirectos").append(row);
});

// Add row on add button click
$('#listaServiciosDirectos tbody').on("click", ".add", function(){
    var empty = false;
    var input = $(this).parents("tr").find('input');
    input.each(function(){
        if(!$(this).val()){
            $(this).addClass("error");
            empty = true;
        } else{
            $(this).removeClass("error");
        }
    });
    $(this).parents("tr").find(".error").first().focus();
    if(!empty){
        var descripcion = '';
        var total = 0;

        input.each(function(){
            if ($(this)[0].name == 'descripcion'){
                descripcion = $(this).val();
            } 
            else if ($(this)[0].name == 'total'){
                total = $(this).val();
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var idx = $(this).parents("tr")[0].id;

        let servicio = {
            'index':idx,
            'descripcion':descripcion,
            'total':total
        }
        lista_servicios.push(servicio);
    }		
});

// Delete row on delete button click
$('#listaServiciosDirectos tbody').on("click", ".delete", function(){
    $(this).parents("tr").remove();

    var idx = $(this).parents("tr")[0].id;
    var index = lista_servicios.findIndex(function(item, i){
        console.log('idx'+idx+' index'+item.index);
        return parseInt(item.index) == parseInt(idx);
    });
    console.log(index);
    if (index !== -1){
        lista_servicios.splice(index,1);
    }
});

//Sobrantes
$(".add-new-sobrante").on('click',function(){
    $(this).attr("disabled", "disabled");

    var cant = $("#listaSobrantes tbody tr").length;
    var index = 0;
    if (cant > 0){
        index = $("#listaSobrantes tbody tr:last-child")[0].id;
    }
    console.log();
    var row = `<tr id="${parseInt(index)+1}">
        <td><input type="text" class="form-control" name="part_number" id="part_number"></td>
        <td><input type="text" class="form-control" name="descripcion" id="descripcion"></td>
        <td><input type="number" class="form-control calcula" name="cantidad" id="cantidad"></td>
        <td>UND.</td>
        <td><input type="number" class="form-control calcula" name="unitario" id="unitario"></td>
        <td><input type="number" class="form-control" name="total" readOnly id="total"></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaSobrantes").append(row);
});
// Calcula total
$('#listaSobrantes tbody').on("change", ".calcula", function(){
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    console.log('cantidad'+cantidad+' unitario'+unitario);
    if (cantidad !== '' && unitario !== ''){
        $(this).parents("tr").find('input[name=total]').val(parseFloat(cantidad) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});
// Add row on add button click
$('#listaSobrantes tbody').on("click", ".add", function(){
    var empty = false;
    var input = $(this).parents("tr").find('input');
    input.each(function(){
        if(!$(this).val()){
            $(this).addClass("error");
            empty = true;
        } else{
            $(this).removeClass("error");
        }
    });
    $(this).parents("tr").find(".error").first().focus();
    if(!empty){
        var part_number = '';
        var descripcion = '';
        var cantidad = 0;
        var unitario = 0;
        var total = 0;

        input.each(function(){
            if ($(this)[0].name == 'descripcion'){
                descripcion = $(this).val();
            } 
            else if ($(this)[0].name == 'part_number'){
                part_number = $(this).val();
            }
            else if ($(this)[0].name == 'cantidad'){
                cantidad = parseFloat($(this).val());
            }
            else if ($(this)[0].name == 'unitario'){
                unitario = parseFloat($(this).val());
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var idx = $(this).parents("tr")[0].id;

        let sobrante = {
            'index':idx,
            'part_number':part_number,
            'descripcion':descripcion,
            'cantidad':cantidad,
            'unitario':unitario,
            'total':(cantidad * unitario)
        }
        lista_sobrantes.push(sobrante);
    }		
});

// Delete row on delete button click
$('#listaSobrantes tbody').on("click", ".delete", function(){
    $(this).parents("tr").remove();

    var idx = $(this).parents("tr")[0].id;
    var index = lista_sobrantes.findIndex(function(item, i){
        console.log('idx'+idx+' index'+item.index);
        return parseInt(item.index) == parseInt(idx);
    });
    console.log(index);
    if (index !== -1){
        lista_sobrantes.splice(index,1);
    }
});