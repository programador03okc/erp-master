@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Lista de Presupuestos
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
        <li><a href="{{route('finanzas.index')}}"><i class="fa fa-usd"></i> Finanzas</a></li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Datos Generales</h3>
            <div class="box-tools pull-right">
                {{-- <button type="button" data-toggle="modal" data-target="actualizarPartidas"
                    title="Actualizar descripcion de partidas" class="btn btn-box-tool btn-sm btn-info"
                    onClick="actualizarPartidas();">
                    <i class="fas fa-sync-alt"></i>
                </button> --}}
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view"
                        id="listaPresupuestos">
                        <thead>
                            <tr>
                                <th hidden></th>
                                <th scope="col">Código</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Fecha Emisión</th>
                                <th scope="col">Activo</th>
                                <th scope="col">Empresa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presupuestos as $presup)
                            <tr>
                                <td hidden>{{ $presup->id_presup }}</td>
                                <td><label class="lbl-codigo" title="Abrir Presupuesto" onClick="abrirPresupuesto('{{ $presup->id_presup }}')">{{ $presup->codigo }}</label></td>
                                <td>{{ $presup->descripcion }}</td>
                                <td>{{ $presup->fecha_emision }}</td>
                                <td class="text-center"> <span class="pull-right badge bg-{{ ($presup->activo=='t'?'green':'red') }}">{{ ($presup->activo=='t'?'activo':'inactivo') }}</span></td>
                                <td>{{ $presup->empresa->contribuyente->razon_social }}</td>
                            </tr>
                            @empty
                                <tr><td colSpan="6">No hay registros para mostrar</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script> -->
    <!-- <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            seleccionarMenu(window.location);

            var vardataTables = funcDatatables();

            $('#listaPresupuestos').DataTable({
                'dom': vardataTables[1],
                'buttons': vardataTables[2],
                'language' : vardataTables[0],
                'destroy' : true,
            });
        });

        function abrirPresupuesto(id){
            console.log('abrirPresupuesto()');
            localStorage.setItem("id_presup",id);
            location.assign("/finanzas/presupuesto/create");
        }

        function actualizarPartidas()
        {
            $.ajax({
                type: 'GET',
                url: 'actualizarPartidas',
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    alert("Se actualizaron correctamente las descripciones");
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

    </script>
@endsection
