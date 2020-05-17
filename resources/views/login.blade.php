<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistema ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
	<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/fonts/ionicons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">
</head>
<body>
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
                    <img src="{{ asset('images/logo_okc.png') }}">
                </div>
                <form id="formLogin" action="{{ route('login') }}">
                @csrf
                    <div class="form-group has-feedback">
                        <input type="hidden" name="role">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" onblur="cargarRol(this.value);">
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
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('template/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js')}}"></script>
    <script src="{{ asset('addons/sweetalert/sweetalert2@8.js') }}"></script>
    <script src="{{ asset('js/app.js')}}"></script>
    <script src="{{ asset('js/publico/notas_lanzamiento.js')}}"></script>
    <script>
        function cargarRol(value){
            baseUrl = 'cargar_usuarios/'+value;
            var opt ='';
            $.ajax({
                type: 'GET',
                // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    $('[name=role]').val(response.rol);
                }
            }).fail( function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    </script>


<!-- Modal -->
<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel">Notas de Lanzamiento <small>{{$lastVersion}}</small></h3>
      </div>
      <div class="modal-body">
        <div name="text_nota_lanzamiento"></div>
      </div>
    </div>
  </div>
</div>


</body>
</html>


