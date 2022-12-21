<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Recuperar clave</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('template/fontawesome/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('template/adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('template/adminlte/css/AdminLTE.min.css') }}">
  <link
  rel="stylesheet"
  href="{{ asset('template/dist/css/animate.css') }}"
  />
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .lockscreen-image {
        border-radius: 50% ;
        position: absolute;
        left: -17px !important;
        top: -10px !important;
        background: #fff;
        padding: 7px !important;
        z-index: 10;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2/sweetalert2.min.css')}}">
</head>
<body class="hold-transition lockscreen" style="
height: 0% !important;
">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
    <a href="../../index2.html"><b>OK</b>Computer</a>
  </div>
  <!-- User name -->
  <div class="lockscreen-name">Recuperar clave</div>

  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item">
    <!-- lockscreen image -->
    <div class="lockscreen-image">
      <img src="{{ asset('images/user2-160x160.jpg') }}" alt="User Image">
    </div>
    <!-- /.lockscreen-image -->

    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" data-form="enviar-formulario" action="{{route('enviar.correo')}}" method="POST">
      <div class="input-group">
        @csrf
        {{-- <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" /> --}}
        <input type="text" class="form-control animate__animated" placeholder="Usuario..." name="usuario" required>
        <input type="email" class="form-control animate__animated" placeholder="Ejemplo@okcomputer.com.pe" name="email" required>
        <div class="input-group-btn">
            <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  <!-- /.lockscreen-item -->
  <div class="help-block text-center">
    Ingrese su correo electronico que esta afiliado a su cuenta para recuperar su cuenta.
  </div>
  <div class="text-center">
    <a href="{{route('login')}}">O iniciar sesión como un usuario diferente</a>
  </div>
  <div class="lockscreen-footer text-center">
    Copyright &copy; <b><a href="{{route('login')}}" class="text-black">OKComputer</a></b><br>
    Todos los derechos reservados
  </div>
</div>
<!-- /.center -->

<!-- jQuery 3 -->
<script src="{{ asset('template/adminlte/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('template/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    $(document).on('submit','[data-form="enviar-formulario"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('[data-form="enviar-formulario"] [name="_token"]').val()
            },
            url: $(this).attr('action'),
            data: data,
            // processData: false,
            // contentType: false,
            dataType: 'JSON',
            beforeSend: (data) => {

            }
        }).done(function(response) {
            if (response.status===200) {
                Swal.fire(
                    'Éxito!',
                    'Se envío con éxito un código a su correo',
                    'success'
                )
            }else{
                Swal.fire(
                    'Información!',
                    response.message,
                    'warning'
                )
            }

        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
</script>
</body>
</html>
