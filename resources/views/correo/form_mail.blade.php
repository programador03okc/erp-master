@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')


<div class="row">
  <div class="col-md-12">
       <p>Estado: <img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> <span id="estado_email" name="estado_email" class="label label-info"></span></p>
       

    <form id="f_enviar_correo" name="f_enviar_correo" action="enviar_correo" class="formarchivo" enctype="multipart/form-data" >
     <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Crear Nuevo Correo</h3>
      </div><!-- /.box-header -->
      <div class="box-body">

        <div class="form-group">
          <input class="form-control" placeholder="Para:" id="destinatario" name="destinatario" value="raulscodes@gmail.com">
        </div>
        <div class="form-group">
          <input class="form-control" placeholder="Asunto:" id="asunto" name="asunto" value="prueba laravel envio de email con adjunto">
        </div>
        <div class="form-group">
          <textarea id="contenido_mail" name="contenido_mail" class="form-control" style="height: 200px" placeholder="escriba aquí...">Señores _____, de nuestra consideración tengo el agrado de dirigirme a usted, para saludarle cordialmente en nombre del OK COMPUTER EIRL y le solicitamos cotizar los siguientes productos de acuerdo a los términos que se adjuntan.
          &#013;&#010;RICHARD BALTAZAR DORADO BACA - Jefe de Logística 
        </textarea>
        </div>
        <div class="form-group">
          <div class="btn btn-default btn-file">
            <i class="fa fa-paperclip"></i> Adjuntar Archivo
            <input type="file" id="file" name="file" class="email_archivo">
          </div>
          <p class="help-block">Max. 20MB</p>
          <div id="texto_notificacion">

          </div>
        </div>



      </div><!-- /.box-body -->
      <div class="box-footer">
        <div class="pull-right">

          <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> ENVIAR</button>
        </div>
        <br />
      </div><!-- /.box-footer -->
  </div><!-- /. box -->

  </form>
</div><!-- /.col -->
</div><!-- /.row -->

@include('layout.footer')
@include('layout.scripts')
@include('layout.fin_html')


<script src="{{ asset('js/configuracion/correo.js')}}"></script>