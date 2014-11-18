<?php
class clsSubRubro {
	private $objData;
	
	function clsSubRubro(){
		$this->objData = new Db();
	}

	public function Entidad(){
		$arrayest = array(
			'tp_idsubrubro' => 0,
			'tp_codigo' => '',
			'tp_nombre' => '',
			'Activo' => 0,
			'IdUsuarioReg' => 0,
			'FechaReg' => date("Y-m-d h:i:s"),
			'IdUsuarioAct' => 0,
			'FechaAct' => date("Y-m-d h:i:s")
		);
		return $arrayest;
	}

	function Listar($tipo = "L", $param1 = "", $param2 = false, $param3 = false){
		$bd = $this->objData;
		$tabla = "tp_subrubro";

		if ($tipo == "L" || $tipo == "ALL")
		{			
			if ($tipo == "L")
				$campos = array("tp_idsubrubro", "tp_nombre");
			else if ($tipo == "ALL")
				$campos = "*";
			
			$condicion = "tp_nombre like '$param1%'";
			$orden = " tp_nombre ";
		}
		else if ($tipo == "O")
		{			
			$campos = "*";
			$condicion = "tp_idsubrubro = $param1";
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tp_idsubrubro'] == 0)
			$rpta = $bd->set_insert($entidad, "tp_subrubro");
		else
			$rpta = $bd->set_update($entidad, "tp_subrubro", "tp_idsubrubro = ".$entidad['tp_idsubrubro']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_delete("tp_subrubro", "tp_idsubrubro IN ($listIds)");
		return $rpta;
	}
}
?>