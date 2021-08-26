class ListarProveedorView {

    constructor(proveedorCtrl) {
        this.proveedorCtrl = proveedorCtrl;
    }

    initializeEventHandler(){
        $('#form-listaProveedores').on("click","button.handleClickNuevoProveedor", ()=>{
            this.nuevoProveedor();
        });
        $('#modal-proveedor').on("click","button.handleClickNuevoCuentaBancariaProveedor", ()=>{
            this.agregarCuentaBancaria();
        });
        $('#modal-proveedor').on("click","button.handleClickNuevoAdjuntoProveedor", ()=>{
            this.agregarAdjuntoProveedor();
        });
        $('#modal-proveedor').on("click","button.handleClickNuevoContactoProveedor", ()=>{
            this.agregarContactoProveedor();
        });
    }

    mostrar(){
        vista_extendida();
        var vardataTables = funcDatatables();
        let $tablaListaProveedores= $('#listaProveedores').DataTable({
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
                { 'data': 'contribuyente.tipo_documento_identidad.descripcion', 'name': 'contribuyente.tipoDocumentoIdentidad.descripcion', 'className': 'text-center' },
                { 'data': 'contribuyente.nro_documento', 'name': 'contribuyente.nro_documento', 'className': 'text-center' },
                { 'data': 'contribuyente.razon_social', 'name': 'contribuyente.razon_social', 'className': 'text-left' },
                { 'data': 'contribuyente.tipo_contribuyente.descripcion', 'name': 'contribuyente.tipoContribuyente.descripcion', 'className': 'text-center' },
                { 'data': 'contribuyente.pais.descripcion', 'name': 'contribuyente.pais.descripcion', 'className': 'text-center' },
                { 'data': 'contribuyente.ubigeo', 'name': 'contribuyente.ubigeo', 'className': 'text-center' },
                { 'data': 'contribuyente.direccion_fiscal', 'name': 'contribuyente.direccion_fiscal', 'className': 'text-left' },
                { 'data': 'contribuyente.telefono', 'name': 'contribuyente.telefono', 'className': 'text-center' },
                { 'data': 'estado_proveedor.descripcion', 'name': 'estadoProveedor.descripcion', 'className': 'text-center' },
                { 'data': 'id_proveedor', 'name': 'id_proveedor', 'className': 'text-center', 'searchable': false  },
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        return row.contribuyente.ubigeo_completo?row.contribuyente.ubigeo_completo:'';
                    }, targets: 5
                },
                {
                    'render': function (data, type, row) {

                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                            <button type="button" class="btn btn-xs btn-info btnVerDetalle handleClickVerDetalleProveedor" data-id-proveedor="${row.id_proveedor}" title="Ver detalle" ><i class="fas fa-eye fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-warning btnEditarProveedor handleClickEditarProveedor" data-id-proveedor="${row.id_proveedor}" title="Editar" ><i class="fas fa-edit fa-xs"></i></button>
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
            "drawCallback": function( settings ) {
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

    construirTablaListaProveedores(data){
        console.log(data);
    }


    nuevoProveedor(){
        $('#modal-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
    }
    agregarCuentaBancaria(){
        $('#modal-agregar-cuenta-bancaria').modal({
            show: true,
            backdrop: 'true'
        });
    }
    agregarAdjuntoProveedor(){
        $('#modal-agregar-adjunto-proveedor').modal({
            show: true,
            backdrop: 'true'
        });
    }
    agregarContactoProveedor(){
        $('#modal-agregar-contacto').modal({
            show: true,
            backdrop: 'true'
        });
    }
    // mostrar(){
    //     this.proveedorCtrl.getListaProveedores().then((res)=> {
    //         this.construirTablaListaProveedores(res);
    //     }).catch(function (err) {
    //         console.log(err)
    //     })
    // }


}