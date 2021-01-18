var iTableCounter=1;
var oInnerTable;

$('#requerimientosEnProceso tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        //  This row is already open - close it
       row.child.hide();
       tr.removeClass('shown');
    }
    else {
       // Open this row
    //    row.child( format(iTableCounter, id) ).show();
       format(iTableCounter, id, row);
       tr.addClass('shown');
       // try datatable stuff
       oInnerTable = $('#requerimientosEnProceso_' + iTableCounter).dataTable({
        //    data: sections, 
           autoWidth: true, 
           deferRender: true, 
           info: false, 
           lengthChange: false, 
           ordering: false, 
           paging: false, 
           scrollX: false, 
           scrollY: false, 
           searching: false, 
           columns:[ 
            //   { data:'refCount' },
            //   { data:'section.codeRange.sNumber.sectionNumber' }, 
            //   { data:'section.title' }
            ]
       });
       iTableCounter = iTableCounter + 1;
   }
});

$('#requerimientosElaborados tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableElaborado.row( tr );
    console.log($(this).data('id'));
    var id = $(this).data('id');
    console.log(id);
    
    if ( row.child.isShown() ) {
       row.child.hide();
       tr.removeClass('shown');
    }
    else {
       format(iTableCounter, id, row);
       tr.addClass('shown');
       oInnerTable = $('#requerimientosElaborados_' + iTableCounter).dataTable({
        //    data: sections, 
           autoWidth: true, 
           deferRender: true, 
           info: false, 
           lengthChange: false, 
           ordering: false, 
           paging: false, 
           scrollX: false, 
           scrollY: false, 
           searching: false, 
           columns:[ ]
       });
       iTableCounter = iTableCounter + 1;
   }
});

$('#requerimientosConfirmados tbody').on('click', 'td button.detalle', function () {
   var tr = $(this).closest('tr');
   var row = tableCompras.row( tr );
   console.log($(this).data('id'));
   var id = $(this).data('id');
   console.log(id);
   
   if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
   }
   else {
      format(iTableCounter, id, row);
      tr.addClass('shown');
      oInnerTable = $('#requerimientosConfirmados_' + iTableCounter).dataTable({
       //    data: sections, 
          autoWidth: true, 
          deferRender: true, 
          info: false, 
          lengthChange: false, 
          ordering: false, 
          paging: false, 
          scrollX: false, 
          scrollY: false, 
          searching: false, 
          columns:[ ]
      });
      iTableCounter = iTableCounter + 1;
  }
});

$('#ordenesDespacho tbody').on('click', 'td button.detalle', function () {
   var tr = $(this).closest('tr');
   var row = tableDespachos.row( tr );
   console.log($(this).data('id'));
   var id = $(this).data('id');
   console.log(id);
   
   if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
   }
   else {
      format(iTableCounter, id, row);
      tr.addClass('shown');
      oInnerTable = $('#ordenesDespacho_' + iTableCounter).dataTable({
       //    data: sections, 
          autoWidth: true, 
          deferRender: true, 
          info: false, 
          lengthChange: false, 
          ordering: false, 
          paging: false, 
          scrollX: false, 
          scrollY: false, 
          searching: false, 
          columns:[ ]
      });
      iTableCounter = iTableCounter + 1;
  }
});

$('#ordenesDespacho tbody').on('click', 'td button.detalle', function () {
   var tr = $(this).closest('tr');
   var row = tableDespachos.row( tr );
   console.log($(this).data('id'));
   var id = $(this).data('id');
   console.log(id);
   
   if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
   }
   else {
      format(iTableCounter, id, row);
      tr.addClass('shown');
      oInnerTable = $('#ordenesDespacho_' + iTableCounter).dataTable({
       //    data: sections, 
          autoWidth: true, 
          deferRender: true, 
          info: false, 
          lengthChange: false, 
          ordering: false, 
          paging: false, 
          scrollX: false, 
          scrollY: false, 
          searching: false, 
          columns:[ ]
      });
      iTableCounter = iTableCounter + 1;
  }
});

$('#gruposDespachados tbody').on('click', 'td button.detalle', function () {
   var tr = $(this).closest('tr');
   var row = tableGrupos.row( tr );
   console.log($(this).data('id'));
   var id = $(this).data('id');
   console.log(id);
   
   if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
   }
   else {
      format(iTableCounter, id, row);
      tr.addClass('shown');
      oInnerTable = $('#gruposDespachados_' + iTableCounter).dataTable({
       //    data: sections, 
          autoWidth: true, 
          deferRender: true, 
          info: false, 
          lengthChange: false, 
          ordering: false, 
          paging: false, 
          scrollX: false, 
          scrollY: false, 
          searching: false, 
          columns:[ ]
      });
      iTableCounter = iTableCounter + 1;
  }
});

$('#pendientesRetornoCargo tbody').on('click', 'td button.detalle', function () {
   var tr = $(this).closest('tr');
   var row = tableCargo.row( tr );
   console.log($(this).data('id'));
   var id = $(this).data('id');
   console.log(id);
   
   if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
   }
   else {
      format(iTableCounter, id, row);
      tr.addClass('shown');
      oInnerTable = $('#pendientesRetornoCargo_' + iTableCounter).dataTable({
       //    data: sections, 
          autoWidth: true, 
          deferRender: true, 
          info: false, 
          lengthChange: false, 
          ordering: false, 
          paging: false, 
          scrollX: false, 
          scrollY: false, 
          searching: false, 
          columns:[ ]
      });
      iTableCounter = iTableCounter + 1;
   }
});

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}