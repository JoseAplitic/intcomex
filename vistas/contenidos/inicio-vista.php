<?php 
	require_once "./controladores/inicioControlador.php";
	$instancia = new inicioControlador();
	
	$productosNuevos=0;
	$CategoriasNuevas=0;
	$productosActualizados=0;
	$CategoriasActualizadas=0;

	/* Parametros para el HTTP Request */
	$apiKey = "7b39a118-84ac-4245-9ae2-da0a4de4cc9f";
	$accessKey = "2992fc67-377d-42de-87bc-2d68fc5ccaf8";
	date_default_timezone_set('UTC');
	$dia=date('Y-m-d');
	$hora=date('H:i:s');
	$fecha = $dia."T".$hora.'Z';
	echo $fecha;
	$claveFirma = "$apiKey,$accessKey,$fecha";
	$signature = hash("sha256", $claveFirma);
	$url = "https://intcomex-test.apigee.net/v1/getcatalog?apiKey=$apiKey&utcTimeStamp=$fecha&signature=$signature&locale=es";
	$productos = json_decode(file_get_contents($url));
	// $categorias = array();
	// foreach ($productos as $producto)
	// {
	// 	$categoria  = get_object_vars($producto->Category);
	// 	$padre = "";
	// 	array_push($categorias, array("Nombre"=>$categoria['Description'], "Padre"=>$padre));
	// 	$padre = $categoria['Description'];
	// 	$subcategoria = $categoria['Subcategories'];
	// 	$fin = false;
	// 	while($fin == false)
	// 	{
	// 		if(count($subcategoria)>0)
	// 		{
	// 			$subcategoria = get_object_vars($subcategoria[0]);
	// 			array_push($categorias, array("Nombre"=>$subcategoria['Description'], "Padre"=>$padre));
	// 			$padre = $subcategoria['Description'];
	// 			$subcategoria = $subcategoria['Subcategories'];
	// 		}
	// 		else{$fin = true;}
	// 	}
	// }
	// $simplificacionCategorias = array();
	// foreach($categorias as $categoria)
	// {
	// 	$agregar = true;
	// 	foreach($simplificacionCategorias as $existente)
	// 	{
	// 		if($existente['Nombre'] == $categoria['Nombre'] && $existente['Padre'] == $categoria['Padre'])
	// 		{
	// 			$agregar = false;
	// 		}
	// 	}
	// 	if($agregar == true)
	// 	{
	// 		array_push($simplificacionCategorias, $categoria);
	// 	}
	// }
	echo "<hr/>";
	print_r($productos);
?>