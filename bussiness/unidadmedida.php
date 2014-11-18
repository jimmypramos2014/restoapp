<?php
class clsUnidadMedida
{
	function clsUnidadMedida()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $param1, $param2 = '')
	{
		$bd = $this->objData;
		$tabla = "tm_unidad_medida";

		if ($tipo == "L" || $tipo == "ALL")
		{
			if ($tipo == "L")
				$campos = array("tm_idunidadmedida", "UPPER(tm_nombre) tm_nombre", "tm_abreviatura");
			else if ($tipo == "ALL")
				$campos = "*";
			
			$condicion = "tm_nombre like '$param1%'";
			$orden = " tm_nombre ";
		}
		else if ($tipo == "O")
		{			
			$campos = "*";
			$condicion = "tm_idunidadmedida = $param1";
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
}
?>