$(function(){
    $('[name=id_empresa]').val(4);
    $('[name=almacen]').val(1);
    var fecha = new Date();
    var yyyy = fecha.getFullYear();
    $('[name=fecha_inicio]').val(yyyy+'-01-01');
    $('[name=fecha_fin]').val(yyyy+'-12-31');

    $('[name=todos_documentos]').prop('checked', true);
    $('[name=documento] option').each(function(){
        $(this).prop("selected",true);
    });
    // console.log($('[name=todos_documentos]').prop('checked'));
    $('[name=todas_condiciones]').prop('checked', true);
    $('[name=condicion] option').each(function(){
        $(this).prop("selected",true);
    });
    // console.log($('[name=todas_condiciones]').prop('checked'));

    $('#modal-filtros').on("change", "select.handleChangeFiltroEmpresa", (e) => {
        handleChangeFiltroEmpresa(e);
    });
    $('#modal-filtros').on("click", "input[type=checkbox]", (e) => {
        estadoCheckFiltroOrdenesCompra(e);
    });

    $('#modal-filtros').on('hidden.bs.modal', ()=> {
        if(updateContadorFiltro() ==0){
            actualizarLista('DEFAULT');
        }else{
            actualizarLista();
        }
    });
    actualizarLista('DEFAULT');

});

function updateContadorFiltro(){
    let contadorCheckActivo= 0;
    const allCheckBoxFiltro = document.querySelectorAll("div[id='modal-filtros'] input[type='checkbox']");
    allCheckBoxFiltro.forEach(element => {
        if(element.checked==true){
            contadorCheckActivo++;
        }
    });
    document.querySelector("button[id='btnFiltros'] span")?document.querySelector("button[id='btnFiltros'] span").innerHTML ='<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : '+contadorCheckActivo:false
    return contadorCheckActivo;
}

function getDataSelectSede(id_empresa){
        
    return new Promise(function(resolve, reject) {
        if(id_empresa >0){
            $.ajax({
                type: 'GET',
                url: `listar-sedes-por-empresa/` + id_empresa,
                dataType: 'JSON',
                success(response) {
                    resolve(response) // Resolve promise and go to then() 
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
                });
            }else{
                resolve(false);
            }
        });
} 

function llenarSelectSede(array) {
    let selectElement = document.querySelector("div[id='modal-filtros'] select[name='sede']");

    if (selectElement.options.length > 0) {
        var i, L = selectElement.options.length - 1;
        for (i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
    }

    array.forEach(element => {
        let option = document.createElement("option");
        option.text = element.descripcion;
        option.value = element.id_sede;
        selectElement.add(option);
    });
}

function handleChangeFiltroEmpresa(event){
    let id_empresa = event.target.value;
    getDataSelectSede(id_empresa).then((res) => {
        llenarSelectSede(res);
    }).catch(function (err) {
        console.log(err)
    })
}

function estadoCheckFiltroOrdenesCompra(e){
    const modalFiltro =document.querySelector("div[id='modal-filtros']");
    switch (e.currentTarget.getAttribute('name')) {
        case 'chkEmpresa':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='empresa']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='empresa']").setAttribute("readOnly", true)
            }
            break;
        case 'chkSede':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='sede']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='sede']").setAttribute("readOnly", true)
            }
            break;
        case 'chkAlmacen':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='almacen']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='almacen']").setAttribute("readOnly", true)
            }
            break;
        case 'chkCondicion':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='condicion']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='condicion']").setAttribute("readOnly", true)
            }
            break;
        case 'chkSede':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='sede']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='sede']").setAttribute("readOnly", true)
            }
            break;
        case 'chkProveedor':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("input[name='razon_social']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("input[name='razon_social']").setAttribute("readOnly", true)
            }
            break;
        case 'chkCliente':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("input[name='cliente_razon_social']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("input[name='cliente_razon_social']").setAttribute("readOnly", true)
            }
            break;
        case 'chkFechaRegistro':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("input[name='fecha_inicio']").removeAttribute("readOnly")
                modalFiltro.querySelector("input[name='fecha_fin']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("input[name='fecha_inicio']").setAttribute("readOnly", true)
                modalFiltro.querySelector("input[name='fecha_fin']").setAttribute("readOnly", true)
            }
            break;
        case 'chkMoneda':
            if (e.currentTarget.checked == true) {
                modalFiltro.querySelector("select[name='moneda']").removeAttribute("readOnly")
            } else {
                modalFiltro.querySelector("select[name='moneda']").setAttribute("readOnly", true)
            }
            break;
        default:
            break;
    }
}


function open_filtros(){
    $('#modal-filtros').modal({
        show:true
    });
}
$('[name=todos_documentos]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=documento] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=documento] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});
$('[name=todas_condiciones]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=condicion] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=condicion] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});
$('[name=todas_empresas]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",false);
        });
    }
    // if($(this).prop('checked') == true) {
    //     $('[name=id_empresa] option').each(function(){
    //         $(this).prop("selected",true);
    //     });
    // }else{
    //     $('[name=id_empresa] option').each(function(){
    //         $(this).prop("selected",false);
    //     });
    // }
});
$('[name=todos_almacenes]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});

$('[name=id_empresa]').change(function(){
    var emp = $('[name=id_empresa]').val();
    if (emp > 0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'select_almacenes_empresa/'+emp,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var htmls = '';
                var prim = false;
                Object.keys(response).forEach(function (key){
                    if (!prim){
                        htmls += '<option value="'+response[key]['id_almacen']+'" selected>'+response[key]['descripcion']+'</option>';
                        prim = true;
                    } else {
                        htmls += '<option value="'+response[key]['id_almacen']+'">'+response[key]['descripcion']+'</option>';
                    }
                });
                console.log(htmls);
                $('[name=almacen]').html(htmls);
                $('[name=todas_empresas]').prop("checked",false);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});
function limpiar_proveedor(){
    $('[name=id_proveedor]').val('');
    $('[name=id_contrib]').val('');
    $('[name=razon_social]').val('');
}
function limpiar_transportista(){
    $('[name=id_proveedor_tra]').val('');
    $('[name=id_contrib_tra]').val('');
    $('[name=razon_social_tra]').val('');
}