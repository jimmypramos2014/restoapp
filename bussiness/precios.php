<?php
class clsPrecio {
	private $objData;
	
	function clsPrecio(){
		$this->objData = new Db();
	}

	function Listar($tipo, $arrayParams){
		$bd = $this->objData;
		
		$tabla = '';
		$condicion = '';
		$groupby = false;
		$orden = false;
		$limit = false;
		$lastid = 1;
		$firstLimit = 0;

		$idcategoria = '0';
		$idsubcategoria = '0';
		$criterio = '';

		if ($tipo === 'ListPrecioProducto'){
			$campos = 'a.tm_idproducto, a.tm_codigo, a.tm_nombre as nombreProducto, a.tm_foto, b.tm_idmoneda, b.td_precio, c.tm_simbolo as simboloMoneda, a.tm_idcategoria, a.tm_idsubcategoria, d.tm_nombre as Categoria, e.tm_nombre as SubCategoria';
			$tabla = 'tm_producto a';
			$tabla .= ' inner join td_vigencia_precio b on a.tm_idproducto = b.tm_idproducto';
			$tabla .= ' inner join tm_moneda c on b.tm_idmoneda = c.tm_idmoneda';
			$tabla .= ' INNER JOIN tm_categoria as d on a.tm_idcategoria = d.tm_idcategoria ';
			$tabla .= ' INNER JOIN tm_categoria as e on a.tm_idsubcategoria = e.tm_idcategoria ';
			
			
			if (is_array($arrayParams)) {
				$criterio = $arrayParams['criterio'];
				$idcategoria = $arrayParams['idcategoria'];
				$idsubcategoria = $arrayParams['idsubcategoria'];
				$lastid = $arrayParams['lastid'];
			}

			$condicion = " a.tm_nombre like '%$criterio%' ";
			
			if ($idcategoria != '0')
				$condicion .= " and a.tm_idcategoria = $idcategoria ";
		
			if ($idsubcategoria != '0')
				$condicion .= " and a.tm_idsubcategoria = $idsubcategoria ";

			$condicion .= ' and (NOW() BETWEEN b.td_fechainicio AND b.td_fechafin)';
			$orden = 'a.tm_nombre';

			$firstLimit = 42;
			$start = ($lastid * $firstLimit) - $firstLimit;
			$limit = " $start, $firstLimit ";
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, $groupby, $limit);
		return $rs;
	}

	function GetListPricesByCurrency($idempresa, $idmoneda, $listProductos)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$campos = '*';
		$condicion = '';

		$tabla = 'td_vigencia_precio a ';
		$tabla .= 'inner join ( ';
		$tabla .= 'select vm.td_importe, m.tm_idmoneda, m.tm_simbolo, vm.tm_idempresa from td_vigencia_moneda vm  ';
		$tabla .= 'inner join tm_moneda m on vm.tm_idmoneda = m.tm_idmoneda ';
		$tabla .= 'where m.tm_idmoneda = '.$idmoneda.' and vm.tm_idempresa = '.$idempresa;
		$tabla .= ' and (NOW() BETWEEN vm.td_fechainicio AND vm.td_fechafin) ';
		$tabla .= ') x ';
		$tabla .= 'on a.tm_idempresa = x.tm_idempresa ';
	
		$campos = 'a.tm_idproducto, x.tm_idmoneda, ROUND(a.td_precio * x.td_importe, 2) as precio, x.tm_simbolo';

		$condicion = 'a.tm_idempresa = '.$idempresa;
		$condicion .= ' and a.tm_idproducto IN ('.$listProductos.')';
		$condicion .= ' and (NOW() BETWEEN a.td_fechainicio AND a.td_fechafin)';

		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
}
?>