<?php      
	header("Content-Type: application/vnd.ms-excel; charset=utf-8");
	header("Content-type: application/x-msexcel; charset=utf-8");
	header("content-disposition: attachment;filename=Cuadro Comparativo.xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style type="text/css">
		table tbody tr td{
		  mso-number-format:"\@";
		}
	</style>
</head>
<body>
	<!-- <img src="{{ asset('images/img-avatar.png') }}"> -->
	<!-- <img src="{{ asset('https://www.okcomputer.com.pe/wp-content/uploads/2014/11/LogoSlogan-Peque.png') }}"> -->
 	<?=$data?>
</body>
</html>