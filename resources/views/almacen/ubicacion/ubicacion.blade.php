@extends('layout.main')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Posiciones en Almacén
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Ubicaciones</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="ubicacion">
    <div class="row">
        <div class="col-md-12" id="tab-ubicacion">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a type="#estante">Estante</a></li>
                <li class=""><a type="#nivel">Nivel</a></li>
                <li class=""><a type="#posicion">Posición</a></li>
            </ul>
            <div class="content-tabs">
                <section id="estante" hidden>
                    {{-- <div class="page-main" type="estante"> --}}
                        <form id="form-estante" type="register">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>Almacén</h5>
                                            <select class="form-control activation js-example-basic-single" 
                                                name="id_almacen" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($almacenes as $alm)
                                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <h5>Estante</h5>
                                            <input type="hidden" name="id_estante" primary="ids">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="codigo" disabled="true">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <fieldset class="group-importes"><legend><h6>Crear Estantes</h6></legend>
                                                {{-- <h5>Crear Estantes</h5> --}}
                                                <div class="input-group">
                                                    <span class="input-group-addon"> Desde: </span>
                                                    <input type="number" name="desde" class="form-control activation" disabled="true" >
                                                    <span class="input-group-addon"> Hasta: </span>
                                                    <input type="number" name="hasta" class="form-control activation" disabled="true" >
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                        id="listaEstante">
                                        <thead>
                                            <tr>
                                                <td hidden></td>
                                                <td>Almacén</td>
                                                <td>Estante</td>
                                                <td>Estado</td>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    {{-- </div> --}}
                </section>
                <section id="nivel" hidden>
                    {{-- <div class="page-main" type="nivel"> --}}
                        <form id="form-nivel" type="register">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>Almacén</h5>
                                            <select class="form-control js-example-basic-single" name="id_almacen_nivel" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($almacenes as $alm)
                                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <h5>Estante</h5>
                                            <select class="form-control activation " name="id_estante_nivel" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($estantes as $est)
                                                    <option value="{{$est->id_estante}}">{{$est->codigo}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <fieldset class="group-importes"><legend><h6>Crear Niveles</h6></legend>
                                                <div class="input-group">
                                                    <span class="input-group-addon"> Desde: </span>
                                                    <input type="text" name="nivel_desde" class="form-control activation" disabled="true" 
                                                    style="text-transform:uppercase;" maxlength="1" onkeypress="return sololetras(event)">
                                                    <span class="input-group-addon"> Hasta: </span>
                                                    <input type="text" name="nivel_hasta" class="form-control activation" disabled="true" 
                                                    style="text-transform:uppercase;" maxlength="1" onkeypress="return sololetras(event)">
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-4">
                                            <h5>Nivel</h5>
                                            <input type="hidden" name="id_nivel" primary="ids">
                                            <input type="text" class="form-control" name="codigo_nivel" disabled="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                        id="listaNivel">
                                        <thead>
                                            <tr>
                                                <td hidden></td>
                                                <td>Almacén</td>
                                                <td>Estante</td>
                                                <td>Nivel</td>
                                                <td>Estado</td>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    {{-- </div> --}}
                </section>
                <section id="posicion" hidden>
                    <form id="form-posicion" type="register">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5>Almacén</h5>
                                            <select class="form-control js-example-basic-single" name="id_almacen_posicion" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($almacenes as $alm)
                                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Estante</h5>
                                            <select class="form-control js-example-basic-single" name="id_estante_posicion" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($estantes as $est)
                                                    <option value="{{$est->id_estante}}">{{$est->codigo}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Nivel</h5>
                                            <select class="form-control " name="id_nivel_posicion" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($niveles as $niv)
                                                    <option value="{{$niv->id_nivel}}">{{$niv->codigo}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Posicion</h5>
                                            <input type="hidden" name="id_posicion" primary="ids">
                                            <input type="text" class="form-control" name="codigo_posicion" disabled="true">
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset class="group-importes"><legend><h6>Crear Posiciones</h6></legend>
                                                <div class="input-group">
                                                    <span class="input-group-addon"> Desde: </span>
                                                    <input type="number" name="posicion_desde" class="form-control activation" disabled="true">
                                                    <span class="input-group-addon"> Hasta: </span>
                                                    <input type="number" name="posicion_hasta" class="form-control activation" disabled="true">
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-8">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                        id="listaPosicion">
                                        <thead>
                                            <tr>
                                                <td hidden></td>
                                                <td>Almacén</td>
                                                <td>Estante</td>
                                                <td>Nivel</td>
                                                <td>Posición</td>
                                                <td>Estado</td>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                </section>
            </div>
        </div>
    </div>
</div>
@include('almacen.ubicacion.almacenModal')
@include('almacen.ubicacion.estanteModal')
@include('almacen.ubicacion.nivelModal')

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

    <script src="{{ asset('js/almacen/ubicacion/ubicacion.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/estante.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/nivel.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/posicion.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/almacenModal.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/estanteModal.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/nivelModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection