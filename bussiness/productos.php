<?php
class clsProducto
{
	private $objData;
	
	function clsProducto()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = "";
		$condicion = "";
		$groupby = false;
		$orden = false;
		$limit = false;
		$lastid = 1;

		$idcategoria = '0';
		$idsubcategoria = '0';
		$criterio = '';

		if ($tipo === 'L' || $tipo === "ALL") {
			$tabla = 'tm_producto as a ';
			$tabla .= 'INNER JOIN tm_categoria as b on a.tm_idcategoria = b.tm_idcategoria ';
			$tabla .= 'INNER JOIN tm_categoria as c on a.tm_idsubcategoria = c.tm_idcategoria ';
			
			if (is_array($arrayParams)) {
				$criterio = $arrayParams['criterio'];
				$idcategoria = $arrayParams['idcategoria'];
				$idsubcategoria = $arrayParams['idsubcategoria'];
				$lastid = $arrayParams['lastid'];
			}

			if ($tipo === 'L' || $tipo === 'ALL')
				$campos = array('a.tm_idproducto', 'a.tm_codigo', 'a.tm_nombre', 'a.tm_foto', 'b.tm_nombre as Categoria', 'c.tm_nombre as SubCategoria');
			else if ($tipo == "ALL")
				$campos = array('a.*', 'b.tm_nombre as Categoria', 'c.tm_nombre as SubCategoria');
			
			$condicion = " a.Activo = 1 ";
			
			if ($criterio != '')
				$condicion .= " and a.tm_nombre like '%$criterio%' ";
			
			if ($idcategoria != '0')
				$condicion .= " and a.tm_idcategoria = $idcategoria ";
			
			if ($idsubcategoria != '0')
				$condicion .= " and a.tm_idsubcategoria = $idsubcategoria ";

			$orden = ' a.tm_nombre ';
			$firstLimit = 42;
			$start = ($lastid * $firstLimit) - $firstLimit;
			$limit = " $start, $firstLimit ";
		}
		else if ($tipo === 'O') {
			$tabla = 'tm_producto';
			$campos = 'tm_idproducto, tm_codigo, tm_nombre, tm_idcategoria, tm_idsubcategoria, tm_foto, td_contenido';
			$condicion = " tm_idproducto = $arrayParams ";
		}
		else if ($tipo === "VAL") {
			$tabla = 'tm_producto';
			$campos = 'tm_nombre';
			$condicion = $arrayParams;
		}
		else if ($tipo === 'VALID-VENTAS') {
			$campos = 'a.tm_idproducto';
			$tabla = 'tm_producto as a inner join td_venta as b on a.tm_idproducto = b.tm_idproducto';
			$condicion = 'a.tm_idproducto IN ('.$arrayParams.') and b.Activo = 1';
		}
		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idproducto'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_producto');
		else
			$rpta = $bd->set_update($entidad, 'tm_producto', 'tm_idproducto = '.$entidad['tm_idproducto']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'tm_producto', "tm_idproducto IN ($listIds)");
		return $rpta;
	}

	function ToogleState($iditem, $state)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => $state), 'tm_producto', "tm_idproducto = $iditem");
		return $rpta;
	}
}
?>