@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Presupuesto Interno
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<style>
    .lbl-codigo:hover{
        color:#007bff !important;
        cursor:pointer;
    }
    .d-none{
        display: none;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><i class="fa fa-usd"></i> Finanzas </li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('finanzas.presupuesto.presupuesto-interno.guardar') }}" method="post" data-form="guardar-partida" enctype="multipart/formdata">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">NUEVO PRESUPUESTO INTERNO</h3>
                    <div class="box-tools pull-right">
                        {{-- <div class="btn-group" role="group"> --}}
                            <button title="Volver a la lista de presupuesto interno"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                Volver
                            </button>
                            <button title="Guardar" type="submit"
                                class="btn btn-sm btn-success">
                                <i class="fa fa-save"></i>
                                Guardar
                            </button>
                            <button title="" type="button"
                                class="btn btn-sm btn-success" data-action="generar" data-tipo="1">
                                <i class="fa fa-retweet"></i>
                                Ingresos
                            </button>
                            <button title="" type="button"
                                class="btn btn-sm btn-success" data-action="generar" data-tipo="3">
                                <i class="fa fa-retweet"></i>
                                Gasto
                            </button>
                            <!-- <a target="_blank" href="#" title="Imprimir" class="btn">
                                <i class="glyphicon glyphicon-search" aria-hidden="true"></i>
                            </a> -->
                        {{-- </div> --}}
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_grupo">Grupo :</label>
                                <select class="form-control" name="id_grupo" id="id_grupo">
                                    <option value="">Seleccione...</option>
                                    @foreach ($grupos as $item)
                                    <option value="{{ $item->id_grupo }}">{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_grupo">Area :</label>
                                <select class="form-control" name="id_grupo" id="id_grupo">
                                    <option value="">Seleccione...</option>
                                    @foreach ($area as $item)
                                    <option value="{{ $item->id_area }}">{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_grupo">Moneda :</label>
                                <select class="form-control" name="id_grupo" id="id_grupo">
                                    <option value="">Seleccione...</option>
                                    @foreach ($moneda as $item)
                                    <option value="{{ $item->id_moneda }}">{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripcion : </label>
                                <textarea id="descripcion" class="form-control" name="descripcion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 animate__animated d-none">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-1"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 animate__animated d-none">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-2"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-md-offset-3 animate__animated d-none">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-3"></div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="modal-partida" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="post" data-form="agregar-partida">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Agregar</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="key" value="">
                    <p>Content</p>
                </div>
                <div class="modal-footer">
                    Footer
                </div>
            </form>
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script>
        // let csrf_token = "{{ csrf_token() }}";
        // $(document).ready(function () {
        //     seleccionarMenu(window.location);
        // });
    </script>

    <script src="{{asset('js/finanzas/presupuesto_interno/crear.js') }}""></script>
@endsection
