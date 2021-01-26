@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Importar Inicial
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Control de Stock</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="importar">

    <div class="col-md-12" id="tab-importar"  style="padding-left:0px;padding-right:0px;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#productos">Carga de Productos</a></li>
            <li class=""><a type="#stocks">Carga de Stocks por Almacén</a></li>
        </ul>
        <div class="content-tabs">
            <section id="productos" >
                <form id="form-productos" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mae-line">
                                <label class="alert" style="margin-bottom:0px; padding-left:0px;">
                                Seleccione un archivo CSV o de texto plano separado por tabulaciones para cargar (no archivo excel)
                                </label>
                                <a href="javascript:void(0);" id="descargar_xls" class="xls_export">
                                    <i class="fas fa-download"></i> Descarga el formato</a>
                                <br/>
                                <input id="file" name="file" size="30" type="file" class="selected right">
                            </div>
                            <br/>
                            <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                            title="Cargar Productos" onClick="open_guia_create_seleccionadas();">Cargar Productos</button>
                        </div>
                    </div>
                </form>
            </section>
            <section id="stocks" hidden>
                <form id="form-stocks" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mae-line">
                                Seleccione un archivo CSV o de texto plano separado por tabulaciones para cargar,
                                <label class="alert" style="margin-bottom:0px;">
                                no un archivo excel
                                </label>
                            </div>
                            <input id="file" name="file" size="30" type="file" class="selected">
                            <br/>
                            <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" 
                            title="Cargar stocks" onClick="open_guia_create_seleccionadas();">Cargar Productos</button>
                        </div>
                    </div>
                </form>
            </section>
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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/js-xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
    // $(document).ready(function(){
    //     seleccionarMenu(window.location);
    //     iniciar('{{Auth::user()->tieneAccion(83)}}');
    // });
    </script>
@endsection