$(document).on('change','[data-select="departamento-select"]',function () {
    var id_departamento = $(this).val()
        this_select = $(this).closest('div.row').find('div [name="provincia"]'),
        html='';

    if (id_departamento!==null && id_departamento!=='') {
        getProvincias(this_select,id_departamento);
    }else{
        this_select.html('<option value=""> Seleccione...</option>');
        $(this).closest('div.modal-body').find('div [name="distrito"]').html('<option value=""> Seleccione...</option>');
    }

});
function getProvincias(this_select,id_departamento) {
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'provincia/'+id_departamento,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                html='<option value=""> Seleccione...</option>';
                $.each(response.data, function (index, element) {
                    html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                });
                // console.log(this_select);
                // $('[data-form="guardar-cliente"] [name="provincia"]').html(html);
                this_select.html(html);
            }else{
                this_select.html(html);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
$(document).on('change','[data-select="provincia-select"]',function () {
    var id_provincia = $(this).val(),
        this_select = $(this).closest('div.row').find('div [name="distrito"]'),
        html='';

    if (id_provincia!==null && id_provincia!=='') {
        distrito(this_select,id_provincia);
    } else {
        this_select.html('<option value=""> Seleccione...</option>');
    }

});
function distrito(this_select,id_provincia) {
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'distrito/'+id_provincia,
        data: {},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                html='<option value=""> Seleccione...</option>';
                $.each(response.data, function (index, element) {
                    html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                });
                this_select.html(html);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
$(document).on('click','.agregar-establecimiento',function () {
    $('#nuevo-establecimiento').modal('show');
    $('[data-form="guardar-establecimiento"]')[0].reset();
});
$(document).on('submit','[data-form="guardar-establecimiento"]',function (e) {
    e.preventDefault();
    $('#nuevo-establecimiento').modal('hide');
    var html ='',
        direccion = $('[data-form="guardar-establecimiento"] [name="direccionEstablecimiento"]').val(),
        departamento = $('[data-form="guardar-establecimiento"] select[name="departamento"] option:selected').text(),
        provincia = $('[data-form="guardar-establecimiento"] select[name="provincia"] option:selected').text(),
        distrito = $('[data-form="guardar-establecimiento"] select[name="distrito"] option:selected').text(),
        horario = $('[data-form="guardar-establecimiento"] [name="horarioEstablecimiento"]').val(),
        distrito_id = $('[data-form="guardar-establecimiento"] [name="distrito"]').val(),
        random = Math.random();
    html +='<tr key='+random+'>'
        html +='<td data-select="direccion"><input type="hidden" multiple name="establecimiento['+random+'][direccion]" value="'+direccion+'"> <label>'+direccion+'</label></td>'
        html +='<td data-select="ubigeo"><input type="hidden" multiple name="establecimiento['+random+'][ubigeo]" value="'+distrito_id+'"> <label>'+departamento+' - '+ provincia+' - '+ distrito+'</label></td>'
        html +='<td data-select="horario"> <input type="hidden" multiple name="establecimiento['+random+'][horario]" value="'+horario+'"> <label>'+horario+'</label></td>'
        html +='<td data-select="action">'
            html +='<button class="btn btn-warning editar-establecimiento" type="button" data-key="'+random+'"> <i class="fa fa-edit"></i></button> <button class="btn btn-danger anular-establecimiento" type="button" data-key="'+random+'"> <i class="fas fa-trash"></i></button>'
        html +='</td>'
    html +='</tr>'
    html +='';
    $('[data-table="tbody-establecimiento"]').append(html);
});
$(document).on('click','.editar-establecimiento',function () {
    $('#editar-establecimiento').modal('show');
    var data_key = $(this).attr('data-key');
    var establecimiento = $(this).closest('td').closest('tr').find('[name="establecimiento['+data_key+'][direccion]"]').val();
    var ubigeo = $(this).closest('td').closest('tr').find('[name="establecimiento['+data_key+'][ubigeo]"]').val();
    var horario = $(this).closest('td').closest('tr').find('[name="establecimiento['+data_key+'][horario]"]').val(),
        html= '';
    $('[data-form="editar-establecimiento"] [name="direccionEstablecimiento"]').val(establecimiento);
    $('[data-form="editar-establecimiento"] [name="horarioEstablecimiento"]').val(horario);
    $('[data-form="editar-establecimiento"] [name="id_establecimiento"]').val($(this).attr('data-key'));
    $.ajax({
        type: 'get',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get-distrito/'+ubigeo,
        data: {},
        dataType: 'JSON',
        success: function(response){
            $('[data-form="editar-establecimiento"] [name="departamento"] option[value="'+response.departamento.id_dpto+'"]').attr("selected",true);
            $.each(response.provincia_all, function (index, element) {
                html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
            });
            $('#editar-establecimiento [name="provincia"]').html(html);
            html='';
            $.each(response.distrito_all, function (index, element) {
                html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
            });
            $('#editar-establecimiento [name="distrito"]').html(html);

            $('[data-form="editar-establecimiento"] [name="provincia"] option[value="'+response.provincia.id_prov+'"]').attr("selected",true);
            $('[data-form="editar-establecimiento"] [name="distrito"] option[value="'+response.distrito.id_dis+'"]').attr("selected",true);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
    console.log(ubigeo +' - '+ horario);
});
$(document).on('submit','[data-form="editar-establecimiento"]',function (e) {
    e.preventDefault();
    var id_establecimiento = $('[data-form="editar-establecimiento"] [name="id_establecimiento"]').val(),
        direccion = $('[data-form="editar-establecimiento"] [name="direccionEstablecimiento"]').val(),
        departamento = $('[data-form="editar-establecimiento"] select[name="departamento"] option:selected').text(),
        provincia = $('[data-form="editar-establecimiento"] select[name="provincia"] option:selected').text(),
        distrito = $('[data-form="editar-establecimiento"] select[name="distrito"] option:selected').text(),
        horario = $('[data-form="editar-establecimiento"] [name="horarioEstablecimiento"]').val(),
        distrito_id = $('[data-form="editar-establecimiento"] [name="distrito"]').val(),
        data_key = $('[data-form="editar-establecimiento"] [name="id_establecimiento"]').val();

    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="direccion"] label').text(direccion);
    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="direccion"] input[name="establecimiento['+data_key+'][direccion]"]').val(direccion);

    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="ubigeo"] label').text(departamento+' - '+provincia+' - '+distrito);
    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="ubigeo"] input[name="establecimiento['+data_key+'][ubigeo]"]').val(distrito_id);

    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="horario"] label').text(horario);
    $('[data-table="tbody-establecimiento"]').find('tr[key="'+id_establecimiento+'"]').find('td[data-select="horario"] input[name="establecimiento['+data_key+'][horario]"]').val(horario);

    $('#editar-establecimiento').modal('hide');
});
$(document).on('click','.anular-establecimiento',function () {
    var data_key = $(this).attr('data-key')
    $(this).closest('tr[key="'+data_key+'"]').remove();
});
// contacto
$(document).on('click','.agregar-contactos',function () {
    $('#nuevo-contacto').modal('show');
    $('[data-form="guardar-contacto"]')[0].reset();
});
$(document).on('submit','[data-form="guardar-contacto"]',function (e) {
    e.preventDefault();
    var departamento = $('[data-form="guardar-contacto"] select[name="departamento"] option:selected').text(),
        provincia = $('[data-form="guardar-contacto"] select[name="provincia"] option:selected').text(),
        distrito = $('[data-form="guardar-contacto"] select[name="distrito"] option:selected').text(),
        distrito_id = $('[data-form="guardar-contacto"] [name="distrito"]').val(),
        nombre =$('[data-form="guardar-contacto"] [name="nombreContacto"]').val(),
        cargo = $('[data-form="guardar-contacto"] [name="cargoContacto"]').val(),
        telefono = $('[data-form="guardar-contacto"] [name="telefonoContacto"]').val(),
        direccion = $('[data-form="guardar-contacto"] [name="direccionContacto"]').val(),
        horario = $('[data-form="guardar-contacto"] [name="horarioContacto"]').val(),
        email = $('[data-form="guardar-contacto"] [name="emailContacto"]').val(),
        random = Math.random(),
        html='';

    html +='<tr key='+random+'>'
        html +='<td data-select="nombre">'
            html +='<input type="hidden" name="contacto['+random+'][nombre]" value="'+nombre+'">   <label>'+nombre+'</label>'
        html +='</td>'
        html +='<td data-select="cargo">'
            html +='<input type="hidden" name="contacto['+random+'][cargo]" value="'+cargo+'"> <label>'+cargo+'</label>'
        html +='</td>'
        html +='<td data-select="telefono">'
            html +='<input type="hidden" name="contacto['+random+'][telefono]" value="'+telefono+'"> <label>'+telefono+'</label>'
        html +='</td>'
        html +='<td data-select="email">'
            html +='<input type="hidden" name="contacto['+random+'][email]" value="'+email+'"> <label>'+email+'</label>'
        html +='</td>'
        html +='<td data-select="direccion">'
            html +='<input type="hidden" name="contacto['+random+'][direccion]" value="'+direccion+'"> <label>'+direccion+'</label>'
        html +='</td>'
        html +='<td data-select="ubigeo">'
            html +='<input type="hidden" name="contacto['+random+'][ubigeo]" value="'+distrito_id+'"> <label>'+departamento+' - '+provincia+' - '+distrito+'</label>'
        html +='</td>'
        html +='<td data-select="horario">'
            html +='<input type="hidden" name="contacto['+random+'][horario]" value="'+horario+'"> <label>'+horario+'</label>'
        html +='</td>'

        html +='<td data-select="action">'
            html +='<button class="btn btn-warning editar-contacto" type="button" data-key="'+random+'"> <i class="fa fa-edit"></i></button> <button class="btn btn-danger anular-contacto" type="button" data-key="'+random+'"> <i class="fas fa-trash"></i></button>'
        html +='</td>'
    html +='</tr>'
    html +='';
    $('[data-table="lista-contactos"]').append(html);
    $('#nuevo-contacto').modal('hide');
});
$(document).on('click','.editar-contacto',function () {
    $('#editar-contacto').modal('show');
    var data_key =$(this).attr('data-key'),
        nombre = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="nombre"]').find('[name="contacto['+data_key+'][nombre]"]').val(),
        cargo = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="cargo"]').find('[name="contacto['+data_key+'][cargo]"]').val(),
        telefono = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="telefono"]').find('[name="contacto['+data_key+'][telefono]"]').val(),
        email = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="email"]').find('[name="contacto['+data_key+'][email]"]').val(),
        direccion = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="direccion"]').find('[name="contacto['+data_key+'][direccion]"]').val(),
        ubigeo = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="ubigeo"]').find('[name="contacto['+data_key+'][ubigeo]"]').val(),
        horario = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="horario"]').find('[name="contacto['+data_key+'][horario]"]').val();

        $('[data-form="editar-contacto"] [name="nombreContacto"]').val(nombre);
        $('[data-form="editar-contacto"] [name="cargoContacto"]').val(cargo);
        $('[data-form="editar-contacto"] [name="telefonoContacto"]').val(telefono);
        $('[data-form="editar-contacto"] [name="direccionContacto"]').val(direccion);
        $('[data-form="editar-contacto"] [name="horarioContacto"]').val(horario);
        $('[data-form="editar-contacto"] [name="emailContacto"]').val(email);
        $('[data-form="editar-contacto"] [name="id_contacto"]').val(data_key);

        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'get-distrito/'+ubigeo,
            data: {},
            dataType: 'JSON',
            success: function(response){
                $('[data-form="editar-contacto"] [name="departamento"] option[value="'+response.departamento.id_dpto+'"]').attr("selected",true);
                $.each(response.provincia_all, function (index, element) {
                    html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                });
                $('#editar-contacto [name="provincia"]').html(html);
                html='';
                $.each(response.distrito_all, function (index, element) {
                    html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                });
                $('#editar-contacto [name="distrito"]').html(html);

                $('[data-form="editar-contacto"] [name="provincia"] option[value="'+response.provincia.id_prov+'"]').attr("selected",true);
                $('[data-form="editar-contacto"] [name="distrito"] option[value="'+response.distrito.id_dis+'"]').attr("selected",true);
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })

});
$(document).on('submit','[data-form="editar-contacto"]',function (e) {
    e.preventDefault();
    var departamento = $('[data-form="editar-contacto"] select[name="departamento"] option:selected').text(),
        provincia = $('[data-form="editar-contacto"] select[name="provincia"] option:selected').text(),
        distrito = $('[data-form="editar-contacto"] select[name="distrito"] option:selected').text(),
        distrito_id = $('[data-form="editar-contacto"] [name="distrito"]').val(),
        nombre =$('[data-form="editar-contacto"] [name="nombreContacto"]').val(),
        cargo = $('[data-form="editar-contacto"] [name="cargoContacto"]').val(),
        telefono = $('[data-form="editar-contacto"] [name="telefonoContacto"]').val(),
        direccion = $('[data-form="editar-contacto"] [name="direccionContacto"]').val(),
        horario = $('[data-form="editar-contacto"] [name="horarioContacto"]').val(),
        email = $('[data-form="editar-contacto"] [name="emailContacto"]').val(),
        data_key = $('[data-form="editar-contacto"] [name="id_contacto"]').val();



        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="nombre"]').find('[name="contacto['+data_key+'][nombre]"]').val(nombre);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="nombre"]').find('label').text(nombre);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="cargo"]').find('[name="contacto['+data_key+'][cargo]"]').val(cargo);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="cargo"]').find('label').text(cargo);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="telefono"]').find('[name="contacto['+data_key+'][telefono]"]').val(telefono);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="telefono"]').find('label').text(telefono);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="email"]').find('[name="contacto['+data_key+'][email]"]').val(email);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="email"]').find('label').text(email);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="direccion"]').find('[name="contacto['+data_key+'][direccion]"]').val(direccion);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="direccion"]').find('label').text(direccion);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="horario"]').find('[name="contacto['+data_key+'][horario]"]').val(horario);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="horario"]').find('label').text(horario);

        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="ubigeo"]').find('[name="contacto['+data_key+'][ubigeo]"]').val(distrito_id);
        $('[data-table="lista-contactos"]').find('tr[key="'+data_key+'"]').find('[data-select="ubigeo"]').find('label').text(departamento+' - '+provincia+ ' - '+distrito);

        $('#editar-contacto').modal('hide');
});
$(document).on('click','.anular-contacto',function () {
    var data_key = $(this).attr('data-key');
    $(this).closest('tr[key="'+data_key+'"]').remove();
});
$(document).on('click','.agregar-cuenta-bancaria',function () {
    $('#nuevo-cuenta-bancaria').modal('show');
    $('[data-form="nuevo-cuenta-bancaria"]')[0].reset();
});
$('[data-form="nuevo-cuenta-bancaria"]').submit(function (e) {
    e.preventDefault();
    var data = $(this).serializeArray(),
        banco = $(this).find('[name="idBanco"] option:selected').text(),
        tipo_cuenta = $(this).find('[name="idTipoCuenta"] option:selected').text(),
        tipo_moneda = $(this).find('[name="idMoneda"] option:selected').text(),
        random = Math.random(),
        html='';

    html +='<tr key='+random+'>'
        html +='<td data-select="banco">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][banco]" value="'+data[0].value+'">   <label>'+banco+'</label>'
        html +='</td>'
        html +='<td data-select="tipo_cuenta">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][tipo_cuenta]" value="'+data[1].value+'"> <label>'+tipo_cuenta+'</label>'
        html +='</td>'
        html +='<td data-select="moneda">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][moneda]" value="'+data[2].value+'"> <label>'+tipo_moneda+'</label>'
        html +='</td>'
        html +='<td data-select="numero_cuenta">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][numero_cuenta]" value="'+data[3].value+'"> <label>'+data[3].value+'</label>'
        html +='</td>'
        html +='<td data-select="cuenta_interbancaria">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][cuenta_interbancaria]" value="'+data[4].value+'"> <label>'+data[4].value+'</label>'
        html +='</td>'
        html +='<td data-select="swift">'
            html +='<input type="hidden" name="cuenta_bancaria['+random+'][swift]" value="'+data[5].value+'"> <label>'+data[5].value+'</label>'
        html +='</td>'

        html +='<td data-select="action">'
            html +='<button class="btn btn-warning editar-cuenta-bancaria" type="button" data-key="'+random+'"> <i class="fa fa-edit"></i></button> <button class="btn btn-danger anular-cuenta-bancaria" type="button" data-key="'+random+'"> <i class="fas fa-trash"></i></button>'
        html +='</td>'
    html +='</tr>'
    html +='';

    $('[data-table="lista-cuenta-bancaria"]').append(html);
    $('#nuevo-cuenta-bancaria').modal('hide');
});
$(document).on('click','.editar-cuenta-bancaria',function () {
    var data_key = $(this).attr('data-key'),
        banco                       = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="banco"]').find('[name="cuenta_bancaria['+data_key+'][banco]"]').val(),
        banco_text                  = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="banco"]').find('label').text(),

        tipo_cuenta                 = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="tipo_cuenta"]').find('[name="cuenta_bancaria['+data_key+'][tipo_cuenta]"]').val(),
        tipo_cuenta_text            = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="tipo_cuenta"]').find('label').text(),

        moneda                      = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="moneda"]').find('[name="cuenta_bancaria['+data_key+'][moneda]"]').val(),
        moneda_text                 = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="moneda"]').find('label').text(),

        numero_cuenta                = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="numero_cuenta"]').find('[name="cuenta_bancaria['+data_key+'][numero_cuenta]"]').val(),

        nuero_cuenta_interbancaria  = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="cuenta_interbancaria"]').find('[name="cuenta_bancaria['+data_key+'][cuenta_interbancaria]"]').val(),

        swift                       = $(this).closest('tr[key="'+data_key+'"]').find('[data-select="swift"]').find('[name="cuenta_bancaria['+data_key+'][swift]"]').val();

    $('[data-form="editar-cuenta-bancaria"] [name="idBanco"] option').removeAttr("selected");
    $('[data-form="editar-cuenta-bancaria"] [name="idTipoCuenta"] option').removeAttr("selected");
    $('[data-form="editar-cuenta-bancaria"] [name="idMoneda"] option').removeAttr("selected");

    $('[data-form="editar-cuenta-bancaria"] [name="idBanco"] option[value="'+banco+'"]').attr("selected",true);
    $('[data-form="editar-cuenta-bancaria"] [name="idTipoCuenta"] option[value="'+tipo_cuenta+'"]').attr("selected",true);
    $('[data-form="editar-cuenta-bancaria"] [name="idMoneda"] option[value="'+moneda+'"]').attr("selected",true);

    $('[data-form="editar-cuenta-bancaria"] [name="nroCuenta"]').val(numero_cuenta);
    $('[data-form="editar-cuenta-bancaria"] [name="nroCuentaInterbancaria"]').val(nuero_cuenta_interbancaria);
    $('[data-form="editar-cuenta-bancaria"] [name="swift"]').val(swift);
    $('[data-form="editar-cuenta-bancaria"] [name="id_cuenta_bancaria"]').val(data_key);
    $('#editar-cuenta-bancaria').modal('show');
});
$('[data-form="editar-cuenta-bancaria"]').submit(function (e) {
    e.preventDefault();
    var data = $(this).serializeArray(),
        banco_text = $(this).find('[name="idBanco"] option:selected').text(),
        tipo_cuenta_text = $(this).find('[name="idTipoCuenta"] option:selected').text(),
        moneda_text = $(this).find('[name="idMoneda"] option:selected').text()
        ;
    console.log(banco_text);
    console.log(data);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="banco"]').find('input[name="cuenta_bancaria['+data[0].value+'][banco]"]').val(data[1].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="banco"]').find('label').text(banco_text);

    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="tipo_cuenta"]').find('input[name="cuenta_bancaria['+data[0].value+'][tipo_cuenta]"]').val(data[2].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="tipo_cuenta"]').find('label').text(tipo_cuenta_text);

    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="moneda"]').find('input[name="cuenta_bancaria['+data[0].value+'][moneda]"]').val(data[3].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="moneda"]').find('label').text(moneda_text);

    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="numero_cuenta"]').find('input[name="cuenta_bancaria['+data[0].value+'][numero_cuenta]"]').val(data[4].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="numero_cuenta"]').find('label').text(data[4].value);

    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="cuenta_interbancaria"]').find('input[name="cuenta_bancaria['+data[0].value+'][cuenta_interbancaria]"]').val(data[5].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="cuenta_interbancaria"]').find('label').text(data[5].value);

    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="swift"]').find('input[name="cuenta_bancaria['+data[0].value+'][swift]"]').val(data[6].value);
    $('[data-table="lista-cuenta-bancaria"]').find('tr[key="'+data[0].value+'"]').find('td[data-select="swift"]').find('label').text(data[6].value);
    $('#editar-cuenta-bancaria').modal('hide');
});
$(document).on('click','.anular-cuenta-bancaria',function () {
    $(this).closest('tr[key="'+$(this).attr('data-key')+'"]').remove();
});
$(document).on('submit','[data-form="guardar-cliente"]',function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    // $.ajax({
    //     type: 'POST',
    //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //     url: $(this).attr('action'),
    //     data: data,
    //     dataType: 'JSON',
    //     success: function(response){
    //         console.log(response);
    //     }
    // }).fail( function(jqXHR, textStatus, errorThrown) {
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // })


    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: $(this).attr('action'),
                data: data,
                dataType: 'JSON',
                beforeSend: (data) => {
                    console.log(data);
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                Swal.fire({
                    title: result.value.title,
                    text: result.value.text,
                    icon: result.value.icon,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        window.location.href = "cliente";
                    }
                })
            }else{
                Swal.fire(
                    result.value.title,
                    result.value.text,
                    result.value.icon
                )
            }
        }
    });

});
$(document).on('click','.volver-cliente',function () {
    window.location.href = "cliente";
});
$(document).on('change','[name="documento"]',function () {
    var documento = $(this).val(),
        this_input = $(this);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'buscar-cliente-documento',
        data: {documento:documento},
        dataType: 'JSON',
        beforeSend: (data) => {

        }
    }).done(function(response) {
        if (response.success===true) {
            Swal.fire(
                'Información',
                'Número de documento se encuentra en uso',
                'warning'
            )
            this_input.val('');
        }

    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
