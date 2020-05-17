@include('layout.head')
@include('layout.menu_equipo')
@include('layout.body')
<div class="page-main" type="equipo">
    <legend class="mylegend">
        <h2>Maquinaria y Equipos</h2>
        <ol class="breadcrumb">
            <li><label id="tipo_descripcion"> </li>
            <li><label id="cat_descripcion"></li>
            <li><label id="subcat_descripcion"></li>
        </ol>
    </legend>
    <form id="form-equipo" type="register" form="formulario">
        <input class="oculto" name="id_equipo" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="row">
            <div class="col-md-3">
                <h5>Categoría</h5>
                <select class="form-control activation" onChange="elabora_descripcion('id_categoria');"
                    name="id_categoria" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($categorias as $cat)
                        <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <h5>Propietario</h5>
                <select class="form-control activation" name="propietario" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($propietarios as $prop)
                        <option value="{{$prop->id_empresa}}">{{$prop->razon_social}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Código</h5>
                <input type="text" class="form-control" name="codigo" disabled="true">
            </div>              
            <div class="col-md-3">
                <h5>Marca</h5>
                <input type="text" class="form-control activation" name="marca" onChange="elabora_descripcion('marca');" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Modelo</h5>
                <input type="text" class="form-control activation" name="modelo" onChange="elabora_descripcion('modelo');" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Placa</h5>
                <input type="text" class="form-control activation" name="placa" onChange="elabora_descripcion('placa');" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Código Tarj. de Propiedad</h5>
                <input type="text" class="form-control activation" name="cod_tarj_propiedad" disabled="true">
            </div>
            <div class="col-md-9">
                <h5>Descripción</h5>
                <input type="text" class="form-control activation" name="descripcion" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Serie</h5>
                <input type="text" class="form-control activation" name="serie" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Año de Fabricación</h5>
                <input type="text" class="form-control activation" name="anio_fabricacion" disabled="true">
            </div>
            <div class="col-md-6">
                <h5>Características Adicionales</h5>
                <textarea name="caracteristicas_adic" class="form-control activation" rows="4" cols="50"></textarea>
            </div>
        </div>
    </form>
</div>
@include('equipo.equipoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/equipo.js')}}"></script>
@include('layout.fin_html')