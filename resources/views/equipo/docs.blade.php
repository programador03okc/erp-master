@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body_sin_option')
<div class="page-main" type="docs">
    <legend class="mylegend">
        <h2>Documentos del Equipo</h2>
        {{-- <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Mantenimiento" 
                onClick="mantenimiento_create();">Generar Mantenimiento</button>
            </li>
        </ol> --}}
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-striped table-condensed table-bordered"
             id="listaDocs">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Nro.Doc</th>
                        <th>Proveedor</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Importe</th>
                        <th>Adjunto</th>
                        {{-- <th>Acción</th> --}}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('equipo.mtto_programacion') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/docs.js')}}"></script>
@include('layout.fin_html')