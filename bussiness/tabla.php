<?php
class clsTabla
{
	function clsTabla()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $param1, $param2 = '')
	{
		$bd = $this->objData;
		$tabla = '';
		$campos = '';
		$condicion = '';
		$orden = false;
		$limit = false;

		if ($tipo === 'BY-FIELD'){
			$tabla = 'ta_tabla';
			$campos = 'ta_idtabla, ta_campo, ta_codigo, ta_denominacion, ta_colorleyenda';
			$condicion = 'ta_campo = \''.$param1.'\'';
			$orden = 'ta_codigo';
		}
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function GetSpecificValue($field, $key, $value)
	{
		$bd = $this->objData;
		$specificValue = '';
		$tabla = 'ta_tabla';
		$condicion = ' ta_campo = \''.$key.'\' and ta_codigo = \''.$value.'\'';
		$rs = $bd->set_select($field, $tabla, $condicion);
		$countRs = count($rs);
		if ($countRs > 0)
			$specificValue = $rs[0][$field];
		return $specificValue;
	}
}
?>