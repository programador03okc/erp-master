$(function () {
    vista_extendida();

});


function exportarCuadroCostos() {
    var id_presup = $('[name=id_presup]').val();
    var form = $(`<form action="cuadroGastosExcel" method="post" target="_blank">
        <input type="hidden" name="_token" value="${csrf_token}"/>
        <input type="hidden" name="id_presupuesto" value="${id_presup}"/>
        </form>`);
    $('body').append(form);
    form.trigger('submit');
}