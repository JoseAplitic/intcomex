<?php
	if($peticionAjax){
		require_once "../core/mainModel.php";
	}else{
		require_once "./core/mainModel.php";
	}

	class inicioModelo extends mainModel
	{

		protected function verificar_slug($slug)
		{
			$sql=mainModel::conectar()->prepare("SELECT * FROM taxonomias WHERE slug = :Slug AND taxonomia = 'categoria';");
            $sql->bindParam(":Slug",$slug);
			$sql->execute();
			return $sql;
		}

		protected function verificar_marca_slug($slug)
		{
			$sql=mainModel::conectar()->prepare("SELECT * FROM taxonomias WHERE slug = :Slug AND taxonomia = 'marca';");
            $sql->bindParam(":Slug",$slug);
			$sql->execute();
			return $sql;
		}

		protected function verificar_producto_slug($slug)
		{
			$sql=mainModel::conectar()->prepare("SELECT * FROM productos WHERE slug = :Slug;");
            $sql->bindParam(":Slug",$slug);
			$sql->execute();
			return $sql;
		}
	}