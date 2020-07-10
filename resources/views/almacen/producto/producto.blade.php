@extends('layout.main')
@include('layout.menu_logistica')

@if(Auth::user()->tieneAccion(69))
    @section('option')
        @include('layout.option')
    @endsection
@elseif(Auth::user()->tieneAccion(70))
    @section('option')
        @include('layout.option_historial')
    @endsection
@endif

@section('cabecera')
    Producto
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Catálogo</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="producto">
    <!-- <legend class="mylegend">
        <h2>Producto</h2>
        
    </legend> -->
    <input type="hidden" name="id_producto" primary="ids">
    <div class="col-md-12" id="tab-producto">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#general">Datos Generales</a></li>
            <li class=""><a type="#ubicacion">Ubicaciones</a></li>
            <li class=""><a type="#serie">Control de Series</a></li>
        </ul>
        <div class="content-tabs">
            <section id="general" hidden>
                <form id="form-general" type="register">  <!--form="formulario"-->
                <input type="hidden" name="id_producto">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <img id="img" src="{{ asset('images/product-default.png')}}">
                                    </div>
                                    <div class="row">
                                        <input type="file" name="imagen" id="imagen" class="filestyle"
                                            data-buttonName="btn-primary" data-buttonText="Seleccionar imagen"
                                            data-size="sm" data-iconName="fa fa-folder-open" data-disabled="true">
                                    </div>
                                    <div class="row" style="margin-bottom: 0;">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <h5></h5>
                                                <div class="icheckbox_flat-blue">
                                                    <label style="display:flex;">
                                                        <input type="checkbox" class="flat-red" name="series" value="0">
                                                    </label>
                                                </div> Control de Series
                                            </div>
                                            <div class="form-group">
                                                <h5></h5>
                                                <div class="icheckbox_flat-blue">
                                                    <label style="display:flex;">
                                                        <input type="checkbox" class="flat-red" name="afecto_igv" value="0">
                                                    </label>
                                                </div> Afecto a I.G.V. (Gravado)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <fieldset class="group-importes" style="margin-top: 0px;"><legend><h6>Códigos Antiguos</h6></legend>
                                            <table id="antiguos" class="table-group">
                                                <tbody></tbody>
                                            </table>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <div class="row" style="margin-bottom: 0;margin-top: 0;">
                                <div class="col-md-12">
                                    <ol class="breadcrumb"  style="padding-left: 20px;padding-right: 10px;">
                                        <li><label id="codigo"></label></li>
                                        <li><label id="tipo_descripcion"></label></li>
                                        <li><label id="cat_descripcion"></label></li>
                                        <li><label id="subcat_descripcion"></label></li>
                                    </ol>
                                </div>
                                <div class="col-md-4">
                                    <h5>Categoría</h5>
                                    <select class="form-control activation js-example-basic-single" name="id_categoria" disabled="true">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($categorias as $cat)
                                            <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <h5>SubCategoría</h5>
                                    <select class="form-control activation js-example-basic-single" name="id_subcategoria" disabled="true">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($subcategorias as $subcat)
                                            <option value="{{$subcat->id_subcategoria}}">{{$subcat->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="col-md-4">
                                    <h5>Subcategoría</h5>
                                    <div class="input-group-okc">
                                        <input type="hidden" name="id_subcategoria" >
                                        <input type="text" class="form-control" aria-describedby="basic-addon2" 
                                            readonly name="subcat_descripcion" disabled="true">
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text" id="basic-addon2"
                                                onClick="subCategoriaModal();">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <h5>Clasificación</h5>
                                    <select class="form-control activation js-example-basic-single" name="id_clasif" disabled="true">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($clasificaciones as $clasif)
                                            <option value="{{$clasif->id_clasificacion}}">{{$clasif->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col-md-12">
                                    <h5>Descripción</h5>
                                    <input type="text" class="form-control activation" name="descripcion" disabled="true">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col-md-4">
                                    <h5>Código Anexo</h5>
                                    <input type="text" class="form-control activation" name="codigo_anexo" disabled="true">
                                </div>              
                                <div class="col-md-4">
                                    <h5>Part Number</h5>
                                    <input type="text" class="form-control activation" name="part_number" disabled="true">
                                </div>
                                <div class="col-sm-4">
                                    <h5>Moneda</h5>
                                    <select class="form-control group-elemento activation" name="id_moneda" disabled="true">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($monedas as $mon)
                                            <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <fieldset class="group-importes"><legend><h6>Unidades de Medida</h6></legend>
                                        <table id="unidad" class="table-group">
                                            <tbody>
                                                <tr>
                                                    <td>Unidad de Medida:</td>
                                                    <td>
                                                        <span hidden name="abr_id_unidad_medida"></span>
                                                        <select class="form-control activation " name="id_unidad_medida" 
                                                            disabled="true" onChange="unid_abrev('id_unidad_medida');">
                                                            <option value="0">Elija una opción</option>
                                                            @foreach ($unidades as $unid)
                                                                <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}} - {{$unid->abreviatura}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Cantidad x Presentación:</td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control activation" name="cant_pres" disabled="true"/>
                                                            <span class="input-group-addon" name="abr_id_unid_equi"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Unidad Equivalente:</td>
                                                    <td>
                                                        <select class="form-control activation " style="font-size:12px;" 
                                                            name="id_unid_equi" disabled="true" onChange="unid_abrev('id_unid_equi');">
                                                            <option value="0">Elija una opción</option>
                                                            @foreach ($unidades as $unid)
                                                                <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}} - {{$unid->abreviatura}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </div>
                                <div class="col-md-7">
                                    <h5>Descripción</h5>
                                    <textarea name="notas" class="form-control activation" rows="8" cols="30"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
            <section id="ubicacion" hidden>
                <form id="form-ubicacion" type="register">
                    <input type="hidden" name="id_producto">
                    <div class="row">
                        <!-- <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Almacén</h5>
                                    <input type="hidden" name="id_prod_ubi" primary="ids"/>
                                    <input type="text" class="form-control" name="alm_descripcion" disabled="true"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Posición</h5>
                                    <select class="form-control activation js-example-basic-single" 
                                    name="id_posicion" disabled="true" onChange="posicion();">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($posiciones as $pos)
                                            <option value="{{$pos->id_posicion}}">{{$pos->codigo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5>Stock</h5>
                                    <div class="input-group">
                                        <input type="number" class="form-control activation" name="stock" disabled="true"/>
                                        <span class="input-group-addon" name="abreviatura"></span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaUbicacion">
                                <thead>
                                    <tr>
                                        <td hidden></td>
                                        <td>Cod.Almacén</td>
                                        <td>Almacén</td>
                                        <td>Posición</td>
                                        <td>Stock</td>
                                        <td>Estado</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="serie" hidden>
                <form id="form-serie" type="register">
                <input type="hidden" name="id_producto">
                    <div class="row">
                        <div class="col-md-10">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaSerie">
                                <thead>
                                    <tr>
                                        <td hidden></td>
                                        <td>Almacén</td>
                                        <td>Serie</td>
                                        <td>Guía Compra</td>
                                        <td>Guía Venta</td>
                                        <td>Estado</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            {{-- <section id="merma" hidden>
                <form id="form-merma" type="register">
                <input type="hidden" name="id_producto">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <h5></h5>
                                <div class="icheckbox_flat-green">
                                    <label style="display:flex;">
                                        <input type="checkbox" class="flat-red" name="desmedro" value="0">
                                    </label>
                                </div> Desmedro
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <h5></h5>
                                <div class="icheckbox_flat-green">
                                    <label style="display:flex;">
                                        <input type="checkbox" class="flat-red" name="merma" value="0">
                                    </label>
                                </div> Merma
                            </div>
                        </div>
                    </div>
                </form>
            </section> --}}
        </div>
    </div>
</div>

@include('almacen.producto.subcategoriaModal')
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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{ asset('js/almacen/producto/producto.js')}}"></script>
    <!-- <script src="{{ asset('js/almacen/producto/subcategoriaModal.js')}}"></script> -->
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/producto_ubicacion.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/producto_serie.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
