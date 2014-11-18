<?php 

include("bussiness/usuarios.php");

$objData = new clsUsuario();

$row = $objData->Listar("2", $id);

if (!$row)
    $row[0] = $objData->Entidad();

if ($_POST)

{
    $validNombre = "";
    if ($_POST['hdId'] == '0'){
        $validNombre = " tm_login = '".$_POST['txtUsuario']."' ";
    }
    else {
        $validNombre = " tm_login = '".$_POST['txtUsuario']."' and tm_idusuario <> ".$_POST['hdId'];
    }
    $rsVal = $objData->Listar('VAL', $validNombre);
    $countRsVal = count($rsVal);

    if ($countRsVal > 0){
        $jsondata = array("rpta" => "ERRUSU001"); //Ya existe una empresa con el mismo nombre
        echo json_encode($jsondata);
        exit(0);
    }

    $entidad = $objData->Entidad();

    $entidad['tm_idusuario'] = $_POST['hdId'];

    $entidad['tp_idpais'] = $_POST['ddlPais'];

    $entidad['tm_idperfil'] = $_POST['ddlPerfil'];

    $entidad['tm_login'] = $_POST['txtUsuario'];

    $entidad['tm_clave'] = $_POST['txtContrasena'];

    $entidad['tm_nombres'] = $_POST['txtNombres'];

    $entidad['tm_nroruc'] = $_POST['txtNroRUC'];

    $entidad['tm_nrodni'] = $_POST['txtNroDNI'];

    $entidad['tm_telefono'] = $_POST['txtTelefonos'];

    $entidad['tm_email'] = $_POST['txtEmail'];

    $entidad['tm_direccion'] = $_POST['txtDireccion'];

    $entidad['tm_apellidopaterno'] = $_POST['txtApellidoPaterno'];

    $entidad['tm_apellidomaterno'] = $_POST['txtApellidoMaterno'];

    $entidad['tm_sexo'] = $_POST['ddlSexo'];
    
    $entidad['tm_foto'] = $_POST['hdFoto'];

    $entidad['Activo'] = 1;

    $entidad['IdUsuarioReg'] = 1;

    $entidad['FechaReg'] = date("Y-m-d h:i:s");

    $entidad['IdUsuarioAct'] = 1;

    $entidad['FechaAct'] = date("Y-m-d h:i:s");

    

    $rpta = $objData->Registrar($entidad);

    $jsondata = array("rpta" => $rpta);

    echo json_encode($jsondata);

    exit(0);

}
?>
<script type="text/javascript" src="scripts/droparea.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#ddlPerfil").focus();
        
        $("a.back-button").click(function(){
            window.parent.hideFrame();
            return false;
        });
        
        $('.droparea').droparea({
            'instructions': '<?php $translate->__('arrastre una imagen o haga click aqu&iacute;'); ?>',
            'init' : function(result){
                //console.log('custom init',result);
                
                $("#area").find(".spot").append($('<img>',{'src': '<?php echo $row[0]['tm_foto'] == "" ? "images/user-nosetimg.jpg" : $row[0]['tm_foto']; ?>'}));
            },
            'start' : function(area){
                area.find('.error').remove(); 
            },
            'error' : function(result, input, area){
                $('<div class="error">').html(result.error).prependTo(area); 
                return 0;
                //console.log('custom error',result.error);
            },
            'complete' : function(result, file, input, area){
                if((/image/i).test(file.type)){
                    area.find('img').remove();
                    //area.data('value',result.filename);
                    area.append($('<img>',{'src': result.filename + '?' + Math.random() }));
                    $('#hdFoto').val(result.path + result.filename);
                } 
                //console.log('custom complete',result);
            }
        });
    });
    
     
</script>
<style type="text/css">
    .droparea {
        position:relative;
        text-align: center;
        color: #fff;
    }
    .droparea div, .droparea input, .multiple div, .multiple input {
        position: absolute;
        color: #fff;
        top:0;
        width: 100%;
        height: 100%;
    }
    .droparea input, .multiple input {
        cursor: pointer; 
        color: #fff;
        opacity: 0; 
    }
    .droparea .instructions, .multiple .instructions {
        border: 2px dashed #ddd;
        color:#fff;
        opacity: .8;
    }
    .droparea .instructions.over, .multiple .instructions.over {
        border: 2px dashed #000;
        background: #ffa;
    }
    .droparea .progress, .multiple .progress {
        position:absolute;
        bottom: 0;
        width: 100%;
        height: 0;
        color: #fff;
        background: #6b0;
    }
    .multiple .progress {
        width: 0;
        height: 100%;
    }
    div.spot {
        float: left;
        margin: 0 20px 0 0;
        width: 235px;
        min-height: 235px;
    }
    .thumb {
        float: left;
        margin:0 20px 20px 0;
        width: 235px;
        min-height: 235px;
    }
