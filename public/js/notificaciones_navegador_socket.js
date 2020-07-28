// function showNotificacionUsuario(data){
//     $.ajax({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
    
//         //  url: urlBase + '/like',
//         url: '/notification',
//         type: "POST",
//         data: {
//             title:'notificacion de usuario',
//             message: "Código: "+data,
//             id_area: 9,
//             id_rol: 0
//         },
//         success: function(result) {
//             console.log('success!');
//         }
//     });
    
//     }

// function get_session_actual(){
//     return new Promise(function(resolve, reject) {
//         const baseUrl = '/session-rol-aprob';
//         $.ajax({
//             type: 'GET',
//             url:baseUrl,
//             dataType: 'JSON',
//             success(response) {
//                 resolve(response) // Resolve promise and go to then()
//             },
//             error: function(err) {
//                 reject(err) // Reject the promise and go to catch()
//             }
//         });
//     });
// }
// $.ajax({
//     type: 'GET',
//     url: '/socket_setting/activado',
//     success: function(response){
//         if(response.status == 200){
//             if(response.data.activado == true){
//                 socket_setting(response.data);
//             }
//         }
//     }
// });
// function socket_setting(data){
//     var socket = io(data.host);
//     // var socket = io('http://localhost:8008'); // modo dev
//     // var socket = io('http://192.168.20.2:8008'); // modo dev
//     socket.on('notification', function(response) {
//         //  notifyMe(response);
//         let id_area_user_session_array=[];
//         get_session_actual().then(function(data) {
//             console.log(data);
//             if(data.roles.length >0){
//                 data.roles.forEach(element => {
//                     id_area_user_session_array.push(parseInt(element.id_area));
//                 });
//                 // console.log(id_area_user_session_array);
//                 // console.log(response.id_area);
//                 // console.log(id_area_user_session_array.includes(parseInt(response.id_area)));
//                 if(id_area_user_session_array.includes(parseInt(response.id_area))){
//                     notifyMe(response);
//                 }
//             }
//         }).catch(function(err) {
//             // Run this when promise was rejected via reject()
//             console.log(err)
//         })
//     });
// }
// function notifyMe(data) {
//     if (!window.Notification) {
//         console.log('El navegador no soporta notificaciones.');
//     } else {
//         // check if permission is already granted
//         if (Notification.permission === 'granted') {
//             // show notification here
//             var notify = new Notification( data.title, {
//                 body: data.message,
//                 icon: '/images/icono.ico'
//                 // icon: 'http://www.okcomputer.com.pe/wp-content/uploads/2017/02/LogoSlogan-80.png'
//             });
//         } else {
//             // request permission from user
//             Notification.requestPermission().then(function (p) {
//                 if (p === 'granted') {
//                     // show notification here
//                     var notify = new Notification(data.title, {
//                         body: data.message,
//                         icon: '/images/icono.ico'
//                     });
//                 } else {
//                     console.log('User blocked notifications.');
//                 }
//             }).catch(function (err) {
//                 console.error(err);
//             });
//         }
//     }
// }