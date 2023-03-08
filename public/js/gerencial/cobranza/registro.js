$(function () {
    // list();
    listarRegistros();
    actualizarDocVentReq();

    $(document).on('click','[data-action="nuevo-registro"]',function () {
        $('#modal-cobranza').modal('show');
        $('#formulario')[0].reset();
        $('.search-vendedor-guardar').val(null).trigger('change');
    });
    $(document).on('click','[data-action="modal-search-customer"]',function () {
        $('#modal-buscar-cliente').modal('show');
        let data =$(this).attr('data-form');
        $('#modal-buscar-cliente .formPage .modal-footer button').attr('data-form',data);
        customerList();
    });

    $('#tabla-clientes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('selected')){
            $(this).removeClass('selected');
            // document.querySelector("button[id='edit_customer']").setAttribute('disabled',true)
            document.querySelector("button[id='btnAgregarCliente']").setAttribute('disabled',true)

        } else {
            $('#tablaClientes').dataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            // document.querySelector("button[id='edit_customer']").removeAttribute('disabled')
            document.querySelector("button[id='btnAgregarCliente']").removeAttribute('disabled')
            // $("button[id='edit_customer']").attr('data-id',$(this)[0].firstChild.innerHTML);

        }
        var id = $(this)[0].firstChild.innerHTML;
        var nombre = $(this)[0].childNodes[1].textContent;
        var ruc = $(this)[0].childNodes[2].innerHTML;
        tempClienteSelected = {
            id,nombre,ruc
        };
    });

    $(document).on('click','.modal-editar',function () {
        $('#modal-editar-cliente').modal({show: true});
        var id = $(this).attr('data-id'),
            html= '';
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'get-cliente/'+id,
            data: {},
            dataType: 'JSON',
            success: function(response){
                if (response.status === 200) {

                    $('#modal-editar-cliente .modal-body input[name="edit_ruc_dni_cliente"]').val(response.data.nro_documento);
                    $('#modal-editar-cliente .modal-body input[name="edit_cliente"]').val(response.data.razon_social);


                    html='<option value="">Seleccione...</option>';
                    $.each(response.provincia, function (index, element) {
                        html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                    });
                    $('#modal-editar-cliente .modal-body select[name="provincia"]').html(html);

                    html='<option value="">Seleccione...</option>';
                    $.each(response.distrito, function (index, element) {
                        html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                    });
                    $('#modal-editar-cliente .modal-body select[name="distrito"]').html(html);

                    $('#modal-editar-cliente .modal-body select[name="pais"] option').removeAttr('selected');
                    $('#modal-editar-cliente .modal-body select[name="pais"] option[value="'+response.data.id_pais+'"]').attr('selected',true)

                    $('#modal-editar-cliente .modal-body select[name="departamento"] option').removeAttr('selected');
                    $('#modal-editar-cliente .modal-body select[name="departamento"] option[value="'+response.id_dpto+'"]').attr('selected',true)

                    $('#modal-editar-cliente .modal-body select[name="provincia"] option').removeAttr('selected');
                    $('#modal-editar-cliente .modal-body select[name="provincia"] option[value="'+response.id_prov+'"]').attr('selected',true)

                    $('#modal-editar-cliente .modal-body select[name="distrito"] option').removeAttr('selected');
                    $('#modal-editar-cliente .modal-body select[name="distrito"] option[value="'+response.id_dis+'"]').attr('selected',true)

                    // $('#modal-editar-cliente .modal-body input[name="id_cliente"]').val(response.data_old.id_cliente)
                    $('#modal-editar-cliente .modal-body input[name="id_contribuyente"]').val(response.data.id_contribuyente)
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('click','[data-action="agregar-cliente"]',function () {
        let data = $(this).data('button'),
            data_form = $(this).attr('data-form');
        $('#modal-buscar-cliente').modal('hide');

        if (data == 'ventas') {
            document.querySelector("form[form='ventas'] input[id='cliente']").value= tempClienteSelected.nombre;
            document.querySelector("form[form='ventas'] input[id='id_cliente']").value= tempClienteSelected.id;
            document.querySelector("form[form='ventas'] input[id='ruc_dni_cliente']").value= tempClienteSelected.ruc;
        }
        // else {
        //     document.querySelector("form[form='cobranza'] input[id='cliente']").value= tempClienteSelected.nombre;
        //     document.querySelector("form[form='cobranza'] input[id='id_cliente']").value= tempClienteSelected.id;
        // }



        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'buscar-cliente-seleccionado/'+tempClienteSelected.id,
            data: data,
            dataType: 'JSON',
            success: function(response){
                if (response.status===200) {
                    switch (data_form) {
                        case 'guardar-formulario':
                            document.querySelector("form[form='cobranza'] input[id='cliente']").value= response.data.razon_social;
                            // document.querySelector("form[form='cobranza'] input[id='id_cliente']").value= null;
                            document.querySelector("form[form='cobranza'] input[name='id_contribuyente']").value= response.data.id_contribuyente;
                        break;

                        case 'editar-formulario':

                            // $('[data-form="editar-formulario"] .modal-body [name="id_cliente"]').val(null);
                            $('[data-form="editar-formulario"] .modal-body [name="cliente"]').val(response.data.razon_social);
                            $('[data-form="editar-formulario"] .modal-body [name="id_contribuyente"]').val(response.data.id_contribuyente);
                        break;
                    }
                }

            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('submit','[data-form="guardar-cliente"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        Swal.fire({
            title: '¿Está seguro de guardar?',
            text: "Se guardara como un registro nuevo",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: 'nuevo-cliente',
                        data: data,
                        dataType: 'JSON',
                        success: function(response){
                            $('#tabla-clientes').DataTable().ajax.reload();
                        }
                    }).fail( function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    })
            },
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Éxito',
                    text: "Se actualizo su clave con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // window.location.reload();
                    }
                })
            }
        })
    });

    $(document).on('change','[data-select="departamento-select"]',function () {
        var id_departamento = $(this).val()
            this_select = $(this).closest('div.modal-body').find('div [name="provincia"]'),
            html='';

        if (id_departamento!==null && id_departamento!=='') {
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
        }else{
            this_select.html('<option value=""> Seleccione...</option>');
            $(this).closest('div.modal-body').find('div [name="distrito"]').html('<option value=""> Seleccione...</option>');
        }

    });

    $(document).on('change','[data-select="provincia-select"]',function () {
        var id_provincia = $(this).val(),
            this_select = $(this).closest('div.modal-body').find('div [name="distrito"]'),
            html='';

        if (id_provincia!==null && id_provincia!=='') {
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
        } else {
            this_select.html('<option value=""> Seleccione...</option>');
        }

    });

    $(document).on('click','.select-source',function () {
        var fuente = $('#fuente').val();
        var rubro = $('#rubro').val();
        var text = fuente.concat('-', rubro);
        var data_form = $(this).attr('data-form');
        $('[data-form="'+data_form+'"] [name="ff"]').val(text);
        $('#modal-fue-fin').modal('hide');
    });

    $('#formulario').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var form = $(this).attr('form');
        var type = $(this).attr('type');
        var page = 'formulario';

        var url;
        var msj;
        Swal.fire({
            title: 'Guardar',
            text: "¿Esta seguro de guardar este registro?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return  $.ajax({
                    type: 'POST',
                    url: 'guardar-registro-cobranza',
                    dataType: 'JSON',
                    data:data,
                    success: function(response){
                        if (response.status===200) {
                            Swal.fire(
                            'Éxito',
                            'Se guardo con éxito.',
                            'success'
                            ).then((result) => {
                                $('#istar-registro').DataTable().ajax.reload(null, false);
                                $('#modal-cobranza').modal('hide');
                            })
                        }else{
                            Swal.fire(
                                'Error',
                                'No se pudo eliminar.',
                                'error'
                            );
                        }
                        //
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {

        })
    });

    $(document).on('click','[data-action="actualizar"]',function () {
        actualizarDocVentReq();
    });

    $(document).on('click','.modal-lista-procesadas',function () {
        const input = $(this).closest('div').find('input').val();
        const action = $(this).closest('div').find('input').data('action');
        let data_form = $(this).attr('data-form');
        if (input) {
            $('#lista-procesadas').modal('show');
            $('#lista-procesadas .btn-seleccionar').attr('disabled','true');
            $('#lista-procesadas .modal-footer .btn-seleccionar').attr('data-form',data_form);
            listarRegistrosProcesadas(input, action);
        }

    });

    $(document).on('click','.selecionar',function () {
        const id_requerimiento = $(this).find('input').val();
        if (id_requerimiento) {
            $('#lista-procesadas .btn-seleccionar').removeAttr('disabled');
            $('#lista-procesadas .btn-seleccionar').attr('data-id',id_requerimiento);
        }

    });

    $(document).on('click','#lista-procesadas .btn-seleccionar',function () {
        const id_requerimiento = $(this).attr('data-id');
        var data_form =$(this).attr('data-form');
        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'seleccionar-registro/'+id_requerimiento,
            data: {},
            dataType: 'JSON',
            success: function(response){

                if (response.status===200) {
                    $('#lista-procesadas').modal('hide');

                    if ('guardar-formulario'===data_form) {
                            $('[data-form="guardar-formulario"] .modal-body select[name="moneda"]').removeAttr('selected');
                            $('[data-form="guardar-formulario"] .modal-body select[name="moneda"] option[value="'+response.data.id_moneda+'"]').attr('selected','true');
                            $('[data-form="guardar-formulario"] .modal-body input[name="importe"]').val(response.data.total_a_pagar)
                            $('[data-form="guardar-formulario"] .modal-body input[name="plazo_credito"]').val(response.data.credito_dias)
                            $('[data-form="guardar-formulario"] .modal-body input[name="fecha_emi"]').val(response.data.fecha_emision)
                            $('[data-form="guardar-formulario"] .modal-body input[name="oc"]').val(response.data.nro_orden)
                            $('[data-form="guardar-formulario"] .modal-body input[name="cdp"]').val(response.data.codigo_oportunidad)
                            // $('[data-form="guardar-formulario"] .modal-body input[name="id_cliente"]').val('')
                            $('[data-form="guardar-formulario"] .modal-body input[name="id_contribuyente"]').val(response.data.id_cliente)
                            $('[data-form="guardar-formulario"] .modal-body input[name="cliente"]').val(response.data.razon_social)
                            $('[data-form="guardar-formulario"] .modal-body input[name="id_doc_ven"]').val(response.data.id_doc_ven)

                            if (response.factura && response.factura) {
                                $('[data-form="guardar-formulario"] .modal-body input[name="fact"]').val(response.factura.serie+'-'+response.factura.numero);
                            }

                            $('[data-form="guardar-formulario"] .modal-body select[name="empresa"]').removeAttr('selected');
                            $('[data-form="guardar-formulario"] .modal-body select[name="empresa"] option[value="'+response.data.id_contribuyente_empresa+'"]').attr('selected','true');

                            $('[data-form="guardar-formulario"] .modal-body input[name="fecha_inicio"]').val(response.oc.inicio_entrega)
                            $('[data-form="guardar-formulario"] .modal-body input[name="fecha_entrega"]').val(response.oc.fecha_entrega)
                            $('[data-form="guardar-formulario"] .modal-body input[name="id_oc"]').val(response.oc.id)
                    }else{
                        $('[data-form="editar-formulario"] .modal-body select[name="moneda"]').removeAttr('selected');
                        $('[data-form="editar-formulario"] .modal-body select[name="moneda"] option[value="'+response.data.id_moneda+'"]').attr('selected','true');

                        $('[data-form="editar-formulario"] .modal-body input[name="importe"]').val(response.data.total_a_pagar)
                        $('[data-form="editar-formulario"] .modal-body input[name="plazo_credito"]').val(response.data.credito_dias)
                        $('[data-form="editar-formulario"] .modal-body input[name="fecha_emi"]').val(response.data.fecha_emision)
                        $('[data-form="editar-formulario"] .modal-body input[name="oc"]').val(response.data.nro_orden)
                        $('[data-form="editar-formulario"] .modal-body input[name="cdp"]').val(response.data.codigo_oportunidad)

                        // $('[data-form="editar-formulario"] .modal-body input[name="id_cliente"]').val('')
                        $('[data-form="editar-formulario"] .modal-body input[name="id_contribuyente"]').val(response.data.id_cliente)
                        $('[data-form="editar-formulario"] .modal-body input[name="cliente"]').val(response.data.razon_social)
                        $('[data-form="editar-formulario"] .modal-body input[name="id_doc_ven"]').val(response.data.id_doc_ven)

                        if (response.factura && response.factura) {
                            $('[data-form="editar-formulario"] .modal-body input[name="fact"]').val(response.factura.serie+'-'+response.factura.numero);
                        };

                        $('[data-form="editar-formulario"] .modal-body select[name="empresa"]').removeAttr('selected');
                        $('[data-form="editar-formulario"] .modal-body select[name="empresa"] option[value="'+response.data.id_contribuyente_empresa+'"]').attr('selected','true');

                        $('[data-form="editar-formulario"] .modal-body input[name="fecha_inicio"]').val(response.oc.inicio_entrega)
                        $('[data-form="editar-formulario"] .modal-body input[name="fecha_entrega"]').val(response.oc.fecha_entrega)

                        $('[data-form="editar-formulario"] .modal-body input[name="id_oc"]').val(response.oc.id)
                    }
                    // $('.dias-atraso').trigger('change');

                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('submit','[data-form="editar"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        Swal.fire({
            title: '¿Esta seguro de guardar?',
            text: "Se modificara su registro",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'editar-cliente',
                    data: data,
                    dataType: 'JSON'
                }).done(function( data ) {
                    return data;
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
            },
            allowOutsideClick: () => !Swal.isLoading()

        }).then((result) => {
            if (result.isConfirmed && result.value.status ===200) {
                Swal.fire(
                    'Éxito!',
                    'Se guardo con éxito',
                    'success'
                )
                $('#modal-editar-cliente').modal('hide');
            }
        })

    });

    $(document).on('click','.editar-registro',function (e) {
        e.preventDefault();
        let id_registro_cobranza = $(this).data('id');
        var fecha_emision ,fecha_vencimiento, numero_dias=0;

        $('#editar-formulario-cobranzas')[0].reset();
        // $('.search-vendedor-guardar').val(null).trigger('change');

        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'editar-registro/'+id_registro_cobranza,
            data: {},
            dataType: 'JSON'
        }).done(function( data ) {
            if (data.status===200) {

                $('#modal-editar-cobranza').modal('show');

                $('[data-form="editar-formulario"] .modal-body select[name="empresa"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="empresa"] option[value="'+data.data.id_empresa+'"]').attr('selected','true');

                $('[data-form="editar-formulario"] .modal-body select[name="sector"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="sector"] option[value="'+data.data.id_sector+'"]').attr('selected','true');

                $('[data-form="editar-formulario"] .modal-body select[name="tramite"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="tramite"] option[value="'+data.data.id_tipo_tramite+'"]').attr('selected','true');

                $('[data-form="editar-formulario"] .modal-body select[name="periodo"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="periodo"] option[value="'+data.data.id_periodo+'"]').attr('selected','true');

                // $('[data-form="editar-formulario"] .modal-body input[name="id_cliente"]').val(data.data.id_cliente);
                $('[data-form="editar-formulario"] .modal-body input[name="id_contribuyente"]').val(data.data.id_cliente);
                if (data.cliente) {
                    $('[data-form="editar-formulario"] .modal-body input[name="cliente"]').val(data.cliente.razon_social);
                }

                $('[data-form="editar-formulario"] .modal-body input[name="cdp"]').val(data.data.cdp);
                $('[data-form="editar-formulario"] .modal-body input[name="oc"]').val(data.data.ocam);
                $('[data-form="editar-formulario"] .modal-body input[name="orden_compra"]').val(data.data.oc_fisica);
                $('[data-form="editar-formulario"] .modal-body input[name="fact"]').val(data.data.factura);
                $('[data-form="editar-formulario"] .modal-body input[name="siaf"]').val(data.data.siaf);
                $('[data-form="editar-formulario"] .modal-body input[name="ue"]').val(data.data.uu_ee);
                $('[data-form="editar-formulario"] .modal-body input[name="ff"]').val(data.data.fuente_financ);
                $('[data-form="editar-formulario"] .modal-body select[name="moneda"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="moneda"] option[value="'+data.data.moneda+'"]').attr('selected','true');
                $('[data-form="editar-formulario"] .modal-body input[name="importe"]').val(data.data.importe);
                $('[data-form="editar-formulario"] .modal-body input[name="categ"]').val(data.data.categoria);
                $('[data-form="editar-formulario"] .modal-body input[name="fecha_emi"]').val(data.data.fecha_emision);
                $('[data-form="editar-formulario"] .modal-body input[name="fecha_rec"]').val(data.data.fecha_recepcion);
                $('[data-form="editar-formulario"] .modal-body select[name="estado_doc"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="estado_doc"] option[value="'+data.data.id_estado_doc+'"]').attr('selected','true');
                if (data.programacion_pago) {
                    $('[data-form="editar-formulario"] .modal-body input[name="fecha_ppago"]').val(data.programacion_pago.fecha);
                }

                // $('[data-form="editar-formulario"] .modal-body input[name="atraso"]').val(data.data.id_cliente);
                $('[data-form="editar-formulario"] .modal-body input[name="plazo_credito"]').val(data.data.plazo_credito);

                $('[data-form="editar-formulario"] .modal-body select[name="area"] option').removeAttr('selected');
                $('[data-form="editar-formulario"] .modal-body select[name="area"] option[value="'+data.data.id_area+'"]').attr('selected','true');

                $('[data-form="editar-formulario"] .modal-body input[name="fecha_inicio"]').val(data.data.inicio_entrega);
                $('[data-form="editar-formulario"] .modal-body input[name="fecha_entrega"]').val(data.data.fecha_entrega);
                if (!$.isEmptyObject(data.vendedor)) {
                    $('.search-vendedor').val(null).trigger('change');
                    var newOption = new Option(data.vendedor.nombre, data.vendedor.id_vendedor, true, true);
                    $('.search-vendedor').append(newOption).trigger('change');

                }else{
                    $('.search-vendedor').val(null).trigger('change');
                    var newOption = new Option(null, null, true, true);
                    // $('.search-vendedor').val(null).trigger('change');
                    // var newOption = new Option(data.vendedor.nombre, data.vendedor.id_vendedor, false, false);
                    // $('.search-vendedor').append(newOption).trigger('change');
                }


                $('[data-form="editar-formulario"] .modal-body input[name="id_doc_ven"]').val(data.data.id_doc_ven);
                $('[data-form="editar-formulario"] .modal-body input[name="id_registro_cobranza"]').val(data.data.id_registro_cobranza);

                fecha_emision = new Date($('[data-form="editar-formulario"] input[name="fecha_rec"]').val().split('/').reverse().join('-')).getTime();


                var fecha_actual = new Date().getTime();
                var atraso = fecha_actual - fecha_emision;
                atraso = atraso/(1000*60*60*24);
                if (atraso>0) {
                    atraso = Math.trunc(atraso);
                }else{
                    atraso = 0;
                }
                $('[data-form="editar-formulario"] input[name="atraso"]').val(atraso);


            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('change','.dias-atraso',function () {
        var data_form = $(this).attr('data-form');
        var fecha_emision = new Date($('[data-form="'+data_form+'"] input[name="fecha_rec"]').val().split('/').reverse().join('-')).getTime() ,
            fecha_vencimiento= new Date($('[data-form="'+data_form+'"] input[name="fecha_ppago"]').val().split('/').reverse().join('-')).getTime(),
            numero_dias=0;

        numero_dias = fecha_vencimiento - fecha_emision  ;
        numero_dias = numero_dias/(1000*60*60*24)
        numero_dias = numero_dias*-1;
        if (numero_dias<=0) {
            numero_dias = 0;
        }



        var fecha_actual = new Date().getTime();
        var atraso = fecha_actual - fecha_emision;
        atraso = atraso/(1000*60*60*24);
        if (atraso>0) {
            atraso = Math.trunc(atraso);
        }else{
            atraso = 0;
        }

        $('[data-form="'+data_form+'"] input[name="atraso"]').val(atraso);
        $('[data-form="'+data_form+'"] input[name="dias_atraso"]').val(atraso);
        $('[data-form="editar-formulario"] input[name="atraso"]').val(atraso);
        $('[data-form="editar-formulario"] input[name="dias_atraso"]').val(atraso);

    });

    $(document).on('submit','[data-form="editar-formulario"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        Swal.fire({
            title: '¿Está seguro de editar el registro?',
            text: 'Se editará el registro',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, continuar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'modificar-registro',
                    data: data,
                    dataType: 'JSON',
                    success: function(response) {
                        // $('#listar-registro').DataTable().ajax.reload(null, false);
                        // $('#modal-editar-cobranza').modal('hide');
                        window.location.reload();
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            }
        });

    });

    $(document).on('click','.modal-fase',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),
            html = '';
        $('#modal-agregar-fase [data-form="guardar-fase"] input[name="id_registro_cobranza"]').val(id);
        $('#modal-agregar-fase').modal('show');
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'obtener-fase/'+id,
            data: {},
            dataType: 'JSON'
        }).done(function( data ) {
            if (data.status===200) {
                $.each(data.fases, function (index, element) {
                    html+='<tr>'+
                        '<td class="text-center">'+element.fase+'</td>'+
                        '<td class="text-center">'+element.fecha+'</td>'+
                        '<td class="text-center"><button class="btn btn-danger eliminar-fase" data-id="'+element.id+'"><i class="fa fa-trash"></i></button></td>'+
                    '</tr>';
                });
                $('[data-table="table-fase"]').html(html);
                // $('#listar-registros').DataTable().ajax.reload();
            }else{
                $('[data-table="table-fase"]').html(html);
            }

        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('submit','[data-form="guardar-fase"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        Swal.fire({
            title: '¿Está seguro de guardar?',
            text: "Se guardara el registro",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
            return $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'guardar-fase',
                    data: data,
                    dataType: 'JSON'
                }).done(function( data ) {
                    return data
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status === 200) {
                    $('#modal-agregar-fase').modal('hide');
                    $('#istar-registro').DataTable().ajax.reload(null, false);
                }
            }
        })
    });

    $('#modal-filtros').on('hidden.bs.modal', () => {
        listarRegistros(data_filtros);
    });

    $(document).on('click','.eliminar-fase',function () {
        var id = $(this).attr('data-id');

        Swal.fire({
            title: '¿Está seguro de eliminar?',
            text: "Se eliminara el registro",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'eliminar-fase',
                    data: {id:id},
                    dataType: 'JSON'
                }).done(function( data ) {
                    return data
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status === 200) {
                    // $('#modal-agregar-fase').modal('hide');
                    $(this).closest('tr').remove();
                    $('#istar-registro').DataTable().ajax.reload(null, false);
                }
            }
        })
    });

    $(document).on('change','.select-check',function () {
        var key = $(this).attr('data-check'),
            this_check = $(this);
        checkFiltros(key,this_check);
    });

    $(document).on('change','[data-select="select"]',function () {
        var key = $(this).attr('data-check'),
            this_check = $(this).closest('div.row').find('[data-check="'+key+'"]');
        checkFiltros(key,this_check);
    });

    $("#listar-registros").on('click','.modal-penalidad',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),
            titulo = $(this).attr('title'),
            html='';
        $('[data-form="guardar-penalidad"]')[0].reset();
        $('[data-form="guardar-penalidad"]').find('[name="id"]').val(0);
        $('#modal-penalidad-cobro input[name="id_cobranza_penal"]').val(id);
        $('#modal-penalidad-cobro h3').text('REGISTRO DE ' + titulo);
        $('#modal-penalidad-cobro #btnPenalidad').text('Grabar ' + titulo.toLowerCase());
        $('#modal-penalidad-cobro').modal('show');

        $('[data-form="guardar-penalidad"]').find('[name="tipo_penal"]').val(titulo);
        if (titulo !== 'PENALIDAD') {
            $('[data-estado="cambio"]').attr('hidden','true');
        }else{
            $('[data-estado="cambio"]').removeAttr('hidden');
        }

        obtenerPenalidades(id, titulo);
    });

    $("#form-penalidad").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        Swal.fire({
            title: '¿Está seguro de guardar?',
            text: "Se guardara el registro",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'guardar-penalidad',
                    data: data,
                    dataType: 'JSON'
                }).done(function( data ) {
                    return data;
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status === 200) {
                    obtenerPenalidades(result.value.data.id_registro_cobranza, result.value.data.tipo);
                    $('[data-form="guardar-penalidad"]')[0].reset();
                }
            }
        })
    });

    $(document).on('click','.eliminar',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: '¿Esta seguro de eliminar?',
            text: "Se eliminara su registro",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return $.ajax({
                    type: 'get',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'eliminar-registro-cobranza/'+id,
                    data: {},
                    dataType: 'JSON'
                }).done(function( data ) {
                    return data;
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
            },
            allowOutsideClick: () => !Swal.isLoading()

        }).then((result) => {
            if (result.isConfirmed && result.value.status ===200) {

                Swal.fire(
                    'Éxito!',
                    'Se elimino con éxito',
                    'success'
                )
                $('#istar-registro').DataTable().ajax.reload(null, false);
            }
        })

    });

    $("#tablaPenalidad").on('click','[data-action="editar-penalidad"]',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'editar-penalidad/'+id,
            data: {},
            dataType: 'JSON'
        }).done(function( data ) {

            $('#form-penalidad').find('[name="fecha_penal"]').val(data.fecha);
            $('#form-penalidad').find('[name="doc_penal"]').val(data.documento);
            $('#form-penalidad').find('[name="importe_penal"]').val(data.monto);
            $('#form-penalidad').find('[name="obs_penal"]').val(data.observacion);
            $('#form-penalidad').find('[name="id"]').val(data.id_penalidad);
            $('#form-penalidad').find('[name="id_cobranza_penal"]').val(data.id_registro_cobranza);
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $("#tablaPenalidad").on('click','[data-action="estados-penalidad"]',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),
            titulo =$('[name="tipo_penal"]').val(),
            estado_penalidad =$(this).attr('data-title'),
            id_registro_cobranza =$(this).attr('data-id-registro-cobranza');

        if (estado_penalidad == 'DEVOLUCION') {
            Swal.fire({
                title: '¿Quién es responsable de la devolución?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Entidad',
                denyButtonText: 'Marca',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    estadoPenalidad(titulo, id, id_registro_cobranza, estado_penalidad, 'ENTIDAD');
                } else if (result.isDenied) {
                    estadoPenalidad(titulo, id, id_registro_cobranza, estado_penalidad, 'MARCA');
                }
            });
        } else {
            estadoPenalidad(titulo, id, id_registro_cobranza, estado_penalidad);
        }

    });

    $("#tablaPenalidad").on('click','[data-action="eliminar-penalidad"]',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),
            titulo =$('[data-form="guardar-penalidad"]').find('[name="tipo_penal"]').val(),
            id_registro_cobranza =$(this).attr('data-id-registro-cobranza'),
            estado = $(this).attr('data-estado');
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'eliminar-penalidad',
            data: {tipo:titulo,id:id,id_registro_cobranza:id_registro_cobranza,estado:estado},
            dataType: 'JSON'
        }).done(function( data ) {
            obtenerPenalidades(id_registro_cobranza, 'PENALIDAD');
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('click','.modal-observaciones',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),html='';
        $('#modal-observaciones [data-form="guardar-observaciones"]').find('[name="id"]').val(id);
        $('#modal-observaciones').modal('show');
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'obtener-observaciones',
            data: {id:id},
            dataType: 'JSON'
        }).done(function( data ) {
            listarObservaciones(data)

        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })

    });

    $(document).on('submit','[data-form="guardar-observaciones"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'guardar-observaciones',
            data: data,
            dataType: 'JSON'
        }).done(function( data ) {
            listarObservaciones(data)
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $(document).on('click','[data-action="eliminar-observacion"]',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id'),
            id_registro_cobranza =$(this).attr('data-id-registro-cobranza');
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'eliminar-observacion',
            data: {id:id,id_registro_cobranza:id_registro_cobranza},
            dataType: 'JSON'
        }).done(function( data ) {
            listarObservaciones(data);
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });
});

