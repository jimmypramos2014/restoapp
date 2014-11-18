<?php
class clsMoneda {
	private $objData;
	
	function clsMoneda(){
		$this->objData = new Db();
	}

	function Listar($tipo, $para1)
	{
		$bd = $this->objData;
		
		$tabla = 'tm_moneda';
		$campos = '*';
		$condicion = '';
		$groupby = false;
		$orden = false;
		$limit = false;
		$lastid = 1;

		$criterio = '';

		if ($tipo === 'L' || $tipo === 'ALL'){			
			if ($tipo === 'L')
				$campos = array('tm_idmoneda', 'tm_nombre', 'tm_simbolo');
			elseif ($tipo === 'ALL')
				$campos = '*';
			
			$condicion = 'Activo = 1';
			if ($param2 != '')
				$condicion .= ' tm_nombre like \'%'.$param2.'%\'';
			$orden = 'tm_nombre';
		}
		elseif ($tipo === "O"){
			$condicion = 'tm_idmoneda = '.$param1;
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
}
?>