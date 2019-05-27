<?php 
	require_once "./controladores/inicioControlador.php";
	$instancia = new inicioControlador();
	
	$productosNuevos=0;
	$CategoriasNuevas=0;
	$productosActualizados=0;
	$CategoriasActualizadas=0;
	$productos = json_decode(file_get_contents("https://intcomex-prod.apigee.net/v1/getcatalog?apiKey=21B17E10-351D-40EC-9042-3AD080858584&utcTimeStamp=2015-02-26T15:06:18Z&signature=c99f870db10f4fd62706ef13afc2d6a46f1022e94b4a3c7f5096fb07d14a7296&locale=es"));
	$categorias = array();
	foreach ($productos as $producto)
	{
		$categoria  = get_object_vars($producto->Category);
		$padre = "";
		array_push($categorias, array("Nombre"=>$categoria['Description'], "Padre"=>$padre));
		$padre = $categoria['Description'];
		$subcategoria = $categoria['Subcategories'];
		$fin = false;
		while($fin == false)
		{
			if(count($subcategoria)>0)
			{
				$subcategoria = get_object_vars($subcategoria[0]);
				array_push($categorias, array("Nombre"=>$subcategoria['Description'], "Padre"=>$padre));
				$padre = $subcategoria['Description'];
				$subcategoria = $subcategoria['Subcategories'];
			}
			else{$fin = true;}
		}
	}
	$simplificacionCategorias = array();
	foreach($categorias as $categoria)
	{
		$agregar = true;
		foreach($simplificacionCategorias as $existente)
		{
			if($existente['Nombre'] == $categoria['Nombre'] && $existente['Padre'] == $categoria['Padre'])
			{
				$agregar = false;
			}
		}
		if($agregar == true)
		{
			array_push($simplificacionCategorias, $categoria);
		}
	}
	echo "<hr/>";
	print_r($productos);
?>