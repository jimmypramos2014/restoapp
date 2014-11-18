<?php
include('bussiness/mesas.php');
include('bussiness/atencion.php');
$IdEmpresa = 1;
$IdCentro = 1;
$counterMesa = 0;
$counterRowMesaUnida = 0;
$il = 0;
$objAtencion = new clsAtencion();
$objMesa = new clsMesa();
$rpta = 0;
$rptaMov = 0;
$rptaDetailsMov = 0;
$EstadoMesa = '04';
$countCheck = 0;
if ($_POST){
    $hdEstadoActual = isset($_POST['hdEstadoActual']) ? $_POST['hdEstadoActual'] : '00';
    $hdEstadoNuevo = isset($_POST['hdEstadoNuevo']) ? $_POST['hdEstadoNuevo'] : '01';
    $hdIdAtencion = isset($_POST['hdIdAtencion']) ? $_POST['hdIdAtencion'] : '0';
    $hdIdMesa = isset($_POST['hdIdMesa']) ? $_POST['hdIdMesa'] : '0';
    $chkItem = isset($_POST['chkItem']) ? $_POST['chkItem'] : '0';
    $hdTipoAccion = isset($_POST['hdTipoAccion']) ? $_POST['hdTipoAccion'] : 'SELECTION';
    
    if ($hdTipoAccion == 'SELECTION')
        $paramsId = implode(',', $chkItem);
    else
        $paramsId = $hdIdAtencion;
    $valid = $objAtencion->ActualizarEstadoItem($hdTipoAccion, $hdEstadoNuevo, $paramsId);
    if ($valid){
        if ($hdTipoAccion == 'ALL' && $hdEstadoNuevo == '02')
            $EstadoMesa = '05';
        else {
            $rsCheck = $objAtencion->CheckStateDetails($hdIdAtencion);
            $countCheck = $rsCheck[0]['countCheck'];
            if ($countCheck > 0)
                $EstadoMesa = '04';
            else
                $EstadoMesa = '05';
        }
        $objAtencion->DeletePrevAtencionMesa($hdIdAtencion);
        $entidadMesaAtencion = array(
            'tm_idmesa' => $hdIdMesa,
            'tm_idatencion' => $hdIdAtencion,
            'Activo' => 1, 
            'IdUsuarioReg' => $idusuario,
            'FechaReg' => date("Y-m-d h:i:s"),
            'IdUsuarioAct' => $idusuario,
            'FechaAct' => date("Y-m-d h:i:s"));
        $objAtencion->RegistrarAtencionMesa($entidadMesaAtencion);
        $entidadMov = array(
            'tm_idatencion' => $IdAtencion,
            'ta_estadoatencion' => $EstadoMesa,
            'td_fechamov'  => date("Y-m-d h:i:s"),
            'td_direccionIP' => $realIp,
            'Activo' => 1,
            'IdUsuarioReg' => $idusuario,
            'FechaReg' => date("Y-m-d h:i:s"),
            'IdUsuarioAct' => $idusuario,
            'FechaAct' => date("Y-m-d h:i:s")
        );
        $rptaMov = $objAtencion->RegistrarMovimiento($entidadMov);
        if ($rptaMov > 0){
            $entidadMovMesa = array(
                'tm_idmesa' => $hdIdMesa, 
                'ta_estadoatencion'=>$EstadoMesa, 
                'Activo' => 1,
                'IdUsuarioReg' => $idusuario,
                'FechaReg' => date("Y-m-d h:i:s"),
                'IdUsuarioAct' => $idusuario,
                'FechaAct' => date("Y-m-d h:i:s"));
            $rptaDetailsMov = $objMesa->RegistrarMovimiento($entidadMovMesa);
            if ($rptaDetailsMov > 0){
                $rpta = $objMesa->UpdateEstado($EstadoMesa, $hdIdMesa);
            }
        } 
    }
    $jsondata = array('rpta' => $rpta, 'Estado' => $EstadoMesa );
    echo json_encode($jsondata);
    exit(0);
}
$rowMesa = $objMesa->Listar('COCINA', $IdEmpresa, $IdCentro, '\'03\', \'04\'');
$countRowMesa = count($rowMesa);
$rowAtencionMesaUnidas = $objAtencion->ListarAtencionsMesasUnidas('COCINA', $IdEmpresa, $IdCentro, '\'03\', \'04\'');
$countrowAtencionMesaUnidas = count($rowAtencionMesaUnidas);
$rowMesaUnida = $objMesa->Listar('UNIDAS-COCINA', $IdEmpresa, $IdCentro, '\'03\', \'04\'');
$countRowMesaUnida = count($rowMesaUnida);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdAtencion" name="hdIdAtencion" value="0" />
    <input type="hidden" id="hdIdMesa" name="hdIdMesa" value="0" />
    <input type="hidden" id="hdTipoAccion" name="hdTipoAccion" value="SELECTION" />
    <input type="hidden" id="hdEstadoActual" name="hdEstadoActual" value="00" />
    <input type="hidden" id="hdEstadoNuevo" name="hdEstadoNuevo" value="00" />
    <div class="page-region">
        <div id="pnlListado" class="inner-page">
            <h1 class="title-window">
                <?php $translate->__('Cocina'); ?>
            </h1>
            <div class="divContent">
                <div class="moduloTwoPanel">
                    <div class="colTwoPanel1 column-panel">
                        <h2 class="header-panel"><?php $translate->__('Mesas'); ?></h2>
                        <div class="body-panel">
                            <div class="scroll-panel">
                                <div id="pnlGroupMesas">
                                    <h3><?php $translate->__('Agrupadas'); ?></h3>
                                    <div class="tile-area">
                                        <?php
                                        if ($countrowAtencionMesaUnidas > 0){
                                            for ($il=0; $il < $countrowAtencionMesaUnidas; $il++) { 
                                        ?>
                                        <div class="tile" style="background-color: <?php echo $rowMesaUnida[$il]['ta_colorleyenda']; ?>;" data-idatencion="<?php echo $rowMesaUnida[$il]['tm_idatencion']; ?>" data-state="<?php echo $rowMesaUnida[$il]['ta_estadoatencion']; ?>">
                                            <div class="tile-content">
                                                <div class="text-right padding10 ntp">
                                                    <h3 class="fg-white">
                                        <?php
                                                $strMesasUnidas = '';
                                                for ($jl=0; $jl < $countRowMesaUnida; $jl++) {
                                                    if ($rowMesaUnida[$jl]['tm_idatencion'] == $rowAtencionMesaUnidas[$il]['tm_idatencion']){
                                                        if (strlen($strMesasUnidas) > 0)
                                                            $strMesasUnidas .= ' - ';
                                                        $strMesasUnidas .= $rowMesaUnida[$jl]['tm_codigo'];
                                                    }
                                                }
                                                echo $strMesasUnidas;
                                        ?>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        }
                                        else {
                                        ?>
                                        <h4><?php $translate->__('No hay mesas agrupadas por el momento.'); ?></h4>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="pnlMesas">
                                    <h3><?php $translate->__('Individuales'); ?></h3>
                                    <div class="tile-area">
                                        <?php
                                        for ($counterMesa=0; $counterMesa < $countRowMesa; $counterMesa++) {
                                        ?>
                                        <div class="tile" data-idatencion="<?php echo $rowMesa[$counterMesa]['tm_idatencion']; ?>" data-state="<?php echo $rowMesa[$counterMesa]['ta_estadoatencion']; ?>" style="background-color: <?php echo $rowMesa[$counterMesa]['ta_colorleyenda']; ?>;" rel="<?php echo $rowMesa[$counterMesa]['tm_idmesa']; ?>">
                                            <div class="tile-content">
                                                <div class="text-right padding10 ntp">
                                                    <h1 class="fg-white"><?php echo $rowMesa[$counterMesa]['tm_codigo']; ?></h1>
                                                </div>
                                            </div>
                                            <div class="brand"><span class="badge bg-dark"><?php echo $rowMesa[$counterMesa]['tm_nrocomensales']; ?></span></div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="colTwoPanel2 column-panel">
                        <h2 class="header-panel"><?php $translate->__('Detalle de pedido'); ?></h2>
                        <div class="body-panel">
                            <div class="detalleAtencion">
                                <div class="container-details">
                                    <div class="headerPedido">
                                        <ul>
                                            <li class="colProducto"><a href="#"><h4 class="fg-white"><?php $translate->__('Art&iacute;culos'); ?></h4></a></li>
                                            <li class="colCantidad"><a href="#"><h4 class="fg-white"><?php $translate->__('Cantidad'); ?></h4></a></li>
                                            <li class="colEstado"><a href="#"><h4 class="fg-white"><?php $translate->__('Estado'); ?></h4></a></li>
                                        </ul>
                                    </div>
                                    <div class="contentPedido">
                                        <div class="scroll-content">
                                            <table>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="scrollup">Scroll</a>
            </div>
        </div>
    </div>
    <div class="appbar">
        <button id="btnCompletarPedido" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/apply-2.png" alt="<?php $translate->__('Completar todo'); ?>" />
                <span class="text"><?php $translate->__('Completar todo'); ?></span>
            </span>
        </button>
        <button id="btnCompletarItem" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/apply-2.png" alt="<?php $translate->__('Completar selecci&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Completar selecci&oacute;n'); ?></span>
            </span>
        </button>
        <button id="btnAtenderPedido" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/go.png" alt="<?php $translate->__('Atender todo'); ?>" />
                <span class="text"><?php $translate->__('Atender todo'); ?></span>
            </span>
        </button>
        <button id="btnAtenderItem" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/go.png" alt="<?php $translate->__('Atender selecci&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Atender selecci&oacute;n'); ?></span>
            </span>
        </button>
        <button id="btnBackTables" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/back.png" alt="<?php $translate->__('Regresar a mesas'); ?>" />
                <span class="text"><?php $translate->__('Regresar a mesas'); ?></span>
            </span>
        </button>
        <button id="btnListaPedidos" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/layout.png" alt="<?php $translate->__('Pedidos realizados'); ?>" />
                <span class="text"><?php $translate->__('Pedidos realizados'); ?></span>
            </span>
        </button>
        <button id="btnClearSelection" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/icon_uncheck.png" alt="<?php $translate->__('Limpiar selecci&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Limpiar selecci&oacute;n'); ?></span>
            </span>
        </button>
    </div>
</form>
<?php
include('common/libraries-js.php');
?>
<script>
    var TipoBusqueda = '00';
    $(document).ready(function(){
        $('#btnBackTables').click(function(){
            BackToTables();
            return false;
        });
        $('.divContent').scroll(function(){
            if ($(this).scrollTop() > 100)
                $('#btnBackTables').removeClass('oculto');
            else
                $('#btnBackTables').addClass('oculto');
        });
        $('.colTwoPanel1').on('click', '.tile', function () {
            var IdAtencion = $(this).attr('data-idatencion');
            var IdMesa = $(this).attr('rel');
            $('#hdIdAtencion').val(IdAtencion);
            $('#hdIdMesa').val(IdMesa);
            $(this).removeClass('blink');
            $('.colTwoPanel1 .tile').removeClass('selected');
            $(this).addClass('selected');
            $('.divContent').animate({ scrollTop: $('.detalleAtencion').offset().top }, 'slow');
            listarDetalle(IdAtencion);
            return false;
        });
        $('.contentPedido table tbody').on('click', 'tr', function () {
            var estadoBotones = false;
            var estado = $(this).attr('data-state');
            var inputCheck = $(this).find('input:checkbox');
            $('#hdEstadoActual').val(estado);
            if ($(this).hasClass('selected')){
                $(this).removeClass('selected');
                if ($(this).siblings('.selected').length == 0)
                    stateButtons(false);
            }
            else {
                $(this).addClass('selected');
                if ($(this).siblings('.selected').length == 0)
                    stateButtons(true);
            }
            inputCheck[0].checked = $(this).hasClass('selected');
        });
        $('#btnAtenderItem').on('click', function () {
            $('#hdTipoAccion').val('SELECTION');
            $('#hdEstadoNuevo').val('01');
            GuardarCambios();
            return false;
        });
        $('#btnCompletarItem').on('click', function () {
            $('#hdTipoAccion').val('SELECTION');
            $('#hdEstadoNuevo').val('02');
            GuardarCambios();
            return false;
        });
        $('#btnAtenderPedido').on('click', function () {
            $('#hdTipoAccion').val('ALL');
            $('#hdEstadoNuevo').val('01');
            GuardarCambios();
            return false;
        });
        $('#btnCompletarPedido').on('click', function () {
            $('#hdTipoAccion').val('ALL');
            $('#hdEstadoNuevo').val('02');
            GuardarCambios();
            return false;
        });
        $('#btnClearSelection').on('click', function () {
            $('.contentPedido table tbody tr.selected').removeClass('selected');
            $('#btnAtenderItem, #btnCompletarItem').addClass('oculto');
            $('.contentPedido table tbody input:checkbox').removeAttr('checked');
            $('#btnAtenderPedido, #btnCompletarPedido').removeClass('oculto');
            $(this).addClass('oculto');
            return false;
        });
        setInterval(function () {
          NotificarAtencion('NOTIFCOCINA');
        }, 3000);
    });
    function BackToTables () {
        $(".divContent").animate({ scrollTop: 0 }, 500);
        $('#btnAtenderPedido, #btnCompletarPedido').addClass('oculto');
    }
    function GuardarCambios () {
        var serializedReturn = $("#form1 input[type!=text]").serialize();
        var selector = '';
        $.ajax({
            type: "POST",
            url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
            cache: false,
            data: serializedReturn,
            success: function(data){
                var datos = eval( "(" + data + ")" );
                var EstadoNuevo = $('#hdEstadoNuevo').val();
                var TipoAccion = $('#hdTipoAccion').val();
                var IdAtencion = $('#hdIdAtencion').val();
                var showMessage = false;
                if (EstadoNuevo == '02' && TipoAccion == 'ALL')
                    showMessage = true;
                else {
                    if (datos.Estado != '05')
                        listarDetalle(IdAtencion);
                    else
                        showMessage = true;
                }
                if (showMessage == true){
                    MessageBox('<?php $translate->__('Pedido terminado'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                        BackToTables();
                        limpiarDetalle();
                        $('.colTwoPanel1 .tile.selected').remove();
                    });
                }
            }
        });
    }
    function stateButtons (state) {
        if (state){
            $('#btnAtenderPedido, #btnCompletarPedido').addClass('oculto');
            $('#btnAtenderItem, #btnCompletarItem').removeClass('oculto');
            $('#btnClearSelection').removeClass('oculto');
        }
        else {
            $('#btnAtenderPedido, #btnCompletarPedido').removeClass('oculto');
            $('#btnAtenderItem, #btnCompletarItem').addClass('oculto');
            $('#btnClearSelection').addClass('oculto');
        }
    }
    function limpiarDetalle () {
        stateButtons(false);
        $('.contentPedido table tbody').html('');
    }
    function setIcon (codEstado, colorEstado) {
        var strIcon = '';
        var iconState = '';
        if (codEstado == '00')
            iconState = 'bookmark';
        else if (codEstado == '01')
            iconState = 'cycle';
        else if (codEstado == '02')
            iconState = 'checkmark';
        strIcon = '<i class="icon-' + iconState + ' on-right on-left" style="background: ' + colorEstado + '; color: white; padding: 10px; border-radius: 50%;"></i>';
        return strIcon;
    }
    function listarDetalle (IdAtencion) {
        precargaExp('.page-region', true);
        limpiarDetalle();
        
        $.ajax({
            type: "GET",
            url: "services/ventas/detallepedido-search.php",
            cache: false,
            data: "idatencion=" + IdAtencion,
            success: function(data){
                var datos = eval( "(" + data + ")" );
                var il = datos.length;
                var i = 0;
                var strDetalle = '';
                var strIcon = '';
                var idDetalle = '0';
                var idProducto = '0';
                var nombreProducto = '';
                var nombreCategoria = '';
                var nombreSubCategoria = '';
                var codEstado = '';
                var colorEstado = '';
                var nombreEstado = '';
                var tipoMenuDia = '';
                var colorMenuDia = '';
                var cantidad = 0;
                
                if (il > 0){
                    while (i < il){
                        idDetalle = datos[i].idDetalle;
                        idProducto = datos[i].idProducto;
                        nombreProducto = datos[i].nombreProducto;
                        nombreCategoria = datos[i].nombreCategoria;
                        nombreSubCategoria = datos[i].nombreSubCategoria;
                        cantidad = datos[i].cantidad;
                        tipoMenuDia = datos[i].tipoMenuDia;
                        colorMenuDia = datos[i].colorMenuDia;
                        codEstado = datos[i].codEstado;
                        colorEstado = datos[i].colorEstado;
                        nombreEstado = datos[i].Estado;
                        strIcon = setIcon(codEstado, colorEstado);
                        strDetalle += '<tr rel="' + idDetalle + '" data-state="' + codEstado + '">';
                        strDetalle += '<td class="colProducto"><input type="checkbox" name="chkItem[]" value="' + idDetalle + '" />';
                        strDetalle += '<h4 class="nombreProducto">' + nombreProducto + '</h4><div class="categoria"><span class="cat">' + nombreCategoria + '</span><span class="subcat">' + nombreSubCategoria + '</span><span class="tipomenudia" style="background-color: ' + colorMenuDia +'">' + tipoMenuDia + '</span></div>';
                        strDetalle += '</td><td class="colCantidad"><h3>' + cantidad + '</h3></td>';
                        strDetalle += '<td class="colEstado"><h4>' + strIcon + '</h4></td></tr>';
                        ++i;
                    }
                    $('.contentPedido table tbody').append(strDetalle);
                    $('.contentPedido table tbody input:checkbox').hide();
                    $('#btnAtenderPedido, #btnCompletarPedido').removeClass('oculto');
                }
                precargaExp('.page-region', false);
            }
        });
    }
</script>