@extends('layout.main')
@include('layout.menu_migracion')

@section('cabecera') Actualizar produtos @endsection

@section('estilos')
    <style>

    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Actualizar</a></li>
    <li>SoftLink</li>
    <li class="active">Productos por serie</li>
</ol>
@endsection

@section('content')
<div class="box box-danger">
    <div class="box-header with-border">
        <h3 class="box-title">Actualizar productos</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form">
        <div class="box-body">

        </div>
        <!-- /.box-body -->

        <div class="box-footer">
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script>
    </script>
@endsection
