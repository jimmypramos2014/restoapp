<?php
class clsCliente
{
	function clsCliente()
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
		$lastid = 1;

		if ($tipo === 'L'){
			$tabla = 'vstcliente';
			
			if (is_array($arrayParams)) {
				$idempresa = $arrayParams['idempresa'];
				$idcentro = $arrayParams['idcentro'];
				$criterio = $arrayParams['criterio'];
				$tipocliente = $arrayParams['tipocliente'];
			}

			$campos = array(
				'tm_codigo_ori', 
				'Descripcion', 
				'TipoDoc',
				'tm_numerodoc', 
				'tm_email', 
				'tm_telefono'
			);
			
			$condicion = 'Activo = 1';
			
			if ($idempresa != '')
				$condicion .= ' and tm_idempresa = '.$idempresa;

			if ($idcentro != '')
				$condicion .= ' and tm_idcentro = '.$idcentro;

			if ($criterio != '')
				$condicion .= ' and Descripcion LIKE \'%'.$criterio.'%\'';
			
			if ($tipocliente != '')
				$condicion .= ' and tm_iditc = \''.$tipocliente.'\'';
			$orden = 'Descripcion';
			$firstLimit = 42;
			$start = ($lastid * $firstLimit) - $firstLimit;
			$limit = " $start, $firstLimit ";
		}
		elseif ($tipo == 'VALID-NA'){
			$tabla = 'tm_cliente_natural';
			$campos = 'tm_idclientenat';
			$condicion = $arrayParams;
			$limit = '0, 1';
		}
		elseif ($tipo == 'VALID-JU'){
			$tabla = 'tm_cliente_juridica';
			$campos = 'tm_idclientejr';
			$condicion = $arrayParams;
			$limit = '0, 1';
		}
		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		
	}

	function MultiInsert($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}
}
?>