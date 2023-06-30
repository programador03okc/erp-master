var $tablaListaProveedores;
class ListarProveedorView {

    constructor(proveedorCtrl) {
        this.proveedorCtrl = proveedorCtrl;
        this.objectBtnEdition;
        this.objectBtnEditionContacto;
        this.objectBtnEditionCuenta;
        this.objectBtnEditionEstablecimiento;
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

    }

    initializeEventHandler() {

        // ver
        $('#form-listaProveedores').on("click", "button.handleClickVerDetalleProveedor", (e) => {
            this.verProveedor(e.currentTarget);
        });
        $('#modal-proveedor').on("click", "button.handleClickNuevoCuentaBancariaProveedor", () => {
            this.agregarCuentaBancaria();
        });
        $('#modal-proveedor').on("click", "button.handleClickNuevoAdjuntoProveedor", () => {
            this.agregarAdjuntoProveedor();
        });
        $('#modal-proveedor').on("click", "button.handleClickNuevoContactoProveedor", () => {
            this.agregarContactoProveedor();
        });
        $('#modal-proveedor').on("keyup", "input.handleKeyUpNroDocumento", (e) => {
            this.validarNroDocumento(e);
        });
        $('#modal-proveedor').on("focusout", "input.handleFocusoutNroDocumento", (e) => {
            this.obtenerDataContribuyenteSegunNroDocumento(e.currentTarget);
        });
        $('#modal-proveedor').on("keyup", "input.handleKeyUpRazonSocial", (e) => {
            this.ponerMayusculaRazonSocial(e);
        });
        $('#modal-proveedor').on("click", "button.handleClickUbigeoSoloNacional", (e) => {
            this.soloUbigeoNacional(e);
        });
        $('#modal-proveedor').on("keyup", "input.handleKeyUpTelefono", (e) => {
            this.validacionRegexSoloNumeros(e);
        });
        $('#modal-proveedor').on("keyup", "input.handleKeyUpCelular", (e) => {
            this.validacionRegexSoloNumeros(e);
        });

        $('#modal-proveedor').on("click", "button.handleClickOpenModalUbigeoProveedor", () => {
            this.openModalUbigeoProveedor();
        });
        //  modal establecimiento
        $('#modal-proveedor').on("click", "button.handleClickNuevoEstablecimiento", () => {
            this.agregarEstablecimientoProveedor();
        });
        $('#modal-agregar-establecimiento').on("click", "button.handleClickOpenModalUbigeoEstablecimiento", () => {
            this.openModalUbigeoEstablecimiento();
        });
        $('#modal-agregar-establecimiento').on("click", "button.handleClickAgregarEstablecimiento", () => {
            this.agregarEstablecimientoAProveedor();
        });
        //  modal contacto
        $('#modal-agregar-contacto').on("click", "button.handleClickOpenModalUbigeoContacto", () => {
            this.openModalUbigeoContacto();
        });

        $('#modal-agregar-contacto').on("click", "button.handleClickAgregarContacto", () => {
            this.agregarContactoAProveedor();
        });
        $('#modal-agregar-contacto').on("click", "button.handleClickActualizarContacto", () => {
            this.actualizarContactoProveedor();
        });
        $('#modal-agregar-cuenta-bancaria').on("click", "button.handleClickActualizarCuentaBancaria", () => {
            this.actualizarCuentaBancaria();
        });
        $('#modal-agregar-establecimiento').on("click", "button.handleClickActualizarEstablecimiento", () => {
            this.actualizarEstablecimiento();
        });
        $('#modal-agregar-contacto').on("keyup", "input.handleKeyUpTelefono", (e) => {
            this.validacionRegexSoloNumeros(e);
        });
        $('#modal-proveedor').on("click", "button.handleClickAnularContacto", (e) => {
            this.anularContactoProveedor(e.currentTarget);
        });
        $('#modal-proveedor').on("click", "button.handleClickEditarContacto", (e) => {
            this.editarContactoProveedor(e.currentTarget);
        });
        $('#modal-proveedor').on("click", "button.handleClickEditarCuentaBancaria", (e) => {
            this.editarCuentaBancariaProveedor(e.currentTarget);
        });

        $('#modal-proveedor').on("click", "button.handleClickEditarEstablecimiento", (e) => {
            this.editarEstablecimientoProveedor(e.currentTarget);
        });


        $('#modal-proveedor').on("click", "button.handleClickAnularEstablecimiento", (e) => {
            this.anularEstablecimientoProveedor(e.currentTarget);
        });
        //fin modal contacto

        //  modal cuenta bancaria
        $('#modal-agregar-cuenta-bancaria').on("click", "button.handleClickAgregarCuentaBancaria", () => {
            this.agregarCuentaBancariaAProveedor();
        });

        $('#modal-proveedor').on("click", "button.handleClickAnularCuentaBancaria", (e) => {
            this.anularCuentaBancariaProveedor(e.currentTarget);
        });

        // guardar
        $('#modal-proveedor').on("click", "button.handleClickGuardarProveedor", (e) => {
            e.currentTarget.setAttribute("disabled", true);
            this.guardarProveedor(e.currentTarget);
        });
        // editar
        $('#form-listaProveedores').on("click", "button.handleClickEditarProveedor", (e) => {
            this.editarProveedor(e.currentTarget);
        });
        // anular
        $('#form-listaProveedores').on("click", "button.handleClickAnularProveedor", (e) => {
            this.anularProveedor(e.currentTarget);
        });
        // actualizar
        $('#modal-proveedor').on("click", "button.handleClickActualizarProveedor", (e) => {
            this.actualizarProveedor(e.currentTarget);
        });

        $('#form-proveedor').on("keypress",  (e)=> {
            if (e.key == 'Enter'){
                if(document.querySelector("form[id='form-proveedor']").getAttribute("type")=='register'){
                    this.guardarProveedor(e.currentTarget);
                }else{
                    this.actualizarProveedor(e.currentTarget);
                }

            };
        });
        $('#form-agregar-establecimiento').on("keypress",  (e)=> {
            if (e.key == 'Enter'){
                if(document.querySelector("form[id='form-proveedor']").getAttribute("type")=='register'){
                    this.agregarEstablecimientoAProveedor();
                }else{
                    this.actualizarEstablecimiento();
                }

            };
        });
        $('#form-agregar-contacto').on("keypress",  (e)=> {
            if (e.key == 'Enter'){
                if(document.querySelector("form[id='form-proveedor']").getAttribute("type")=='register'){
                    this.agregarContactoAProveedor();
                }else{
                    this.actualizarContactoProveedor();
                }

            };
        });
        $('#form-agregar-cuenta-bancaria-proveedor').on("keypress",  (e)=> {
            if (e.key == 'Enter'){
                if(document.querySelector("form[id='form-proveedor']").getAttribute("type")=='register'){
                    this.agregarCuentaBancariaAProveedor();
                }else{
                    this.actualizarCuentaBancaria();
                }

            };
        });

        let optionByURL =location.search.split('accion=')[1];
        if(optionByURL == 'nuevo'){
            document.getElementById("btnCrearProveedor").click();
        }

    }
    // limpiar tabla
    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }
    mostrar() {
        vista_extendida();
        var vardataTables = funcDatatables();
        const button_nuevo = (array_accesos.find(element => element === 254)?{
                text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo',
                attr:  {
                    id: 'btnCrearProveedor'
                },
                action: ()=>{
                    this.nuevoProveedor();

                },
                className: 'btn-success btn-sm'
            }:[]);
        $tablaListaProveedores = $('#listaProveedores').DataTable({
            'dom': vardataTables[1],
            'buttons': [button_nuevo,
                // {
                //     text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                //     attr:  {
                //         id: 'btnFiltrosProveedor'
                //     },
                //     action: ()=>{
                //         this.abrirFiltrosProveedor();

                //     },
                //     className: 'btn-default btn-sm'
                // }
            ],
            'language': vardataTables[0],
            'order': [[2, 'asc']],
            'bLengthChange': false,
            'serverSide': true,
            'ajax': {
                'url': 'obtener-data-listado',
                'type': 'POST',
                beforeSend: data => {

                    $("#listaProveedores").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },

            },
            'columns': [
                { 'data': 'contribuyente.tipo_documento_identidad.descripcion', 'name': 'contribuyente.tipoDocumentoIdentidad.descripcion', 'className': 'text-center tipoDocumento' },
                { 'data': 'contribuyente.nro_documento', 'name': 'contribuyente.nro_documento', 'className': 'text-center nroDocumento' },
                { 'data': 'contribuyente.razon_social', 'name': 'contribuyente.razon_social', 'className': 'text-left razonSocial' },
                { 'data': 'contribuyente.tipo_contribuyente.descripcion', 'name': 'contribuyente.tipoContribuyente.descripcion', 'className': 'text-center tipoEmpresa' },
                { 'data': 'contribuyente.pais.descripcion', 'name': 'contribuyente.pais.descripcion', 'className': 'text-center pais' },
                { 'data': 'contribuyente.ubigeo', 'name': 'contribuyente.ubigeo', 'className': 'text-center ubigeo' },
                { 'data': 'contribuyente.direccion_fiscal', 'name': 'contribuyente.direccion_fiscal', 'className': 'text-left direccion' },
                { 'data': 'contribuyente.telefono', 'name': 'contribuyente.telefono', 'className': 'text-center telefono' },
                { 'data': 'estado_proveedor.descripcion', 'name': 'estadoProveedor.descripcion', 'className': 'text-center estado' },
                { 'data': 'id_proveedor', 'name': 'id_proveedor', 'className': 'text-center', 'searchable': false, 'orderable': false },
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        return row.contribuyente.ubigeo_completo ? row.contribuyente.ubigeo_completo : '';
                    }, targets: 5
                },
                {
                    'render': function (data, type, row) {

                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                            `+(array_accesos.find(element => element === 255)?`<button type="button" class="btn btn-xs btn-info btnVerDetalle handleClickVerDetalleProveedor" data-id-proveedor="${row.id_proveedor}" title="Ver detalle" ><i class="fas fa-eye fa-xs"></i></button>`:``)+
                            (array_accesos.find(element => element === 256)?`<button type="button" class="btn btn-xs btn-warning btnEditarProveedor handleClickEditarProveedor" data-id-proveedor="${row.id_proveedor}" title="Editar" ><i class="fas fa-edit fa-xs"></i></button>`:``)+
                            (array_accesos.find(element => element === 257)?`<button type="button" class="btn btn-xs btn-danger btnAnularProveedor handleClickAnularProveedor" data-id-proveedor="${row.id_proveedor}" title="Anular" ><i class="fas fa-times fa-xs"></i></button>`:``)+`
                        </div></center>`;
                    }, targets: 9
                },

            ],
            'initComplete': function () {
                //Boton de busqueda
                const $filter = $('#listaProveedores_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaProveedores.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaProveedores_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaProveedores_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaProveedores").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaProveedores.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        $('#listaProveedores').DataTable().on("draw", function () {
            resizeSide();
        });
    }

    construirTablaListaProveedores(data) {
        console.log(data);
    }


    nuevoProveedor() {
        $('#modal-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
        if (document.querySelector("form[id='form-proveedor']").getAttribute("type") == 'edition') {
            $("#form-proveedor")[0].reset();
            this.limpiarTabla('listaContactoProveedor');
            this.limpiarTabla('listaCuentaBancariasProveedor');
            this.limpiarTabla('listaEstablecimientoProveedor');

            document.querySelector("form[id='form-proveedor']").setAttribute("type", "register");
        }
        document.querySelector("div[id='modal-proveedor'] h3[class='modal-title']").textContent = 'Nuevo Proveedor';
        document.querySelector("button[id='btnGuardarProveedor']").classList.remove("oculto");
        document.querySelector("button[id='btnActualizarProveedor']").classList.add("oculto");
        document.getElementById("btnGuardarProveedor").removeAttribute("disabled");
        if (((document.querySelector("form[id='form-proveedor'] input[name='razonSocial']").value).trim().length > 0) || (document.querySelector("form[id='form-proveedor'] input[name='nroDocumento']").value).trim().length > 0) {
            Swal.fire({
                title: 'Se encontro un ingreso de razón social / número de documento en el formulario, desea limpiar el formulario ?',
                text: "No podrás revertir esto. Si acepta se perdera la data registrada en el formulario",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Si, limpiar'

            }).then((result) => {
                if (result.isConfirmed) {
                    $("#form-proveedor")[0].reset();
                }
            })
        }
    }
    agregarCuentaBancaria() {
        $('#modal-agregar-cuenta-bancaria').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] button[class~='btnAgregarCuentaBancaria']").classList.remove("oculto");
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] button[class~='btnActualizarCuentaBancaria']").classList.add("oculto");

        $("#form-agregar-cuenta-bancaria-proveedor")[0].reset();

    }

    agregarAdjuntoProveedor() {
        $('#modal-agregar-adjunto-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
    }
    agregarContactoProveedor() {
        $('#modal-agregar-contacto').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-agregar-contacto'] button[class~='btnAgregarContacto']").classList.remove("oculto");
        document.querySelector("div[id='modal-agregar-contacto'] button[class~='btnActualizarContacto']").classList.add("oculto");

        $("#form-agregar-contacto")[0].reset();

    }
    agregarEstablecimientoProveedor() {
        $('#modal-agregar-establecimiento').modal({
            show: true,
            backdrop: 'true'
        });
        $("#form-agregar-establecimiento")[0].reset();

    }

    validacionRegexSoloNumeros(e) {

        let expressionSoloNumeros = '^[0-9]+$';
        let regexSoloNumeros = new RegExp(expressionSoloNumeros);
        if (regexSoloNumeros.test(e.target.value) == false) {
            e.target.value = e.target.value.replace(/.$/, "");
        }
    }

    validacionRegexCantidadLimite(e, maxLength) {
        let expressionCantidadLimite = '^.{' + maxLength + '}$';
        let regexCantidadLimite = new RegExp(expressionCantidadLimite);

        if (regexCantidadLimite.test(e.target.value) == true) {
            e.target.value = e.target.value.replace(/.$/, "");
        }
    }

    validarNroDocumento(e) {
        let tipoDocumento = document.querySelector("select[name='tipoDocumentoIdentidad']").value;
        switch (tipoDocumento) {
            case '1': //DNI
                this.validacionRegexSoloNumeros(e);
                this.validacionRegexCantidadLimite(e, 9);

                break;

            case '2': //RUC

                this.validacionRegexSoloNumeros(e);
                this.validacionRegexCantidadLimite(e, 12);

            default:
                break;
        }
    }

    obtenerDataContribuyenteSegunNroDocumento(obj){
        // console.log(obj);
        let nroDocumento = obj.value;
        let tipoDocumento= document.querySelector("select[name='tipoDocumentoIdentidad']").value;
            if(obj.value.length > 0){
                $.ajax({
                    type: 'POST',
                    url: 'obtener-data-contribuyente-segun-nro-documento',
                    data:{'nroDocumento':nroDocumento,'tipoDocumento':tipoDocumento},
                    dataType: 'JSON',
                    beforeSend: data => {

                        $("input[name='nroDocumento']").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        $("input[name='nroDocumento']").LoadingOverlay("hide", true);

                        console.log(response);

                            if(response.tipo_estado=='success'){
                                if(response.data!=null){
                                    document.querySelector("div[id='modal-proveedor'] input[name='contribuyenteEncontrado']").value = true;
                                    this.mostrarFormularioProveedor(response.data);
                                    document.querySelector("form[id='form-proveedor']").setAttribute("type", "edition");
                                    document.querySelector("div[id='modal-proveedor'] h3[class='modal-title']").textContent = 'Editar Proveedor';
                                    document.querySelector("button[id='btnGuardarProveedor']").classList.add("oculto");
                                    document.querySelector("button[id='btnActualizarProveedor']").classList.remove("oculto");

                                }
                                Lobibox.notify(response.tipo_estado, {
                                    title: false,
                                    size: 'mini',
                                    rounded: true,
                                    sound: false,
                                    delayIndicator: false,
                                    msg: response.mensaje
                                });
                            }else{
                                let tempNueroDocumentoIngresado = document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value;
                                document.querySelector("div[id='modal-proveedor'] input[name='contribuyenteEncontrado']").value = false;
                                document.querySelector("div[id='modal-proveedor'] h3[class='modal-title']").textContent = 'Nuevo Proveedor';
                                document.querySelector("form[id='form-proveedor']").setAttribute("type", "register");

                                $("#form-proveedor")[0].reset();
                                this.limpiarTabla('listaContactoProveedor');
                                this.limpiarTabla('listaCuentaBancariasProveedor');
                                document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value= tempNueroDocumentoIngresado;
                            }

                    }
                }).fail((jqXHR, textStatus, errorThrown) => {
                    $("input[name='nroDocumento']").LoadingOverlay("hide", true);

                    Swal.fire(
                        '',
                        'Hubo un problema al intentar obtener la data del contribuyente, por favor vuelva a intentarlo.',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }

    }

    ponerMayusculaRazonSocial(e) {
        e.target.value = (e.target.value).toUpperCase();
    }

    openModalUbigeoProveedor() {

        modalPage = 'modal-proveedor';
    }

    soloUbigeoNacional(e) {
        if (document.querySelector("select[name='pais']").value != 170) {
            Swal.fire(
                'No aplica',
                'Esta campo solo aplica para proveedores nacionales.',
                'info'
            );

            $('#modal-ubigeo').modal('hide');

        }

    }


    openModalUbigeoEstablecimiento() {

        modalPage = 'modal-establecimiento-proveedor';
    }

    validarModalAgregarEstablecimiento() {
        let mensaje = '';
        let data = {
            'direccionEstablecimiento': document.querySelector("div[id='modal-agregar-establecimiento'] input[name='direccionEstablecimiento']").value,
            'ubigeoEstablecimiento': document.querySelector("div[id='modal-agregar-establecimiento'] input[name='ubigeoEstablecimiento']").value,
            'descripcionUbigeoEstablecimiento': document.querySelector("div[id='modal-agregar-establecimiento'] input[name='descripcionUbigeoEstablecimiento']").value,
            'horarioEstablecimiento': document.querySelector("div[id='modal-agregar-establecimiento'] input[name='horarioEstablecimiento']").value,
        }

        if (data.direccionEstablecimiento == null || data.direccionEstablecimiento.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar una dirección.</li>';
        }
        return { data, mensaje };
    }

    actualizarEstablecimiento() {
        let validado = this.validarModalAgregarEstablecimiento();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-establecimiento').modal('hide');

            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("input[name='direccionEstablecimiento[]']").value= validado.data.direccionEstablecimiento;
            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("span[name='direccionEstablecimiento']").textContent= validado.data.direccionEstablecimiento;
            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("input[name='ubigeoEstablecimiento[]']").value= validado.data.ubigeoEstablecimiento;
            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("span[name='descripcionUbigeoEstablecimiento']").textContent= validado.data.descripcionUbigeoEstablecimiento;
            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("input[name='horarioEstablecimiento[]']").value= validado.data.horarioEstablecimiento;
            this.objectBtnEditionEstablecimiento.closest("tr").querySelector("span[name='horarioEstablecimiento']").textContent= validado.data.horarioEstablecimiento;


        }
    }

    agregarEstablecimientoAProveedor() {
        let validado = this.validarModalAgregarEstablecimiento();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-establecimiento').modal('hide');
            this.construirTablaEstablecimientoProveedor([validado.data])
        }
    }

    construirTablaEstablecimientoProveedor(data){
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaEstablecimientoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idEstablecimiento[]" value="0"><input type="hidden" name="direccionEstablecimiento[]" value="${(element.direccionEstablecimiento != null && element.direccionEstablecimiento != '') ? element.direccionEstablecimiento : ''}"><span name="direccionEstablecimiento">${(element.direccionEstablecimiento != null && element.direccionEstablecimiento != '') ? element.direccionEstablecimiento : ''}</span></td>
                    <td><input type="hidden" name="ubigeoEstablecimiento[]" value="${(element.ubigeoEstablecimiento != null && element.ubigeoEstablecimiento != '') ? element.ubigeoEstablecimiento : ''}"><input type="hidden" name="descripcionUbigeoEstablecimiento[]" value="${(element.descripcionUbigeoEstablecimiento != null && element.descripcionUbigeoEstablecimiento != '') ? element.descripcionUbigeoEstablecimiento : ''}"><span name="descripcionUbigeoEstablecimiento">${(element.descripcionUbigeoEstablecimiento != null && element.descripcionUbigeoEstablecimiento != '') ? element.descripcionUbigeoEstablecimiento : ''}</span></td>
                    <td><input type="hidden" name="horarioEstablecimiento[]" value="${(element.horarioEstablecimiento != null && element.horarioEstablecimiento != '') ? element.horarioEstablecimiento : ''}"><span name="horarioEstablecimiento">${(element.horarioEstablecimiento != null && element.horarioEstablecimiento != '') ? element.horarioEstablecimiento : ''}</span></td>
                    <td>
                    <input type="hidden" class="estadoEstablecimiento" name="estadoEstablecimiento[]" value="1">
                    <div id="contenedorBotoneraAccionEstablecimiento">
                        <button type="button" class="btn btn-xs btn-warning btnEditarEstablecimiento handleClickEditarEstablecimiento" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                        <button type="button" class="btn btn-xs btn-danger btnAnularEstablecimiento handleClickAnularEstablecimiento" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                    </div>
                    </td>
                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaEstablecimientoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

    openModalUbigeoContacto() {

        modalPage = 'modal-contacto-proveedor';
    }

    validarModalAgregarContacto() {
        let mensaje = '';
        let data = {
            'nombreContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='nombreContacto']").value,
            'cargoContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='cargoContacto']").value,
            'telefonoContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='telefonoContacto']").value,
            'ubigeoContactoProveedor': document.querySelector("div[id='modal-agregar-contacto'] input[name='ubigeoContactoProveedor']").value,
            'descripcionUbigeoContactoProveedor': document.querySelector("div[id='modal-agregar-contacto'] input[name='descripcionUbigeoContactoProveedor']").value,
            'direccionContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='direccionContacto']").value,
            'horarioContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='horarioContacto']").value,
            'emailContacto': document.querySelector("div[id='modal-agregar-contacto'] input[name='emailContacto']").value
        }

        if (data.nombreContacto == null || data.nombreContacto.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar un nombre de contacto.</li>';
        }
        if ((data.telefonoContacto == null || data.telefonoContacto.trim() == '') && (data.emailContacto == null || data.emailContacto.trim() == '')) {
            mensaje += '<li style="text-align: left;">Debe ingresar un telefono o email.</li>';
        }
        return { data, mensaje };
    }


    actualizarContactoProveedor() {
        let validado = this.validarModalAgregarContacto();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-contacto').modal('hide');
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='nombreContacto[]']").value= validado.data.nombreContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='nombreContacto']").textContent= validado.data.nombreContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='cargoContacto[]']").value= validado.data.cargoContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='cargoContacto']").textContent= validado.data.cargoContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='telefonoContacto[]']").value= validado.data.telefonoContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='telefonoContacto']").textContent= validado.data.telefonoContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='ubigeoContactoProveedor[]']").value= validado.data.ubigeoContactoProveedor;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='descripcionUbigeoContactoProveedor[]']").value= validado.data.descripcionUbigeoContactoProveedor;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='descripcionUbigeoContactoProveedor']").textContent= validado.data.descripcionUbigeoContactoProveedor;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='direccionContacto[]']").value= validado.data.direccionContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='direccionContacto']").textContent= validado.data.direccionContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='horarioContacto[]']").value= validado.data.horarioContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='horarioContacto']").textContent= validado.data.horarioContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("input[name='emailContacto[]']").value= validado.data.emailContacto;
            this.objectBtnEditionContacto.closest("tr").querySelector("span[name='emailContacto']").textContent= validado.data.emailContacto;
        }
    }

    agregarContactoAProveedor() {
        let validado = this.validarModalAgregarContacto();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-contacto').modal('hide');
            this.construirTablaContactosProveedor([validado.data])
        }
    }

    construirTablaContactosProveedor(data) {
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaContactoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idContacto[]" value="0"><input type="hidden" name="nombreContacto[]" value="${(element.nombreContacto != null && element.nombreContacto != '') ? element.nombreContacto : ''}"> <span name="nombreContacto">${(element.nombreContacto != null && element.nombreContacto != '') ? element.nombreContacto : ''}</span> </td>
                    <td><input type="hidden" name="cargoContacto[]" value="${(element.cargoContacto != null && element.cargoContacto != '') ? element.cargoContacto : ''}"><span name="cargoContacto">${(element.cargoContacto != null && element.cargoContacto != '') ? element.cargoContacto : ''}</span></td>
                    <td><input type="hidden" name="telefonoContacto[]" value="${(element.telefonoContacto != null && element.telefonoContacto != '') ? element.telefonoContacto : ''}"><span name="telefonoContacto">${(element.telefonoContacto != null && element.telefonoContacto != '') ? element.telefonoContacto : ''}</span></td>
                    <td><input type="hidden" name="emailContacto[]" value="${(element.emailContacto != null && element.emailContacto != '') ? element.emailContacto : ''}"><span name="emailContacto">${(element.emailContacto != null && element.emailContacto != '') ? element.emailContacto : ''}</span></td>
                    <td><input type="hidden" name="direccionContacto[]" value="${(element.direccionContacto != null && element.direccionContacto != '') ? element.direccionContacto : ''}"><span name="direccionContacto">${(element.direccionContacto != null && element.direccionContacto != '') ? element.direccionContacto : ''}</span></td>
                    <td><input type="hidden" name="ubigeoContactoProveedor[]" value="${(element.ubigeoContactoProveedor != null && element.ubigeoContactoProveedor != '') ? element.ubigeoContactoProveedor : ''}"><input type="hidden" name="descripcionUbigeoContactoProveedor[]" value="${(element.descripcionUbigeoContactoProveedor != null && element.descripcionUbigeoContactoProveedor != '') ? element.descripcionUbigeoContactoProveedor : ''}"><span name="descripcionUbigeoContactoProveedor">${(element.descripcionUbigeoContactoProveedor != null && element.descripcionUbigeoContactoProveedor != '') ? element.descripcionUbigeoContactoProveedor : ''}</span></td>
                    <td><input type="hidden" name="horarioContacto[]" value="${(element.horarioContacto != null && element.horarioContacto != '') ? element.horarioContacto : ''}"><span name="horarioContacto">${(element.horarioContacto != null && element.horarioContacto != '') ? element.horarioContacto : ''}</span></td>
                    <td>
                    <input type="hidden" class="estadoContacto" name="estadoContacto[]" value="1">
                    <div id="contenedorBotoneraAccionContacto">
                        <button type="button" class="btn btn-xs btn-warning btnEditarContacto handleClickEditarContacto" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                        <button type="button" class="btn btn-xs btn-danger btnAnularContacto handleClickAnularContacto" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                    </div>
                    </td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaContactoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

    anularEstablecimientoProveedor(obj) {
        obj.closest("td").querySelector("input[class='estadoEstablecimiento']").value = 7;
        obj.closest("tr").setAttribute('class', 'text-danger');
        obj.closest("td").querySelector("button[class~='btnAnularEstablecimiento']").classList.add("oculto");

        let contenedorBotoneraAccionEstablecimiento = obj.closest("td").querySelector("div[id='contenedorBotoneraAccionEstablecimiento']");

        Lobibox.notify('success', {
            title: false,
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: `Establecimiento anulado`
        });

        if (contenedorBotoneraAccionEstablecimiento.querySelector("button[id='btnRestablecerEstablecimiento']") == null) {
            let buttonRestablecerEstablecimiento = document.createElement("button");
            buttonRestablecerEstablecimiento.type = "button";
            buttonRestablecerEstablecimiento.title = "Restablecer";
            buttonRestablecerEstablecimiento.id = "btnRestablecerEstablecimiento";
            buttonRestablecerEstablecimiento.className = "btn btn-xs btn-info";
            buttonRestablecerEstablecimiento.innerHTML = "<i class='fas fa-undo'></i>";
            buttonRestablecerEstablecimiento.addEventListener('click', function () {
                obj.closest("td").querySelector("input[class='estadoEstablecimiento']").value = 1;
                obj.closest("tr").setAttribute('class', '');
                obj.closest("td").querySelector("button[class~='btnAnularEstablecimiento']").classList.remove("oculto")
                obj.closest("td").querySelector("button[id='btnRestablecerEstablecimiento']").classList.add("oculto")
                Lobibox.notify('info', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `Establecimiento restablecido`
                });

            }, false);
            contenedorBotoneraAccionEstablecimiento.appendChild(buttonRestablecerEstablecimiento);
        } else {
            obj.closest("td").querySelector("button[id='btnRestablecerEstablecimiento']").classList.remove("oculto")

        }


    }
    anularContactoProveedor(obj) {
        obj.closest("td").querySelector("input[class='estadoContacto']").value = 7;
        obj.closest("tr").setAttribute('class', 'text-danger');
        obj.closest("td").querySelector("button[class~='btnAnularContacto']").classList.add("oculto");

        let contenedorBotoneraAccionContacto = obj.closest("td").querySelector("div[id='contenedorBotoneraAccionContacto']");

        Lobibox.notify('success', {
            title: false,
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: `Contacto anulado`
        });

        if (contenedorBotoneraAccionContacto.querySelector("button[id='btnRestablecerContacto']") == null) {
            let buttonRestablecerContacto = document.createElement("button");
            buttonRestablecerContacto.type = "button";
            buttonRestablecerContacto.title = "Restablecer";
            buttonRestablecerContacto.id = "btnRestablecerContacto";
            buttonRestablecerContacto.className = "btn btn-xs btn-info";
            buttonRestablecerContacto.innerHTML = "<i class='fas fa-undo'></i>";
            buttonRestablecerContacto.addEventListener('click', function () {
                obj.closest("td").querySelector("input[class='estadoContacto']").value = 1;
                obj.closest("tr").setAttribute('class', '');
                obj.closest("td").querySelector("button[class~='btnAnularContacto']").classList.remove("oculto")
                obj.closest("td").querySelector("button[id='btnRestablecerContacto']").classList.add("oculto")
                Lobibox.notify('info', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `Contacto restablecido`
                });

            }, false);
            contenedorBotoneraAccionContacto.appendChild(buttonRestablecerContacto);
        } else {
            obj.closest("td").querySelector("button[id='btnRestablecerContacto']").classList.remove("oculto")

        }


    }

    editarContactoProveedor(obj){
        this.objectBtnEditionContacto= obj;
        document.querySelector("div[id='modal-agregar-contacto'] button[class~='btnAgregarContacto']").classList.add("oculto");
        document.querySelector("div[id='modal-agregar-contacto'] button[class~='btnActualizarContacto']").classList.remove("oculto");
        $('#modal-agregar-contacto').modal({
            show: true,
            backdrop: 'true'
        });
        $("#form-agregar-contacto")[0].reset();
        document.querySelector("div[id='modal-agregar-contacto'] input[name='nombreContacto']").value=obj.closest("tr").querySelector("input[name='nombreContacto[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='cargoContacto']").value=obj.closest("tr").querySelector("input[name='cargoContacto[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='telefonoContacto']").value=obj.closest("tr").querySelector("input[name='telefonoContacto[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='ubigeoContactoProveedor']").value=obj.closest("tr").querySelector("input[name='ubigeoContactoProveedor[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='descripcionUbigeoContactoProveedor']").value=obj.closest("tr").querySelector("input[name='descripcionUbigeoContactoProveedor[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='direccionContacto']").value=obj.closest("tr").querySelector("input[name='direccionContacto[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='horarioContacto']").value=obj.closest("tr").querySelector("input[name='horarioContacto[]']").value;
        document.querySelector("div[id='modal-agregar-contacto'] input[name='emailContacto']").value=obj.closest("tr").querySelector("input[name='emailContacto[]']").value;
    }

    validarModalAgregarCuentaBancaria() {
        let mensaje = '';
        let data = {
            'idBanco': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idBanco']").value,
            'nombreBanco': (document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idBanco']")).options[document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idBanco']").selectedIndex].textContent,
            'idTipoCuenta': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idTipoCuenta']").value,
            'nombreTipoCuenta': (document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idTipoCuenta']")).options[document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idTipoCuenta']").selectedIndex].textContent,
            'idMoneda': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idMoneda']").value,
            'nombreMoneda': (document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idMoneda']")).options[document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idMoneda']").selectedIndex].textContent,
            'nroCuenta': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nroCuenta']").value,
            'nroCuentaInterbancaria': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nroCuentaInterbancaria']").value,
            'swift': document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='swift']").value
        }

        if (data.nroCuenta == null || data.nroCuenta.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar un numero de cuenta.</li>';
        }
        return { data, mensaje };
    }

    actualizarCuentaBancaria() {
        let validado = this.validarModalAgregarCuentaBancaria();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-cuenta-bancaria').modal('hide');

            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='idBanco[]']").value= validado.data.idBanco;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='nombreBanco[]']").value= validado.data.nombreBanco;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='nombreBanco']").textContent= validado.data.nombreBanco;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='idTipoCuenta[]']").value= validado.data.idTipoCuenta;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='nombreTipoCuenta']").textContent= validado.data.nombreTipoCuenta;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='idMoneda[]']").value= validado.data.idMoneda;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='nombreMoneda']").textContent= validado.data.nombreMoneda;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='nroCuenta[]']").value= validado.data.nroCuenta;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='nroCuenta']").textContent= validado.data.nroCuenta;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='nroCuentaInterbancaria[]']").value= validado.data.nroCuentaInterbancaria;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='nroCuentaInterbancaria']").textContent= validado.data.nroCuentaInterbancaria;
            this.objectBtnEditionCuenta.closest("tr").querySelector("input[name='swift[]']").value= validado.data.swift;
            this.objectBtnEditionCuenta.closest("tr").querySelector("span[name='swift']").textContent= validado.data.swift;

        }
    }




    agregarCuentaBancariaAProveedor() {
        let validado = this.validarModalAgregarCuentaBancaria();

        if (validado.mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + validado.mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            $('#modal-agregar-cuenta-bancaria').modal('hide');
            this.construirTablaCuentaBancariaProveedor([validado.data])
        }
    }

    editarCuentaBancariaProveedor(obj){
        this.objectBtnEditionCuenta= obj;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] button[class~='btnAgregarCuentaBancaria']").classList.add("oculto");
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] button[class~='btnActualizarCuentaBancaria']").classList.remove("oculto");
        $('#modal-agregar-cuenta-bancaria').modal({
            show: true,
            backdrop: 'true'
        });
        $("#form-agregar-cuenta-bancaria-proveedor")[0].reset();
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idBanco']").value=obj.closest("tr").querySelector("input[name='idBanco[]']").value;
        // document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nombreBanco']").value=obj.closest("tr").querySelector("input[name='nombreBanco[]']").value;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idTipoCuenta']").value=obj.closest("tr").querySelector("input[name='idTipoCuenta[]']").value;
        // document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nombreTipoCuenta']").value=obj.closest("tr").querySelector("input[name='nombreTipoCuenta[]']").value;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] select[name='idMoneda']").value=obj.closest("tr").querySelector("input[name='idMoneda[]']").value;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nroCuenta']").value=obj.closest("tr").querySelector("input[name='nroCuenta[]']").value;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='nroCuentaInterbancaria']").value=obj.closest("tr").querySelector("input[name='nroCuentaInterbancaria[]']").value;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria'] input[name='swift']").value=obj.closest("tr").querySelector("input[name='swift[]']").value;


    }
    editarEstablecimientoProveedor(obj){
        this.objectBtnEditionEstablecimiento= obj;
        document.querySelector("div[id='modal-agregar-establecimiento'] button[class~='btnAgregarEstablecimiento']").classList.add("oculto");
        document.querySelector("div[id='modal-agregar-establecimiento'] button[class~='btnActualizarEstablecimiento']").classList.remove("oculto");
        $('#modal-agregar-establecimiento').modal({
            show: true,
            backdrop: 'true'
        });
        $("#form-agregar-establecimiento")[0].reset();
        document.querySelector("div[id='modal-agregar-establecimiento'] input[name='direccionEstablecimiento']").value=obj.closest("tr").querySelector("input[name='direccionEstablecimiento[]']").value;
        document.querySelector("div[id='modal-agregar-establecimiento'] input[name='ubigeoEstablecimiento']").value=obj.closest("tr").querySelector("input[name='ubigeoEstablecimiento[]']").value;
        document.querySelector("div[id='modal-agregar-establecimiento'] input[name='descripcionUbigeoEstablecimiento']").value=obj.closest("tr").querySelector("input[name='descripcionUbigeoEstablecimiento[]']").value;
        document.querySelector("div[id='modal-agregar-establecimiento'] input[name='horarioEstablecimiento']").value=obj.closest("tr").querySelector("input[name='horarioEstablecimiento[]']").value;


    }

    construirTablaCuentaBancariaProveedor(data) {
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaCuentasBancariasProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td>
                        <input type="hidden" name="idCuenta[]" value="0">
                        <input type="hidden" name="idBanco[]" value="${(element.idBanco != null && element.idBanco != '') ? element.idBanco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.nombreBanco != null && element.nombreBanco != '') ? element.nombreBanco : ''}"> <span name="nombreBanco">${(element.nombreBanco != null && element.nombreBanco != '') ? element.nombreBanco : ''}</span> </td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.idTipoCuenta != null && element.idTipoCuenta != '') ? element.idTipoCuenta : ''}"><span name="nombreTipoCuenta">${(element.nombreTipoCuenta != null && element.nombreTipoCuenta != '') ? element.nombreTipoCuenta : ''}</span></td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.idMoneda != null && element.idMoneda != '') ? element.idMoneda : ''}"><span name="nombreMoneda">${(element.nombreMoneda != null && element.nombreMoneda != '') ? element.nombreMoneda : ''}</span></td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nroCuenta != null && element.nroCuenta != '') ? element.nroCuenta : ''}"><span name="nroCuenta">${(element.nroCuenta != null && element.nroCuenta != '') ? element.nroCuenta : ''}</span></td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nroCuentaInterbancaria != null && element.nroCuentaInterbancaria != '') ? element.nroCuentaInterbancaria : ''}"><span name="nroCuentaInterbancaria">${(element.nroCuentaInterbancaria != null && element.nroCuentaInterbancaria != '') ? element.nroCuentaInterbancaria : ''}</span></td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}"><span name="swift">${(element.swift != null && element.swift != '') ? element.swift : ''}</span></td>
                    <td>
                        <input type="hidden" class="estadoCuenta" name="estadoCuenta[]" value="1">
                        <div id="contenedorBotoneraAccionCuentaBancaria">
                            <button type="button" class="btn btn-xs btn-warning btnEditarCuentaBancaria handleClickEditarCuentaBancaria" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-danger btnAnularCuentaBancaria handleClickAnularCuentaBancaria" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                        </div>
                    </td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaCuentasBancariasProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }



    anularCuentaBancariaProveedor(obj) {
        obj.closest("td").querySelector("input[class='estadoCuenta']").value = 7;
        obj.closest("tr").setAttribute('class', 'text-danger');
        obj.closest("td").querySelector("button[class~='btnAnularCuentaBancaria']").classList.add("oculto");

        let contenedorBotoneraAccionCuentaBancaria = obj.closest("td").querySelector("div[id='contenedorBotoneraAccionCuentaBancaria']");

        Lobibox.notify('success', {
            title: false,
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: `Cuenta anulado`
        });

        if (contenedorBotoneraAccionCuentaBancaria.querySelector("button[id='btnRestablecerCuentaBancaria']") == null) {
            let buttonRestablecerCuenta = document.createElement("button");
            buttonRestablecerCuenta.type = "button";
            buttonRestablecerCuenta.title = "Restablecer";
            buttonRestablecerCuenta.id = "btnRestablecerCuentaBancaria";
            buttonRestablecerCuenta.className = "btn btn-xs btn-info";
            buttonRestablecerCuenta.innerHTML = "<i class='fas fa-undo'></i>";
            buttonRestablecerCuenta.addEventListener('click', function () {
                obj.closest("td").querySelector("input[class='estadoCuenta']").value = 1;
                obj.closest("tr").setAttribute('class', '');
                obj.closest("td").querySelector("button[class~='btnAnularCuentaBancaria']").classList.remove("oculto")
                obj.closest("td").querySelector("button[id='btnRestablecerCuentaBancaria']").classList.add("oculto")
                Lobibox.notify('info', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `Cuenta restablecido`
                });

            }, false);
            contenedorBotoneraAccionCuentaBancaria.appendChild(buttonRestablecerCuenta);
        } else {
            obj.closest("td").querySelector("button[id='btnRestablecerCuentaBancaria']").classList.remove("oculto")

        }


    }




    validarModalProveedor() {
        let mensaje = '';

        let ValorNroDocumento = document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value;
        let ValorRazonSocial= document.querySelector("div[id='modal-proveedor'] input[name='razonSocial']").value;
        // let ValorDireccion =document.querySelector("div[id='modal-proveedor'] input[name='direccion']").value;
        // let valorTelefono =document.querySelector("div[id='modal-proveedor'] input[name='telefono']").value;
        // let ValorEmail = document.querySelector("div[id='modal-proveedor'] input[name='email']").value;

        let CantidadRegistrosTablaListaContacto = document.querySelector("table[id='listaContactoProveedor']").tBodies.length??0;




        if (ValorNroDocumento == null || ValorNroDocumento.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar un número de documento.</li>';
        }
        if (ValorRazonSocial == null || ValorRazonSocial.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar una razón social.</li>';
        }
        // if (ValorDireccion == null || ValorDireccion.trim() == '') {
        //     mensaje += '<li style="text-align: left;">Debe ingresar una dirección.</li>';
        // }
        // if (valorTelefono == null || valorTelefono.trim() == '') {
        //     mensaje += '<li style="text-align: left;">Debe ingresar un teléfono.</li>';
        // }
        // if (ValorEmail == null || ValorEmail.trim() == '') {
        //     mensaje += '<li style="text-align: left;">Debe ingresar un email.</li>';
        // }
        if (CantidadRegistrosTablaListaContacto ==0) {
            mensaje += '<li style="text-align: left;">Debe ingresar al menos un contacto.</li>';
        }
        return mensaje;
    }

    guardarProveedor(obj) {
        let mensaje = this.validarModalProveedor();
        if (mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + mensaje + '</ol>',
                icon: 'warning'
            }
            );
            obj.removeAttribute("disabled");

        } else {
            let formData = new FormData($('#form-proveedor')[0]);
            $.ajax({
                type: 'POST',
                url: 'guardar',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => { // Are not working with dataType:'jsonp'

                    $('#modal-proveedor .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    // console.log(response);
                    if (response.id_proveedor > 0) {
                        $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `Proveedor creado`
                        });
                        obj.removeAttribute("disabled");
                        $("#form-proveedor")[0].reset();
                        this.limpiarTabla('listaContactoProveedor');
                        this.limpiarTabla('listaCuentaBancariasProveedor');
                        $('#modal-proveedor').modal('hide');
                        $tablaListaProveedores.ajax.reload(null,false);


                    } else {
                        $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);
                        console.log(response);
                        if(response.mensaje.length>0){
                            Swal.fire(
                                '',
                                response.mensaje,
                                response.status
                            );
                        }
                        obj.removeAttribute("disabled");

                    }
                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar el proveedor, por favor vuelva a intentarlo',
                        'error'
                    );
                    obj.removeAttribute("disabled");

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }
    anularProveedor(obj) {
        let idProveedor = obj.dataset.idProveedor;

        Swal.fire({
            title: 'Esta seguro que desea anular este proveedor?',
            text: "No podrás revertir esto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No,',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append(`idProveedor`, idProveedor);
                $.ajax({
                    type: 'POST',
                    url: 'anular',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => { // Are not working with dataType:'jsonp'

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        // console.log(response);
                        if (response.id_proveedor > 0) {
                            $('#wrapper-okc').LoadingOverlay("hide", true);

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Proveedor anulado`
                            });
                            obj.closest("tr").remove();

                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            console.log(response);
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un problema en el servidor al intentar anular el proveedor, por favor vuelva a intentarlo',
                                'error'
                            );

                        }
                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar anular el proveedor, por favor vuelva a intentarlo',
                            'error'
                        );

                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        })
    }

    editarProveedor(obj) {
        let idProveedor = obj.dataset.idProveedor;
        this.objectBtnEdition = obj;
        this.limpiarTabla('listaCuentaBancariasProveedor');
        this.limpiarTabla('listaContactoProveedor');
        this.limpiarTabla('listaEstablecimientoProveedor');
        $('#modal-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
        if (document.querySelector("form[id='form-proveedor']").getAttribute("type") == 'register') {
            $("#form-proveedor")[0].reset();
            document.querySelector("form[id='form-proveedor']").setAttribute("type", "edition");
        }
        console.log(document.querySelector("div[id='modal-proveedor'] h3[class='modal-title']"));
        document.querySelector("div[id='modal-proveedor'] h3[class='modal-title']").textContent = 'Editar Proveedor';
        // document.querySelector("div[id='modal-proveedor'] span[id='tituloAdicional']")?document.querySelector("div[id='modal-proveedor'] span[id='tituloAdicional']").textContent = res.contribuyente.razon_social:null;

        document.querySelector("button[id='btnGuardarProveedor']").classList.add("oculto");
        document.querySelector("button[id='btnActualizarProveedor']").classList.remove("oculto");

        this.proveedorCtrl.getProveedor(idProveedor).then((res) => {

            this.mostrarFormularioProveedor(res);

        }).catch(function (err) {
            console.log(err)
            Swal.fire(
                '',
                'Hubo un problema al intentar obtener la data del proveedor',
                'error'
            );
        })

    }

    mostrarFormularioProveedor(res){
        document.querySelector("div[id='modal-proveedor'] input[name='idContribuyente']").value = res.id_contribuyente;
        document.querySelector("div[id='modal-proveedor'] input[name='idProveedor']").value = res.proveedor!=null ?res.proveedor.id_proveedor:'';
        document.querySelector("div[id='modal-proveedor'] select[name='tipoContribuyente']").value = res.id_tipo_contribuyente;
        document.querySelector("div[id='modal-proveedor'] select[name='tipoDocumentoIdentidad']").value = res.tipo_documento_identidad.id_doc_identidad;
        document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value = res.nro_documento;
        document.querySelector("div[id='modal-proveedor'] input[name='razonSocial']").value = res.razon_social;
        document.querySelector("div[id='modal-proveedor'] input[name='direccion']").value = res.direccion_fiscal;
        document.querySelector("div[id='modal-proveedor'] select[name='pais']").value = res.pais.id_pais;
        document.querySelector("div[id='modal-proveedor'] input[name='ubigeoProveedor']").value = res.ubigeo;
        document.querySelector("div[id='modal-proveedor'] input[name='descripcionUbigeoProveedor']").value = res.ubigeo_completo;
        document.querySelector("div[id='modal-proveedor'] input[name='telefono']").value = res.telefono;
        document.querySelector("div[id='modal-proveedor'] input[name='celular']").value = res.celular;
        document.querySelector("div[id='modal-proveedor'] input[name='email']").value = res.email;
        document.querySelector("div[id='modal-proveedor'] textarea[name='observacion']").value = res.proveedor!=null ?res.proveedor.observacion:'';

        if (res.proveedor!=null && res.proveedor.establecimiento_proveedor.length > 0) {
            this.llenarTablaEstablecimientoDeProveedorSeleccionado(res.proveedor.establecimiento_proveedor);
        }
        if (res.contacto_contribuyente.length > 0) {
            this.llenarTablaContactosDeProveedorSeleccionado(res.contacto_contribuyente);
        }
        if (res.cuenta_contribuyente.length > 0) {
            this.llenarTablaCuentaBancariaDeProveedorSeleccionado(res.cuenta_contribuyente);
        }
    }


    llenarTablaEstablecimientoDeProveedorSeleccionado(data) {
        this.limpiarTabla('listaEstablecimientoProveedor');

        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaEstablecimientoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idEstablecimiento[]" value="${(element.id_establecimiento != null && element.id_establecimiento != '') ? element.id_establecimiento : ''}"><input type="hidden" name="direccionEstablecimiento[]" value="${(element.direccion != null && element.direccion != '') ? element.direccion : ''}"><span name="direccionEstablecimiento">${(element.direccion != null && element.direccion != '') ? element.direccion : ''}</span></td>
                    <td><input type="hidden" name="ubigeoEstablecimiento[]" value="${(element.ubigeo != null && element.ubigeo != '') ? element.ubigeo : ''}"><input type="hidden" name="descripcionUbigeoEstablecimiento[]" value="${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}"><span name="descripcionUbigeoEstablecimiento">${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}</span></td>
                    <td><input type="hidden" name="horarioEstablecimiento[]" value="${(element.horario != null && element.horario != '') ? element.horario : ''}"><span name="horarioEstablecimiento">${(element.horario != null && element.horario != '') ? element.horario : ''}</span></td>
                    <td>
                    <input type="hidden" class="estadoEstablecimiento" name="estadoEstablecimiento[]" value="1">
                    <div id="contenedorBotoneraAccionEstablecimiento">
                        <button type="button" class="btn btn-xs btn-warning btnEditarEstablecimiento handleClickEditarEstablecimiento" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                        <button type="button" class="btn btn-xs btn-danger btnAnularEstablecimiento handleClickAnularEstablecimiento" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                    </div>
                    </td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaEstablecimientoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

    llenarTablaContactosDeProveedorSeleccionado(data) {
        this.limpiarTabla('listaContactoProveedor');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaContactoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idContacto[]" value="${(element.id_datos_contacto != null && element.id_datos_contacto != '') ? element.id_datos_contacto : ''}"><input type="hidden" name="nombreContacto[]" value="${(element.nombre != null && element.nombre != '') ? element.nombre : ''}"><span name="nombreContacto">${(element.nombre != null && element.nombre != '') ? element.nombre : ''}</span></td>
                    <td><input type="hidden" name="cargoContacto[]" value="${(element.cargo != null && element.cargo != '') ? element.cargo : ''}"><span name="cargoContacto">${(element.cargo != null && element.cargo != '') ? element.cargo : ''}</span></td>
                    <td><input type="hidden" name="telefonoContacto[]" value="${(element.telefono != null && element.telefono != '') ? element.telefono : ''}"><span name="telefonoContacto">${(element.telefono != null && element.telefono != '') ? element.telefono : ''}</span></td>
                    <td><input type="hidden" name="emailContacto[]" value="${(element.email != null && element.email != '') ? element.email : ''}"><span name="emailContacto">${(element.email != null && element.email != '') ? element.email : ''}</span></td>
                    <td><input type="hidden" name="direccionContacto[]" value="${(element.direccion != null && element.direccion != '') ? element.direccion : ''}"><span name="direccionContacto">${(element.direccion != null && element.direccion != '') ? element.direccion : ''}</span></td>
                    <td><input type="hidden" name="ubigeoContactoProveedor[]" value="${(element.ubigeo != null && element.ubigeo != '') ? element.ubigeo : ''}"><input type="hidden" name="descripcionUbigeoContactoProveedor[]" value="${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}"><span name="descripcionUbigeoContactoProveedor">${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}</span></td>
                    <td><input type="hidden" name="horarioContacto[]" value="${(element.horario != null && element.horario != '') ? element.horario : ''}"><span name="horarioContacto">${(element.horario != null && element.horario != '') ? element.horario : ''}</span></td>
                    <td>
                    <input type="hidden" class="estadoContacto" name="estadoContacto[]" value="1">
                    <div id="contenedorBotoneraAccionContacto">
                        <button type="button" class="btn btn-xs btn-warning btnEditarContacto handleClickEditarContacto" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                        <button type="button" class="btn btn-xs btn-danger btnAnularContacto handleClickAnularContacto" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                    </div>
                    </td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaContactoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

    llenarTablaCuentaBancariaDeProveedorSeleccionado(data) {
        this.limpiarTabla('listaCuentaBancariasProveedor');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaCuentasBancariasProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td>
                        <input type="hidden" name="idCuenta[]" value="${(element.id_cuenta_contribuyente != null && element.id_cuenta_contribuyente != '') ? element.id_cuenta_contribuyente : ''}">
                        <input type="hidden" name="idBanco[]" value="${(element.id_banco != null && element.id_banco != '') ? element.id_banco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.banco.contribuyente != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}"><span name="nombreBanco">${(element.banco.contribuyente.razon_social != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}</span></td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.id_tipo_cuenta != null && element.id_tipo_cuenta != '') ? element.id_tipo_cuenta : ''}"><span name="nombreTipoCuenta">${(element.tipo_cuenta != null && element.tipo_cuenta.descripcion != '') ? element.tipo_cuenta.descripcion : ''}</span></td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.id_moneda != null && element.id_moneda != '') ? element.id_moneda : ''}"><span name="nombreMoneda">${(element.moneda != null && element.moneda.descripcion != '') ? element.moneda.descripcion : ''}</span></td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}"><span name="nroCuenta">${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}</span></td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}"><span name="nroCuentaInterbancaria">${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}</span></td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}"><span name="swift">${(element.swift != null && element.swift != '') ? element.swift : ''}</span></td>
                    <td>${(element.fecha_registro != null && element.fecha_registro != '') ? element.fecha_registro : ''}</td>
                    <td>${(element.updated_at != null && element.updated_at != '') ? element.updated_at : ''}</td>
                    <td>${(element.usuario != null && element.usuario.nombre_corto != '') ? element.usuario.nombre_corto : ''}</td>
                    <td>
                        <input type="hidden" class="estadoCuenta" name="estadoCuenta[]" value="1">
                        <div id="contenedorBotoneraAccionCuentaBancaria">
                            <button type="button" class="btn btn-xs btn-warning btnEditarCuentaBancaria handleClickEditarCuentaBancaria" title="Editar"><i class="fas fa-edit fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-danger btnAnularCuentaBancaria handleClickAnularCuentaBancaria" title="Anular"><i class="fas fa-times fa-xs"></i></button>
                        </div>
                    </td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaCuentasBancariasProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }



    actualizarProveedor(obj) {
        let mensaje = this.validarModalProveedor();
        if ( (Boolean(document.querySelector("div[id='modal-proveedor'] input[name='contribuyenteEncontrado']").value) ==false) && (!document.querySelector("div[id='modal-proveedor'] input[name='idProveedor']").value > 0)) {
            mensaje += '<li style="text-align: left;">Hubo un problema, no se encontro un id de proveedor, vuelva a intenta seleccionar el proveedor.</li>';
        }
        if (mensaje.length > 0) {
            Swal.fire({
                title: '',
                html: '<ol>' + mensaje + '</ol>',
                icon: 'warning'
            }
            );
        } else {
            let formData = new FormData($('#form-proveedor')[0]);
            $.ajax({
                type: 'POST',
                url: 'actualizar',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => { // Are not working with dataType:'jsonp'

                    $('#modal-proveedor .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    // console.log(response);
                    if (response.id_proveedor > 0) {
                        $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `Proveedor actualizado`
                        });
                        $("#listaProveedores").DataTable().ajax.reload(null, false);

                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='tipoDocumento']").textContent = response.data.tipo_documento_identidad.descripcion ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='nroDocumento']").textContent = response.data.nro_documento ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='razonSocial']").textContent = response.data.razon_social ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='tipoEmpresa']").textContent = response.data.tipo_contribuyente.descripcion ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='pais']").textContent = response.data.pais.descripcion ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='ubigeo']").textContent = response.data.ubigeo_completo ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='direccion']").textContent = response.data.direccion_fiscal ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='telefono']").textContent = response.data.telefono ?? '';
                        // this.objectBtnEdition.closest("tr").querySelector("td[class~='estado']").textContent = response.data.proveedor.estado_proveedor.descripcion ?? '';

                        obj.removeAttribute("disabled");
                        $("#form-proveedor")[0].reset();
                        this.limpiarTabla('listaCuentaBancariasProveedor');
                        this.limpiarTabla('listaContactoProveedor');
                        this.limpiarTabla('listaEstablecimientoProveedor');

                        $('#modal-proveedor').modal('hide');


                    } else {
                        $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);
                        console.log(response);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar actualizar el proveedor, por favor vuelva a intentarlo',
                            'error'
                        );
                        obj.removeAttribute("disabled");

                    }
                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#modal-proveedor .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar actualizar el proveedor, por favor vuelva a intentarlo',
                        'error'
                    );
                    obj.removeAttribute("disabled");

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }


    verProveedor(obj) {
        this.limpiarTabla('listaContactoProveedorSoloLectura');
        this.limpiarTabla('listaCuentaBancariasProveedorSoloLectura');
        this.limpiarTabla('listaEstablecimientoProveedorSoloLectura');
        $('#modal-ver-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
        let idProveedor = obj.dataset.idProveedor;
        this.proveedorCtrl.getProveedor(idProveedor).then((res) => {

            document.querySelector("div[id='modal-ver-proveedor'] span[id='tituloAdicional']")?document.querySelector("div[id='modal-ver-proveedor'] span[id='tituloAdicional']").textContent = res.razon_social:null;

            document.querySelector("div[id='modal-ver-proveedor'] p[name='tipoContribuyente']").textContent = res.tipo_contribuyente.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='tipoDocumentoIdentidad']").textContent = res.tipo_documento_identidad.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='nroDocumento']").textContent = res.nro_documento;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='razonSocial']").textContent = res.razon_social;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='direccion']").textContent = res.direccion_fiscal;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='pais']").textContent = res.pais.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='descripcionUbigeoProveedor']").textContent = res.ubigeo_completo;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='telefono']").textContent = res.telefono;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='celular']").textContent = res.celular;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='email']").textContent = res.email;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='observacion']").textContent = res.proveedor.observacion;

            if (res.proveedor.establecimiento_proveedor.length > 0) {
                this.llenarTablaEstablecimientosDeProveedorSeleccionadoSoloLectura(res.proveedor.establecimiento_proveedor);
            }
            if (res.contacto_contribuyente.length > 0) {
                this.llenarTablaContactosDeProveedorSeleccionadoSoloLectura(res.contacto_contribuyente);
            }
            if (res.cuenta_contribuyente.length > 0) {
                this.llenarTablaCuentaBancariaDeProveedorSeleccionadoSoloLectura(res.cuenta_contribuyente);
            }

        }).catch(function (err) {
            console.log(err)
            Swal.fire(
                '',
                'Hubo un problema al intentar obtener la data del proveedor',
                'error'
            );
        })

    }

    llenarTablaEstablecimientosDeProveedorSeleccionadoSoloLectura(data) {
        this.limpiarTabla('listaEstablecimientoProveedorSoloLectura');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaEstablecimientoProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idEstablecimiento[]" value="${(element.id_establecimiento != null && element.id_establecimiento != '') ? element.id_establecimiento : ''}"><input type="hidden" name="direccionEstablecimiento[]" value="${(element.direccion != null && element.direccion != '') ? element.direccion : ''}">${(element.direccion != null && element.direccion != '') ? element.direccion : ''}</td>
                    <td><input type="hidden" name="ubigeoEstablecimiento[]" value="${(element.ubigeo != null && element.ubigeo != '') ? element.ubigeo : ''}"><input type="hidden" name="descripcionUbigeoEstablecimiento[]" value="${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}">${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}</td>
                    <td><input type="hidden" name="horarioEstablecimiento[]" value="${(element.horario != null && element.horario != '') ? element.horario : ''}">${(element.horario != null && element.horario != '') ? element.horario : ''}</td>
                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaEstablecimientoProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }
    llenarTablaContactosDeProveedorSeleccionadoSoloLectura(data) {
        this.limpiarTabla('listaContactoProveedorSoloLectura');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaContactoProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idContacto[]" value="${(element.id_datos_contacto != null && element.id_datos_contacto != '') ? element.id_datos_contacto : ''}"><input type="hidden" name="nombreContacto[]" value="${(element.nombre != null && element.nombre != '') ? element.nombre : ''}"> ${(element.nombre != null && element.nombre != '') ? element.nombre : ''}</td>
                    <td><input type="hidden" name="cargoContacto[]" value="${(element.cargo != null && element.cargo != '') ? element.cargo : ''}">${(element.cargo != null && element.cargo != '') ? element.cargo : ''}</td>
                    <td><input type="hidden" name="telefonoContacto[]" value="${(element.telefono != null && element.telefono != '') ? element.telefono : ''}">${(element.telefono != null && element.telefono != '') ? element.telefono : ''}</td>
                    <td><input type="hidden" name="emailContacto[]" value="${(element.email != null && element.email != '') ? element.email : ''}">${(element.email != null && element.email != '') ? element.email : ''}</td>
                    <td><input type="hidden" name="direccionContacto[]" value="${(element.direccion != null && element.direccion != '') ? element.direccion : ''}">${(element.direccion != null && element.direccion != '') ? element.direccion : ''}</td>
                    <td><input type="hidden" name="ubigeoContactoProveedor[]" value="${(element.ubigeo != null && element.ubigeo != '') ? element.ubigeo : ''}"><input type="hidden" name="descripcionUbigeoContactoProveedor[]" value="${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}">${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}</td>
                    <td><input type="hidden" name="horarioContacto[]" value="${(element.horario != null && element.horario != '') ? element.horario : ''}">${(element.horario != null && element.horario != '') ? element.horario : ''}</td>
                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaContactoProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

    llenarTablaCuentaBancariaDeProveedorSeleccionadoSoloLectura(data) {
        this.limpiarTabla('listaCuentaBancariasProveedorSoloLectura');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaCuentasBancariasProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idBanco[]" value="${(element.id_banco != null && element.id_banco != '') ? element.id_banco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.banco!=null && element.banco.contribuyente != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}"> ${(element.banco.contribuyente.razon_social != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}</td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.id_tipo_cuenta != null && element.id_tipo_cuenta != '') ? element.id_tipo_cuenta : ''}">${(element.tipo_cuenta != null && element.tipo_cuenta.descripcion != '') ? element.tipo_cuenta.descripcion : ''}</td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.id_moneda != null && element.id_moneda != '') ? element.id_moneda : ''}">${(element.moneda != null && element.moneda.descripcion != '') ? element.moneda.descripcion : ''}</td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}">${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}</td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}">${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}</td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}">${(element.swift != null && element.swift != '') ? element.swift : ''}</td>
                    <td>${(element.fecha_registro != null && element.fecha_registro != '') ? element.fecha_registro : ''}</td>
                    <td>${(element.updated_at != null && element.updated_at != '') ? element.updated_at : ''}</td>
                    <td>${(element.usuario != null && element.usuario.nombre_corto != '') ? element.usuario.nombre_corto : ''}</td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaCuentasBancariasProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

}
