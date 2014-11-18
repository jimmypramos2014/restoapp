<?php
class clsFormaPago
{
	function clsFormaPago()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $param1, $param2 = '')
	{
		$bd = $this->objData;
		$tabla = "tm_forma_pago";

		if ($tipo == "L" || $tipo == "ALL")
		{
			if ($tipo == "L")
				$campos = array("tm_idformapago", "UPPER(tm_nombre) tm_nombre", "CodigoSunat", "Abreviatura");
			else if ($tipo == "ALL")
				$campos = "*";
			
			$condicion = "Activo = 1 AND tm_nombre like '$param1%'";
			$orden = " tm_nombre ";
		}
		else if ($tipo == "O")
		{			
			$campos = "*";
			$condicion = "tm_idformapago = $param1";
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
}
?>