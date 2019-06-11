<?php
	if($peticionAjax){
		require_once "../modelos/inicioModelo.php";
	}else{
		require_once "./modelos/inicioModelo.php";
	}

	class inicioControlador extends inicioModelo
	{

        /* Controladores para las funciones de categoria */
        public function verificar_categoria_controlador($nombre, $padre)
		{
			$sql=mainModel::conectar()->prepare("SELECT id FROM taxonomias WHERE nombre=:Nombre AND taxonomia = 'categoria' AND padre=:Padre");
			$sql->bindParam(":Nombre",$nombre);
			$sql->bindParam(":Padre",$padre);
			$sql->execute();
			return $sql;
        }

        public function verificar_padre_controlador($nombre)
		{
			$sql=mainModel::conectar()->prepare("SELECT id FROM taxonomias WHERE nombre=:Nombre AND taxonomia = 'categoria'");
			$sql->bindParam(":Nombre",$nombre);
			$sql->execute();
			return $sql;
        }

        public function insertar_categoria_controlador($nombre, $slug, $padre)
        {
			$sql=mainModel::conectar()->prepare("INSERT INTO taxonomias (nombre, slug, padre, taxonomia) VALUES(:Nombre, :Slug, :Padre, 'categoria')");
			$sql->bindParam(":Nombre",$nombre);
			$sql->bindParam(":Slug",$slug);
			$sql->bindParam(":Padre",$padre);
			$sql->execute();
			return $sql;
        }

        public function obtener_slug($cadena)
        {
            $slug = $cadena;
            $disponible = false;
            $numero = 0;
            $slugOficial = "";
            while($disponible == false)
            {
                $slugVerificar = $slug;
                if($numero > 0)
                {
                    $slugVerificar = $slug." ".$numero;
                }
                $slugVerificar = inicioControlador::url_slug($slugVerificar);
                $verificarSlugDisponible = inicioModelo::verificar_slug($slugVerificar);
                if($verificarSlugDisponible->rowCount()>0)
                {
                    $numero++;
                }
                else
                {
                    $slugOficial = $slugVerificar;
                    $disponible = true;
                }
            }
            return $slugOficial;
        }
        
        public function verificar_categoria_principal_controlador($nombre)
		{
			$sql=mainModel::conectar()->prepare("SELECT id FROM taxonomias WHERE nombre=:Nombre AND taxonomia = 'categoria'");
			$sql->bindParam(":Nombre",$nombre);
			$sql->execute();
			return $sql;
        }

        
        public function agregar_regla_controlador($slug)
        {
            $sql=mainModel::conectar()->prepare("SELECT id FROM taxonomias WHERE slug=:Slug");
			$sql->bindParam(":Slug",$slug);
            $sql->execute();
            if($sql->rowCount()>0)
            {
                $datos = $sql->fetch();
                $id = $datos['id'];
                $sql=mainModel::conectar()->prepare("INSERT INTO reglas (id_categoria, regla_visitantes, regla_usuarios, regla_empresas) VALUES (:Id, '0', '0', '0')");
                $sql->bindParam(":Id",$id);
                $sql->execute();
            }
        }

        /* Controladores para las funciones de marca */
        public function verificar_marca_controlador($nombre)
		{
			$sql=mainModel::conectar()->prepare("SELECT id FROM taxonomias WHERE nombre=:Nombre AND taxonomia = 'marca'");
			$sql->bindParam(":Nombre",$nombre);
			$sql->execute();
			return $sql;
        }

        public function obtener_marca_slug($cadena)
        {
            $slug = $cadena;
            $disponible = false;
            $numero = 0;
            $slugOficial = "";
            while($disponible == false)
            {
                $slugVerificar = $slug;
                if($numero > 0)
                {
                    $slugVerificar = $slug." ".$numero;
                }
                $slugVerificar = inicioControlador::url_slug($slugVerificar);
                $verificarSlugDisponible = inicioModelo::verificar_marca_slug($slugVerificar);
                if($verificarSlugDisponible->rowCount()>0)
                {
                    $numero++;
                }
                else
                {
                    $slugOficial = $slugVerificar;
                    $disponible = true;
                }
            }
            return $slugOficial;
        }

        public function insertar_marca_controlador($marca, $slug)
        {
			$sql=mainModel::conectar()->prepare("INSERT INTO taxonomias (nombre, slug, taxonomia) VALUES(:Nombre, :Slug, 'marca')");
			$sql->bindParam(":Nombre",$marca);
			$sql->bindParam(":Slug",$slug);
			$sql->execute();
			return $sql;
        }

        /* Controladores para las funciones de producto */
        public function verificar_producto_controlador($sku)
        {
			$sql=mainModel::conectar()->prepare("SELECT sku FROM productos WHERE sku = :Sku");
			$sql->bindParam(":Sku",$sku);
			$sql->execute();
			return $sql;
        }

        public function obtener_producto_slug($cadena)
        {
            $slug = $cadena;
            $disponible = false;
            $numero = 0;
            $slugOficial = "";
            while($disponible == false)
            {
                $slugVerificar = $slug;
                if($numero > 0)
                {
                    $slugVerificar = $slug." ".$numero;
                }
                $slugVerificar = inicioControlador::url_slug($slugVerificar);
                $verificarSlugDisponible = inicioModelo::verificar_producto_slug($slugVerificar);
                if($verificarSlugDisponible->rowCount()>0)
                {
                    $numero++;
                }
                else
                {
                    $slugOficial = $slugVerificar;
                    $disponible = true;
                }
            }
            return $slugOficial;
        }

        public function insertar_producto_controlador($datos)
        {
			$sql=mainModel::conectar()->prepare("INSERT INTO productos (sku, nombre, slug, descripcion, mpn, fabricante, tipo, nuevo, precio, stock, oferta fecha) VALUES(:Sku, :Nombre, :Slug, :Descripcion, :Mpn, :Fabricante, :Tipo, :Nuevo, '0', '0', 'no', :Fecha);");
			$sql->bindParam(":Sku",$datos["Sku"]);
			$sql->bindParam(":Nombre",$datos["Nombre"]);
			$sql->bindParam(":Slug",$datos["Slug"]);
			$sql->bindParam(":Descripcion",$datos["Descripcion"]);
			$sql->bindParam(":Mpn",$datos["Mpn"]);
			$sql->bindParam(":Fabricante",$datos["Fabricante"]);
			$sql->bindParam(":Tipo",$datos["Tipo"]);
			$sql->bindParam(":Nuevo",$datos["Nuevo"]);
			$sql->bindParam(":Fecha",$datos["Fecha"]);
			$sql->execute();
			return $sql;
        }

        public function actualizar_producto_controlador($datos)
        {
			$sql=mainModel::conectar()->prepare("UPDATE productos SET nombre = :Nombre, descripcion = :Descripcion, mpn = :Mpn, fabricante = :Fabricante, tipo = :Tipo, nuevo = :Nuevo, fecha = :Fecha);");
			$sql->bindParam(":Nombre",$datos["Nombre"]);
			$sql->bindParam(":Descripcion",$datos["Descripcion"]);
			$sql->bindParam(":Mpn",$datos["Mpn"]);
			$sql->bindParam(":Fabricante",$datos["Fabricante"]);
			$sql->bindParam(":Tipo",$datos["Tipo"]);
			$sql->bindParam(":Nuevo",$datos["Nuevo"]);
			$sql->bindParam(":Fecha",$datos["Fecha"]);
			$sql->execute();
			return $sql;
        }

        /* Controlador para insertar las relaciones de los productos con las categorias y marcas */
        public function insertar_relacion_controlador($sku, $id_taxonomia)
        {
            $insertar = false;
            $sql = mainModel::conectar()->prepare("SELECT sku FROM relaciones WHERE sku = :SkuVer AND id_taxonomia = :IdTaxonomiaVer;");
			$sql->bindParam(":SkuVer",$sku);
            $sql->bindParam(":IdTaxonomiaVer",$id_taxonomia);
            $sql->execute();
            if($sql->rowCount()>0)
            {
                $insertar = false;
            }
            else
            {
                $sql=mainModel::conectar()->prepare("INSERT INTO relaciones (sku, id_taxonomia) VALUES(:Sku, :IdTaxonomia);");
                $sql->bindParam(":Sku",$sku);
                $sql->bindParam(":IdTaxonomia",$id_taxonomia);
                $sql->execute();
                if($sql->rowCount()>0)
                {
                    $insertar = true;
                }
            }
			return $insertar;
        }

        /* Convertir texto a slug */
        public function url_slug($string){
            return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
        }
    }