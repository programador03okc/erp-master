@extends('layout.main')
@include('layout.menu_proyectos')

@section('cabecera')
Cuadro de gastos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
    <li>Reportes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="cuadro_gastos">
    <div class="thumbnail" style="padding-left: 20px;padding-right: 20px;padding-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <h5>Presupuesto</h5>

                <div style="display:flex;">
                    {{-- <input type="text" name="codigo_proyecto" class="form-control group-elemento" style="width:130px; text-align:center;" readonly> --}}
                    <div class="input-group-okc">
                        <select class="form-control activation" name="id_presup">
                            <option value="0">Seleccione un Proyecto</option>
                            @foreach ($presupuestos as $p)
                            <option value="{{$p->id_presup}}">{{$p->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button data-toggle="tooltip" data-placement="bottom" title="Exportar a excel" 
                        class="btn btn-success btn-sm exportar" style="color:#fff !important;" onClick="exportarCuadroCostos()">
                        <i class="fas fa-file-excel"></i> Exportar a excel
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="listaEstructura" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">N° Req.</th>
                                <th class="text-center">Cuenta(Partida)</th>
                                <th class="text-center">Cuenta(Sub partida)</th>
                                <th class="text-center">Tipo Doc.</th>
                                <th class="text-center">Serie - número</th>
                                <th class="text-center">RUC/DNI</th>
                                <th class="text-center">Proveedor o persona asignada</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-center">Und.</th>
                                <th class="text-center">Desripción</th>
                                <th class="text-center">Mon.</th>
                                <th class="text-center">P.U</th>
                                <th class="text-center">V.Compra</th>
                                <th class="text-center">IGV</th>
                                <th class="text-center">P.Compra</th>
                                <th class="text-center">Estado Pago</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>

<script src="{{ asset('js/proyectos/reportes/cuadro_gastos.js')}}"></script>
<script>
    // let csrf_token = "{{ csrf_token() }}";
    $(document).ready(function() {
        seleccionarMenu(window.location);
        
    });
</script>
@endsection