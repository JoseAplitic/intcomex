<html>
    <head>
        <title>API Intcomex</title>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
    </head>
    <body>
<?php
$peticionAjax = false;
require_once "./controladores/vistasControlador.php";

$vt = new vistasControlador();
$vistasR=$vt->obtener_vistas_controlador();

if($vistasR=="inicio"){
	require_once "./vistas/contenidos/inicio-vista.php";
}
elseif ($vistasR=="404"){}
else{
	//include "./vistas/modulos/sidebar.php";
	require_once $vistasR;
}
?>
    </body>
</html>