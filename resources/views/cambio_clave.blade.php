<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>OK Computer</title>
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
  <!-- iCheck -->
  {{-- <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css"> --}}

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link
    rel="stylesheet"
    href="{{ asset('template/dist/css/animate.css') }}"
    />
  <!-- Google Font -->
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
        <!-- /.login-logo -->

        <div class="row animate__animated animate__fadeIn" data-step="form1">
            <div class="col-md-12">
                <div class="login-box-body">
                    <p class="login-box-msg">INGRESE CÓDIGO DE VERIFIACCIÓN</p>

                    <form action="" method="post">
                        <div class="form-group">
                            <input type="number" class="form-control text-center validar-input" >
                        </div>
                        <div class="row">

                        <!-- /.col -->
                        <div class="col-xs-12">
                            <button type="button" class="btn btn-primary btn-block btn-flat" data-action="step-1">Siguinte</button>
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

                    <form action="../../index2.html" method="post">
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="row">

                        <!-- /.col -->
                        <div class="col-xs-12">
                            <button type="button" class="btn btn-primary btn-block btn-flat"><i class="fa fa-save"></i> Guardar</button>
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
<script>
    $(document).on('keyup','.validar-input',function () {
        var numero = parseInt($(this).val());

        if (!Number.isInteger(numero)) {
            $(this).val('');
        }

    });
    $(document).on('click','[data-action="step-1"]',function () {
        // $('[data-step="form1"]').addClass('d-none');
        $('[data-step="form1"]').removeClass('animate__fadeIn');
            $('[data-step="form1"]').addClass('animate__bounceOutLeft');
        setTimeout(function(){
            $('[data-step="form1"]').addClass('d-none');
            $('[data-step="form2"]').removeClass('d-none');
            $('[data-step="form2"]').addClass('animate__bounceInRight');
        }, 350);
    });

</script>
</body>
</html>
