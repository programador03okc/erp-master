@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Kardex por Producto
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Reportes</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="kardex_detallado">
    <div class="row">
        <div class="col-md-12">
            <div class="input-group-okc">
                <input class="oculto" name="id_producto">
                <input type="text" class="form-control" readonly 
                    placeholder="Seleccione un producto..." aria-describedby="basic-addon2" name="descripcion">
                <div class="input-group-append">
                    <button type="button" class="input-group-text" id="basic-addon2" onClick="productoModal();">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <h5>Empresas</h5>
            <select class="form-control js-example-basic-single" name="id_empresa" >
                <option value="0">Elija una opción</option>
                @foreach ($empresas as $alm)
                    <option value="{{$alm->id_empresa}}">{{$alm->razon_social}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <h5>Almacén</h5>
            <select class="form-control js-example-basic-single" name="almacen">
                {{-- @foreach ($almacenes as $alm)
                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                @endforeach --}}
            </select>
        </div>
        <div class="col-md-6">
            <h5>Rango de Fechas</h5>
            <div class="input-group">
                <span class="input-group-addon"> Desde: </span>
                <input type="date" class="form-control" name="fecha_inicio">
                <span class="input-group-addon"> Hasta: </span>
                <input type="date" class="form-control" name="fecha_fin">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Kardex" 
                onClick="generar_kardex();">Actualizar Kardex</button>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Exportar Kardex" 
                onClick="download_kardex_excel();">Excel</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id ="datos_producto" class="table-group">
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" id="kardex_producto">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Documento</th>
                        <th>Factura</th>
                        <th>Proveedor</th>
                        <th>Ingreso</th>
                        <th>Salida</th>
                        <th>Saldo</th>
                        <th>Ingreso</th>
                        <th>Salida</th>
                        <th>Valorizacion</th>
                        <th>Posicion</th>
                        <th>Ope</th>
                        <th>Tipo de Operacion</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <th colSpan="4"></th>
                    <th class="right"><label name="suma_ing_cant"></label></th>
                    <th class="right"><label name="suma_sal_cant"></label></th>
                    <th></th>
                    <th class="right"><label name="suma_ing_val"></label></th>
                    <th class="right"><label name="suma_sal_val"></label></th>
                    <th colSpan="4"></th>
                </tfoot>
            </table>
        </div>
    </div>
    {{-- <div class="row">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="kardexGeneral">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Fecha Mov.</th>
                        <th>Posicion</th>
                        <th>Und</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Saldo</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Valoriz.</th>
                        <th>Tp</th>
                        <th>Movimiento</th>
                        <th>Guía</th>
                        <th>Doc</th>
                        <th>Req.</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div> --}}
</div>
@include('almacen.producto.productoModal')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{ asset('js/almacen/reporte/kardex_detallado.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection