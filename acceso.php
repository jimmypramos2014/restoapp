<?php
require('common/class.translation.php');
require("common/sesion.class.php");
require("adata/Db.class.php");
require("bussiness/usuarios.php");

$lang = (isset($_GET['lang'])) ? $_GET['lang'] : 'es';
$translate = new Translator($lang);

$rpta = 0;
$sesion = new sesion();
$usuario = new clsUsuario();
if ($_POST){
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (isset($_POST['btnLogin'])){
        $validUsuario = $usuario->loginUsuario($username, $password);
        if (strlen($validUsuario['idusuario']) > 0){
            $sesion->set("idusuario", $validUsuario['idusuario']);
            $sesion->set("codigo", $validUsuario['codigo']);
            $sesion->set("login", $validUsuario['login']);
            $sesion->set("nombres", $validUsuario['nombres']);
            $sesion->set("idperfil", $validUsuario['idperfil']);
            $sesion->set("foto", $validUsuario['foto']);
            header("location: index.php");
        }
        else
            header("location: failed-login.php");
    }
    else {
        $txtUsuario = $_POST['txtUsuario'];
        $txtContrasena = $_POST['txtContrasena'];
        $txtEmail = $_POST['txtEmail'];
        $entidadUsuario = array(  
            'tm_idusuario' => '0',
            'tm_idperfil' => '1',
            'tm_login' => $txtUsuario,
            'tm_clave' => $txtContrasena,
            'tp_idpais' => 1,
            'tm_email' => $txtEmail,
            'Activo' => 1,
            'IdUsuarioReg' => 1,
            'FechaReg' => date("Y-m-d h:i:s"),
            'IdUsuarioAct' => 1,
            'FechaAct' => date("Y-m-d h:i:s")
        );
        $rpta = $usuario->Registrar($entidadUsuario);
        if ($rpta > 0){
            $validUsuario = $usuario->loginUsuario($txtUsuario, $txtContrasena);
            if (strlen($validUsuario['idusuario']) > 0){
                $sesion->set("idusuario", $validUsuario['idusuario']);
                $sesion->set("codigo", $validUsuario['codigo']);
                $sesion->set("login", $validUsuario['login']);
                $sesion->set("nombres", $validUsuario['nombres']);
                $sesion->set("idperfil", $validUsuario['idperfil']);
                $sesion->set("foto", $validUsuario['foto']);
                header("location: index.php");
            }
            else
                header("location: failed-login.php");
        }
        else
            header("location: failed-login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">
	<title>RESTORA APP</title>
    <link rel="stylesheet" href="styles/iconFont.css">
    <link rel="stylesheet" href="styles/metro-bootstrap.css"/>
    <link rel="stylesheet" href="styles/metro-bootstrap-responsive.css"/>
    <link rel="stylesheet" href="styles/jquery-ui-1.10.1.custom.min.css"/>
    <link rel="stylesheet" href="styles/login.css"/>
    <style>
        #frmLogin .output {color:#FB3A3A;font-weight:bold;}
    </style>
</head>
<body class="metro">
    <h1 class="text-center fg-white">RESTORA</h1>
    <div id="pnlLogin">
        <form id="frmLogin" name="frmLogin" method="post" action="acceso.php">
            <div id="pnlUserPass" class="grid">
                <div class="row">
            		<div class="span6">
                        <div class="input-control text">
                            <input type="text" id="username" name="username" class="bg-darker fg-white" placeholder="<?php $translate->__('Usuario'); ?>" />
                            <button class="btn-clear fg-white bg-darker" tabindex="-1" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                         <div class="input-control password">
                            <input type="password" id="password" name="password" class="bg-darker fg-white" placeholder="<?php $translate->__('Clave'); ?>" />
                            <button class="btn-reveal fg-white bg-darker" tabindex="-1" type="button"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="pnlButtonsLogin" class="grid">
                <div class="row">
                    <div class="span6">
                        <button id="btnLogin" name="btnLogin" type="submit">
                            <h2 class="fg-white"><?php $translate->__('Ingresar'); ?></h2>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <h3 class="text-center fg-white"><?php $translate->__('Desea realizar una prueba'); ?></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <button id="btnRegister" name="btnRegister" type="button">
                            <h2 class="fg-white"><?php $translate->__('Registrese'); ?></h2>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="pnlRegister">
        <form id="frmRegistro" name="frmRegistro" method="post" action="acceso.php">
            <div class="grid">
                <div class="row">
                    <div class="span6">
                        <div class="input-control text">
                            <input id="txtUsuario" name="txtUsuario" type="text" class="bg-darker fg-white" placeholder="<?php $translate->__('Usuario'); ?>" autofocus="" value="">
                            <button class="btn-clear fg-white bg-darker" tabindex="-1" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <div class="input-control password">
                            <input id="txtContrasena" name="txtContrasena" type="password" class="bg-darker fg-white" placeholder="<?php $translate->__('Contrase&ntilde;a'); ?>" value="">
                            <button class="btn-reveal fg-white bg-darker" tabindex="-1" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <div class="input-control password">
                            <input id="txtConfirmContrasena" name="txtConfirmContrasena" type="password" class="bg-darker fg-white" placeholder="<?php $translate->__('Confirmar contrase&ntilde;a'); ?>" value="">
                            <button class="btn-reveal fg-white bg-darker" tabindex="-1" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <div class="input-control text">
                            <input id="txtEmail" name="txtEmail" type="text" class="bg-darker fg-white" placeholder="me@domain.com" value="">
                            <button class="btn-clear fg-white bg-darker" tabindex="-1" type="button">
                            </button>
                        </div>
                    </div>
                </div>
                <div id="pnlSave" class="row">
                    <div class="span6">
                        <div class="grid">
                            <div class="row">
                                <div class="span6">
                                    <button id="btnRegistro" name="btnRegistro" type="submit" class="command-button primary"><?php $translate->__('Registrese'); ?></button>
                                </div>
                                <div class="span6">
                                    <button id="btnBackLogin" name="btnBackLogin" type="button" class="command-button inverse"><?php $translate->__('No gracias'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="scripts/jquery/jquery-1.9.0.min.js"></script>
    <script src="scripts/jquery/jquery-ui-1.10.1.custom.js"></script>
    <script src="scripts/jquery.blockUI.js"></script>
    <script src="scripts/jquery/jquery.widget.min.js"></script>
    <script src="scripts/jquery/jquery.mousewheel.js"></script>
    <script src="scripts/load-metro.js"></script>
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
    <script>
        $(function () {
            choosePanel();
            
            $("#frmLogin").validate({
                rules: {
                    username: 'required',
                    password: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    username: '<?php $translate->__("Por favor ingrese un usuario valido"); ?>',
                    password: {
                        required: '<?php $translate->__("Por favor ingrese una clave"); ?>',
                        minlength: '<?php $translate->__("Tu clave debe tener al menos 5 caracteres"); ?>'
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
            $("#frmRegistro").validate({
                rules: {
                    'txtUsuario': {
                        required: true,
                        remote: "services/usuarios/check-username.php"
                    },
                    'txtContrasena': {
                        required: true,
                        minlength: 5
                    },
                    'txtConfirmContrasena':{
                        required: true,
                        equalTo: "#txtContrasena"
                    },
                    'txtEmail': {
                        required: true,
                        email: true
                    }
                },
                
                messages: {
                    'txtUsuario': {
                        required: '<?php $translate->__("Por favor ingrese un usuario valido"); ?>',
                        remote: '<?php $translate->__("El nombre de usuario ya ha sido ingresado"); ?>'
                    },
                    'txtContrasena': {
                        required: '<?php $translate->__("Por favor ingrese una clave"); ?>',
                        minlength: '<?php $translate->__("Tu clave debe tener al menos 5 caracteres"); ?>'
                    },
                    'txtConfirmContrasena': {
                        required: '<?php $translate->__("Por favor confirma la clave"); ?>',
                        equalTo: '<?php $translate->__("La clave debe ser la misma que la ingresada arriba"); ?>'
                    },
                    'txtEmail': '<?php $translate->__("Por favor ingrese un e-mail valido"); ?>'
                },
                
                submitHandler: function(form) {
                    form.submit();
                }
            });
            $('#btnRegister').on('click', function () {
                showPanelRegister();
                return false;
            });
            $('#btnBackLogin').on('click', function () {
                showPanelLogin();
                return false;
            });
        });
        function showPanelLogin () {
            $('#pnlRegister').fadeOut(500, function () {
                $('#pnlLogin').fadeIn(500);
            });
        }
        function showPanelRegister () {
            $('#pnlLogin').fadeOut(500, function () {
                $('#pnlRegister').fadeIn(500);
            });
        }
        function choosePanel () {
            var hashLink = window.location.hash;
            if (hashLink == '#login'){
                $('#pnlRegister').hide();
                showPanelLogin();
            }
            else if (hashLink == '#register'){
                $('#pnlLogin').hide();
                showPanelRegister();
            }
        }
    </script>
</body>
</html>