</style>
<div class="page secondary">
    <form id="form1" name="form1" method="post">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="fg-color-white"><?php $translate->__('Usuarios'); ?></h1>
                <a href="#" title="<?php $translate->__('Regresar'); ?>" class="back-button big page-back white"></a>
            </div>
        </div>
        <div class="form-twilight page-region">
            <input id="hdId" name="hdId" type="hidden" value="<?php echo $id; ?>" />
            <input id="hdFoto" name="hdFoto" type="hidden" value="<?php echo $row[0]['tm_foto'] == "" ? "images/user-nosetimg.jpg" : $row[0]['tm_foto']; ?>" />
            <div class="page-region-content">
                <div class="grid">
                    <div class="row">
                        <div class="grid">
                            <div class="span6">
                                <div class="grid">
                                    <div class="row">
                                        <h2>Perfil</h2>
                                        <div class="input-control select">
                                            <select id="ddlPerfil" name="ddlPerfil">
                                                <?php 
                                                echo loadOpcionSel("tm_perfil", "Activo=1", "tm_idperfil", "tm_nombre", $row[0]['tm_idperfil']);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h2><?php $translate->__('Usuario'); ?></h2>
                                        <div class="input-control text">
                                            <input id="txtUsuario" name="txtUsuario" type="text" autofocus="" value="<?php echo utf8_decode($row[0]['tm_login']); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h2><?php $translate->__('Contrase&ntilde;a'); ?></h2>
                                        <div class="input-control password">
                                            <input id="txtContrasena" name="txtContrasena" type="password" autofocus="" value="<?php echo utf8_decode($row[0]['tm_clave']); ?>">
                                            <button class="btn-reveal" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="area" class="span6">
                                <input id="userFoto" type="file" class="droparea spot" name="xfile" data-post="upload.php" data-width="235" data-height="235" data-crop="true"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <h2><?php $translate->__('Apellido paterno'); ?></h2>
                            <div class="input-control text">
                                <input id="txtApellidoPaterno" name="txtApellidoPaterno" type="text" autofocus="" value="<?php echo $row[0]['tm_apellidopaterno']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                        <div class="span6">
                            <h2><?php $translate->__('Apellido materno'); ?></h2>
                            <div class="input-control text">
                                <input id="txtApellidoMaterno" name="txtApellidoMaterno" type="text" autofocus="" value="<?php echo $row[0]['tm_apellidomaterno']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <h2><?php $translate->__('Nombres'); ?></h2>
                            <div class="input-control text">
                                <input id="txtNombres" name="txtNombres" type="text" autofocus="" value="<?php echo $row[0]['tm_nombres']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                        <div class="span6">
                            <h2><?php $translate->__('Sexo'); ?></h2>
                            <div class="input-control select">
                                <select id="ddlSexo" name="ddlSexo">
                                    <option value="00"<?php echo $row[0]['tm_sexo'] == "00" ? ' selected="selected"' : ''; ?>>MASCULINO</option>
                                    <option value="01"<?php echo $row[0]['tm_sexo'] == "01" ? ' selected="selected"' : ''; ?>>FEMENINO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <h2>DNI</h2>
                            <div class="input-control text">
                                <input id="txtNroDNI" name="txtNombres" type="text" autofocus="" value="<?php echo utf8_decode($row[0]['tm_nrodni']); ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                        <div class="span6">
                            <h2><?php $translate->__('C&oacute;digo de contribuyente'); ?></h2>
                            <div class="input-control text">
                                <input id="txtNroRUC" name="txtNombres" type="text" autofocus="" value="<?php echo utf8_decode($row[0]['tm_nroruc']); ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <h2><?php $translate->__('Pa&iacute;s del usuario'); ?></h2>
                            <div class="input-control select">
                                <select id="ddlPais" name="ddlPais">
                                    <?php 
                                    echo loadOpcionSel("tp_pais", "Activo=1", "tp_idpais", "tp_nombre", $row[0]['tp_idpais']);
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="span6">
                            <h2><?php $translate->__('Direcci&oacute;n'); ?></h2>
                            <div class="input-control text">
                                <input id="txtDireccion" name="txtDireccion" type="text" autofocus="" value="<?php echo $row[0]['tm_direccion']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <h2><?php $translate->__('Email'); ?></h2>
                            <div class="input-control text">
                                <input id="txtEmail" name="txtEmail" type="text" autofocus="" value="<?php echo $row[0]['tm_email']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button">
                                </button>
                            </div>
                        </div>
                        <div class="span6">
                            <h2>Tel&eacute;fonos</h2>
                            <div class="input-control text">
                                <input id="txtTelefonos" name="txtTelefonos" type="text" autofocus="" value="<?php echo $row[0]['tm_telefono']; ?>">
                                <button class="btn-clear" tabindex="-1" type="button">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="appbar">
            <button id="btnCancelar" type="button" class="metro_button float-right">
                <span class="content">
                    <img src="images/cancel.png" alt="Cancelar" />
                    <span class="text"><?php $translate->__('Cancelar'); ?></span>
                </span>
            </button>
            <button id="btnGuardar" type="button" class="metro_button float-right">
                <span class="content">
                    <img src="images/save.png" alt="Guardar" />
                    <span class="text"><?php $translate->__('Guardar'); ?></span>
                </span>
            </button>
            <div class="clear"></div>
        </div>
    </div>
</div>