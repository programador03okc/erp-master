@extends('layout.head')
@include('layout.menu_proyectos')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Cronograma de Ejecución
@endsection

@section('content')
<div class="page-main" type="cronoeje">
    <form id="form-cronoeje" type="register" form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
            <legend class="mylegend">
                <h2>Cronograma de Ejecución</h2>
                <ol class="breadcrumb" style="background-color: white;">
                    <li><label id="codigo"></label></li>
                    <li><label id="descripcion"></label></li>
                </ol>
            </legend>
            <div class="row">
                <input type="text" class="oculto" name="id_presupuesto" primary="ids">
                <input type="text" class="oculto" name="modo">
                <div class="col-md-12">
                    <div id="tab-cronoeje">
                    <ul class="nav nav-tabs" id="myTab">
                        <li class="active"><a type="#crono">Cronograma de Ejecución</a></li>
                        <li class=""><a type="#gant">Diagrama Gant</a></li>
                    </ul>
                    <div class="content-tabs">
                        <section id="crono" hidden>
                            <form id="form-crono" type="register">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h5>Mostrar cronograma en:</h5>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control group-elemento activation" name="unid_program" disabled="true">
                                            <option value="0">Elija una opción</option>
                                            @foreach ($unid_program as $unid)
                                                <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-2">
                                        <h5>Fecha de Inicio:</h5>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control activation" name="fecha_inicio_crono"/>
                                    </div> --}}
                                </div>
                                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                    id="listaPartidas" style="margin-top:10px;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th>N°</th>
                                            <th>Código</th>
                                            <th width="40%">Descripción</th>
                                            <th>Unid.</th>
                                            <th width="70">Cantidad</th>
                                            <th width="100">Rendim.</th>
                                            <th width="100">Días</th>
                                            <th width="100">Fecha Inicio</th>
                                            <th width="100">Fecha Fin</th>
                                            <th width="100">Tp.Pred.</th>
                                            <th width="100">Días Pos</th>
                                            <th width="100">Predecesora</th>
                                            <th width="70">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </form>
                        </section>
                        <section id="gant" hidden>
                            <form id="form-gant" type="register">
                                <div class="gantt_control">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <h5>Parámetros de visualización:</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" name="unid_program_gantt" onChange="changeUnidProgram();">
                                                <option value="day">Días</option>
                                                <option value="week" >Semanas</option>
                                                <option value="month" >Meses</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
	                                        <input class="form-control btn-success" type="button" value="Actualizar Gantt" onClick="reinit();">
                                        </div>
                                        <div class="col-md-2">
                                            {{-- <input type="checkbox" id="ruta_critica" value="second_checkbox"> <label>Ruta Crítica</label> --}}
                                            <input class="form-control btn-danger" type="button" value="Ruta Crítica Gantt" onClick="calculaRutaCritica();">
	                                        {{-- <button onClick="updateCriticalPath();">Show Critical Path</button> --}}
                                        </div>
                                    </div>
                                </div>
                                <div id="gantt_here" style="width:100%; height:auto; min-height: 600px;"></div>
                                <div id="gantt_here2" style='width:100%; height:40%;'></div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@include('proyectos.presupuesto.presejeModal')
@include('proyectos.presupuesto.verAcu')
@include('proyectos.presupuesto.presLeccion')
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

    <script src="{{('/js/proyectos/cronograma/cronoeje.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/verAcu.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/presejeModal.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/presLeccion.js')}}"></script>
@endsection