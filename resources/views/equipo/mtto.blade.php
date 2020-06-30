@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@include('layout.option')
@endsection

@section('cabecera')
    Mantenimiento de Equipos
@endsection

@section('content')
<div class="page-main" type="mtto">
    <legend class="mylegend">
        <h2>Mantenimiento de Equipos</h2>
    </legend>
    <form id="form-mtto" type="register" form="formulario">
        <input class="oculto" name="id_mtto" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" class="form-control" name="codigo" disabled="true">
                    </div>
                    <div class="col-md-6">
                        <h5>Proveedor</h5>
                        <div style="display:flex;">
                            <input class="oculto" name="id_proveedor"/>
                            <input class="oculto" name="id_contrib"/>
                            <input type="text" class="form-control activation" name="razon_social" placeholder="Seleccione un proveedor..." 
                                disabled aria-describedby="basic-addon1" required>
                            <button type="button" class="input-group-text activation" id="basic-addon1" onClick="proveedorModal();">
                                <i class="fa fa-search"></i>
                            </button>
                            <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                <i class="fas fa-plus"></i></button>
                        </div>
                        {{-- <div style="display:flex;">
                            <select class="form-control activation" name="id_proveedor" disabled="true">
                                <option value="0" >Elija una opción</option>
                                @foreach ($proveedores as $pro)
                                    <option value="{{$pro->id_proveedor}}">{{$pro->razon_social}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="activation btn-primary" title="Agregar Proveedor" disabled="true"
                                onClick="agregar_proveedor();">
                            <strong>+</strong></button>
                        </div> --}}
                    </div>
                    <div class="col-md-3">
                        <h5>Fecha de Mantenimiento</h5>
                        <input type="date" class="form-control activation" name="fecha_mtto" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <h5>Equipo</h5>
                        <select class="form-control activation" name="id_equipo" disabled="true" onChange="ver_kilometraje();">
                            <option value="0" >Elija una opción</option>
                            @foreach ($equipos as $item)
                                <option value="{{$item->id_equipo}}">{{$item->codigo}} - {{$item->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="kilometraje" class="col-md-3">
                        <h5>Kilometraje Actual</h5>
                        <input type="number" class="form-control activation right" name="kilometraje" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Empresa</h5>
                        <select name="id_empresa" id="id_empresa" class="form-control activation"
                            disabled="true" required>
                            <option value="">Elija una opción</option>
                            @foreach ($empresa as $emp)
                                <option value="{{$emp->id_empresa}}">{{ $emp->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h5>Area</h5>
                        <input type="hidden" class="form-control" name="id_grupo">
                        <input type="hidden" class="form-control" name="id_area">
                        <div class="input-group-okc">
                            <input type="text" class="form-control" name="nombre_area" disabled="true">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text activation" onclick="modal_area();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Observaciones</h5>
                        <textarea name="observaciones" cols="30" rows="20" class="form-control activation" disabled="true"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <fieldset class="group-importes"><legend><h6>Detalle del Mantenimiento</h6></legend>
                    <table id="detalle" class="table-group">
                        <thead>
                            {{-- <tr>
                                <td colSpan="7">
                                    <div style="width: 100%; display:flex;">
                                        <div style="width:90%;">
                                            <select class="form-control js-example-basic-single" name="id_programacion">
                                            </select>
                                        </div>
                                        <div style="width:10%;">
                                            <button type="button" class="btn btn-success boton"
                                                style="padding:5px;height:29px;font-size:12px;" 
                                                data-toggle="tooltip" data-placement="bottom" title="Agregar Mtto"
                                                onClick="agregar_mtto();">
                                                Agregar mtto
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr> --}}
                            <tr>
                                <th width="10%">Tipo de Mtto</th>
                                <th width="30%">Descripción del Mtto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Precio Total</th>
                                <th>Obs</th>
                                <th>
                                    <i class="fas fa-plus-square icon-tabla green boton activation" 
                                    data-toggle="tooltip" data-placement="bottom" disabled="true"
                                    title="Agregar Detalle" onClick="mtto_detalleModal();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</div>
@include('equipo.mttoModal')
@include('equipo.mtto_detalle')
@include('equipo.partidasModal')
@include('publico.modal_area')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
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
    <script src="{{('/js/equipo/mtto.js')}}"></script>
    <script src="{{('/js/equipo/partidasModal.js')}}"></script>
    <script src="{{('/js/equipo/mtto_detalle.js')}}"></script>
    <script src="{{('/js/equipo/mttoModal.js')}}"></script>
    <script src="{{('/js/publico/modal_area.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
@endsection