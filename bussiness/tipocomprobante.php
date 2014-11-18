<?php
class clsTipoComprobante
{
	function clsTipoComprobante()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $param1, $param2 = '')
	{
		$bd = $this->objData;
		$tabla = "tm_tipo_comprobante";

		if ($tipo == "L" || $tipo == "ALL")
		{
			if ($tipo == "L")
				$campos = array("tm_idtipocomprobante", "UPPER(tm_nombre) tm_nombre", "CodigoSunat", "Abreviatura");
			else if ($tipo == "ALL")
				$campos = "*";
			
			$condicion = "Activo = 1 AND tm_nombre like '%$param1%'";
			$orden = " tm_nombre ";
		}
		else if ($tipo == "O")
		{			
			$campos = "*";
			$condicion = "tm_idtipocomprobante = $param1";
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
}
?>