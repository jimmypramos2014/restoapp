<?php
class clsAlmacen
{
	private $objData;
	
	function clsAlmacen()
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

		$IdEmpresa = '0';
		$IdCentro = '0';
		$criterio = '';

		if (($tipo === 'L') || ($tipo === 'ALM')){
			if (is_array($arrayParams)){
				$IdEmpresa = $arrayParams['IdEmpresa'];
				$IdCentro = $arrayParams['IdCentro'];
				$criterio = $arrayParams['criterio'];
			}

			if ($tipo === 'L') {
				$tabla = 'tm_almacen as a';
				$campos = 'a.tm_idalmacen, a.tm_nombre, a.tm_direccion, a.Activo';
			}
			elseif ($tipo === 'ALM'){
				$tabla = 'tm_almacen as a ';
				$tabla .= 'INNER JOIN td_almacen_insumo as b ON a.tm_idalmacen = b.tm_idalmacen ';
				$campos = 'a.tm_idalmacen, a.tm_nombre, a.Activo, a.tm_direccion, COUNT(b.tm_idinsumo) CountInsumo';
				$groupby = 'a.tm_idalmacen, a.tm_nombre, a.Activo, a.tm_direccion';
			}

			$condicion = 'a.Activo = 1';

			if ($IdEmpresa != '0')
				$condicion .= ' AND a.tm_idempresa = '.$IdEmpresa;

			if ($IdCentro != '0')
				$condicion .= ' AND a.tm_idcentro = '.$IdCentro;

			if (strlen($criterio) > 0)
				$condicion .= ' AND a.tm_nombre LIKE \'%'.$criterio.'%\'';

			$orden = '2';
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}
}
?>