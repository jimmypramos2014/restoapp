<?php
class clsInsumo
{
	function clsInsumo()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$campos = '';
		$condicion = '';
		$orden = false;
		$groupby = false;
		$limit = false;

		$IdEmpresa = '0';
		$IdCentro = '0';
		$IdProducto = '0';
		$IdAlmacen = '0';
		$criterio = '';

		if (is_array($arrayParams)){
			$IdEmpresa = $arrayParams['IdEmpresa'];
			$IdCentro = $arrayParams['IdCentro'];
		}

		if ($tipo == 'L') {
			if (is_array($arrayParams))
				$IdProducto = $arrayParams['IdProducto'];

			$tabla = 'tm_insumo as a ';
			$tabla .= 'INNER JOIN td_receta as b ON a.tm_idinsumo = b.tm_idinsumo_orig ';
			$campos = array(
				'b.td_idreceta', 
				'a.tm_idinsumo', 
				'a.tm_nombre', 
				'b.td_descripcion', 
				'b.td_precio', 
				'b.td_cantidad', 
				'b.td_subtotal');
			$condicion = 'b.Activo = 1 and a.Activo = 1';
			$condicion .= ' and b.tm_idempresa = '.$IdEmpresa;
			$condicion .= ' and b.tm_idcentro = '.$IdCentro;
			$condicion .= ' and b.tm_idproducto = '.$IdProducto;
		}
		elseif ($tipo === 'ALM') {
			if (is_array($arrayParams))
				$IdAlmacen = $arrayParams['IdAlmacen'];

			$tabla = ' td_almacen_insumo as a ';
			$tabla .= 'INNER JOIN tm_insumo as b on a.tm_idinsumo = b.tm_idinsumo ';
			$campos = ' b.tm_idinsumo, b.tm_nombre, a.td_stock, a.td_stockpedido, a.td_stockventa';

			$condicion = 'b.Activo = 1 and a.Activo = 1';
			$condicion .= ' and b.tm_idempresa = '.$IdEmpresa;
			$condicion .= ' and b.tm_idcentro = '.$IdCentro;
			$condicion .= ' and a.tm_idalmacen = '.$IdAlmacen;
		}
		elseif ($tipo === 'SEARCH') {
			if (is_array($arrayParams))
				$criterio = $arrayParams['criterio'];

			$tabla = 'tm_insumo';
			$campos = 'tm_idinsumo, tm_nombre';

			$condicion = 'Activo = 1';
			$condicion .= ' and tm_idempresa = '.$IdEmpresa;
			$condicion .= ' and tm_idcentro = '.$IdCentro;
			
			$orden = '2';
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rs = $bd->set_select('tm_idinsumo', 'tm_insumo', 'tm_nombre = \''.$entidad['tm_nombre'].'\'');
		$countrow = count($rs);
		if ($countrow == 0)
			$rpta = $bd->set_insert($entidad, 'tm_insumo');
		else
			$rpta = $rs[0]['tm_idinsumo'];
		return $rpta;
	}

	function RegistrarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function DeletePrevDetInsumo($IdProducto)
	{
		$bd = $this->objData;
		$rpta = $bd->set_update(array('Activo' => '0'), 'td_receta', 'tm_idproducto = '.$IdProducto);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'tm_producto', "tm_idproducto IN ($listIds)");
		return $rpta;
	}
}
?>