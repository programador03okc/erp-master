@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="equi_tipo">
    <legend><h2>Tipo de Equipo</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaEquiTipo">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-equi_tipo" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input class="oculto" name="id_tipo" primary="ids">
                {{-- <div class="row">
                    <div class="col-md-6">
                        <h5>Codigo</h5>
                        <input type="text" class="form-control" readonly name="codigo">
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion">
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-6">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/equi_tipo.js')}}"></script>
@include('layout.fin_html')