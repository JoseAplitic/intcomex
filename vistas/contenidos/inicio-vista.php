<?php 
	require_once "./controladores/inicioControlador.php";
	$instancia = new inicioControlador();
	
	$productosNuevos=0;
	$productosActualizados=0;
	$CategoriasNuevas=0;
	$MarcasNuevas=0;
	$relacionesCreadas = 0;

	/* Parametros HTTP Request API Intcomex */
	$apiKey = "7b39a118-84ac-4245-9ae2-da0a4de4cc9f";
	$accessKey = "2992fc67-377d-42de-87bc-2d68fc5ccaf8";
	date_default_timezone_set('UTC');
	$dia=date('Y-m-d');
	$hora=date('H:i:s');
	$fecha = $dia."T".$hora.'Z';
	$claveFirma = "$apiKey,$accessKey,$fecha";
	$signature = hash("sha256", $claveFirma);

	/* Cargar los datos de los productos */
	$url = "https://intcomex-test.apigee.net/v1/getCatalog?apiKey=$apiKey&utcTimeStamp=$fecha&locale=es&signature=$signature&locale=es";
	$productos = json_decode(file_get_contents($url));

	/* Recoleccion de categorias */
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
	
	/* Insertando las categorías a la base de datos local */
	foreach ($simplificacionCategorias as $categoria)
	{
		$id_padre = 0;
		if($categoria['Padre'] != "")
		{
			$padre = $instancia->verificar_padre_controlador($categoria['Padre']);
			if($padre->rowCount()>0)
			{
				$padre = $padre->fetch();
				$id_padre = $padre['id'];
			}
			else
			{
				$slug = $instancia->obtener_slug($categoria['Padre']);
				$insertar = $instancia->insertar_categoria_controlador($categoria['Padre'], $slug, 0);
				if($insertar->rowCount()>0)
				{
					$CategoriasNuevas++;
					$padre = $instancia->verificar_padre_controlador($categoria['Padre']);
					if($padre->rowCount()>0)
					{
						$padre = $padre->fetch();
						$id_padre = $padre['id'];
						$agregarRegla = $instancia->agregar_regla_controlador($slug);
					}
				}
			}
		}
		$existente = $instancia->verificar_categoria_controlador($categoria['Nombre'], $id_padre);
		if ($existente->rowCount()<1)
		{
			$slug = $instancia->obtener_slug($categoria['Nombre']);
			$insertar = $instancia->insertar_categoria_controlador($categoria['Nombre'], $slug, $id_padre);
			if($insertar->rowCount()>0)
			{
				$CategoriasNuevas++;
				$agregarRegla = $instancia->agregar_regla_controlador($slug);
			}
		}
	}

	/* Recoleccion de marcas */
	$marcas = array();
	foreach ($productos as $producto)
	{
		if($producto->Brand->Description!="")
		{
			array_push($marcas,$producto->Brand->Description);
		}
	}

	$simplificacionMarcas = array();
	foreach($marcas as $marca)
	{
		$agregar = true;
		foreach($simplificacionMarcas as $existente)
		{
			if($existente == $marca)
			{
				$agregar = false;
			}
		}
		if($agregar == true)
		{
			array_push($simplificacionMarcas, $marca);
		}
	}

	/* Insertando las marcas a la base de datos local */
	foreach ($simplificacionMarcas as $marca)
	{
		$existente = $instancia->verificar_marca_controlador($marca);
		if ($existente->rowCount()<1)
		{
			$slug = $instancia->obtener_marca_slug($marca);
			$insertar = $instancia->insertar_marca_controlador($marca, $slug);
			if($insertar->rowCount()>0)
			{
				$MarcasNuevas++;
			}
		}
	}

	/* Insertando los productos a la base de datos local */
	foreach ($productos as $producto)
	{
		$nuevo = "";
		if($producto->New){
			$nuevo = "si";
		}
		else{
			$nuevo = "no";
		}
		$verificarProducto = $instancia->verificar_producto_controlador($producto->Sku);
		if($verificarProducto->rowCount()>0)
		{
			$datosProducto = [
				"Nombre"=>$producto->Description,
				"Descripcion"=>$producto->Description,
				"Mpn"=>$producto->Mpn,
				"Fabricante"=>$producto->Manufacturer->Description,
				"Tipo"=>$producto->Type,
				"Nuevo"=>$nuevo,
				"Fecha"=>$producto->CompilationDate
			];
			$actualizarProducto = $instancia->actualizar_producto_controlador($datosProducto);
			if($actualizarProducto->rowCount()>0)
			{
				$productosActualizados++;
			}
		}
		else
		{
			$slug = $instancia->obtener_producto_slug($producto->Description);
			$datosProducto = [
				"Sku"=>$producto->Sku,
				"Nombre"=>$producto->Description,
				"Slug"=>$slug,
				"Descripcion"=>$producto->Description,
				"Mpn"=>$producto->Mpn,
				"Fabricante"=>$producto->Manufacturer->Description,
				"Tipo"=>$producto->Type,
				"Nuevo"=>$nuevo,
				"Fecha"=>$producto->CompilationDate
			];
			$insertarProducto = $instancia->insertar_producto_controlador($datosProducto);
			if($insertarProducto->rowCount()>0)
			{
				$productosNuevos++;
			}
		}
	}

	/* Insertando relaciones para categorias de los productos */
	foreach ($productos as $producto)
	{
		$id_padre = 0;
		$id_categoria=0;
		$obtener_id = $instancia->verificar_categoria_principal_controlador($producto->Category->Description);
		if($obtener_id->rowCount()>0)
		{
			$datos = $obtener_id->fetch();
			$id_categoria = $datos["id"];
			$subcategoria = $producto->Category->Subcategories;
			$fin = false;
			while($fin == false)
			{
				if(count($subcategoria)>0)
				{
					$id_padre = $id_categoria;
					$hijo = $instancia->verificar_categoria_controlador($subcategoria[0]->Description, $id_padre);
					if($hijo->rowCount()>0)
					{
						$datos = $hijo->fetch();
						$id_categoria = $datos["id"];
						$subcategoria = $subcategoria[0]->Subcategories;
					}
					else {$fin = true;}
				}
				else{$fin = true;}
			}
			$insertarRelacionCategoria = $instancia->insertar_relacion_controlador($producto->Sku, $id_categoria);
			if($insertarRelacionCategoria==true)
			{
				$relacionesCreadas++;
			}
		}
	}

	/* Insertando relaciones para marcas de los productos */
	foreach ($productos as $producto)
	{
		$obtener_id_marca = $instancia->verificar_marca_controlador($producto->Brand->Description);
		if($obtener_id_marca->rowCount()>0)
		{
			$datos = $obtener_id_marca->fetch();
			$id = $datos["id"];
			$insertarRelacionMarca = $instancia->insertar_relacion_controlador($producto->Sku, $id);
			if($insertarRelacionMarca==true)
			{
				$relacionesCreadas++;
			}
		}
	}

	// /* Cargar los datos de venta de los productos */
	// $url = "https://intcomex-test.apigee.net/v1/getcatalogsalesdata?apiKey=$apiKey&utcTimeStamp=$fecha&locale=es&signature=$signature&locale=es";
	// $datosVenta = json_decode(file_get_contents($url));

	// foreach ($datosVenta as $dato)
	// {
	// 	print_r($dato);
	// }
?>