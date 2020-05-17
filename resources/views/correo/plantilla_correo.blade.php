<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>correo</title>
   <style>

   .titulo {
    color: #1e80b6;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    }

    .body{
     background-color: #f9f9f9;	
    }


    .div_contenido{
    color: #1e80b6;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    background-color: #ffffff !important;
   }

   </style>

</head> 

<body class="body">

<div class="titulo" > <h2><?= $remitente; ?> </h2></div>
<hr>
<div class=".div_contenido" > <?= $contenido;   ?></div>

<div class=".div_contenido" >
<br/> <b></b>
</div>
	
</body>
</html>