function list() {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'listar-registros',
        data: {},
        dataType: 'JSON',
        success: function(response){
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarRegistros(filtros) {
    const $tabla = $('#listar-registros').DataTable({
        dom: 'Bfrtip',
        pageLength: 30,
        language: idioma,
        serverSide: true,
        destroy:true,
        initComplete: function (settings, json) {
            const $filter = $('#listar-registros_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tabla.search($input.val()).draw();
            });
        },
        drawCallback: function (settings) {
            $('#listar-registros_filter input').prop('disabled', false);
            $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
            $('#listar-registros_filter input').trigger('focus');
        },
        order: [[0, 'asc']],
        ajax: {
            url: 'listar-registros',
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrf_token},
            // data: {
            //     empresa: meOrAll,
            //     estado: idEmpresa,
            //     fase: idSede,
            //     fecha_emision_inicio: idGrupo,
            //     fecha_emision_fin: idDivision,
            //     simbolo: fechaRegistroDesde
            // },
        },
        columns: [
            {data: 'empresa', className: "text-center"},
            {data: 'ocam', className: "text-center"},
            {data: 'cliente'},
            {data: 'factura', className: "text-center"},
            {data: 'uu_ee', className: "text-center"},
            {data: 'fuente_financ', className: "text-center"},
            {data: 'oc_fisica', className: "text-center"},
            {data: 'siaf', className: "text-center"},
            {data: 'fecha_emision', className: "text-center"},
            {data: 'fecha_recepcion', className: "text-center"},
            {data: 'atraso', className: "text-center"},
            {data: 'moneda', className: "text-center"},
            {data: 'importe', className: "text-right"},
            {data: 'estado', className: "text-right"},
            // {data: 'estado_documento', className: "text-right"},
            {data: 'area', className: "text-right"},
            {
                render: function (data, type, row) {
                    return (`<label class="label label-primary" style="font-size: 10px;">${row['fase']}</label>`);
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    var fecha_inicio = row['inicio_entrega'] ? row['inicio_entrega']:'-';
                    var fecha_entrega = row['fecha_entrega'] ? row['fecha_entrega']:'-';
                    return (`${fecha_inicio} <br> ${fecha_entrega}`);
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    html='';
                        html+='<div class="btn-group">'+
                            '<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  <span class="caret"></span></button>'+
                            '<ul class="dropdown-menu dropdown-menu-right">'+
                                '<li><a href="#" class="editar-registro" data-id="'+row['id_registro_cobranza']+'" data-toggle="tooltip" title="Editar" data-original-title="Editar">Editar</a></li>'+

                                '<li><a href="#" class="modal-fase" data-id="'+row['id_registro_cobranza']+'" title="Fases">Fases</a></li>'

                                if (row['id_estado_doc'] ===5) {
                                    html+='<li><a href="#" class="modal-penalidad" data-toggle="tooltip" data-id="'+row['id_registro_cobranza']+'" title="PENALIDAD">Penalidades</a></li>'+

                                    '<li><a href="#" class="modal-penalidad" data-toggle="tooltip" data-id="'+row['id_registro_cobranza']+'" title="RETENCION">Retenciones</a></li>'+

                                    '<li><a href="#" class="modal-penalidad" data-toggle="tooltip" data-id="'+row['id_registro_cobranza']+'" title="DETRACCION">Detracciones</a></li>'
                                }
                                html+='<li><a href="#" class="modal-observaciones" data-id="'+row['id_registro_cobranza']+'" title="OBSERVACIONES">Observaciones</a></li>'+
                                '<li role="separator" class="divider"></li>'+

                                '<li><a href="#" class="eliminar" data-id="'+row['id_registro_cobranza']+'" title="Eliminar">Eliminar</a></li>'+
                            '</ul>'+
                        '</div>'
                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        buttons: [
            {
                text: '<i class="fas fa-filter"></i> Filtros : 0',
                attr: { id: 'btnFiltros' },
                action: () => {
                    $('#modal-filtros').modal('show');
                }, className: 'btn-default btn-sm'
            },
            {
                text: '<i class="fas fa-file-excel"></i> Descargar',
                attr: { id: 'btnExcel' },
                action: () => {
                    exportarExcel();
                }, className: 'btn-default btn-sm'
            },
            {
                text: '<i class="fas fa-file-excel"></i> Descargar Power BI',
                attr: { id: 'btnExcelPowerBI' },
                action: () => {
                    exportarExcelPowerBi();
                }, className: 'btn-default btn-sm'
            }
        ]
    });
    $tabla.on('search.dt', function() {
        $('#listar-registros_filter input').attr('disabled', true);
        $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
    });
    $tabla.on('init.dt', function(e, settings, processing) {
        $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
    });
    $tabla.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });
}

function customerList() {
    var vardataTables = funcDatatables();
    tableRequerimientos = $("#tabla-clientes").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        ajax: {
            url: "listar-clientes",
            type: "POST"
        },
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'text-center'}
        ],
        columns: [
            {data: 'id_contribuyente', name:"id_contribuyente"},
            {data: 'razon_social', name:"razon_social"},
            {data: 'nro_documento', name:"nro_documento"},
        ],
        order: [[1, "asc"]],
        initComplete: function(data){
            if(tempoNombreCliente.length >0) {
                // $('#example_filter input').val(tempoNombreCliente);
                this.api().search(tempoNombreCliente).draw();
                document.querySelector("input[type='search']").focus();
                document.querySelector("input[type='search']").setSelectionRange(tempoNombreCliente.length,tempoNombreCliente.length );
                tempoNombreCliente='';
            }
        }
    });
}

function ModalAddNewCustomer() {
    $('#modal-agregar-cliente').modal({show: true});
    $('[name=nuevo_ruc_dni_cliente]').val('');
    $('[name=nuevo_cliente]').val('');
}

function agregarCliente(tipo){
    $('#modal-buscar-cliente').modal('hide');
    let data_form = $(this).attr('data-form');
    if (tipo == 'ventas') {
        document.querySelector("form[form='ventas'] input[id='cliente']").value= tempClienteSelected.nombre;
        document.querySelector("form[form='ventas'] input[id='id_cliente']").value= tempClienteSelected.id;
        document.querySelector("form[form='ventas'] input[id='ruc_dni_cliente']").value= tempClienteSelected.ruc;
    } else {
        document.querySelector("form[form='cobranza'] input[id='cliente']").value= tempClienteSelected.nombre;
        document.querySelector("form[form='cobranza'] input[id='id_cliente']").value= tempClienteSelected.id;
    }
    tempClienteSelected = {};
}

function searchSource(type){
    $('#modal-fue-fin').modal({show: true, backdrop: 'static'});
    $('#modal-fue-fin').on('shown.bs.modal', function(){
        $('[name=fuente]').select();
    });
    $('#modal-fue-fin .modal-footer button').attr('data-form',type);
}

