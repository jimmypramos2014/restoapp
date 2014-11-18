<?php
class clsCategoria {
	private $objData;
	
	function clsCategoria(){
		$this->objData = new Db();
	}

	function Listar($tipo, $param1 = '', $param2 = '', $param3 = ''){
		$bd = $this->objData;
		$tabla = 'tm_categoria';

		if ($tipo === 'L' || $tipo === 'ALL'){			
			if ($tipo === 'L')
				$campos = array('tm_idcategoria', 'tm_nombre');
			elseif ($tipo === 'ALL')
				$campos = '*';
			
			$condicion = 'Activo = 1';
			if ($param1 != '')
				$condicion .= ' and tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and tm_idcentro = '.$param2;
			if ($param3 != '')
				$condicion .= ' tm_nombre like \'%'.$param3.'%\'';
			$orden = 'tm_nombre';
		}
		elseif ($tipo === 'M'){
			$tabla = 'tm_categoria';
			$campos = array('tm_idcategoria', 'tm_nombre');
			$condicion = 'Activo = 1';
			if ($param1 != '')
				$condicion .= ' and tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and tm_idcentro = '.$param2;
			if ($param3 != '')
				$condicion .= ' and tm_idrefcategoria = '.$param3;
		}
		elseif ($tipo === 'REF'){
			$tabla = 'tm_categoria';
			$campos = array('tm_idcategoria', 'tm_nombre', 'tm_idrefcategoria');
			$condicion = 'Activo = 1 and tm_idrefcategoria <> 0';
			if ($param1 != '')
				$condicion .= ' and tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and tm_idcentro = '.$param2;
		}
		elseif ($tipo === "O"){
			$campos = '*';
			$condicion = 'tm_idcategoria = '.$param1;
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function Correlativo($IdEmpresa, $IdCentro)
	{
		$bd = $this->objData;
		$campos = 'COUNT(*) + 1 AS Correlativo';
		$tabla = 'tm_categoria';
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
		if ($entidad['tm_idcategoria'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_categoria');
		else
			$rpta = $bd->set_update($entidad, 'tm_categoria', 'tm_idcategoria = '.$entidad['tm_idcategoria']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => 0), 'tm_categoria', 'tm_idcategoria IN ('.$listIds.')');
		return $rpta;
	}

	function Delete($IdCategoria)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => 0), 'tm_categoria', 'tm_idcategoria = '.$IdCategoria);
		return $rpta;
	}
}
?>