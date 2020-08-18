
function get_notificaciones_sin_leer_interval(){
    get_notificaciones_sin_leer();
    setInterval(get_notificaciones_sin_leer, ( 60 * 3 * 1000)); // cada 3 minutos aprox

}
function get_notificaciones_sin_leer(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/logistica/notificaciones/no-leidas',
        type: "GET",
        success: function(response) {
            // console.log(response);
            let cantidad_notificaciones = response.data.length;
            if(cantidad_notificaciones >0){
                document.querySelector("span[id='cantidad_notificaciones']").textContent = cantidad_notificaciones;
                var ul = document.getElementById("lista_notificaciones");
                while(ul.firstChild) {
                    ul.removeChild(ul.firstChild);
                }
                response.data.forEach(function(item) {
                    var li = document.createElement("li");
                    var a = document.createElement("a");
                    li.appendChild(document.createTextNode(item.mensaje));
                    li.innerHTML="<a href=''><p>"+item.mensaje+"</p> <span><i class='far fa-clock'></i> "+item.fecha+"</span></a>";
                    ul.prepend(li);
                });
            }
        }
    });
}