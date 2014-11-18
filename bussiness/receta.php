<?php
class clsReceta
{
	function clsReceta()
	{
		$this->objData = new Db();
	}

	function Listar($tipo, $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$campos = '';
		$condicion = '';
		$orden = false;
		$groupby = false;
		$limit = false;

		$IdEmpresa = '0';
		$IdCentro = '0';
		$IdProducto = '0';

		if ($tipo == 'L') {
			if (is_array($arrayParams)){
				$IdEmpresa = $arrayParams['IdEmpresa'];
				$IdCentro = $arrayParams['IdCentro'];
				$IdProducto = $arrayParams['IdProducto'];
				$TipoMenuDia = $arrayParams['TipoMenuDia'];
			}

			$tabla = 'tm_insumo as a ';
			$tabla .= 'INNER JOIN td_receta as b ON a.tm_idinsumo = b.tm_idinsumo_orig ';
			$tabla .= 'INNER JOIN tm_unidad_medida as c ON a.tm_idunidadmedida = c.tm_idunidadmedida ';
			$campos = array(
				'b.td_idreceta', 
				'a.tm_idinsumo', 
				'b.ta_tipomenudia',
				'a.tm_idunidadmedida',
				'c.tm_abreviatura UnidadMedida',
				'a.tm_nombre', 
				'b.td_descripcion', 
				'b.td_precio', 
				'b.td_cantidad', 
				'b.td_subtotal');
			$condicion = 'b.Activo = 1 and a.Activo = 1';
			$condicion .= ' and b.tm_idempresa = '.$IdEmpresa;
			$condicion .= ' and b.tm_idcentro = '.$IdCentro;
			$condicion .= ' and b.tm_idproducto = '.$IdProducto;
			$condicion .= ' and b.ta_tipomenudia = '.$TipoMenuDia;
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function RegistrarDetalle($bulkQuery){
		$bd = $this->objData;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function DeletePrevDetReceta($IdProducto, $TipoMenuDia)
	{
		$bd = $this->objData;
		$rpta = $bd->set_update(array('Activo' => '0'), 'td_receta', 'tm_idproducto = '.$IdProducto.' and ta_tipomenudia = \''.$TipoMenuDia.'\'');
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'tm_producto', "tm_idproducto IN ($listIds)");
		return $rpta;
	}
}
?>