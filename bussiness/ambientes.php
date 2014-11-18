<?php
class clsAmbiente {
	private $objData;
	
	function clsAmbiente(){
		$this->objData = new Db();
	}

	function Listar($tipo, $param1 = '', $param2 = '', $param3 = ''){
		$bd = $this->objData;
		$tabla = 'tm_ambiente';
		$groupby = false;

		if ($tipo === 'L' || $tipo === 'ALL'){
			if ($tipo === 'L')
				$campos = array('tm_idambiente', 'tm_nombre');
			elseif ($tipo === 'ALL')
				$campos = '*';
			
			$condicion = 'Activo = 1';
			if ($param1 != '')
				$condicion .= ' and tm_idempresa = '.$param1;
			if ($param2 != '')
				$condicion .= ' and tm_idempresa = '.$param2;
			if ($param3 != '')
				$condicion .= ' and tm_nombre like \'%'.$param3.'%\'';
			$orden = 'tm_nombre';
		}
		elseif ($tipo === 'GroupAmbiente'){
			$campos = 'a.tm_idambiente, a.tm_nombre, COUNT(*) CountMesas ';
			$tabla = ' tm_mesa b inner join tm_ambiente a on b.tm_idambiente = a.tm_idambiente ';
			$condicion = 'a.Activo = 1 and b.Activo = 1';
			if ($param1 !== ''){
				$condicion .= ' and a.tm_idempresa = '.$param1;
				$condicion .= ' and b.tm_idempresa = '.$param1;
			}
			if ($param2 !== ''){
				$condicion .= ' and a.tm_idcentro = '.$param2;
				$condicion .= ' and b.tm_idcentro = '.$param2;
			}
			$groupby = '1, 2';
		}
		elseif ($tipo === 'O'){			
			$campos = '*';
			$condicion = 'tm_idambiente = '.$param1;
		}
		$rs = $bd->set_select($campos, $tabla, $condicion, false, $groupby);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idambiente'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_ambiente');
		else
			$rpta = $bd->set_update($entidad, 'tm_ambiente', 'tm_idambiente = '.$entidad['tm_idambiente']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => 0), 'tm_ambiente', 'tm_idambiente IN ('.$listIds.')');
		return $rpta;
	}

	function Delete($IdAmbiente)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => 0), 'tm_ambiente', 'tm_idambiente = '.$IdAmbiente);
		return $rpta;
	}
}
?>