function fuenteFinan(value){
    $('#rubro').empty();
    $('#rubro').append('<option value="" disabled selected>Elija una opción</option>');
    var opcion;
    if (value == 1){
        opcion = '<option value="00">RECURSOS ORDINARIOS</option>';
    }else if(value == 2){
        opcion = '<option value="09">RECURSOS DIRECTAMENTE RECAUDADOS</option>';
    }else if(value == 3){
        opcion = '<option value="19">RECURSOS POR OPERACIONES OFICIALES DE CREDITO</option>';
    }else if(value == 4){
        opcion = '<option value="13">DONACIONES Y TRANSFERENCIAS</option>';
    }else if(value == 5){
        opcion = '<option value="04">CONTRIBUCIONES A FONDOS</option><option value="07">FONDO DE COMPENSACION MUNICIPAL</option><option value="08">IMPUESTOS MUNICIPALES</option><option value="18">CANON Y SOBRECANON, REGALIAS, RENTA DE ADUANAS Y PARTICIPACIONES</option>';
    }
    $('#rubro').append(opcion);
}

function actualizarDocVentReq() {
    $.ajax({
        type: 'GET',
        url: 'actualizar-ven-doc-req',
        dataType: 'JSON',
        data:{},
        success: function(response){

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarRegistrosProcesadas(input, action) {
    var vardataTables = funcDatatables();

    $("#lista-ventas-procesadas").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        destroy: true,
        pageLength: 20,
        lengthChange: false,
        serverSide: true,
        ajax: {
            url: 'buscar-registro/'+input+'/'+action,
            type: "GET"
        },
        columns: [
            { data: "id_requerimiento_logistico", name:"requerimiento_logistico_view.id_requerimiento_logistico"
             },
            { data: "nro_orden", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['nro_orden']+'')
                }
            },
            { data: "codigo_oportunidad", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['codigo_oportunidad']+'')
                }
            },
            {
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['serie']+'-'+row['numero']);
                },
                className: "text-center selecionar",
            },
            { data: "fecha_emision", className: "text-center selecionar",
                render: function (data, type, row) {
                    return ('<input type="hidden" value="'+row['id_requerimiento_logistico']+'">'+row['fecha_emision']+'')
                }
            },

        ],
        order: [[1, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }]
    });
}

