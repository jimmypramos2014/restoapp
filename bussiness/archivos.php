<?php
class clsArchivo {
	private $objData;
	
	function clsArchivo(){
		$this->objData = new Db();
	}

	public function Entidad(){
		$arrayest = array(
			'td_idfile' => 0,
			'ta_tipoorigen' => '',
			'tm_idorigen' => 0,
			'td_url' => '',
			'td_type' => '',
			'td_title' => '',
			'td_description' => '',
			'td_correodestino' => '',
			'Activo' => 0,
			'IdUsuarioReg' => 0,
			'FechaReg' => date("Y-m-d h:i:s"),
			'IdUsuarioAct' => 0,
			'FechaAct' => date("Y-m-d h:i:s")
		);
		return $arrayest;
	}

	function Listar($tipo='L', $arrayParams)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$condicion = '';
		$orden = false;
		$limit = false;

		$tipoorigen = '00';
		$idorigen = 0;
		$tipofile = 'jpg';
		$notipofile = 'jpg';
		$urlfile = '';

		if ($tipo == 'L' || $tipo == 'ALL')
		{
			$tabla = ' td_files ';

			if (is_array($arrayParams)) {
				$tipoorigen = $arrayParams['tipoorigen'];
				$idorigen = $arrayParams['idorigen'];
				$tipofile = $arrayParams['tipofile'];
				$notipofile = $arrayParams['notipofile'];
				$urlfile = $arrayParams['urlfile'];
			}

			if ($tipo == 'L')
				$campos = array('td_idfile', 'td_url', 'tm_idorigen', 'td_type', 'td_title', 'td_description', 'Activo');
			else if ($tipo == 'ALL')
				$campos = '*';
			
			$condicion = " td_url like '%$urlfile%' ";
			$condicion .= " and ta_tipoorigen = '$tipoorigen'";
			

			if ($tipoorigen != '04'){
				$condicion .= " and tm_idorigen = $idorigen";
				if ($idorigen == '0')
					$condicion .= ' and Activo = 0 ';
			}
			else {
				if ($idorigen != '0')
					$condicion .= " and tm_idorigen = $idorigen";
			}
			if ($tipofile != '')
				$condicion .= " and td_type in ($tipofile)";

			if ($notipofile != '')
				$condicion .= " and not td_type in ($notipofile)";

			$orden = ' FechaReg ASC ';
		}
		else if ($tipo == "M"){
			$tabla = 'td_files';
			$campos = array('td_idfile', 'td_title', 'td_description', 'td_url', 'td_type');
			$condicion = " td_idfile = $arrayParams ";	
		}
		else if ($tipo == "O")
		{
			$tabla = 'td_files';
			$campos = '*';
			$condicion = " td_idfile = $arrayParams ";
		}

		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['td_idfile'] == 0)
			$rpta = $bd->set_insert($entidad, 'td_files');
		else
			$rpta = $bd->set_update($entidad, 'td_files', 'td_idfile = '.$entidad['td_idfile']);
		return $rpta;
	}

	function MultiUpdateState($tipoorigen, $idorigen, $idusuario){
		$bd = $this->objData;
		$parametros = array('Activo' => '1', 'tm_idorigen' => $idorigen);
		$rpta = $bd->set_update($parametros, 'td_files', " ta_tipoorigen = '$tipoorigen' and tm_idorigen = 0 and IdUsuarioAct = $idusuario and Activo = 0 ");
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_delete('td_files', "td_idfile IN ($listIds)");
		return $rpta;
	}
}
?>