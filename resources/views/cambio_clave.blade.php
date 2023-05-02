<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>OK Computer</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/adminlte/css/AdminLTE.min.css') }}">
    {{-- <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css"> --}}

    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <link rel="stylesheet" href="{{ asset('template/dist/css/animate.css') }}"/>
    <link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
    .d-none{
        display: none;
    }
  </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo animate__animated animate__fadeIn">
          <a href="../../index2.html"><b>OK</b>Computer</a>
        </div>

        <div class="row animate__animated animate__fadeIn" data-step="form1">
            <div class="col-md-12">
                <div class="login-box-body">
                    <p class="login-box-msg">IDENTIFÍQUESE</p>
                    <form method="post" data-form="form-step1" action="{{ route('buscar.codigo') }}">
                        @csrf
                        <div class="form-group">
                            <input id="" class="form-control text-center" type="text" name="usuario"  placeholder="Usuario..." required>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control text-center validar-input" name="codigo" placeholder="Código..." required>
                        </div>
                        <div class="row">

                            <!-- /.col -->
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-primary btn-block btn-flat" data-action="step-1">Siguinte</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <div class="row animate__animated d-none" data-step="form2">
            <div class="col-md-12">
                <div class="login-box-body">
                    <p class="login-box-msg">Cambiar la contraseña</p>

                    <form action="{{route('guardar.cambio.clave')}}" method="post" data-form="form-step2">
                        @csrf
                        <input type="hidden" name="id_usuario" value="">
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Password"name="clave" minlength="8" required>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Password" name="repita_clave" minlength="8" required>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="row">

                        <!-- /.col -->
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                        <!-- /.col -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- /.login-box-body -->
    </div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="{{ asset('template/adminlte/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('template/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
{{-- <script src="../../plugins/iCheck/icheck.min.js"></script> --}}
<script src="{{ asset('template/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    $(document).on('keyup','.validar-input',function () {
        var numero = parseInt($(this).val());

        if (!Number.isInteger(numero)) {
            $(this).val('');
        }

    });
    $(document).on('submit','[data-form="form-step1"]',function (e) {
        e.preventDefault();
        // $('[data-step="form1"]').addClass('d-none');
        var data = $(this).serialize();
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('[data-form="form-step1"] [name="_token"]').val()
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
                $('[data-step="form1"]').removeClass('animate__fadeIn');
                $('[data-step="form1"]').addClass('animate__bounceOutLeft');
                setTimeout(function(){
                    $('[data-step="form1"]').addClass('d-none');
                    $('[data-step="form2"]').removeClass('d-none');
                    $('[data-step="form2"]').addClass('animate__bounceInRight');
                }, 350);
                $('[data-form="form-step2"] [name="id_usuario"]').val(response.data.id_usuario);
            }else{
                Swal.fire(
                    'Información!',
                    'Identificación no valida',
                    'warning'
                )
            }

        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

    });
    $(document).on('submit','[data-form="form-step2"]',function (e) {
        e.preventDefault();
        var data = $(this).serialize();

        var clave = $('[data-form="form-step2"] [name="clave"]').val(),
            repita_clave = $('[data-form="form-step2"] [name="repita_clave"]').val(),
            // regularExpression  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%.*?&])([A-Za-z\d$@$!%*?&]|[^ ])$/;
            regularExpression = /^(?=^.{8,}$)((.)(?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
            success=false;

        if (regularExpression.test(clave)) {
            success=true;
        }else{
            Swal.fire(
                'Información!',
                'Su nueva contraseña debe tener al menos 8 caracteres alfanuméricos.',
                'warning'
            )
        }
        if (clave !== repita_clave) {
            Swal.fire(
                'Información!',
                'Su contraseña no coincide.',
                'warning'
            )
        }
        if (success) {
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('[data-form="form-step2"] [name="_token"]').val()
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
                    Swal.fire({
                        title: 'Éxito!',
                        text: "Su contraseña se cambio con éxito.",
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{route('login')}}";
                        }
                    })

                }

            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

    });

</script>
</body>
</html>
