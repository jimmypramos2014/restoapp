<?php
class clsMenu {
    private $objData;
	
	function clsMenu(){
		$this->objData = new Db();
	}
    
    function Listar($tipo, $param1='', $param2 = '', $param3 = ''){
		$bd = $this->objData;
		
		$tabla = '';
		$condicion = '';
		
		if ($tipo == 'L' || $tipo == 'ALL'){
			$tabla = 'tm_menu';
			//$tabla .= "inner join tp_pais as b on a.tp_idpais = b.tp_idpais ";
			//$tabla .= "inner join tp_cargo as c on a.tp_idcargo = c.tp_idcargo ";
			
			if ($tipo == 'L'){
				//$campos = array("a.tm_idempresa, a.tm_nroruc, a.tm_nombre, a.tm_email, a.tm_representante, a.Activo", "b.tp_nombre as Pais","c.tp_nombre as CargoRep");
                $campos = array('tm_idmenu', 'tm_titulo', 'tm_idmenuref');
            }
			else if ($tipo == 'ALL')
				$campos = '*';
			
			if ($param1 != '')
				$condicion = " tm_titulo like '%$param1%'";
		}
		else if ($tipo == 'O'){
			$tabla = 'tm_menu';
			$campos = '*';
			$condicion = " tm_idmenu = $param1 ";
		}
		
		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function ListMenuPerfil($tipomenu, $idreferencia, $idperfil)
	{
		$bd = $this->objData;
		
		$tabla = '';
		$condicion = '';
		$campos = 'a.tm_idmenu, a.tm_titulo, a.tm_cabecera, a.tm_descripcion, a.tm_iconbgcolor, a.ta_tipoicon, a.tm_iconuri, a.tm_uri, a.tm_idmenuref';
		$tabla = 'tm_menu as a ';
		$tabla .= 'inner join td_perfilmenu as b on a.tm_idmenu = b.tm_idmenu ';

		$condicion = 'a.tm_idmenuref = '.$idreferencia;
		$condicion .= ' and a.ta_tipomenu = '.$tipomenu;
		$condicion .= ' and a.Activo = 1 and b.tm_idperfil = '.$idperfil;
		$orden = 'tm_orden_mnu';

		$rs = $bd->set_select($campos, $tabla, $condicion);
		return $rs;
	}

	function RegDetallePermisos($bulkQuery){
		$bd = $this->objData;
		$rpta = 0;
		$rpta = $bd->ejecutar($bulkQuery);
		return $rpta;
	}

	function GetControlsMenu($idmenu, $idperfil, $tipoopcion, $tipoconsulta = 'EDIT')
	{
		$bd = $this->objData;

		$tabla = '';
		$campos = '';
		$condicion = '';

		if ($tipoconsulta == 'EDIT'){
			$campos = 'x.tm_nombre, x.tm_idopcion, IFNULL(b.td_idperfilopcion, 0) Id';
			$tabla = 'tm_opcion as x ';
			$tabla .= 'inner join td_controlmenu  as a on x.tm_idopcion = a.tm_idcontrol ';
			$tabla .= 'left join td_perfilopcion as b on a.tm_idcontrol = b.tm_idcontrol and b.tm_idperfil = '.$idperfil;
			$condicion = "a.tm_idmenu = $idmenu and x.ta_tipoopcion = '$tipoopcion'";
		}
		elseif ($tipoconsulta == 'LIST'){
			$campos = 'x.tm_idopcion, x.ta_tipoopcion, x.tm_nombre, x.tm_icono, x.tm_uri, x.tm_tagaction';
			$tabla = 'tm_opcion as x ';
			$tabla .= 'inner join td_controlmenu  as a on x.tm_idopcion = a.tm_idcontrol ';
			$tabla .= 'inner join td_perfilopcion as b on a.tm_idcontrol = b.tm_idcontrol ';
			$condicion = "a.tm_idmenu in ($idmenu, 0) and b.tm_idperfil = $idperfil and x.ta_tipoopcion = '$tipoopcion'";
		}
		
		$orden = '';
		$rs = $bd->set_select($campos, $tabla, $condicion, $orden, false, false);
		return $rs;
	}

	function DelAccesoMenu($idperfil)
	{
		$bd = $this->objData;

		$rpta = 0;
		$rpta = $bd->set_delete('td_perfilmenu', "tm_idperfil = $idperfil");
		return $rpta;
	}

	function DelMainPermission($idperfil)
	{
		$bd = $this->objData;

		$rpta = 0;
		$rpta = $bd->set_delete('td_perfilopcion AS a INNER JOIN tm_opcion AS b ON a.tm_idcontrol = b.tm_idopcion ', "a.tm_idperfil = $idperfil and b.ta_tipoopcion = '00' ");
		return $rpta;
	}

	function DelControlPermission($idperfil, $idmenu)
	{
		$bd = $this->objData;

		$rpta = 0;
		$rpta = $bd->set_delete('td_perfilopcion AS a INNER JOIN tm_opcion AS b ON a.tm_idcontrol = b.tm_idopcion ', "a.tm_idperfil = $idperfil and a.tm_idmenu = $idmenu and b.ta_tipoopcion = '01' ");
		return $rpta;
	}
}
?>