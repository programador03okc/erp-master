@extends('layout.main')
@include('layout.menu_almacen')
@section('cabecera')
    Sin acceso
@endsection
@section('content')
<div class="page-main" type="sin_acceso">
  <div class="row">
    <div class="col-md-6">
      Su usuario no tiene acceso para acceder a este formulario
      <br>
      <button>Salir</button>
    </div>
  </div>
</div>
@endsection
