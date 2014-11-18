<?php
class clsVenta {
	private $objData;
	
	function clsVenta(){
		$this->objData = new Db();
	}

	function ListarVentas()
	{
		$bd = $this->objData;
		$tabla = 'tm_venta as a ';
		$campos = '*';

		$rs = $bd->set_select($campos, $tabla, $condicion, false, false);
		return $rs;
	}

	function RegistrarMaestro($entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idventa'] == 0)
			$rpta = $bd->set_insert($entidad, 'tm_venta');
		else
			$rpta = $bd->set_update($entidad, 'tm_venta', 'tm_idventa = '.$entidad['tm_idventa']);
		return $rpta;
	}

	function RegistrarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function ActualizarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->multiQuery($bulkQuery);
		return $rpta;
	}
}
?>