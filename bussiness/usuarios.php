<?php
class clsUsuario {
	private $objData;
	
	function clsUsuario(){
		$this->objData = new Db();
	}
	
	function checkUsername($username)
	{
		$bd = $this->objData;
		$condicion = "tm_login='".$username."'";
		$tabla = 'tm_usuario';
		$campos = 'tm_idusuario';
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}
	
	function loginUsuario($username, $password){
		$bd = $this->objData;
		$condicion = "tm_login='".$username."' and tm_clave='".$password."'";
		$tabla = "tm_usuario";
		$campos = array(
			"tm_idusuario", 
			"tm_codigo", 
			"tm_login", 
			"tm_apellidopaterno", 
			"tm_apellidomaterno", 
			"tm_nombres", 
			"tm_foto",
			"tm_idperfil",
			"tm_email");
		$fila_rs = $bd->set_select($campos, $tabla, $condicion);
		$arrayest = array(
			'idusuario' => 0,
			'codigo' => "",
			'login' => "",
			'nombres' => "",
			'foto' => "",
			'idperfil' => 0,
			'correo' => ""
		);
		$arrayest['idusuario'] = $fila_rs[0]['tm_idusuario'];
		$arrayest['codigo'] = $fila_rs[0]['tm_codigo'];
		$arrayest['login'] = $fila_rs[0]['tm_login'];
		$arrayest['nombres'] = $fila_rs[0]['tm_apellidopaterno'].' '.$fila_rs[0]['tm_apellidomaterno'].' '.$fila_rs[0]['tm_nombres'];
		$arrayest['idperfil'] = $fila_rs[0]['tm_idperfil'];
		$arrayest['foto'] = $fila_rs[0]['tm_foto'];
		$arrayest['correo'] = $fila_rs[0]['tm_email'];
		return $arrayest;
	}
	
	function Listar($tipo = "1", $arrayParams){
		$bd = $this->objData;
		$tabla = "tm_usuario as a ";
		$tabla .= "inner join tm_perfil as b on a.tm_idperfil = b.tm_idperfil ";
		$tabla .= "inner join tp_pais as c on a.tp_idpais = c.tp_idpais ";
		$campos = array("a.*", 
			"b.tm_nombre as NomPerfil",
			"c.tp_nombre as NomPais");
		$condicion = "";
		$criterio = "";
		$lastid = 0;
		$orden = false;
		$limit = false;


		if ($tipo == "1"){
			if (is_array($arrayParams)) {
				$criterio = $arrayParams['criterio'];
				$lastid = $arrayParams['lastid'];
			}

			$condicion = "a.tm_login like '$criterio%'";

			/*if ($lastid != "0")
				$condicion .= " and a.tm_idusuario < $lastid ";
*/
			$orden = " a.tm_login ";
			if ($lastid != '0'){
				$firstLimit = 200;
				$start = ($lastid * $firstLimit) - $firstLimit;
				$limit = " $start, $firstLimit ";
			}
			/*if ($param2 != false)
				$condicion .= " and b.tm_nombre in (".$param2.") ";
			else if ($param3 != false)
				$condicion .= " and b.tp_pais in (".$param3.") ";*/
		}
		elseif ($tipo == "2")
			$condicion = "a.tm_idusuario = $arrayParams";
		elseif ($tipo == "3")
			$condicion = "b.tm_nombre like '%$arrayParams%'";
		else if ($tipo == "VAL"){
			$tabla = "tm_usuario";
			$campos = "tm_login";
			$condicion = $arrayParams;
		}
		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, $limit);
		return $rs;
	}

	function Registrar(array $entidad)
	{
		$bd = $this->objData;
		$rpta = 0;
		if ($entidad['tm_idusuario'] == 0){
			$rptaUser = $bd->set_insert($entidad, "tm_usuario");
			if ($rptaUser > 0)
				$rpta = $this->RegisterUserPerfil($rptaUser, $entidad['tm_idperfil']);
		}
		else
			$rpta = $bd->set_update($entidad, "tm_usuario", "tm_idusuario = ".$entidad['tm_idusuario']);
		return $rpta;
	}

	function MultiDelete($listIds)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_delete("tm_usuario", "tm_idusuario IN ($listIds)");
		return $rpta;
	}

	function ToogleState($iditem, $state)
	{
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->set_update(array("Activo" => $state), "tm_usuario", "tm_idusuario = ".$iditem);
		return $rpta;
	}

	public function RegisterUserPerfil($idusuario, $idperfil)
	{
		$bd = $this->objData;
		$rpta = 0;
		$entidadPerfilUsuario = array(
			'tm_idperfil' => $idperfil, 
			'tm_idusuario' => $idusuario,
			'IdUsuarioReg' => 1,
			'FechaReg' => date("Y-m-d h:i:s"),
			'IdUsuarioAct' => 1,
			'FechaAct' => date("Y-m-d h:i:s")
		);
		$rpta = $bd->set_insert($entidadPerfilUsuario, "td_perfilusuario");
		return $rpta;
	}
}
?>