function checkFiltros(key,this_check) {
    switch (key) {
        case 'empresa':
            if (this_check.prop('checked')) {
                $('#modal-filtros .modal-body [name="empresa"]').removeAttr('disabled');
                empresa_filtro = $('#modal-filtros .modal-body [name="empresa"]').val();
            }else{
                $('#modal-filtros .modal-body [name="empresa"]').attr('disabled','true');
                empresa_filtro = null;
            }
            break;

        case 'estado':
            if (this_check.prop('checked')) {
                $('#modal-filtros .modal-body [name="fil_estado"]').removeAttr('disabled');
                estado_filttro = $('#modal-filtros .modal-body [name="fil_estado"]').val();
            }else{
                $('#modal-filtros .modal-body [name="fil_estado"]').attr('disabled','true');
                estado_filttro = null;
            }
            break;
        case 'fase':
            if (this_check.prop('checked')) {
                $('#modal-filtros .modal-body [name="fil_fase"]').removeAttr('disabled');
                fase_filtro = $('#modal-filtros .modal-body [name="fil_fase"]').val();
            }else{
                $('#modal-filtros .modal-body [name="fil_fase"]').attr('disabled','true');
                fase_filtro = null;
            }
            break;
        case 'emision':
            if (this_check.prop('checked')) {
                $('#modal-filtros .modal-body [name="fil_emision_ini"]').removeAttr('disabled');
                $('#modal-filtros .modal-body [name="fil_emision_fin"]').removeAttr('disabled');
                fecha_emision_inicio_filtro = $('#modal-filtros .modal-body [name="fil_emision_ini"]').val();
                fecha_emision_fin_filtro = $('#modal-filtros .modal-body [name="fil_emision_fin"]').val();
            }else{
                $('#modal-filtros .modal-body [name="fil_emision_ini"]').attr('disabled','true');
                $('#modal-filtros .modal-body [name="fil_emision_fin"]').attr('disabled','true');
                fecha_emision_inicio_filtro = null;
                fecha_emision_fin_filtro = null;
            }
            break;
        case 'importe':
            if (this_check.prop('checked')) {
                $('#modal-filtros .modal-body [name="fil_simbol"]').removeAttr('disabled');
                $('#modal-filtros .modal-body [name="fil_importe"]').removeAttr('disabled');
                importe_simbolo_filtro  =$('#modal-filtros .modal-body [name="fil_simbol"]').val();
                importe_total_filtro    =$('#modal-filtros .modal-body [name="fil_importe"]').val();
            }else{
                $('#modal-filtros .modal-body [name="fil_simbol"]').attr('disabled','true');
                $('#modal-filtros .modal-body [name="fil_importe"]').attr('disabled','true');
                importe_simbolo_filtro=null;
                importe_total_filtro=null;
            }
            break;
    }
    data_filtros={
        "empresa":empresa_filtro,
        "estado":estado_filttro,
        "fase":fase_filtro,
        "fecha_emision_inicio":fecha_emision_inicio_filtro,
        "fecha_emision_fin":fecha_emision_fin_filtro,
        "simbolo":importe_simbolo_filtro,
        "importe":importe_total_filtro
    };
}

