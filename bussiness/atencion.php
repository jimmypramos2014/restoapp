<?php
/**
* 
*/
class clsAtencion
{
	
	function clsAtencion()
	{
		$this->objData = new Db();
	}

	function ListarAtencionsMesasUnidas($tipo, $IdEmpresa, $IdCentro, $CodEstado = '')
	{
		$bd = $this->objData;
		$limit = false;

		$tabla = 'tm_atencion as a ';
		$tabla .= 'inner join td_atencion as b on a.tm_idatencion = b.tm_idatencion';
		$campos = 'a.tm_idatencion, a.tm_nroatencion, COUNT(*) as CountMesas';
		$condicion = ' b.Activo = 1 and a.ta_tipoubicacion = \'01\' ';
		$condicion .= 'and a.tm_idempresa = '.$IdEmpresa;
		$condicion .= ' and a.tm_idcentro = '.$IdCentro;
		$groupby = '1, 2';

		if ($tipo != 'ATENCION' ){
			if (($tipo === 'COCINA') || ($tipo == 'NOTIFCOCINA')){
				$condicion .= ' and a.ta_estadoatencion in ('.$CodEstado.')';
				if ($tipo == 'NOTIFCOCINA')
					$limit = ' 0, 1 ';
			}
			elseif (($tipo === 'TV') || ($tipo == 'NOTIFTV')){
				$condicion .= ' and NOT a.ta_estadoatencion IN (\'00\', \'05\')';
				if ($tipo == 'NOTIFTV')
					$limit = ' 0, 1 ';
			}
			$orden = ' 1 DESC ';
		}
		else 
			$condicion .= ' AND a.ta_estadoatencion <> \'05\'';
		$rs = $bd->set_select($campos, $tabla, $condicion, false, $groupby, $limit);
		return $rs;
	}

