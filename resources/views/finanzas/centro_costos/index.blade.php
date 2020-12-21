@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Centros de Costos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<style>
    .lbl-codigo:hover{
        color:#007bff !important; 
        cursor:pointer;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><i class="fa fa-usd"></i> Finanzas</li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-hover table-bordered dt-responsive nowrap" 
                        id="listaCentroCostos">
                        <thead>
                            <tr style="background: gainsboro;">
                                <th hidden></th>
                                <th scope="col">Código</th>
                                <th scope="col">Descripción</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <form id="form-centro-costos" style="padding-right: 20px; padding-top: 10px;">
                        <input style="display: none" name="id_centro_costo"/> 
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Código</h5>
                                <input type="text" name="codigo" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Descripción</h5>
                                <input type="text" name="descripcion" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" style="padding-top: 24px;">
                                <input type="submit" id="submit-cc" class="btn btn-success" value="Guardar"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{('/js/finanzas/centro_costos/centro_costos.js')}}"></script>
    <script>
        $(document).ready(function () {
            seleccionarMenu(window.location);
            mostrarCentroCostos();
        });
    </script>
@endsection