<?php
class clsOrganigrama {
	private $objData;
	
	function clsOrganigrama(){
		$this->objData = new Db();
	}

	function Listar($tipo = "1", $arrayParams){
		$bd = $this->objData;
		$condicion = "";
		$orden = false;
		$limit = false;

		$idcargo = 0;
		$criterio = "";
		$lastid = 0;


		if ($tipo == 'L'){
			$tabla = 'tm_personal as a ';
			$tabla .= 'inner join tp_cargo as b on a.tp_idcargo = b.tp_idcargo ';
			
			$campos = array('a.tm_idpersonal', 
				'a.tm_codigo', 
				'CONCAT(a.tm_apellidopaterno, \' \', a.tm_apellidomaterno, \' \', a.tm_nombres) Nombres',
				'a.tm_nrodni',
				'a.tm_email',
				'a.tm_foto',
				'b.tp_nombre as Cargo');

			if (is_array($arrayParams)) {
				$criterio = $arrayParams['criterio'];
				$idcargo = $arrayParams['idcargo'];
				$lastid = $arrayParams['lastid'];
			}

			$condicion = ' a.Activo = 1 ';
			if ($criterio != '')
				$condicion .= ' and CONCAT(a.tm_apellidopaterno, \' \', a.tm_apellidomaterno, \' \', a.tm_nombres) like \'%'.$criterio.'%\'';

			if ($idcargo != "0")
				$condicion .= ' and a.tp_idcargo = '.$idcargo;
			/*if ($lastid != "0")
				$condicion .= " and a.tm_idpersonal < $lastid ";*/

			$orden = " 2 ";
			$firstLimit = 42;
			$start = ($lastid * $firstLimit) - $firstLimit;
			$limit = " $start, $firstLimit ";
			/*if ($param2 != false)
				$condicion .= " and b.tm_nombre in (".$param2.") ";
			else if ($param3 != false)
				$condicion .= " and b.tp_pais in (".$param3.") ";*/
		}
		elseif ($tipo == "O"){
			$tabla = "tm_personal";
			$campos = array(
				'tm_idpersonal',
				'tm_codigo',
				'tm_apellidopaterno',
				'tm_apellidomaterno',
				'tm_nombres',
				'tm_nrodni',
				'tm_email',
				'tm_foto',
				'tp_idcargo');
			$condicion = "tm_idpersonal = $arrayParams";
		}
		elseif ($tipo == "CORREO"){
			$tabla = "tm_personal";
			$campos = "tm_email";
			$condicion = "tm_correoenvio = 1";
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idpersonal'] == 0)
			$rpta = $bd->set_insert($entidad, "tm_personal");
		else
			$rpta = $bd->set_update($entidad, "tm_personal", "tm_idpersonal = ".$entidad['tm_idpersonal']);
		return $rpta;
	}

	function UpdateOrder($campoOrden, $condicion)
	{
		$bd = $this->objData;
		$rpta = $bd->set_update($campoOrden, "tm_personal", $condicion);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array('Activo' => '0'), 'tm_personal', 'tm_idpersonal IN ('.$listIds.')');
		return $rpta;
	}

	function UpdateMailOrigen($idpersona)
	{
		$bd = $this->objData;
		//$rpta = 0;
		/*$rptaUpAll = $bd->set_update('tm_correoenvio = 0', 'tm_personal');
		if ($rptaUpAll)*/
		$rpta = $bd->set_update('tm_correoenvio = 1', 'tm_personal', "tm_idpersonal = $idpersona");
		return $rpta;
	}
}
?>