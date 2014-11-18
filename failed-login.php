<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/html">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">

    <title>RESTORA APP</title>

    <link rel="stylesheet" type="text/css" href="styles/iconFont.css">

    <link rel="stylesheet" type="text/css" href="styles/metro-bootstrap.css"/>

    <link rel="stylesheet" type="text/css" href="styles/metro-bootstrap-responsive.css"/>

    <link rel="stylesheet" type="text/css" href="styles/jquery-ui-1.10.1.custom.min.css"/>

    <link rel="stylesheet" type="text/css" href="styles/login.css"/>

    <script type="text/javascript" src="scripts/jquery/jquery-1.9.0.min.js"></script>

    <script type="text/javascript" src="scripts/jquery/jquery-ui-1.10.1.custom.js"></script>

    <script type="text/javascript" src="scripts/jquery.blockUI.js"></script>

    <script type="text/javascript" src="scripts/jquery/jquery.widget.min.js"></script>

    <script type="text/javascript" src="scripts/jquery/jquery.mousewheel.js"></script>

    <script type="text/javascript" src="scripts/load-metro.js"></script>

</head>

<body class="metro">

	<div style="width:100%; height:100%; background:rgba(0,0,0,0.7);">

        <div class="message-dialog bg-darkRed" style="padding-bottom:20px;">

            <h2 class="fg-white"><?php $translate->__('Error de inicio de sesi&oacute;n'); ?></h2>

            <p class="fg-white"><?php $translate->__('Los datos de usuario o clave proporcionados son incorrectos'); ?></p>

            <a href="acceso.php#login" class="button place-right"><?php $translate->__('Iniciar sesi&oacute;n'); ?></a>

            <a href="acceso.php#register" class="button place-right"><?php $translate->__('Registrarse'); ?></a>

        </div>

    </div>

</body>

</html>