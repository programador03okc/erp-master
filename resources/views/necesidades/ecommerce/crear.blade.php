@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Lista de pedidos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
<link rel="stylesheet" href="{{asset('template/plugins/select2/select2.min.css')}}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li class="active">Ecommerce</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_requerimiento">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header">
                    <h2 class="box-title">Nuevo requerimiento
                      {{-- <small>Pedidos de la pagina </small> --}}
                    </h2>
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                      {{-- <a href="{{route('necesidades.ecommerce.index')}}" class="btn btn-danger " title="Nuevo requerimiento">
                        <i class="fa fa-arrow-left"></i> Volver</a> --}}
                    </div>
                    <!-- /. tools -->
                </div>
                <form action="{{route('necesidades.ecommerce.guardar')}}" method="post" enctype="multipart/form-data" data-form="guardar">
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""> Concepto/Motivo * </label>
                                    <input type="text" class="form-control" name="concepto" required>
                                </div>
                            </div>
                            <div class="col-md-2" id="input-group-moneda">
                                <div class="form-group">
                                    <label for="">Moneda</label>
                                    <select class="form-control" name="moneda" required>
                                        @foreach ($monedas as $moneda)
                                        <option data-simbolo="{{$moneda->simbolo}}" value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Periodo</label>
                                    <select class="form-control" name="periodo" required>
                                        @foreach ($periodos as $periodo)
                                        <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Prioridad</label>
                                    <select class="form-control" name="prioridad" required>
                                        @foreach ($prioridades as $prioridad)
                                        <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Empresa *</label>
                                    <select class="form-control" name="empresa" required>
                                        <option value="0">Elija una opción</option>
                                        @foreach ($empresas as $empresa)
                                        <option value="{{$empresa->id_empresa}}">{{ $empresa->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Fecha límite entrega</label>
                                    <input type="date" class="form-control" name="fecha_entrega" required>
                                </div>
                            </div>
                            {{-- <div class="col-md-2" >
                                <div class="form-group">
                                    <label for="">Solicitado por</label>
                                    <div style="display:flex;">
                                        <input type="text" name="nombre_trabajador" class="form-control group-elemento" placeholder="Trabajador" value="" readonly="" required>
                                        <button type="button" class="group-tex btn-primary activation"">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Solicitado por :</label>
                                    <select class="form-control select2 buscar-trabajador" name="nombre_trabajador" style="width: 100%;" required>

                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Observación:</label>
                                            <textarea class="form-control" name="observacion" required></textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>
                                    Item's de requerimiento
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success agregar-item"><i class="fa fa-plus"></i> Nuevo requerimiento</button>
                                <button type="button" class="btn btn-warning"><i class="fas fa-paperclip"></i> Adjuntar</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed table-bordered"width="100%">
                                        <thead>
                                            <tr>
                                                {{-- <th style="width: 3%">#</th> --}}
                                                <th style="width: 10%">Part number</th>
                                                <th>Descripción de item</th>
                                                <th style="width: 10%">Unidad</th>
                                                <th style="width: 10%">Cantidad</th>
                                                <th style="width: 10%">Precio Unit.<span name="simboloMoneda">S/</span><em>(Sin IGV)</em></th>
                                                <th style="width: 6%">Subtotal</th>
                                                <th style="width: 15%">Motivo</th>
                                                <th style="width: 7%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody data-table="requerimientos">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right"><strong>Monto neto:</strong></td>
                                                <td class="text-right"><span name="simboloMoneda">S/</span><label name="monto_subtotal"> 0.00</label>
                                                    <input type="hidden" name="monto_subtotal" value="">
                                                </td>

                                                <td></td>
                                                <td></td>
                                                {{-- <td></td> --}}
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right">
                                                    <strong>IGV 18% </strong><input class="activation handleClickIncluyeIGV" type="checkbox" name="incluye_igv" checked>
                                                </td>
                                                <td class="text-right"><span name="simboloMoneda">S/</span><label name="monto_igv"> 0.00</label>
                                                    <input type="hidden" name="monto_igv" value="">
                                                </td>

                                                <td></td>
                                                <td></td>
                                                {{-- <td></td> --}}
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"><strong>Monto Total:</strong></td>
                                                <td class="text-right"><span name="simboloMoneda">S/</span><label name="monto_total"> 0.00</label>
                                                    <input type="hidden" name="monto_total" value="">
                                                </td>
                                                <td></td>
                                                <td></td>
                                                {{-- <td></td> --}}
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success pull-right" style="margin-left: 4px;"><i class="fas fa-save"></i> Guardar</button>
                        <a href="{{route('necesidades.ecommerce.index')}}" class="btn btn-danger pull-right" title="Nuevo requerimiento">
                            <i class="fa fa-arrow-left"></i> Volver</a>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<!-- Select2 -->
<script src="{{asset('template/plugins/select2/select2.min.js')}}"></script>

<script src="{{ asset('js/necesidades/ecommerce/crear.js') }}"></script>
<script>
    var unidades_medida = JSON.parse('{!!$unidades_medida!!}');
    $(document).ready(function () {
        $('.select2').select2()
        $('.buscar-trabajador').select2({
            placeholder: 'Selecciona un trabajador',
            ajax: {
                url: 'buscar-trabajador',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, // search term
                        page: params.page || 1
                    };
                    // return query;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data, function (item) {
                        return{
                            text:item.nombre_trabajador,
                            id:item.id_trabajador
                        }
                    }),

                };
            },
            cache: true,
            },
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        function formatRepo (repo) {
            // if (repo.id) {
            //     return repo.text;
            // }
            if (repo.loading) {
                return repo.text;
            }
            var state = $(
                `<span>`+repo.text+`</span>`
            );
            return state;

        }

        function formatRepoSelection (repo) {
            return repo.nombre_trabajador || repo.text;
        }
    });
</script>


@endsection
