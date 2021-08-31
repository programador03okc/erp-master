
class ListarProveedorView {

    constructor(proveedorCtrl) {
        this.proveedorCtrl = proveedorCtrl;
        this.objectBtnEdition;
    }

    initializeEventHandler() {
        $('#form-listaProveedores').on("click", "button.handleClickNuevoProveedor", () => {
            this.nuevoProveedor();
        });
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
        //  modal contacto
        $('#modal-agregar-contacto').on("click", "button.handleClickOpenModalUbigeoContacto", () => {
            this.openModalUbigeoContacto();
        });

        $('#modal-agregar-contacto').on("click", "button.handleClickAgregarContacto", () => {
            this.agregarContactoAProveedor();
        });
        $('#modal-agregar-contacto').on("keyup", "input.handleKeyUpTelefono", (e) => {
            this.validacionRegexSoloNumeros(e);
        });
        $('#modal-proveedor').on("click", "button.handleClickAnularContacto", (e) => {
            this.anularContactoProveedor(e.currentTarget);
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
        let $tablaListaProveedores = $('#listaProveedores').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'order': [[2, 'asc']],
            'bLengthChange': false,
            'serverSide': true,
            'ajax': {
                'url': 'lista-proveedores',
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
                { 'data': 'id_proveedor', 'name': 'id_proveedor', 'className': 'text-center', 'searchable': false },
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
                            <button type="button" class="btn btn-xs btn-info btnVerDetalle handleClickVerDetalleProveedor" data-id-proveedor="${row.id_proveedor}" title="Ver detalle" ><i class="fas fa-eye fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-warning btnEditarProveedor handleClickEditarProveedor" data-id-proveedor="${row.id_proveedor}" title="Editar" ><i class="fas fa-edit fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-danger btnAnularProveedor handleClickAnularProveedor" data-id-proveedor="${row.id_proveedor}" title="Anular" ><i class="fas fa-times fa-xs"></i></button>
                        </div></center>`;
                    }, targets: 9,
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
            document.querySelector("form[id='form-proveedor']").setAttribute("type", "register");
        }
        document.querySelector("h3[class='modal-title']").textContent = 'Nuevo Proveedor';
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
        $("#form-agregar-contacto")[0].reset();

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
                    <td><input type="hidden" name="idContacto[]" value="0"><input type="hidden" name="nombreContacto[]" value="${(element.nombreContacto != null && element.nombreContacto != '') ? element.nombreContacto : ''}"> ${(element.nombreContacto != null && element.nombreContacto != '') ? element.nombreContacto : ''}</td>
                    <td><input type="hidden" name="cargoContacto[]" value="${(element.cargoContacto != null && element.cargoContacto != '') ? element.cargoContacto : ''}">${(element.cargoContacto != null && element.cargoContacto != '') ? element.cargoContacto : ''}</td>
                    <td><input type="hidden" name="telefonoContacto[]" value="${(element.telefonoContacto != null && element.telefonoContacto != '') ? element.telefonoContacto : ''}">${(element.telefonoContacto != null && element.telefonoContacto != '') ? element.telefonoContacto : ''}</td>
                    <td><input type="hidden" name="emailContacto[]" value="${(element.emailContacto != null && element.emailContacto != '') ? element.emailContacto : ''}">${(element.emailContacto != null && element.emailContacto != '') ? element.emailContacto : ''}</td>
                    <td><input type="hidden" name="direccionContacto[]" value="${(element.direccionContacto != null && element.direccionContacto != '') ? element.direccionContacto : ''}">${(element.direccionContacto != null && element.direccionContacto != '') ? element.direccionContacto : ''}</td>
                    <td><input type="hidden" name="ubigeoContactoProveedor[]" value="${(element.ubigeoContactoProveedor != null && element.ubigeoContactoProveedor != '') ? element.ubigeoContactoProveedor : ''}"><input type="hidden" name="descripcionUbigeoContactoProveedor[]" value="${(element.descripcionUbigeoContactoProveedor != null && element.descripcionUbigeoContactoProveedor != '') ? element.descripcionUbigeoContactoProveedor : ''}">${(element.descripcionUbigeoContactoProveedor != null && element.descripcionUbigeoContactoProveedor != '') ? element.descripcionUbigeoContactoProveedor : ''}</td>
                    <td><input type="hidden" name="horarioContacto[]" value="${(element.horarioContacto != null && element.horarioContacto != '') ? element.horarioContacto : ''}">${(element.horarioContacto != null && element.horarioContacto != '') ? element.horarioContacto : ''}</td>
                    <td>
                    <input type="hidden" class="estadoContacto" name="estadoContacto[]" value="1">
                    <div id="contenedorBotoneraAccionContacto">
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

    construirTablaCuentaBancariaProveedor(data) {
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaCuentasBancariasProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td> 
                        <input type="hidden" name="idCuenta[]" value="0">
                        <input type="hidden" name="idBanco[]" value="${(element.idBanco != null && element.idBanco != '') ? element.idBanco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.nombreBanco != null && element.nombreBanco != '') ? element.nombreBanco : ''}"> ${(element.nombreBanco != null && element.nombreBanco != '') ? element.nombreBanco : ''}</td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.idTipoCuenta != null && element.idTipoCuenta != '') ? element.idTipoCuenta : ''}">${(element.nombreTipoCuenta != null && element.nombreTipoCuenta != '') ? element.nombreTipoCuenta : ''}</td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.idMoneda != null && element.idMoneda != '') ? element.idMoneda : ''}">${(element.nombreMoneda != null && element.nombreMoneda != '') ? element.nombreMoneda : ''}</td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nroCuenta != null && element.nroCuenta != '') ? element.nroCuenta : ''}">${(element.nroCuenta != null && element.nroCuenta != '') ? element.nroCuenta : ''}</td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nroCuentaInterbancaria != null && element.nroCuentaInterbancaria != '') ? element.nroCuentaInterbancaria : ''}">${(element.nroCuentaInterbancaria != null && element.nroCuentaInterbancaria != '') ? element.nroCuentaInterbancaria : ''}</td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}">${(element.swift != null && element.swift != '') ? element.swift : ''}</td>
                    <td>
                        <input type="hidden" class="estadoCuenta" name="estadoCuenta[]" value="1">
                        <div id="contenedorBotoneraAccionCuentaBancaria">
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
        let data = {
            'nroDocumento': document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value,
            'razonSocial': document.querySelector("div[id='modal-proveedor'] input[name='razonSocial']").value,
            // 'direccion' :document.querySelector("div[id='modal-proveedor'] input[name='direccion']").value,
            // 'ubigeoProveedor' : document.querySelector("div[id='modal-proveedor'] input[name='ubigeoProveedor']").value,
            // // 'descripcionUbigeoProveedor' : document.querySelector("div[id='modal-proveedor'] input[name='descripcionUbigeoProveedor']").value,
            // 'telefono' :document.querySelector("div[id='modal-proveedor'] input[name='telefono']").value,
            // 'celular' : document.querySelector("div[id='modal-proveedor'] input[name='celular']").value,
            // 'email' : document.querySelector("div[id='modal-proveedor'] input[name='email']").value
        }

        if (data.nroDocumento == null || data.nroDocumento.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar un numero de documento.</li>';
        }
        if (data.razonSocial == null || data.razonSocial.trim() == '') {
            mensaje += '<li style="text-align: left;">Debe ingresar una razón social.</li>';
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
        } else {
            let formData = new FormData($('#form-proveedor')[0]);
            $.ajax({
                type: 'POST',
                url: 'guardar-proveedor',
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
                    url: 'anular-proveedor',
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
        $('#modal-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
        if (document.querySelector("form[id='form-proveedor']").getAttribute("type") == 'register') {
            $("#form-proveedor")[0].reset();
            document.querySelector("form[id='form-proveedor']").setAttribute("type", "edition");
        }
        document.querySelector("h3[class='modal-title']").textContent = 'Editar Proveedor';
        document.querySelector("button[id='btnGuardarProveedor']").classList.add("oculto");
        document.querySelector("button[id='btnActualizarProveedor']").classList.remove("oculto");

        this.proveedorCtrl.getProveedor(idProveedor).then((res) => {

            document.querySelector("div[id='modal-proveedor'] input[name='idProveedor']").value = res.id_proveedor;
            document.querySelector("div[id='modal-proveedor'] select[name='tipoContribuyente']").value = res.contribuyente.id_tipo_contribuyente;
            document.querySelector("div[id='modal-proveedor'] select[name='tipoDocumentoIdentidad']").value = res.contribuyente.tipo_documento_identidad.id_doc_identidad;
            document.querySelector("div[id='modal-proveedor'] input[name='nroDocumento']").value = res.contribuyente.nro_documento;
            document.querySelector("div[id='modal-proveedor'] input[name='razonSocial']").value = res.contribuyente.razon_social;
            document.querySelector("div[id='modal-proveedor'] input[name='direccion']").value = res.contribuyente.direccion_fiscal;
            document.querySelector("div[id='modal-proveedor'] select[name='pais']").value = res.contribuyente.pais.id_pais;
            document.querySelector("div[id='modal-proveedor'] input[name='ubigeoProveedor']").value = res.contribuyente.ubigeo;
            document.querySelector("div[id='modal-proveedor'] input[name='descripcionUbigeoProveedor']").value = res.contribuyente.ubigeo_completo;
            document.querySelector("div[id='modal-proveedor'] input[name='telefono']").value = res.contribuyente.telefono;
            document.querySelector("div[id='modal-proveedor'] input[name='celular']").value = res.contribuyente.celular;
            document.querySelector("div[id='modal-proveedor'] input[name='email']").value = res.contribuyente.email;
            document.querySelector("div[id='modal-proveedor'] textarea[name='observacion']").value = res.observacion;

            if (res.contacto_contribuyente.length > 0) {
                this.llenarTablaContactosDeProveedorSeleccionado(res.contacto_contribuyente);
            }
            if (res.cuenta_contribuyente.length > 0) {
                this.llenarTablaCuentaBancariaDeProveedorSeleccionado(res.cuenta_contribuyente);
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


    llenarTablaContactosDeProveedorSeleccionado(data) {
        this.limpiarTabla('listaContactoProveedor');
        if (data.length > 0) {
            (data).forEach(element => {
                document.querySelector("tbody[id='bodylistaContactoProveedor']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td><input type="hidden" name="idContacto[]" value="${(element.id_datos_contacto != null && element.id_datos_contacto != '') ? element.id_datos_contacto : ''}"><input type="hidden" name="nombreContacto[]" value="${(element.nombre != null && element.nombre != '') ? element.nombre : ''}"> ${(element.nombre != null && element.nombre != '') ? element.nombre : ''}</td>
                    <td><input type="hidden" name="cargoContacto[]" value="${(element.cargo != null && element.cargo != '') ? element.cargo : ''}">${(element.cargo != null && element.cargo != '') ? element.cargo : ''}</td>
                    <td><input type="hidden" name="telefonoContacto[]" value="${(element.telefono != null && element.telefono != '') ? element.telefono : ''}">${(element.telefono != null && element.telefono != '') ? element.telefono : ''}</td>
                    <td><input type="hidden" name="emailContacto[]" value="${(element.email != null && element.email != '') ? element.email : ''}">${(element.email != null && element.email != '') ? element.email : ''}</td>
                    <td><input type="hidden" name="direccionContacto[]" value="${(element.direccion != null && element.direccion != '') ? element.direccion : ''}">${(element.direccion != null && element.direccion != '') ? element.direccion : ''}</td>
                    <td><input type="hidden" name="ubigeoContactoProveedor[]" value="${(element.ubigeo != null && element.ubigeo != '') ? element.ubigeo : ''}"><input type="hidden" name="descripcionUbigeoContactoProveedor[]" value="${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}">${(element.ubigeo_completo != null && element.ubigeo_completo != '') ? element.ubigeo_completo : ''}</td>
                    <td><input type="hidden" name="horarioContacto[]" value="${(element.horario != null && element.horario != '') ? element.horario : ''}">${(element.horario != null && element.horario != '') ? element.horario : ''}</td>
                    <td>
                    <input type="hidden" class="estadoContacto" name="estadoContacto[]" value="1">
                    <div id="contenedorBotoneraAccionContacto">
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
                        <input type="hidden" name="idBanco[]" value="${(element.id_banco != null && element.id_banco != '') ? element.id_banco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.banco.contribuyente != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}"> ${(element.banco.contribuyente.razon_social != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}</td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.id_tipo_cuenta != null && element.id_tipo_cuenta != '') ? element.id_tipo_cuenta : ''}">${(element.tipo_cuenta != null && element.tipo_cuenta.descripcion != '') ? element.tipo_cuenta.descripcion : ''}</td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.id_moneda != null && element.id_moneda != '') ? element.id_moneda : ''}">${(element.moneda != null && element.moneda.descripcion != '') ? element.moneda.descripcion : ''}</td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}">${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}</td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}">${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}</td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}">${(element.swift != null && element.swift != '') ? element.swift : ''}</td>
                    <td>
                        <input type="hidden" class="estadoCuenta" name="estadoCuenta[]" value="1">
                        <div id="contenedorBotoneraAccionCuentaBancaria">
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
        if (!document.querySelector("div[id='modal-proveedor'] input[name='idProveedor']").value > 0) {
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
                url: 'actualizar-proveedor',
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

                        this.objectBtnEdition.closest("tr").querySelector("td[class~='tipoDocumento']").textContent = response.data.contribuyente.tipo_documento_identidad.descripcion ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='nroDocumento']").textContent = response.data.contribuyente.nro_documento ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='razonSocial']").textContent = response.data.contribuyente.razon_social ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='tipoEmpresa']").textContent = response.data.contribuyente.tipo_contribuyente.descripcion ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='pais']").textContent = response.data.contribuyente.pais.descripcion ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='ubigeo']").textContent = response.data.contribuyente.ubigeo_completo ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='direccion']").textContent = response.data.contribuyente.direccion_fiscal ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='telefono']").textContent = response.data.contribuyente.telefono ?? '';
                        this.objectBtnEdition.closest("tr").querySelector("td[class~='estado']").textContent = response.data.estado_proveedor.descripcion ?? '';

                        obj.removeAttribute("disabled");
                        $("#form-proveedor")[0].reset();
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
        $('#modal-ver-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
        let idProveedor = obj.dataset.idProveedor;
        this.proveedorCtrl.getProveedor(idProveedor).then((res) => {
            document.querySelector("div[id='modal-ver-proveedor'] span[id='tituloAdicional']").textContent = res.contribuyente.razon_social;

            document.querySelector("div[id='modal-ver-proveedor'] p[name='tipoContribuyente']").textContent = res.contribuyente.tipo_contribuyente.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='tipoDocumentoIdentidad']").textContent = res.contribuyente.tipo_documento_identidad.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='nroDocumento']").textContent = res.contribuyente.nro_documento;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='razonSocial']").textContent = res.contribuyente.razon_social;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='direccion']").textContent = res.contribuyente.direccion_fiscal;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='pais']").textContent = res.contribuyente.pais.descripcion;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='descripcionUbigeoProveedor']").textContent = res.contribuyente.ubigeo_completo;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='telefono']").textContent = res.contribuyente.telefono;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='celular']").textContent = res.contribuyente.celular;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='email']").textContent = res.contribuyente.email;
            document.querySelector("div[id='modal-ver-proveedor'] p[name='observacion']").textContent = res.observacion;

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
                    <td><input type="hidden" name="idBanco[]" value="${(element.id_banco != null && element.id_banco != '') ? element.id_banco : ''}"><input type="hidden" name="nombreBanco[]" value="${(element.banco.contribuyente != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}"> ${(element.banco.contribuyente.razon_social != null && element.banco.contribuyente.razon_social != '') ? element.banco.contribuyente.razon_social : ''}</td>
                    <td><input type="hidden" name="idTipoCuenta[]" value="${(element.id_tipo_cuenta != null && element.id_tipo_cuenta != '') ? element.id_tipo_cuenta : ''}">${(element.tipo_cuenta != null && element.tipo_cuenta.descripcion != '') ? element.tipo_cuenta.descripcion : ''}</td>
                    <td><input type="hidden" name="idMoneda[]" value="${(element.id_moneda != null && element.id_moneda != '') ? element.id_moneda : ''}">${(element.moneda != null && element.moneda.descripcion != '') ? element.moneda.descripcion : ''}</td>
                    <td><input type="hidden" name="nroCuenta[]" value="${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}">${(element.nro_cuenta != null && element.nro_cuenta != '') ? element.nro_cuenta : ''}</td>
                    <td><input type="hidden" name="nroCuentaInterbancaria[]" value="${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}">${(element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != '') ? element.nro_cuenta_interbancaria : ''}</td>
                    <td><input type="hidden" name="swift[]" value="${(element.swift != null && element.swift != '') ? element.swift : ''}">${(element.swift != null && element.swift != '') ? element.swift : ''}</td>

                    </tr>`);
            });
        } else {
            document.querySelector("tbody[id='bodylistaCuentasBancariasProveedorSoloLectura']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td colspan="5" style="text-align:center;">(Sin registros)</td>
                </tr>`);
        }
    }

}