<?php
class clsCargo {
	private $objData;
	
	function clsCargo(){
		$this->objData = new Db();
	}

	function Listar($tipo = "L", $param1 = "", $param2 = false, $param3 = false){
		$bd = $this->objData;
		$tabla = "tp_cargo";

		if ($tipo == "L" || $tipo == "ALL")
		{
			if ($tipo == "L")
				$campos = array("tp_idcargo", "tp_nombre");
			else if ($tipo == "ALL")
				$campos = "*";
			
			$condicion = "tp_nombre like '$param1%'";
			$orden = " tp_nombre ";
		}
		else if ($tipo == "O")
		{			
			$campos = "*";
			$condicion = "tp_idcargo = $param1";
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tp_idcargo'] == 0)
			$rpta = $bd->set_insert($entidad, "tp_cargo");
		else
			$rpta = $bd->set_update($entidad, "tp_cargo", "tp_idcargo = ".$entidad['tp_idcargo']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_delete("tp_cargo", "tp_idcargo IN ($listIds)");
		return $rpta;
	}
}
?>