function obtenerPenalidades(id, titulo) {
    let html = '';
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'obtener-penalidades',
        data: {tipo:titulo, id:id},
        dataType: 'JSON',
        success: function(data) {
            if (data.status===200) {
                let datos = data.penalidades;

                datos.forEach(element => {
                    html+='<tr>'
                    html+='<td>'+element.tipo+'</td>'
                    html+='<td>'+element.documento+'</td>'
                    html+='<td>'+element.monto+'</td>';

                    switch (element.estado) {
                        case 1:
                            html+='<td>ELABORADO</td>';
                        break;
                        case 2:
                            html+='<td>ANULADO</td>';
                        break;
                    }
                    html+='<td '+(element.tipo!=='PENALIDAD'?`hidden`:``)+'>'+element.estado_penalidad+'</td>'

                    html+='<td>'+element.fecha+'</td>'
                    html+='<td>';
                        if (element.tipo==='PENALIDAD') {
                            html+='<button class="btn btn-xs btn-success" data-action="estados-penalidad" data-title="DEVOLUCION" title="Devolución" data-id="'+element.id_penalidad+'" data-id-registro-cobranza="'+element.id_registro_cobranza+'"><i class="fa fa-exchange-alt"></i></button>'+
                            '<button class="btn btn-xs" data-action="estados-penalidad" data-title="ANULADA" title="Anular" data-id="'+element.id_penalidad+'" data-id-registro-cobranza="'+element.id_registro_cobranza+'"><i class="fa fa-ban"></i></button>';
                        }

                        html+='<button class="btn btn-xs btn-primary" data-action="editar-penalidad" data-title="" title="Editar" data-id="'+element.id_penalidad+'" data-id-registro-cobranza="'+element.id_registro_cobranza+'"><i class="fa fa-edit"></i></button>'+
                            '<button class="btn btn-xs btn-danger" data-action="eliminar-penalidad" data-title="" title="Eliminar" data-id="'+element.id_penalidad+'"data-id-registro-cobranza="'+element.id_registro_cobranza+'" data-estado="7"><i class="fa fa-times"></i></button>'+
                        '</td>'
                    html+='</tr>';
                });
                $('#tablaPenalidad tbody').html(html);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}

function estadoPenalidad(titulo, id, id_registro_cobranza, estado_penalidad, gestion = '') {
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'cambio-estado-penalidad',
        data: {tipo: titulo, id: id, id_registro_cobranza: id_registro_cobranza, estado_penalidad: estado_penalidad, gestion: gestion},
        dataType: 'JSON'
    }).done(function( data ) {
        obtenerPenalidades(id_registro_cobranza, 'PENALIDAD');
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}

function exportarExcel() {
    window.open('exportar-excel/'+JSON.stringify(data_filtros));
}

function exportarExcelPrueba() {
    // window.open('exportar-excel/'+JSON.stringify(data_filtros));
    var data_json = JSON.stringify(data_filtros);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'exportar-excel-prueba',
        data: {data:data_json},
        dataType: 'JSON'
    }).done(function( data ) {
        console.log(data);
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}

function listarObservaciones(data) {
    var html='';
    $.each(data, function (index, element) {
        html+='<tr>'
            html+='<td>'+element.descripcion+'</td>'
            html+='<td>'+element.usuario+'</td>'
            html+='<td>'+element.estado+'</td>'
            html+='<td>'+element.created_at+'</td>'
            html+='<td>'+
                '<button class="btn btn-xs" data-action="eliminar-observacion" title="Eliminar" data-id="'+element.id+'"data-id-registro-cobranza="'+element.cobranza_id+'"><i class="fa fa-times"></i></button>'+
            '</td>'
        html+='</tr>'
    });
    $('[data-table="observaciones"]').html(html);
}

function exportarExcelPowerBi() {
    window.open('exportar-excel-power-bi/'+JSON.stringify(data_filtros));
}

function devolucion(id,id_registro_cobranza,motivo) {
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'cambio-estado-penalidad',
        data: {id:id,id_registro_cobranza:id_registro_cobranza,motivo:motivo},
        dataType: 'JSON'
    }).done(function( data ) {
        obtenerPenalidades(id);
        Swal.fire('Éxito!', 'Devolución por '+motivo+'', 'success')
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
