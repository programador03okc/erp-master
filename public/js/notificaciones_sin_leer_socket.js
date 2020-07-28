

// function get_notificaciones_sin_leer(){
//     $.ajax({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url: '/notificaciones_sin_leer',
//         type: "GET",
//         success: function(result) {
//             console.log('success!!');
//         }
//     });

// }

 
// $.ajax({
//     type: 'GET',
//     url: '/socket_setting/activado',
//     success: function(response){
//         if(response.status == 200){
//             if(response.data.activado == true){
//                 notificaciones_sin_leer(response.data);
//             }
//         }
//     }
// });
// function notificaciones_sin_leer(data){
//     var socket = io(data.host);
//     // var socket = io('http://localhost:8008'); // modo dev
//     // var socket = io('http://192.168.20.2:8008'); // modo dev
//     socket.on('notificaciones_sin_leer', function(response) {
//             console.log(response);
//         let cantidad_notificaciones = Object.keys(response).length;
//         if(cantidad_notificaciones >0){
//             document.querySelector("span[id='cantidad_notificaciones']").textContent = cantidad_notificaciones;
//             var ul = document.getElementById("lista_notificaciones");
//             Object.keys(response).forEach(function(key) {
//                 var li = document.createElement("li");
//                 var a = document.createElement("a");
//                 li.appendChild(document.createTextNode(response[key].mensaje));
//                 li.innerHTML="<a href=''><p>"+response[key].mensaje+"</p> <span><i class='far fa-clock'></i> "+response[key].fecha+"</span></a>";
//                 ul.prepend(li);
//             });
//         }

//     });
// }
