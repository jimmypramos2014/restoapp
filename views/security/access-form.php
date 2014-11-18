<?php 
include("bussiness/menu.php");
$idperfil = isset($_GET['idperfil']) ? $_GET['idperfil'] : '1';
$objData = new clsMenu();
$rs = $objData->Listar("L", "");
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("a.back-button").click(function(){
			window.parent.hideFrame();
			return false;
		});

        $('#ddlPerfil').change(function () {
            window.location = 'index.php?pag=seguridad&subpag=permisos&op=form&idperfil=' + $(this).val();
        });
    });
</script>
<div class="page secondary">
    <form id="form1" name="form1" method="post">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="fg-color-white"><?php $translate->__('Perfiles y permisos'); ?></h1>
                <a href="#" title="Regresar" class="back-button big page-back white"></a>
            </div>
        </div>
        <div class="form-twilight page-region">
            <div class="page-region-content">
                <div class="grid">
                    <div class="row">
                        <div class="span12">
                            <h2>Perfiles</h2>
                            <div class="input-control select">
                                <select id="ddlPerfil" name="ddlPerfil">
                                    <?php 
                                    echo loadOpcionSel("tm_perfil", "Activo=1", "tm_idperfil", "tm_nombre", $idperfil);
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row fg-color-white">
                        <div class="span12">
                            <table class="bordered hovered">
                                
                                <thead>
                                    <tr>
                                        <th class="bg-color-blueDark"></th>
                                        <th class="bg-color-blueDark fg-color-white"><?php $translate->__('Opci&oacute;n'); ?></th>
                                        <th class="bg-color-blueDark fg-color-white"><?php $translate->__('S&oacute;lo lectura'); ?></th>
                                        <th class="bg-color-blueDark fg-color-white"><?php $translate->__('Modificar'); ?></th>
                                        <th class="bg-color-blueDark fg-color-white"><?php $translate->__('Control total'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    treeMenu($rs);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
        <div class="appbar">
            <button id="btnCancelar" type="button" class="metro_button float-right">

                <span class="content">

                    <img src="images/cancel.png" alt="<?php $translate->__('Cancelar'); ?>" />

                    <span class="text"><?php $translate->__('Cancelar'); ?></span>

                </span>

            </button>

            <button id="btnGuardar" type="button" class="metro_button float-right">

                <span class="content">

                    <img src="images/save.png" alt="<?php $translate->__('Guardar'); ?>" />

                    <span class="text"><?php $translate->__('Guardar'); ?></span>

                </span>

            </button>

            <div class="clear"></div>

        </div>
    </form>
</div>
