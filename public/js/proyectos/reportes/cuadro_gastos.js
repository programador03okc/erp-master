$(function () {
    vista_extendida();


    $('div[type=cuadro_gastos]').on("change", "select.handleChangeProyecto", (e) => {
        // console.log(e.currentTarget.value);
        const codigoProyecto = e.target.options[e.target.selectedIndex].getAttribute('data-codigo-proyecto');
        $("input[name='codigo_proyecto']").val(codigoProyecto);
        // const idCentroCosto = e.target.options[e.target.selectedIndex].getAttribute('data-id-centro-costo');
        // const codigoCentroCosto = e.target.options[e.target.selectedIndex].getAttribute('data-codigo-centro-costo');
        // const descripcionCentroCosto = e.target.options[e.target.selectedIndex].getAttribute('data-descripcion-centro-costo');


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

});