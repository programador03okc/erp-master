class Util {

    static seleccionarMenu = (url) => {
        $('ul.sidebar-menu a').filter(function () {
            return this.href == url;
        }).parent().addClass('active');

        $('ul.treeview-menu a').filter(function () {
            return this.href == url;
        }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
    }

    static activarFiltros=(tabla, model)=>
    {
        //Filtros
        let actualizarFiltros = false;
        $('#modalFiltros').find('input[type=checkbox]').change(function () {
            actualizarFiltros = true;
        });

        $('#modalFiltros').find('input[type=text], select').change((e) => {
            if ($(e.currentTarget).hasClass('actualizar') || $(e.currentTarget).closest('div.form-group').find('input[type=checkbox]').is(':checked') == true) {
                actualizarFiltros = true;
            }
        });

        $('#modalFiltros').on('hidden.bs.modal', (e) => {
            if (actualizarFiltros) {
                actualizarFiltros = false;
                model.actualizarFiltros($('#formFiltros').serialize()).then((data) => {
                    if (data.tipo == 'success') {
                        //Util.notify(data.tipo, data.mensaje);
                        $(tabla).DataTable().ajax.reload();
                    }
                    else {
                        alert("Hubo un problema al actualizar los filtros. Por favor actualice la página e intente de nuevo");
                    }
                });
            }
        });
    }

    static activarLimiteSubidaArchivos=(limite)=>
    {
        limite = parseInt(limite.substring(0, limite.length - 1));
        $('body').on('change','input[type=file]',function() {
            const numArchivos = this.files.length;
            let sumaArchivos = 0;
            for (let i = 0; i < numArchivos; i++) {
                sumaArchivos += this.files[i].size;
                if (this.files[i].size / 1024 / 1024 > limite) {
                    alert("No se puede subir el archivo " + this.files[i].name + " porque supera el límite permitido de " + limite + "MB. Seleccione otro archivo e intente de nuevo");
                    $(this).val('');
                    //Sale de la funcion para evitar la advertencia de abajo
                    return false;
                }
            }
            if (sumaArchivos / 1024 / 1024 > limite) {
                alert("La suma del tamaño de los archivos supera el límite permitido de " + limite + "MB. Seleccione otro archivo e intente de nuevo");
                $(this).val('');
            }
        });
    }

    //Requiere de la librería bootstrap-datepicker
    static activarDatePicker = () => {
        $('input.date-picker').datepicker({
            language: "es",
            orientation: "bottom auto",
            format: 'dd-mm-yyyy',
            autoclose: true
        });
    }

    //Requiere de la librería select2
    static activarSeleccionListaSelect2 = () => {
        $('div.modal').on("click", "a.seleccionar", function (e) {
            e.preventDefault();
            var $elemento = $(this);
            var $select = $('select[name=' + $elemento.closest('div.modal-body').find('input[name=select]').val() + ']');
            $select.html('<option value="' + $elemento.data('id') + '">' + $elemento.closest('tr').find('td:eq(-2)').html() + '</option>').trigger('change');
        });
        $('body').on("click", "a.select2-lista", function (e) {
            e.preventDefault();
            var nombre = $(this).closest('span').find('ul').attr('id').replace('select2-', '');
            var indice = nombre.indexOf('-');
            nombre = nombre.substring(0, indice);
            $($(this).data('target')).find('input[name=select]').val(nombre);
        });
    }

    static bloquearConSpinner = (contenedor) => {
        $(contenedor).html('<div class="overlay"><i class="fa fa-spinner fa-spin"></i></div>');
    }

    static liberarBloqueoSpinner = (contenedor) => {
        $(contenedor).find('div').fadeOut(300, function () {
            $(this).remove();
        });
    }

    static mensaje = (locacion, tipo, mensaje, autocerrar = true) => {
        let icono;
        switch (tipo) {
            case 'success':
                icono = 'glyphicon-ok';
                break;
            case 'danger':
                icono = 'glyphicon-remove';
                break;
            case 'warning':
                icono = 'glyphicon-warning-sign';
                break;
        }
        $(locacion).html(`
            <div class="alert alert-${tipo} fade in">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <span class="glyphicon ${icono}"></span> ${mensaje}
            </div>`);

        if (autocerrar) {
            $(locacion).find('div.alert').fadeTo(10000, 500).slideUp(500, function () {
                $(this).alert('close');
            });
        }
    }

    static validarCampos = (contenedor) => {
        let valido = true;

        let mostrarError = (element, mensaje) => {
            $(element).closest('div.form-group').addClass('has-error');
            $(element).closest('div').append(`<div class="text-danger mensaje-error"><span class="glyphicon glyphicon-exclamation-sign"></span> ${mensaje}</div>`);
        };

        $(contenedor).find('input, textarea').each((index, element) => {
            $(element).closest('div.form-group').removeClass('has-error');
            $(element).closest('div').find('div.mensaje-error').remove();

            if ($(element).prop('required')) {
                if ($(element).val() == '') {
                    mostrarError(element, 'Ingrese un valor');
                    valido = false;
                    return 'continue';
                }
            }
            if ($(element).prop('min')) {
                if ($(element).val().length < $(element).attr('min')) {
                    mostrarError(element, `El valor ingresado debe ser mayor o igual a ${$(element).attr('min')}`);
                    valido = false;
                    return 'continue';
                }
            }
        });
        return valido;
    }

    static formatoNumero = (numero, decimales, punto_decimal = ".", separador_miles = ",") => {
        numero = (numero + '')
            .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+numero) ? 0 : +numero,
            prec = !isFinite(+decimales) ? 0 : Math.abs(decimales),
            sep = (typeof separador_miles === 'undefined') ? ',' : separador_miles,
            dec = (typeof punto_decimal === 'undefined') ? '.' : punto_decimal,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
            .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }

    static activarSoloDecimales = () => {
        $('body').on("keypress", ".decimal", function (event) {
            if (event.keyCode == 44 || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 45 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                (event.keyCode == 65 && event.ctrlKey === true)) {
                return;
            } else {
                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) {
                    event.preventDefault();
                }
            }
        });
    }

    static activarSoloEnteros = () => {
        $('body').on("keypress", ".entero", function (event) {
            if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                (event.keyCode == 65 && event.ctrlKey === true)) {
                return;
            } else {
                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) {
                    event.preventDefault();
                }
            }
        });
    }

    //Requiere de la librería lobibox
    static notify = (tipo, mensaje) => {
        Lobibox.notify(tipo, {
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: mensaje
        });
    }

    static generarBarraProgreso = (ubicacion, porcentaje) => {
        $(ubicacion).html(`<div class="progress" style="margin-bottom: 10px">
                <div class="progress-bar ${(porcentaje >= 100 ? 'progress-bar-success' : 'progress-bar-striped active')}" role="progressbar" aria-valuenow="${porcentaje}" aria-valuemin="0" 
                aria-valuemax="100" style="min-width: 2em; width: ${porcentaje}%;"> ${porcentaje}% completado</div>
              </div>`);
    }

    static generarPuntosSvg = () => {
        return `<svg style="width: 16px; height: 16px; margin: 0px; display: inline-block" version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" 
viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
<circle fill="#fff" stroke="none" cx="6" cy="50" r="6">
<animateTransform attributeName="transform" dur="1s" type="translate" values="0 15 ; 0 -15; 0 15" repeatCount="indefinite" begin="0.1"/>
</circle>
<circle fill="#fff" stroke="none" cx="30" cy="50" r="6">
<animateTransform attributeName="transform" dur="1s" type="translate" values="0 10 ; 0 -10; 0 10" repeatCount="indefinite" begin="0.2"/>
</circle>
<circle fill="#fff" stroke="none" cx="54" cy="50" r="6">
<animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.3"/>
</circle>
</svg>`;
    }
}