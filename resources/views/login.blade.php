@extends('layout.base')

@section('cabecera')
Iniciar sesión
@endsection

@section('body')
    <div class="hold-transition login-page">
        <div class="login-box">
            <div class="login-header">
                <code class="text-success">Última Actualización:
                @php
                $mostRecent='';
                $lastVersion='';

                $arrDate=[];
                foreach($notasLanzamiento as $date){
                    $arrDate[] = $date->fecha_detalle_nota_lanzamiento;
                    $lastVersion=$date->version;
                }
                $max = max(array_map('strtotime', $arrDate));
                $mostRecent = date('Y-m-j H:i:s', $max);
                @endphp

                {{$mostRecent}}
                </code>
            </div>
            <br>
            <div class="login-box-body">
                <div class="login-name"><h3>SISTEMA ERP</h3></div>
                <div class="login-img">
                    <img class="img-responsive" src="{{ asset('images/logo_okc.png') }}">
                </div>
                <form id="formLogin" action="{{ route('login') }}">
                @csrf
                    <div class="form-group has-feedback">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-okc-login btn-block btn-flat">Iniciar Sesión</button>
                        </div>
                    </div>
                </form>
            </div>
            <br>
            <div class="row text-center">
                <p class="text-muted" data-toggle="modal" data-target="#myModal"><span class="badge">{{$lastVersion}}</span><br><abbr title="Ver notas de Versión">Notas de Lanzamiento</abbr></p>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/app.js')}}"></script>
@endsection