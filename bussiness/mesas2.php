<?php
class clsMesa {
	private $objData;
	
	function clsMesa(){
		$this->objData = new Db();
	}

	function Listar($tipo, $param1 = '', $param2 = '', $param3 = '', $param4 = ''){
		$bd = $this->objData;
		$tabla = '';
		$campos = '';
		$condicion = '';
		$orden = false;
		$limit = false;

		if (($tipo === 'L') || ($tipo === 'ALL')){
			$tabla = 'tm_mesa';
			if ($tipo === 'L')
				$campos = array('tm_idmesa', 'tm_codigo', 'tm_nombre', 'tm_idambiente');
			elseif ($tipo === 'ALL')
				$campos = '*';
			
			$condicion = 'Activo = 1';
			if ($param1 != '')
				$condicion .= ' and tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and tm_idcentro = '.$param2;
			$orden = 'tm_nombre';
		}
		elseif (($tipo === 'ATENCION') || ($tipo === 'TV') || ($tipo === 'COCINA') || ($tipo === 'NOTIFCOCINA') || ($tipo === 'NOTIFTV') || ($tipo === 'NOTIFATENCION')) {
			$tabla = 'tm_mesa as a ';
			$tabla .= 'INNER JOIN ta_tabla as b on a.ta_estadoatencion = b.ta_codigo and b.ta_campo = \'ta_estadoatencion\'';
			$tabla .= 'LEFT JOIN ( ';
			$tabla .= 'SELECT a.tm_idmesa, a.tm_idatencion, a.FechaAct ';
			$tabla .= 'FROM td_atencion_movimiento as a ';
			$tabla .= 'INNER JOIN tm_atencion as b on a.tm_idatencion = b.tm_idatencion ';
			$tabla .= "WHERE b.ta_estadoatencion <> '00' ";
			$tabla .= "AND b.ta_tipoubicacion = '00' ";
			$tabla .= "AND a.Activo = 1 ORDER BY a.td_idatencion DESC ) AS c ON a.tm_idmesa = c.tm_idmesa ";
			
			$campos = array(
				'IFNULL(c.tm_idatencion, 0) AS tm_idatencion', 
				'a.tm_idmesa', 
				'a.tm_codigo', 
				'a.tm_nombre', 
				'a.tm_nrocomensales', 
				'a.tm_idambiente', 
				'a.ta_estadoatencion', 
				'b.ta_colorleyenda', 
				'IFNULL(c.FechaAct, a.FechaAct) AS Fecha');
			
			$condicion = "a.Activo = 1 AND NOT a.tm_idmesa IN (SELECT a.tm_idmesa FROM td_atencion as a INNER JOIN tm_atencion as b on a.tm_idatencion = b.tm_idatencion WHERE b.ta_estadoatencion <> '00' and b.ta_tipoubicacion = '01' and a.Activo = 1 )  ";
			
			if ($param1 != '')
				$condicion .= ' and a.tm_idempresa = '.$param1;
			
			if ($param2 != '')
				$condicion .= ' and a.tm_idcentro = '.$param2;
			
			$orden = '2';

			if ($tipo != 'ATENCION' ){
				if (($tipo === 'COCINA') || ($tipo === 'NOTIFCOCINA')){
					$condicion .= ' and a.ta_estadoatencion IN ('.$param3.')';
					if ($tipo == 'NOTIFCOCINA')
						$limit = ' 0, 1 ';
				}
				elseif (($tipo === 'TV') || ($tipo === 'NOTIFTV')){
					$condicion .= ' and a.ta_estadoatencion <> \'00\'';
					if ($tipo == 'NOTIFTV')
						$limit = ' 0, 1 ';
				}
				elseif ($tipo === 'NOTIFATENCION')
					$limit = ' 0, 1 ';
				$orden = ' 1 DESC ';
			}
			else
				$condicion .= ' AND a.tm_idambiente = '.$param3;
		}
		elseif (($tipo == 'UNIDAS') || ($tipo == 'UNIDAS-COCINA')){
			$tabla = 'tm_mesa AS a ';
			$tabla .= 'INNER JOIN td_atencion AS b ON a.tm_idmesa = b.tm_idmesa ';
			$tabla .= 'INNER JOIN tm_atencion as c on b.tm_idatencion = c.tm_idatencion ';
			$tabla .= 'INNER JOIN ta_tabla as d on a.ta_estadoatencion = d.ta_codigo and d.ta_campo = \'ta_estadoatencion\'';
			$campos = array('a.tm_idmesa', 'a.tm_codigo', 'a.tm_nombre', 'a.tm_idambiente', 'b.tm_idatencion', 'a.ta_estadoatencion', 'd.ta_colorleyenda');
			$condicion = "a.Activo = 1 AND c.ta_estadoatencion <> '00' AND c.ta_tipoubicacion = '01'  ";
			if ($param1 != '')
				$condicion .= ' and a.tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and a.tm_idcentro = '.$param2;
			$orden = 'a.tm_codigo';
			if ($tipo != 'UNIDAS' ){
				if (($tipo === 'COCINA') || ($tipo === 'NOTIFCOCINA')){
					$condicion .= ' and a.ta_estadoatencion IN ('.$param3.')';
					if ($tipo == 'NOTIFCOCINA')
						$limit = ' 0, 1 ';
				}
				elseif (($tipo === 'TV') || ($tipo === 'NOTIFTV')){
					$condicion .= ' and NOT a.ta_estadoatencion <> \'00\'';
					if ($tipo == 'NOTIFTV')
						$limit = ' 0, 1 ';
				}
				elseif ($tipo === 'NOTIFATENCION')
					$limit = ' 0, 1 ';
				$orden = ' 1 DESC ';
			}
		}
		elseif ($tipo === 'M'){
			$tabla = 'tm_mesa';
			$campos = array('tm_idmesa', 'tm_codigo', 'tm_nombre', 'tm_nrocomensales');
			$condicion = ' tm_idambiente = '.$param1.' AND Activo = 1';
		}
		elseif ($tipo === 'O'){
			$tabla = 'tm_mesa';
			$campos = '*';
			$condicion = 'tm_idmesa = '.$param1;
		}
		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, $limit);
		return $rs;
	}

	function Correlativo($IdEmpresa, $IdCentro)
	{
		$bd = $this->objData;
		$campos = 'IFNULL(MAX(tm_codigo), 0) + 1 AS Correlativo';
		$tabla = 'tm_mesa';
		$condicion = ' tm_idempresa = '.$IdEmpresa;
		$condicion .= ' and tm_idcentro = '.$IdCentro;
		$condicion .= ' and Activo = 1';
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idmesa'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_mesa');
		else
			$rpta = $bd->set_update($entidad, 'tm_mesa', 'tm_idmesa = '.$entidad['tm_idmesa']);
		return $rpta;
	}

	function RegistrarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function RegistrarMovimiento(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_insert($entidad, 'td_mesa_movimiento');
		return $rpta;
	}

	function UpdateEstado($estado, $listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('ta_estadoatencion' => $estado), 'tm_mesa', 'tm_idmesa IN ('.$listIds.')');
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => 0), 'tm_mesa', 'tm_idmesa IN ('.$listIds.')');
		return $rpta;
	}
}
?>