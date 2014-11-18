<?php
class clsCartaDia
{
	function clsCartaDia()
	{
		$this->objData = new Db();
	}

	function ListarAsignaciones($tipo, $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$campos = '';
		$condicion = '';
		$groupby = false;
		$orden = false;
		$limit = false;
		$lastid = 1;

		$IdEmpresa = '0';
		$IdCentro = '0';
		$fechaMenu = '';
		$tipoMenuDia = '00';
		$esfavorito = '0';

		$tabla = 'td_producto_menudia as x ';
		$tabla .= 'INNER JOIN tm_producto as a on x.tm_idproducto = a.tm_idproducto ';
		$tabla .= 'INNER JOIN tm_categoria as b on a.tm_idcategoria = b.tm_idcategoria ';
		$tabla .= 'INNER JOIN tm_categoria as c on a.tm_idsubcategoria = c.tm_idcategoria ';
		$tabla .= 'INNER JOIN tm_moneda as d on x.tm_idmoneda = d.tm_idmoneda ';
		$tabla .= 'INNER JOIN ta_tabla as g on x.ta_tipomenudia = g.ta_codigo and g.ta_campo = \'ta_tipomenudia\'';

		if (is_array($arrayParams)) {
			$IdEmpresa = $arrayParams['IdEmpresa'];
			$IdCentro = $arrayParams['IdCentro'];
			$criterio = trim($arrayParams['criterio']);
			$idcategoria = $arrayParams['idcategoria'];
			$idsubcategoria = $arrayParams['idsubcategoria'];
			$tipoMenuDia = trim($arrayParams['tipoMenuDia']);
			$fechaMenu = $arrayParams['fechaMenu'];
			$esfavorito = $arrayParams['esfavorito'];
			$lastid = $arrayParams['lastid'];
		}

		$campos = array(
			'x.td_idproducto_menudia',
			'x.tm_idproducto',
			'x.tm_idmoneda',
			'a.tm_idcategoria',
			'a.tm_idsubcategoria',
			'a.tm_nombre as nombreProducto',
			'd.tm_simbolo as simboloMoneda',
			'a.tm_foto',
			'x.td_fecha as fechaMenu',
			'x.ta_tipomenudia as codTipoMenuDia',
			'g.ta_denominacion as tipoMenuDia',
			'x.td_precio',
			'x.td_stockdia',
			'x.td_esfavorito',
			'b.tm_nombre as Categoria',
			'c.tm_nombre as SubCategoria'
		);

		$condicion = 'x.Activo = 1';
		
		if ($IdEmpresa != '0')
			$condicion .= ' AND x.tm_idempresa = '.$IdEmpresa;

		if ($IdCentro != '0')
			$condicion .= ' AND x.tm_idcentro = '.$IdCentro;

		if ($fechaMenu != '')
			$condicion .= ' AND DATE(x.td_fecha) = \''.$fechaMenu.'\'';

		if (strlen($tipoMenuDia) > 0)
			$condicion .= ' AND x.ta_tipomenudia = \''.$tipoMenuDia.'\'';

		if ($idcategoria != '0')
			$condicion .= ' AND a.tm_idcategoria = '.$idcategoria;

		if ($idsubcategoria != '0')
			$condicion .= ' AND a.tm_idsubcategoria = '.$idsubcategoria;

		if (strlen($criterio) > 0)
			$condicion .= ' and a.tm_nombre like \'%'.$criterio.'%\' ';

		if (strlen($esfavorito) > 0)
			$condicion .= ' and x.td_esfavorito = '.$esfavorito;

		if ($tipo == 'LISTAPRECIOS'){
			$firstLimit = 42;
			$start = ($lastid * $firstLimit) - $firstLimit;
			$limit = " $start, $firstLimit ";
			$orden = ' x.td_esfavorito DESC ';
		}
		else
			$orden = ' a.tm_nombre ';

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function ListarDiasAsignados($tipo, $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$campos = '';
		$condicion = '';
		$groupby = false;
		$orden = false;
		$limit = false;
		$lastid = 1;

		$IdEmpresa = '0';
		$IdCentro = '0';

		$Year = '2014';
		$Month = '1';

		if (is_array($arrayParams)) {
			$IdEmpresa = $arrayParams['IdEmpresa'];
			$IdCentro = $arrayParams['IdCentro'];
		}

		if ($tipo === 'DIAS') {
			if (is_array($arrayParams)) {
				$Year = $arrayParams['Year'];
				$Month = $arrayParams['Month'];
			}

			$tabla = 'td_producto_menudia';
			$campos = 'DISTINCT YEAR(td_fecha) as anho, MONTH(td_fecha) as mes, DAY(td_fecha) as dia';
			$condicion = 'Activo = 1';

			if ($IdEmpresa != '0')
				$condicion .= ' AND tm_idempresa = '.$IdEmpresa;

			if ($IdCentro != '0')
				$condicion .= ' AND tm_idcentro = '.$IdCentro;

			if ($Year != '0')
				$condicion .= ' AND YEAR(td_fecha) = '.$Year;

			if ($Month != '0')
				$condicion .= ' AND MONTH(td_fecha) = '.$Month;
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function RegistrarAsignacion($bulkQuery){
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function ActualizarAsignacion($bulkQuery)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->multiQuery($bulkQuery);
		return $rpta;
	}

	function AsignarFavorito($EstadoFavorito, $listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('td_esfavorito' => $EstadoFavorito), 'td_producto_menudia', "td_idproducto_menudia IN ($listIds)");
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'td_producto_menudia', "td_idproducto_menudia IN ($listIds)");
		return $rpta;
	}

	function MultiDeleteByProd($listIds, $fechaMenu, $tipoMenuDia)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'td_producto_menudia', 'tm_idproducto IN ('.$listIds.') AND DATE(td_fecha) = \''.$fechaMenu.'\' AND ta_tipomenudia = \''.$tipoMenuDia.'\'');
		return $rpta;
	}
}
?>