	function ListarPedidos()
	{
		$bd = $this->objData;
		$tabla = 'tm_atencion as a ';
		$campos = '*';
		$condicion = " a.ta_estadoatencion <> '05' ";
		$orden = 'a.tm_atencion desc';

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false);
		return $rs;
	}

	function ListarDetallePedidos($IdAtencion)
	{
		$bd = $this->objData;
		$tabla = 'td_atencion_articulo as a ';
		$tabla .= 'INNER JOIN tm_producto as b ON a.tm_idproducto = b.tm_idproducto ';
		$tabla .= 'INNER JOIN tm_moneda as c ON a.tm_idmoneda = c.tm_idmoneda';
		$tabla .= ' INNER JOIN tm_categoria as d on b.tm_idcategoria = d.tm_idcategoria ';
		$tabla .= ' INNER JOIN tm_categoria as e on b.tm_idsubcategoria = e.tm_idcategoria ';
		$tabla .= ' INNER JOIN ta_tabla as f on a.ta_estdetalle_atencion = f.ta_codigo and f.ta_campo = \'ta_estdetalle_atencion\'';
		$tabla .= ' INNER JOIN ta_tabla as g on a.ta_tipomenudia = g.ta_codigo and g.ta_campo = \'ta_tipomenudia\'';
    	
    	$campos = array(
    		'a.td_idatencion_articulo as idDetalle',
    		'a.tm_idproducto as idProducto',
    		'a.td_observacion as nombreProducto',
    		'd.tm_nombre as nombreCategoria',
    		'e.tm_nombre as nombreSubCategoria',
    		'a.tm_idmoneda as idMoneda',
    		'c.tm_simbolo as simboloMoneda',
    		'a.td_precio as precio',
    		'a.td_cantidad as cantidad',
    		'a.td_subtotal as subTotal',
    		'a.ta_tipomenudia as codTipoMenuDia',
    		'g.ta_colorleyenda as colorMenuDia',
    		'g.ta_denominacion as tipoMenuDia',
    		'a.ta_estdetalle_atencion as codEstado',
    		'f.ta_colorleyenda as colorEstado',
    		'f.ta_denominacion as Estado'
    	);

		$condicion = ' a.tm_idatencion = '.$IdAtencion;

		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function CheckStateDetails($IdAtencion)
	{
		$bd = $this->objData;
		$tabla = 'td_atencion_articulo';
		$campos = 'COUNT(td_idatencion_articulo) as countCheck';
		$condicion = 'tm_idatencion = '.$IdAtencion;
		$condicion .= ' AND ta_estdetalle_atencion in (\'00\', \'01\')';
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function Correlativo($IdAmbiente, $IdEmpresa, $IdCentro)
	{
		$bd = $this->objData;
		$campos = 'CONCAT( b.tm_codigo, LPAD(COUNT(*) + 1,5,0) ) AS Correlativo';
		$tabla = 'tm_atencion as a Inner Join tm_ambiente as b on a.tm_idambiente = b.tm_idambiente';
		$condicion = 'DATE(a.tm_fechahora) = DATE(NOW())';
		$condicion .= ' and a.tm_idambiente = '.$IdAmbiente;
		$condicion .= ' and a.tm_idempresa = '.$IdEmpresa;
		$condicion .= ' and a.tm_idcentro = '.$IdCentro;
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function GetCurrentState($IdAtencion)
	{
		$bd = $this->objData;
		$currentState = '00';
		$campos = 'ta_estadoatencion';
		$tabla = 'tm_atencion';
		$condicion = ' tm_idatencion = '.$IdAtencion;
		$rs = $bd->set_select($campos, $tabla, $condicion);
		$countRs = count($rs);
		if ($countRs > 0)
			$currentState = $rs[0]['ta_estadoatencion'];
		return $currentState;
	}

	function RegistrarMaestro($entidad)
	{
		$bd = $this->objData;
		if ($entidad['tm_idatencion'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_atencion');
		else
			$rpta = $bd->set_update($entidad, 'tm_atencion', 'tm_idatencion = '.$entidad['tm_idatencion']);
		return $rpta;
	}

	function AtencionMesaIfExist($IdAtencion)
	{
		$bd = $this->objData;
		$exist = false;
		$campos = 'td_idatencion';
		$tabla = 'td_atencion';
		$condicion = ' tm_idatencion = '.$IdAtencion.' and Activo = 1';
		$rs = $bd->set_select($campos, $tabla, $condicion, false, false, '1, 1');
		$countRs = count($rs);
		if ($countRs > 0)
			$exist = true;
		return $exist;
	}

	function RegistrarAtencionMesa($entidad)
	{
		$bd = $this->objData;
		$rpta = $bd->set_insert($entidad, 'td_atencion');
		return $rpta;
	}

	function ActualizarEstadoItem($tipo, $codEstado, $paramsId)
	{
		$bd = $this->objData;
		$count = 0;
		$rpta = array();
		$tabla = 'td_atencion_articulo as a ';
		$tabla = 'inner join ta_tabla as b on a.ta_estdetalle_atencion = b.ta_codigo and b.ta_campo = \'ta_estdetalle_atencion\'';
		
		$campos = array(
			'a.td_idatencion_articulo', 
			'b.ta_estdetalle_atencion as codEstado', 
			'b.ta_colorleyenda as colorEstado', 
			'b.ta_denominacion as Estado'
		);
		
		if ($tipo == 'SELECTION')
			$condicion = 'td_idatencion_articulo IN ('.$paramsId.')';
		elseif ($tipo == 'ALL')
			$condicion = 'tm_idatencion = '.$paramsId;

		$rpta = $bd->set_update(array('ta_estdetalle_atencion' => $codEstado), 'td_atencion_articulo', $condicion);
		return $rpta;
	}

	function RegistrarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function ActualizarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->multiQuery($bulkQuery);
		return $rpta;
	}

	function RegistrarMovimiento($entidad)
	{
		$bd = $this->objData;
		$rpta = $bd->set_insert($entidad, 'td_atencion_movimiento');
		return $rpta;
	}

	function DeletePrevAtencionMesa($IdAtencion)
	{
		$bd = $this->objData;
		$rpta = $bd->set_update(array('Activo' => '0'), 'td_atencion', 'tm_idatencion IN ('.$IdAtencion.')');
		return $rpta;
	}

	function UpdateEstadoMultiple($estado, $IdAtencion)
	{
		$bd = $this->objData;
		$rpta = $bd->set_update(array('ta_estadoatencion' => $estado), 'tm_atencion', 'tm_idatencion IN ('.$IdAtencion.')');
		return $rpta;
	}

	function MultiInsertDetAtencion($IdAtencion, $idusuario)
	{
		$bd = $this->objData;
		$strSQL = 'INSERT INTO td_atencion(tm_idmesa, tm_idatencion, Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct)';
		$strSQL .= ' SELECT b.tm_idmesa, a.tm_idatencion, 1, '.$idusuario.', NOW(), '.$idusuario.', NOW() FROM tm_atencion as a';
		$strSQL .= ' INNER JOIN td_atencion as b on a.tm_idatencion = b.tm_idatencion ';
		$strSQL .= ' WHERE a.Activo = 1 AND b.tm_idatencion in ('.$IdAtencion.') ORDER BY b.td_idatencion DESC LIMIT 0, 1 ';
		$rpta = $bd->multiQuery($strSQL);
		return $rpta;
	}

	function MultiInsertMovMesas($IdAtencion, $idusuario, $estado)
	{
		$bd = $this->objData;
		$strSQL = 'INSERT INTO td_mesa_movimiento(tm_idmesa, ta_estadoatencion, Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct)';
		$strSQL .= ' SELECT  b.tm_idmesa, \''.$estado.'\', 1, '.$idusuario.', NOW(), '.$idusuario.', NOW() FROM tm_atencion as a';
		$strSQL .= ' INNER JOIN td_atencion as b on a.tm_idatencion = b.tm_idatencion ';
		$strSQL .= ' WHERE a.Activo = 1 AND b.tm_idatencion in ('.$IdAtencion.')  ORDER BY b.td_idatencion DESC LIMIT 0, 1 ';
		$rpta = $bd->multiQuery($strSQL);
		return $rpta;
	}

	function UpdateEstadoByAtencion($estado, $listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		//$rpta = $bd->set_update(array('ta_estadoatencion' => $estado), 'tm_mesa FROM tm_mesa as a inner join td_atencion as b on a.tm_idmesa = b.tm_idmesa', 'b.tm_idatencion IN ('.$listIds.') AND b.ta_estadoatencion = \'07\'');
		$rpta = $bd->ejecutar('UPDATE tm_mesa a INNER JOIN td_atencion b ON a.tm_idmesa = b.tm_idmesa SET a.ta_estadoatencion =  \''.$estado.'\' WHERE b.tm_idatencion IN ('.$listIds.') AND a.ta_estadoatencion = \'07\'');
		return $rpta;
	}
}
?>