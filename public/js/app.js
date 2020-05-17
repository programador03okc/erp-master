/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! ./login */ "./resources/js/login.js"); // require('./bootstrap');
// import React, {Component} from 'react';
// import ReactDOM from 'react-dom';
// // import { BrowserRouter as Router, Route, Redirect,Switch} from 'react-router-dom';
// // import { HashRouter as Router, Route, Redirect,Switch} from 'react-router-dom';
// // import Login from './modules/sistema/usuario/Login';
// // import Modulos from './modules/Modulos';
// // import Configuracion from './modules/sistema/configuracion/Configuracion';
// // import Proyectos from './modules/proyectos/Proyectos';
// // import Rrhh from './modules/rrhh/Rrhh';
// // import Administracion from './modules/administracion/Administracion';
// // import Contabilidad from './modules/contabilidad/Contabilidad';
// // import Logistica from './modules/logistica/Logistica';
// // import Redirection from './modules/Redirection';
// // import Almacen from './modules/almacen/Almacen';
// class App extends Component {
//   render() {
//       return ( 
//         <div>
//         <Router>
//         <div>
//          <Switch>
//           {/* <Route exact path="/login" component={Login}/> */}
//           {/* <Route  path="/modulos" component={Modulos}/> */}
//           {/* <Route  path="/configuracion" component={Configuracion}/> */}
//           {/* <Route  path="/proyectos" component={Proyectos}/> */}
//           {/* <Route  path="/rrhh" component={Rrhh}/> */}
//           {/* <Route  path="/administracion" component={Administracion}/> */}
//           {/* <Route  path="/contabilidad" component={Contabilidad}/> */}
//           {/* <Route  path="/logistica" component={Logistica}/> */}
//            {/* <Route  path="/almacen" component={Almacen}/> */}
//            {/*<Route path="/almacenes" component={ Redirection } target="/meetflo.zendesk.com/hc/en-us/articles/230425728-Privacy-Policies"*/}
//              />
//            {/* <PrivateRoute path='/configuracion' component={Configuracion} /> */}
//           {/*<PrivateRoute path='/modulos' component={Modulos} />*/}
//            {/* <Redirect from="/" to="/login" /> */}
//         </Switch>    
//         </div>
//       </Router>
//       </div>
//       );
//   }
// }
// export default App;
// //  const PrivateRoute = ({ component: Component, ...rest }) => (     
// //   <Route {...rest} render={(props) => (
// //     fakeAuth.isAuthenticated === true
// //       ? <Component {...props} />
// //       : <Redirect to='/login' />
// //   )}
// //    />
// //  );
//   if (document.getElementById('root')) {
//     ReactDOM.render(<App />, document.getElementById('root'));
//   }

/***/ }),

/***/ "./resources/js/login.js":
/*!*******************************!*\
  !*** ./resources/js/login.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  $("#formLogin").submit(function (e) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var formData = $(this).serialize();
    var action = $(this).attr('action');
    var rols = $('[name=role]').val(); // console.log('disabled');
    // document.getElementsByTagName('button')[0].setAttribute('disabled',true)

    if (rols > 0) {
      $.ajax({
        type: 'POST',
        url: action,
        data: formData,
        dataType: 'JSON',
        success: function success(response) {
          if (response.success) {
            var timerInterval;
            Swal.fire({
              type: 'success',
              title: 'Bienvenido!',
              footer: 'Redireccionando a la página principal',
              html: 'Bienvenido al Sistema.',
              timer: 3000,
              onBeforeOpen: function onBeforeOpen() {
                Swal.showLoading();
              },
              onClose: function onClose() {
                clearInterval(timerInterval);
              }
            }).then(function (result) {
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href = response.redirectto;
              }
            });
          }
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: 'No Autorizado!',
          text: jqXHR.responseJSON.message,
          imageUrl: 'images/guard_man.png',
          imageWidth: 100,
          imageHeight: 100,
          backdrop: 'rgba(255, 0, 13, 0.3)'
        });
        document.getElementsByTagName('button')[0].removeAttribute('disabled');
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      });
    } else {
      document.getElementsByTagName('button')[0].removeAttribute('disabled');
      Swal.fire({
        type: 'success',
        title: 'Error!',
        footer: 'El usuario no cuenta con rol de acceso',
        html: 'Acceso Restringido.',
        timer: 5000,
        onBeforeOpen: function onBeforeOpen() {
          Swal.showLoading();
        }
      });
    }

    e.preventDefault();
  });
});

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\xampp\htdocs\erp-master\resources\js\app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! C:\xampp\htdocs\erp-master\resources\sass\app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });