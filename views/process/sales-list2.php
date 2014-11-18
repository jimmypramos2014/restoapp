<?php
include('bussiness/tabla.php');
include('bussiness/tipocomprobante.php');
include('bussiness/formapago.php');
include('bussiness/monedas.php');
include('bussiness/ambientes.php');
include('bussiness/mesas.php');
include('bussiness/categoria.php');
include('bussiness/precios.php');
include('bussiness/ventas.php');
include('bussiness/atencion.php');
$IdEmpresa = 1;
$IdCentro = 1;
$IdAmbiente = 1;
$foundIdAmbiente = '0';
$counterMoneda = 0;
$counterTipoComprobante = 0;
$counterAmbiente = 0;
$counterMesa = 0;
$counterProdPrecio = 0;
$countRowProdPrecio = 0;
$counterRowMesaUnida = 0;
$counterEstadoMesa = 0;
$cssTile = '';
$cssContentTile = '';
$imagenProd = '';
$strQueryMesas = '';
$strDetQueryMesas = '';
$strQueryMovMesas = '';
$strDetQueryMovMesas = '';
$strItemsDetalle ='';
$strQueryDetAtencion = '';
$strQueryUpdateDetalle = '';
$strItemsDetalleVenta = '';
$strQueryDetVenta = '';
$strQueryUpdateDetalleVenta = '';
$strListMesas = '';
$strIdAtencion = '';
$objTabla = new clsTabla();
$objTipoComprobante = new clsTipoComprobante();
$objFormaPago = new clsFormaPago();
$objMoneda = new clsMoneda();
$objAmbiente = new clsAmbiente();
$objMesa = new clsMesa();
$objCategoria = new clsCategoria();
$objPrecio = new clsPrecio();
$objAtencion = new clsAtencion();
$objVenta = new clsVenta();
$rpta = '0';
$rptaPedido = 0;
$rptaVenta = 0;
$rptaDetails = 0;
$rptaDetailsMov = 0;
$rptaMov = 0;
$validSQL = false;
$i = 0;
$il=0;
$jl=0;
$countExistDetails = 0;
$IdAtencion = '0';
$IdVenta = '0';
$realIp = getRealIP();
if ($_POST){
	$hdTipoSave = isset($_POST['hdTipoSave']) ? $_POST['hdTipoSave'] : '00';
	$hdTipoSeleccion = isset($_POST['hdTipoSeleccion']) ? $_POST['hdTipoSeleccion'] : '00';
	$hdEstadoAtencion = isset($_POST['hdEstadoAtencion']) ? $_POST['hdEstadoAtencion'] : '01';
	$hdTipoUbicacion = isset($_POST['hdTipoUbicacion']) ? $_POST['hdTipoUbicacion'] : '00';
	$Id = isset($_POST['hdIdPrimary']) ? $_POST['hdIdPrimary'] : '0';
	$IdVenta = isset($_POST['hdIdVenta']) ? $_POST['hdIdVenta'] : '0';
	$hdIdMesa = isset($_POST['hdIdMesa']) ? $_POST['hdIdMesa'] : '0';
	
	if ($hdTipoSave == '00'){
		//$estadoAtencion = '01';
		if ($Id == '0'){
			$rsCorrelativo = $objAtencion->Correlativo($IdAmbiente, $IdEmpresa, $IdCentro);
			$NroAtencion = $rsCorrelativo[0]['Correlativo'];
		}
		$strListMesas = $_POST['strListMesas'];
		
		$pedidoMaestroI = array(
			'tm_idempresa' => $IdEmpresa,
			'tm_idcentro' => $IdCentro,
			'tm_idambiente' => $IdAmbiente,
			'tm_nroatencion' => $NroAtencion,
			'tm_fechahora' => date("Y-m-d h:i:s"),
			'ta_tipoubicacion' => $hdTipoUbicacion,
			'Activo' => 1,
			'IdUsuarioReg' => $idusuario,
			'FechaReg' => date("Y-m-d h:i:s")
		);
		$pedidoMaestroU = array(
			'tm_idatencion' => $Id, 
			'ta_estadoatencion' => $hdEstadoAtencion,
			'IdUsuarioAct' => $idusuario,
            'FechaAct' => date("Y-m-d h:i:s")
		);
		if ($Id == '0')
			$pedidoMaestro = array_merge($pedidoMaestroI, $pedidoMaestroU);
        else
            $pedidoMaestro = $pedidoMaestroU;
		$rptaPedido = $objAtencion->RegistrarMaestro($pedidoMaestro);
		if ($rptaPedido > 0){
			
			if ($Id == '0')
				$IdAtencion = $rptaPedido;
			else
				$IdAtencion = $Id;
			if ($hdTipoSeleccion == '00'){
				$objAtencion->DeletePrevAtencionMesa($Id);
				$strQueryMesas = 'INSERT INTO td_atencion(tm_idmesa, tm_idatencion, Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
				$strQueryMovMesas = 'INSERT INTO td_mesa_movimiento(tm_idmesa, ta_estadoatencion, Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
				$arrMesas = explode(",", $strListMesas);
				$countArrMesas = count($arrMesas);
				if ($countArrMesas > 0){
					for ($i=0; $i < $countArrMesas; $i++) { 
						if (strlen($strDetQueryMesas) > 0)
							$strDetQueryMesas .= ',';
						if (strlen($strDetQueryMovMesas) > 0)
							$strDetQueryMovMesas .= ',';
						$strDetQueryMesas .= '('.$arrMesas[$i].', '.$IdAtencion.', 1, '.$idusuario.', \''.date("Y-m-d h:i:s").'\', '.$idusuario.', \''.date("Y-m-d h:i:s").'\')';
						$strDetQueryMovMesas .= '('.$arrMesas[$i].', \''.$hdEstadoAtencion.'\', 1, '.$idusuario.', \''.date("Y-m-d h:i:s").'\', '.$idusuario.', \''.date("Y-m-d h:i:s").'\')';
					}
					$strQueryMesas .= $strDetQueryMesas;
					$strQueryMovMesas .= $strDetQueryMovMesas;
					$rptaDetails = $objAtencion->RegistrarDetalle($strQueryMesas);
					if ($rptaDetails > 0){
						$rptaDetailsMov = $objMesa->RegistrarDetalle($strQueryMovMesas);
						if ($rptaDetailsMov > 0)
							$rptaUpdateEstadoMesa = $objMesa->UpdateEstado($hdEstadoAtencion, $strListMesas);
					}
				}
			}
			else {
				if ($hdTipoSeleccion == '01'){
					$detallePedido = json_decode(stripslashes($_POST['detallePedido']));
					$strQueryDetAtencion = 'INSERT INTO td_atencion_articulo (';
					$strQueryDetAtencion .= 'tm_idempresa, tm_idcentro, tm_idatencion, tm_idproducto, tm_idmoneda, td_precio, td_cantidad, td_subtotal, td_observacion, ta_tipomenudia, ta_estdetalle_atencion, ';
					$strQueryDetAtencion .= 'Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
				    
				    foreach($detallePedido as $item){
				    	if ($item->idDetalle == '0'){
				    		if (strlen($strItemsDetalle) > 0)
				    			$strItemsDetalle .= ',';
					    	$strItemsDetalle .= '('.$IdEmpresa.', '.$IdCentro.', '.$IdAtencion.', '.$item->idProducto.', '.$item->idMoneda.', '.$item->precio.', '.$item->cantidad.', '.$item->subTotal.', \''.$item->nombreProducto.'\', \''.$item->codTipoMenuDia.'\', \'00\', ';
					        $strItemsDetalle .= '1, '.$idusuario.', \''.date("Y-m-d h:i:s").'\', '.$idusuario.', \''.date("Y-m-d h:i:s").'\')';
				    	}
				    	else {
				    		if (strlen($strQueryUpdateDetalle) > 0)
				    			$strQueryUpdateDetalle .= ';';
				    		$strQueryUpdateDetalle .= 'UPATE td_atencion_articulo SET';
				    		$strQueryUpdateDetalle .= ' tm_idmoneda = '.$item->idMoneda;
				    		$strQueryUpdateDetalle .= ', td_precio = '.$item->precio;
				    		$strQueryUpdateDetalle .= ', td_cantidad = '.$item->cantidad;
				    		$strQueryUpdateDetalle .= ', td_subtotal = '.$item->subTotal;
				    		$strQueryUpdateDetalle .= ', td_observacion = '.$item->nombreProducto;
				    		$strQueryUpdateDetalle .= ' WHERE td_idatencion_articulo = '.$item->idDetalle;
				    		++$countExistDetails;
				    	}
				    }
				    if ($countExistDetails > 0){
				    	$validSQL = $objAtencion->ActualizarDetalle($strQueryUpdateDetalle);
				    	if ($validSQL)
                			$rptaDetails = 1;
				    }
				    
				    if (strlen($strItemsDetalle) > 0) {
				    	$strQueryDetAtencion .= $strItemsDetalle;
				    	$validSQL = $objAtencion->RegistrarDetalle($strQueryDetAtencion);
				    	if ($validSQL)
                			$rptaDetails = 1;
                	}
				}
				elseif ($hdTipoSeleccion == '04') {
					$ddlTipoComprobante = isset($_POST['ddlTipoComprobante']) ? $_POST['ddlTipoComprobante'] : '0';
					$ddlFormaPago = isset($_POST['ddlFormaPago']) ? $_POST['ddlFormaPago'] : '0';
					$hdIdMoneda = isset($_POST['hdIdMoneda']) ? $_POST['hdIdMoneda'] : '0';
					$hdIdCliente = isset($_POST['hdIdCliente']) ? $_POST['hdIdCliente'] : '1';
					$hdIdPersonal = isset($_POST['hdIdPersonal']) ? $_POST['hdIdPersonal'] : '1';
					$txtSerieComprobante = isset($_POST['txtSerieComprobante']) ? $_POST['txtSerieComprobante'] : '001';
					$txtNroComprobante = isset($_POST['txtNroComprobante']) ? $_POST['txtNroComprobante'] : '00001';
					$txtFechaVenta = isset($_POST['txtFechaVenta']) ? $_POST['txtFechaVenta'] : '00';
					$hdBaseImponible = isset($_POST['hdBaseImponible']) ? $_POST['hdBaseImponible'] : '0';
					$hdImpuesto = isset($_POST['hdImpuesto']) ? $_POST['hdImpuesto'] : '0';
					$hdTotalPedido = isset($_POST['hdTotalPedido']) ? $_POST['hdTotalPedido'] : '0';
					$ventaMaestroI = array(
						'tm_idempresa' => $IdEmpresa,
						'tm_idcentro' => $IdCentro,
						'tm_iddocumento' => $ddlTipoComprobante,
						'tm_idformapago' => $ddlFormaPago,
						'tm_idmoneda' => $hdIdMoneda,
						'tm_idcliente' => $hdIdCliente,
						'tm_idpersonal' => $hdIdPersonal,
						'tm_vserie_documento' => $txtSerieComprobante,
						'tm_vnumero_documento' => $txtNroComprobante,
						'tm_fecha_emision' => $txtFechaVenta,
						'tm_base_imponible' => $hdTotalPedido,
						'tm_igv' => $hdImpuesto,
						'tm_total' => $hdTotalPedido,
						'ta_estadoventa' => '00',
						'Activo' => 1,
						'IdUsuarioReg' => $idusuario,
						'FechaReg' => date("Y-m-d h:i:s"),
					);
					$ventaMaestroU = array(
						'tm_idventa' => $IdVenta, 
						'IdUsuarioAct' => $idusuario,
			            'FechaAct' => date("Y-m-d h:i:s")
					);
					if ($Id == '0')
						$ventaMaestro = array_merge($ventaMaestroI, $ventaMaestroU);
			        else
			            $ventaMaestro = $ventaMaestroU;
					$rptaVenta = $objVenta->RegistrarMaestro($ventaMaestro);
					if ($rptaVenta > 0){
						if ($IdVenta != '0')
							$rptaVenta = $IdVenta;
						$detallePedido = json_decode(stripslashes($_POST['detallePedido']));
						
						$strQueryDetVenta = 'INSERT INTO td_venta (';
						$strQueryDetVenta .= 'tm_idempresa, tm_idcentro, tm_idventa, tm_idproducto, tm_idmoneda, td_precio, td_cantidad, td_subtotal, ';
						$strQueryDetVenta .= 'Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
					    
					    foreach($detallePedido as $item){
					    	if ($item->idDetalle == '0'){
					    		if (strlen($strItemsDetalleVenta) > 0)
					    			$strItemsDetalleVenta .= ',';
						    	$strItemsDetalleVenta .= '('.$IdEmpresa.', '.$IdCentro.', '.$rptaVenta.', '.$item->idProducto.', '.$item->idMoneda.', '.$item->precio.', '.$item->cantidad.', '.$item->subTotal.', ';
						        $strItemsDetalleVenta .= '1, '.$idusuario.', \''.date("Y-m-d h:i:s").'\', '.$idusuario.', \''.date("Y-m-d h:i:s").'\')';
					    	}
					    	else {
					    		if (strlen($strQueryUpdateDetalleVenta) > 0)
					    			$strQueryUpdateDetalleVenta .= ';';
					    		$strQueryUpdateDetalleVenta .= 'UPATE td_venta SET';
					    		$strQueryUpdateDetalleVenta .= ' tm_idmoneda = '.$item->idMoneda;
					    		$strQueryUpdateDetalleVenta .= ', td_precio = '.$item->precio;
					    		$strQueryUpdateDetalleVenta .= ', td_cantidad = '.$item->cantidad;
					    		$strQueryUpdateDetalleVenta .= ', td_subtotal = '.$item->subTotal;
					    		$strQueryUpdateDetalleVenta .= ' WHERE td_idventa = '.$item->idDetalleVenta;
					    		++$countExistDetailsVent;
					    	}
					    }
					    if ($countExistDetailsVent > 0){
					    	$validSQL = $objVenta->ActualizarDetalle($strQueryUpdateDetalleVenta);
					    	if ($validSQL)
	                			$rptaDetails = 1;
					    }
					    
					    if (strlen($strItemsDetalleVenta) > 0) {
					    	$strQueryDetVenta .= $strItemsDetalleVenta;
					    	$validSQL = $objVenta->RegistrarDetalle($strQueryDetVenta);
					    	if ($validSQL)
	                			$rptaDetails = 1;
	                	}
					}
				}
				//$existAtencionMesa = $objAtencion->AtencionMesaIfExist($Id);
				$objAtencion->DeletePrevAtencionMesa($Id);
				$entidadMesaAtencion = array(
					'tm_idmesa' => $hdIdMesa,
					'tm_idatencion' => $IdAtencion,
					'Activo' => 1, 
					'IdUsuarioReg' => $idusuario,
					'FechaReg' => date("Y-m-d h:i:s"),
					'IdUsuarioAct' => $idusuario,
		            'FechaAct' => date("Y-m-d h:i:s"));
				$objAtencion->RegistrarAtencionMesa($entidadMesaAtencion);
			}
			/*$EstadoActual = $objAtencion->GetCurrentState($Id);
			if ($EstadoActual != $hdEstadoAtencion){*/
			$entidadMov = array(
				'tm_idatencion' => $IdAtencion,
				'ta_estadoatencion' => $hdEstadoAtencion,
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
					'ta_estadoatencion'=>$hdEstadoAtencion, 
					'Activo' => 1,
					'IdUsuarioReg' => $idusuario,
					'FechaReg' => date("Y-m-d h:i:s"),
					'IdUsuarioAct' => $idusuario,
					'FechaAct' => date("Y-m-d h:i:s"));
				$rptaDetailsMov = $objMesa->RegistrarMovimiento($entidadMovMesa);
				if ($rptaDetailsMov > 0)
					$objMesa->UpdateEstado($hdEstadoAtencion, $hdIdMesa);
			}
			//}
			$rpta = $IdAtencion;
		}
	}
	elseif ($hdTipoSave == '01'){
		$strIdAtencion = $_POST['strIdAtencion'];
		$objAtencion->UpdateEstadoMultiple($hdEstadoAtencion, $strIdAtencion);
		$objAtencion->DeletePrevAtencionMesa($strIdAtencion);
		$objAtencion->MultiInsertDetAtencion($strIdAtencion, $idusuario);
		$objAtencion->MultiInsertMovMesas($strIdAtencion, $idusuario, $hdEstadoAtencion);
		$objAtencion->UpdateEstadoByAtencion($hdEstadoAtencion, $strIdAtencion);
		$rpta = 0;
	}
	$stateColor = $objTabla->GetSpecificValue('ta_colorleyenda', 'ta_estadoatencion', $hdEstadoAtencion);
	$jsondata = array("rpta" => $rpta, "stateColor" => $stateColor, 'estadoAtencion' => $hdEstadoAtencion, 'rptaVenta' => $rptaVenta);
	echo json_encode($jsondata);
	exit(0);
}
$rowMoneda = $objMoneda->Listar('L', '');
$countRowMoneda = count($rowMoneda);
$rowTipoComprobante = $objTipoComprobante->Listar('L', '');
$countRowTipoComprobante = count($rowTipoComprobante);
$rowFormaPago = $objFormaPago->Listar('L', '');
$countRowFormaPago = count($rowFormaPago);
$rowAmbiente = $objAmbiente->Listar('GroupAmbiente', $IdEmpresa, $IdCentro);
$countRowAmbiente = count($rowAmbiente);
$rowProdPrecio = $objPrecio->Listar('ListPrecioProducto', $parametros);
$countRowProdPrecio = count($rowProdPrecio);
$rowAtencionMesaUnidas = $objAtencion->ListarAtencionsMesasUnidas('ATENCION', $IdEmpresa, $IdCentro);
$countrowAtencionMesaUnidas = count($rowAtencionMesaUnidas);
$rsEstadomesa = $objTabla->Listar('BY-FIELD', 'ta_estadoatencion');
$countEstadoMesa = count($rsEstadomesa);
?>
<form id="form1" name="form1" method="post">
	<input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
	<input type="hidden" id="hdTipoSeleccion" name="hdTipoSeleccion" value="00" />
	<input type="hidden" id="hdTipoSave" name="hdTipoSave" value="00" />
	<input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0" />
	<input type="hidden" id="hdIdVenta" name="hdIdVenta" value="0" />
	<input type="hidden" id="hdIdAmbiente" name="hdIdAmbiente" value="0" />
	<input type="hidden" id="hdIdMesa" name="hdIdMesa" value="0" />
	<input type="hidden" id="hdIdMoneda" name="hdIdMoneda" value="0" />
	<input type="hidden" id="hdIdCliente" name="hdIdCliente" value="0" />
	<input type="hidden" id="hdIdPersonal" name="hdIdPersonal" value="0" />
	<input type="hidden" id="hdIdImpuesto" name="hdIdImpuesto" value="0" />
	<input type="hidden" id="hdTotalPedido" name="hdTotalPedido" value="0" />
	<input type="hidden" id="hdPage" name="hdPage" value="1" />
	<input type="hidden" id="hdIdCategoria" name="hdIdCategoria" value="0" />
	<input type="hidden" id="hdIdSubCategoria" name="hdIdSubCategoria" value="0" />
	<input type="hidden" id="hdEstadoMesa" name="hdEstadoMesa" value="00" />
	<input type="hidden" id="hdVista" name="hdVista" value="MESAS" />
	<div class="page-region">
        <div id="pnlMesas" class="inner-page">
        	<h1 class="title-window">
		        Atenci&oacute;n de mesas
		    </h1>
		    <div class="divContent modern-wrappanel">
            	<div id="sliderAmbientes" class="slider">
                    <a href="#" class="control_next"><i class="icon-arrow-right-3"></i></a>
                    <a href="#" class="control_prev"><i class="icon-arrow-left-3"></i></a>
                    <ul>
                        <?php
                        for ($counterAmbiente=0; $counterAmbiente < $countRowAmbiente; $counterAmbiente++) { 
                            $foundIdAmbiente = $rowAmbiente[$counterAmbiente]['tm_idambiente'];
                        ?>
                        <li data-idcontainer="<?php echo $foundIdAmbiente; ?>">
                        	<h2><?php echo $rowAmbiente[$counterAmbiente]['tm_nombre']; ?></h2>
                            <div class="mesas tile-area gridview"></div>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
        	</div>
        	<div class="clear"></div>
        </div>
        <div id="pnlProductos" class="inner-page with-title-window with-panel-search" style="display:none;">
        	<h1 class="title-window">
	        	<a id="btnBackTables" href="#" title="Regresar a mesas" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
		        <?php $translate->__('Art&iacute;culos'); ?>
		    </h1>
            <div class="panel-search">
            	<div class="input-control text" data-role="input-control">
                    <input type="text" id="txtSearch" name="txtSearch" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                    <button id="btnSearchProducts" type="button" class="btn-search" tabindex="-1"></button>
                </div>
            </div>
            <div id="precargaProd" class="divload">
            	<div id="gvProductos">
					<div class="tile-area gridview"></div>
				</div>
			</div>
		</div>
        <div id="pnlOrden" class="inner-page" style="display:none;">
			<h1 class="title-window">
	        	<a id="btnBackProducts" href="#" title="Regresar a" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
	        	<ul id="mnuNavigateSale" class="dropdown-menu" data-role="dropdown">
	                <li><a href="#" rel="mesas"><?php $translate->__('Atenci&oacute;n de Mesas'); ?></a></li>
	                <li><a href="#" rel="productos"><?php $translate->__('Productos'); ?></a></li>
	            </ul>
		        <?php $translate->__('Detalle'); ?>
		    </h1>
		    <div class="divContent">
		    	<div class="detalleComercio container-details">
					<div class="headerPedido">
						<ul>
							<li class="colProducto"><a href="#"><h4 class="fg-white"><?php $translate->__('Art&iacute;culos'); ?></h4></a></li>
							<li class="colPrecio"><a href="#"><h4 class="fg-white"><?php $translate->__('Precio'); ?></h4></a></li>
							<li class="colCantidad"><a href="#"><h4 class="fg-white"><?php $translate->__('Cantidad'); ?></h4></a></li>
							<li class="colSubTotal"><a href="#"><h4 class="fg-white"><?php $translate->__('Importe'); ?></h4></a></li>
						</ul>
					</div>
					<div class="contentPedido">
						<div class="scroll-content">
							<table>
								<tbody></tbody>
							</table>
						</div>
					</div>
		            <div class="totalbar">
		            	<div class="currency">
		            		<div class="slide-currency">
		            			<?php
		            			for ($counterMoneda=0; $counterMoneda < $countRowMoneda; ++$counterMoneda) { 
		            			?>
		            			<div title="<?php echo $rowMoneda[$counterMoneda]['tm_nombre']; ?>" rel="<?php echo $rowMoneda[$counterMoneda]['tm_idmoneda']; ?>" class="simbol-currency">
		            				<h1><?php echo $rowMoneda[$counterMoneda]['tm_simbolo']; ?></h1>
		            			</div>
		            			<?php
		            			}
		            			?>
		            		</div>
		            		<div class="buttons">
		            			<button type="button" class="upCurrency" disabled=""><i class="icon-arrow-up-4"></i></button>
		            			<button type="button" class="downCurrency"><i class="icon-arrow-down-4"></i></button>
		            		</div>
		            	</div>
		            	<div class="mount">
		            		<h1 id="totalDetails">0.00</h1>
		            	</div>
		            	<div class="clear"></div>
		            </div>
				</div>
	            <div class="clear"></div>
            </div>
            <div class="clear"></div>
		</div>
		<div id="pnlClientes" class="inner-page" style="display:none;">
			<h1 class="title-window">
				<a href="#" id="btnExitCustomer" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
				<?php $translate->__('Clientes'); ?>
			</h1>
			<div class="panel-search">
				<div class="input-control text" data-role="input-control">
                    <input type="text" id="txtSearchCliente" name="txtSearchCliente" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                    <button id="btnSearchCliente" type="button" class="btn-search" tabindex="-1"></button>
                </div>
			</div>
			<div id="precargaCli" class="divload">
            	<div id="gvClientes">
					<div class="tile-area gridview"></div>
				</div>
			</div>
		</div>
		<div id="pnlFormCliente" class="inner-page" style="display:none;"></div>
	</div>
	<div id="pnlCategoria" class="slide-options">
		<div id="lstCategorias" class="list-options">
			<h2 class="fg-white"><?php $translate->__('Categor&iacute;as'); ?></h2>
			<ul>
				<li><a href="#" class="active" rel="0"><h3 class="fg-white"><?php $translate->__('Todos'); ?></h3></a></li>
			</ul>
		</div>
		<div id="lstSubCategorias" class="list-options">
			<h2 class="fg-white"><?php $translate->__('Sub-Categor&iacute;as'); ?></h2>
			<ul>
				<li><a href="#" class="active" rel="0"><h3 class="fg-white"><?php $translate->__('Todos'); ?></h3></a></li>
			</ul>
		</div>
	</div>
	<div id="pnlEstadoMesa" class="slide-options">
		<h2 class="fg-white"><?php $translate->__('Leyenda de etapas'); ?></h2>
		<div class="slide-content">
			<div class="tile-area">
				<div class="tile bg-gray selected" data-codigo="*">
					<div class="tile-content icon">
                        <span class="icon-cycle"></span>
                    </div>
					<div class="tile-status bg-dark opacity">
						<span class="label"><?php $translate->__('TODOS'); ?></span>
					</div>
				</div>
				<?php 
				for ($counterEstadoMesa=0; $counterEstadoMesa < $countEstadoMesa; $counterEstadoMesa++) { 
				?>
				<div class="tile" data-codigo="<?php echo $rsEstadomesa[$counterEstadoMesa]['ta_codigo']; ?>" style="background-color: <?php echo $rsEstadomesa[$counterEstadoMesa]['ta_colorleyenda']; ?>">
					<div class="tile-status bg-dark opacity">
						<span class="label"><?php echo $rsEstadomesa[$counterEstadoMesa]['ta_denominacion'] ?></span>
					</div>
				</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="appbar">
		<button id="btnTomarCuenta" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/calculator.png" alt="<?php $translate->__('Tomar cuenta'); ?>" />
                <span class="text"><?php $translate->__('Tomar cuenta'); ?></span>
            </span>
        </button>
        <button id="btnCobrarPedido" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/dollar.png" alt="<?php $translate->__('Cobrar'); ?>" />
                <span class="text"><?php $translate->__('Cobrar'); ?></span>
            </span>
        </button>
        <button id="btnDividirCuenta" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/calculator.png" alt="<?php $translate->__('Dividir cuenta'); ?>" />
                <span class="text"><?php $translate->__('Opciones de venta'); ?></span>
            </span>
        </button>
		<!-- <button id="btnOpcionesVenta" type="button" class="metro_button oculto float-right">
		            <span class="content">
		                <img src="images/options.png" alt="Opciones de venta" />
		                <span class="text">Opciones de venta</span>
		            </span>
		        </button> -->
		<button id="btnReserva" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/reserve.png" alt="<?php $translate->__('Reservar'); ?>" />
                <span class="text"><?php $translate->__('Reservar'); ?></span>
            </span>
        </button>
		<button id="btnUnirMesas" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/dice.png" alt="<?php $translate->__('Unir mesas'); ?>" />
                <span class="text"><?php $translate->__('Unir mesas'); ?></span>
            </span>
        </button>
        <button id="btnBuscarArticulos" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/boxplot.png" alt="<?php $translate->__('Buscar articulos'); ?>" />
                <span class="text"><?php $translate->__('Buscar articulos'); ?></span>
            </span>
        </button>
        <button id="btnGuardarCambios" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/save.png" alt="<?php $translate->__('Guardar cambios'); ?>" />
                <span class="text"><?php $translate->__('Guardar cambios'); ?></span>
            </span>
        </button>
        <button id="btnLiberarMesa" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/refresh.png" alt="<?php $translate->__('Liberar mesa'); ?>" />
                <span class="text"><?php $translate->__('Liberar mesa'); ?></span>
            </span>
        </button>
        <button id="btnPrintOrder" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/print.png" alt="<?php $translate->__('Ver Imprimir'); ?>" />
                <span class="text"><?php $translate->__('Imprimir'); ?></span>
            </span>
        </button>
		<button id="btnViewOrder" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/tasks.png" alt="<?php $translate->__('Ver pedido'); ?>" />
                <span class="text"><?php $translate->__('Ver pedido'); ?></span>
            </span>
        </button>
		<button id="btnAddOrder" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Agregar a pedido'); ?>" />
                <span class="text"><?php $translate->__('Agregar a pedido'); ?></span>
            </span>
        </button>
        <button id="btnQuitarItem" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Quitar articulo'); ?>" />
                <span class="text"><?php $translate->__('Quitar articulo'); ?></span>
            </span>
        </button>
        <button id="btnMoreFilter" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/find.png" alt="<?php $translate->__('Mostrar filtros de b&uacute;squeda'); ?>" />
                <span class="text"><?php $translate->__('Mostrar filtros de b&uacute;squeda'); ?></span>
            </span>
        </button>
		<button id="btnClearSelection" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/icon_uncheck.png" alt="<?php $translate->__('Limpiar selecci&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Limpiar selecci&oacute;n'); ?></span>
            </span>
        </button>
        <button id="btnListaPedidos" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/layout.png" alt="<?php $translate->__('Pedidos realizados'); ?>" />
                <span class="text"><?php $translate->__('Pedidos realizados'); ?></span>
            </span>
        </button>
        <button id="btnLeyendaMesas" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/legend-info.png" alt="<?php $translate->__('Leyenda de etapas'); ?>" />
                <span class="text"><?php $translate->__('Leyenda de etapas'); ?></span>
            </span>
        </button>
	</div>
	<div id="pnlVenta" class="panelCharm modal-example-content">
		<div class="modal-example-header">
	        <h2 class="no-margin b-hide">
	        	<a id="btnHidePnlVenta" href="#" title="<?php $translate->__('Ocultar'); ?>"><i class="icon-arrow-right-3 fg-darker smaller"></i></a>
	        	Venta
	        </h2>
	        <h1 class="no-margin b-back">
	        	<a id="btnOutPnlVenta" href="#" title="<?php $translate->__('Regresar'); ?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
	        </h1>
	    </div>
	    <div class="modal-example-body">
	        <div class="grid divVenta">
	        	<div class="row">
	        		<label for="ddlTipoComprobante"><?php $translate->__('Tipo de comprobante'); ?></label>
		        	<div class="input-control select">
			        	<select name="ddlTipoComprobante" id="ddlTipoComprobante">
			        		<?php 
                            if ($countRowTipoComprobante > 0){
                                for ($counterTipoComprobante=0; $counterTipoComprobante < $countRowTipoComprobante; $counterTipoComprobante++) {
                            ?>
                            <option value="<?php echo $rowTipoComprobante[$counterTipoComprobante]['tm_idtipocomprobante']; ?>"><?php echo $rowTipoComprobante[$counterTipoComprobante]['tm_nombre']; ?></option>
                            <?php
                                }
                            }
                            ?>
			        	</select>
		        	</div>
		        </div>
	    		<div class="row">
	    			<div class="columna1">
	        			<label for="txtFechaVenta"><?php $translate->__('Fecha de venta'); ?></label>
						<div class="input-control text" data-role="datepicker" data-date="<?php echo date('Y-m-d'); ?>" data-format="dd/mm/yyyy" data-effect="fade">
                            <input id="txtFechaVenta" name="txtFechaVenta" type="text" />
                            <button type="button" class="btn-date"></button>
                        </div>
	        		</div>
	        		<div class="columna2">
	    				<label for="txtSerieComprobante"><?php $translate->__('N° de comprobante'); ?></label>
    					<div class="columna1">
    						<div class="input-control text" data-role="input-control">
	                            <input id="txtSerieComprobante" name="txtSerieComprobante" type="text" placeholder="type text" value="001" />
	                            <button class="btn-clear" tabindex="-1" type="button"></button>
	                        </div>
    					</div>
    					<div class="columna2">
    						<div class="input-control text" data-role="input-control">
	                            <input id="txtNroComprobante" name="txtNroComprobante" type="text" placeholder="type text" value="00001" />
	                            <button class="btn-clear" tabindex="-1" type="button"></button>
	                        </div>
    					</div>
	    			</div>
	    		</div>
		        <div class="clear"></div>
		    </div>
	    </div>
	    <div class="modal-example-footer">
	    	<button id="btnImprimirVenta" type="button" class="command-button warning">Imprimir</button>
	    </div>
    </div>
    <div id="pnlCuentas" class="panelCharm modal-example-content">
		<div class="modal-example-header">
	        <h2 class="no-margin b-hide">
	        	<a id="btnHidePnlCuentas" href="#" title="<?php $translate->__('Ocultar'); ?>"><i class="icon-arrow-right-3 fg-darker smaller"></i></a>
	        	Total
	        	<div class="pnlImporte float-right">
	    			<h2 id="lblMonedaVenta" class="simbolo text-center fg-darkCobalt">S/.</h2>
	    			<h2 id="lblImporteVenta" class="importe text-right fg-emerald">0.00</h2>
    			</div>
	        </h2>
	        <h1 class="no-margin b-back">
	        	<a id="btnOutPnlVenta" href="#" title="<?php $translate->__('Regresar'); ?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
	        </h1>
	    </div>
	    <div class="modal-example-body">
			<div id="sliderCuentas" class="slider">
                <a href="#" class="control_next"><i class="icon-arrow-right-3"></i></a>
                <a href="#" class="control_prev"><i class="icon-arrow-left-3"></i></a>
                <ul>
                    <li data-idcontainer="1">
                    	<h3 class="slider-header"><?php $translate->__('Cuenta 1'); ?></h3>
                        <div class="slider-content"></div>
                    </li>
                    <li data-idcontainer="2">
                    	<h3 class="slider-header"><?php $translate->__('Cuenta 2'); ?></h3>
                        <div class="slider-content"></div>
                    </li>
                    <li data-idcontainer="3">
                    	<h3 class="slider-header"><?php $translate->__('Cuenta 3'); ?></h3>
                        <div class="slider-content"></div>
                    </li>
                </ul>
            </div>
		</div>
		<div class="modal-example-footer">
			<div id="pnlFormaPago" class="modal-example-content">
				<div class="modal-example-header">
					<h3>
						<?php $translate->__('Formas de pago'); ?>
						<a href="#" class="circle-button">
							<i class="icon-menu"></i>
						</a>
					</h3>
					<ul class="dropdown">
						<li class="active" data-idformapago="1">
							<div class="container-item">
								<a href="#tab1"><?php $translate->__('Efectivo'); ?></a>
								<div class="input-control switch" data-role="input-control">
	                                <label>
	                                    SI
	                                    <input type="checkbox" checked="" />
	                                    <span class="check"></span>
	                                </label>
	                            </div>
							</div>
                        </li>
						<li data-idformapago="2">
							<div class="container-item">
								<a href="#tab2"><?php $translate->__('Tarjeta (D&eacute;bito/Cr&eacute;dito)'); ?></a>
								<div class="input-control switch" data-role="input-control">
	                                <label>
	                                    <?php $translate->__('NO'); ?>
	                                    <input type="checkbox" />
	                                    <span class="check"></span>
	                                </label>
	                            </div>
                            </div>
						</li>
						<li data-idformapago="3">
							<div class="container-item">
								<a href="#tab3"><?php $translate->__('Nota (D&eacute;bito/Cr&eacute;dito)'); ?></a>
								<div class="input-control switch" data-role="input-control">
	                                <label>
	                                    <?php $translate->__('NO'); ?>
	                                    <input type="checkbox" />
	                                    <span class="check"></span>
	                                </label>
	                            </div>
							</div>
						</li>
					</ul>
				</div>
				<div class="modal-example-body">
					<div class="tabs-panel">
						<div id="tab1" class="tab">
							<div class="grid">
								<div class="row">
					    			<div class="columna1">
					    				<label for="txtImporteRecibido"><?php $translate->__('Importe recibido'); ?></label>
					    				<div class="input-control text" data-role="input-control">
					                        <input id="txtImporteRecibido" name="txtImporteRecibido" type="text" class="text-right only-numbers" placeholder="0.00" />
					                        <button class="btn-clear" tabindex="-1" type="button"></button>
					                    </div>
					    			</div>
					    			<div class="columna2">
					    				<label for="txtImporteCambio"><?php $translate->__('Cambio o vuelto'); ?></label>
					    				<div class="input-control text" data-role="input-control">
					                        <input id="txtImporteCambio" name="txtImporteCambio" type="text" readonly="" class="text-right only-numbers" placeholder="0.00" value="" />
					                        <button class="btn-clear" tabindex="-1" type="button"></button>
					                    </div>
					    			</div>
					    		</div>
							</div>
						</div>
						<div id="tab2" class="tab">
							<div class="grid">
								<div class="row">
					    		</div>
							</div>
						</div>
						<div id="tab3" class="tab">
							
						</div>
					</div>
				</div>
				<div class="modal-example-footer">
	    			<h3 class="no-margin"><?php $translate->__('Importe de cuenta'); ?></h3>
	    			<div class="pnlImporte">
		    			<h1 id="lblMonedaCuenta" class="simbolo text-center fg-darkCobalt">S/.</h1>
		    			<h1 id="lblImporteCuenta" class="importe text-right fg-emerald">0.00</h1>
	    			</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php
include('common/libraries-js.php');
include('common/validate-js.php');
include('common/bootstrap-js.php');
?>
<script src="scripts/jquery.autogrow-textarea.js"></script>
<script>
	var monedaState = false;
	var checkState = false;
	$(window).load(function () {
		
		TipoBusqueda = '02';
		setInterval(function () {
          NotificarAtencion('NOTIFATENCION');
        }, 3000);
		$('#lstCategorias ul li:first a').on('click', function  () {
			CategoriasByDefault(this, false);
			return false;
		});
		$('#lstSubCategorias ul li:first a').on('click', function  () {
			CategoriasByDefault(this, true);
			return false;
		});
		$('.mesas').on({
			click: function (e) {
				IdMesa = $(this).attr('rel');
				IdAtencion = $(this).attr('data-idatencion');
				EstadoMesa = $(this).attr('data-state');
				$('#hdIdPrimary').val(IdAtencion);
				$('#hdIdMesa').val(IdMesa);
				$('#hdEstadoMesa').val(EstadoMesa);
				listarDetallePedido(IdAtencion);
				selectOnClickMesa();
				return false;
			},
			contextmenu: function (e) {
				selectMesas(this);
				return false;
			}
		}, '.tile');
		/*$('#pnlMesasUnidas .tile').on('click', function (e) {
			IdMesa = $(this).attr('rel');
			IdAtencion = $(this).attr('data-idatencion');
			$('#hdIdMesa').val(IdMesa);
			$('#hdIdPrimary').val(IdAtencion);
			listarDetallePedido(IdAtencion);
			selectOnClickMesa();
			return false;
		}).on('contextmenu', function (e) {
			selectMesasUnidas(this);
			return false;
		});*/
		$('#btnViewOrder').on('click', function () {
			var IdAtencion = '0';
			var IdMesa = '0';
			var EstadoMesa = '00';
			var selectedMesa = $('#pnlMesas .tile.selected');
			if ($('#pnlMesas').is(':visible')){
				
				IdAtencion = selectedMesa.attr('data-idatencion');
				IdMesa = selectedMesa.attr('rel');
				EstadoMesa = selectedMesa.attr('data-state');
				$('#hdIdPrimary').val(IdAtencion);
				$('#hdIdMesa').val(IdMesa);
				$('#hdEstadoMesa').val(EstadoMesa);
				if (IdAtencion != null)
					listarDetallePedido(IdAtencion);
			}
			VerPedido();
			return false;
		});
		$('#btnMoreFilter').on('click', function () {
			toggleSlideButton(this, '#pnlCategoria', {
				msje_active: '<?php $translate->__('Mostrar filtros de b&uacute;squeda'); ?>',
				icon_active: 'images/find.png',
				msje_deactive: '<?php $translate->__('Ocultar filtros de b&uacute;squeda'); ?>',
				icon_deactive: 'images/find-remove.png'
			});
			return false;
		});
		$('#btnAddOrder').on('click', function () {
			addDetallePedido();
			return false;
		});
		$('#btnBackTables').on('click', function () {
			backToTables();
			return false;
		});
		
		$('#btnBackProducts').on('click', function () {
			var EstadoMesa = '00';
			EstadoMesa = $('#hdEstadoMesa').val();
			if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02'))
				$('#mnuNavigateSale').fadeIn(400);
			else
				backToTables();
			return false;
		});
		$('#btnTomarCuenta').on('click', function () {
			var IdAtencion = '0';
			var IdMesa = '0';
			var EstadoMesa = '00';
			var selectedMesa = $('#pnlMesas .tile.selected');
			
			IdAtencion = selectedMesa.attr('data-idatencion');
			IdMesa = selectedMesa.attr('rel');
			EstadoMesa = selectedMesa.attr('data-state');
			$('#hdIdPrimary').val(IdAtencion);
			$('#hdIdMesa').val(IdMesa);
			$('#hdEstadoMesa').val(EstadoMesa);
			TomarCuenta();
			return false;
		});
		$('#mnuNavigateSale li a').on('click', function () {
			if ($(this).attr('rel') == 'mesas')
				backToTables();
			else {
				$('#pnlOrden').fadeOut(400, function () {
					$('#pnlProductos').fadeIn(400, function () {
						$('#hdVista').val('PRODUCTOS');
						if ($("div#gvProductos .gridview .tile").length == 0){
							CargarFiltroCategoria('0', '#lstCategorias');
							BuscarProductos('1');
						}
					});
				});
				$('#btnViewOrder, #btnMoreFilter').removeClass('oculto');
				$('#btnGuardarCambios, #btnOpcionesVenta, #btnBackToTables').addClass('oculto');
			}
		});
		$('#pnlOrden .contentPedido table tbody').on('click', 'tr td.colProducto, tr td.colPrecio, tr td.colSubTotal', function(event) {
			var parentRow = $(this).parent();
			event.preventDefault();
			selectArticulo(parentRow);
			return false;
		});
		$('#pnlOrden .contentPedido table tbody').liveDraggable({
			containment:'window',
			distance: 10,
			helper: 'clone',
			opacity: 0.55,
			zIndex: 1100
		}, 'tr');
		$('#pnlOrden .contentPedido table tbody').on('click', 'tr .button-observacion', function(event) {
			var inputControl = $(this).next().find('.input-control');
			var headerNomProducto = $(this).next().find('.nombreProducto');
			var txtObservaciones = inputControl.find('textarea');
			
			event.preventDefault();
			if ($(this).hasClass('active')) {
				inputControl.addClass('oculto');
				headerNomProducto.text(txtObservaciones.val());
				headerNomProducto.removeClass('oculto');
			} 
			else {
				inputControl.removeClass('oculto');
				txtObservaciones.focus();
				headerNomProducto.addClass('oculto');
			}
			$(this).toggleClass('active');
			return false;
		});
		$('#pnlOrden .contentPedido table tbody').on({
			click: function  (event) {
				event.preventDefault();
				return false;
			},
			focus: function () {
				$(this).autogrow();
			}
		}, 'tr textarea[rel="txtObservaciones"]');
		$('#pnlOrden .contentPedido table tbody').on({
			mouseup: function(){
				return false;
			},
			click: function () {
				$(this).select();
			},
			focus: function () {
				$(this).select();
			},
			keyup: function () {
				var precioProducto = 0;
				var cantidadProducto = 0;
				var subTotal = 0;
				var simbolo = $(this).parent().find('input[rel="hdSimboloMoneda"]').val();
				var hdSubTotal = $(this).parent().find('input[rel="hdSubTotal"]');
				var headerSubTotal = $(this).parent().parent().find('.colSubTotal').children();
				if ($(this).val().trim().length > 0){
					cantidadProducto = Number($(this).val());
					precioProducto = Number($(this).parent().find('input[rel="hdPrecio"]').val());
					subTotal = cantidadProducto * precioProducto;
					hdSubTotal.val(subTotal.toFixed(2));
					headerSubTotal.html(' ' + subTotal.toFixed(2));
				}
				else {
					hdSubTotal.val(0);
					headerSubTotal.html(' 0.00');
				}
				calcularTotal();
			},
			keypress: function (event) {
	            return /\d/.test(String.fromCharCode(event.keyCode));
			}
		}, 'tr input.inputTextInTable');
		$('.tabs-panel .tab').hide().first().show();
		
		$('#pnlFormaPago').on('click', 'a.circle-button', function () {
			displayDropDownMenu(this);
			return false;
		});
		$('#pnlFormaPago ul.dropdown').on('click', 'li a', function () {
			$(this).parent().parent().siblings('.active').removeClass('active');
			$(this).parent().parent().addClass('active');
			displayDropDownMenu('#pnlFormaPago a.circle-button');
			navigateTabs(this);
			return false;
		});
		$('#btnQuitarItem').on('click', function () {
			removeArticulos();
			return false;
		});
		$('#btnClearSelection').on('click', function () {
			resetSelection();
			return false;
		});
		$('#btnBuscarArticulos').on('click', function () {
			var IdAtencion = '0';
			var IdMesa = '0';
			var EstadoMesa = '00';
			var selectedMesa = $('#pnlMesas .tile.selected');
			
			IdAtencion = selectedMesa.attr('data-idatencion');
			IdMesa = selectedMesa.attr('rel');
			EstadoMesa = selectedMesa.attr('data-state');
			$('#hdIdPrimary').val(IdAtencion);
			$('#hdIdMesa').val(IdMesa);
			$('#hdEstadoMesa').val(EstadoMesa);
			AgregarArticulos();
			return false;
		});
		$('#btnUnirMesas').on('click', function () {
			UnirMesas();
			return false;
		});
		$('#btnListaPedidos').on('click', function () {
			window.location = '?pag=procesos&subpag=pedidos&op=list';
			return false;
		});
		$('#btnGuardarCambios').on('click', function () {
			GuardarCambios();
			return false;
		});
		 $('#txtSearch').keydown(function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER) {
                BuscarProductos('1');
                return false;
            }
        }).keypress(function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER)
                return false;
        });
        $('#btnSearchProducts').on('click', function () {
			BuscarProductos('1');
			return false;
		});
		$('#btnReserva').on('click', function () {
			Reservar();
			return false;
		});
		$('#btnLiberarMesa').on('click', function () {
			LiberarMesas();
			return false;
		});
		$('#btnDividirCuenta').on('click', function () {
			$('#lblMonedaVenta').text($('.simbol-currency:visible h1').text());
			$('#lblImporteVenta').text($('#hdTotalPedido').val());
			toggleSlidePanel('#pnlCuentas', true);
			return false;
		});
		$('#btnImprimirVenta').on('click', function () {
			/*toggleSlidePanel('#pnlVenta', false);*/
            return false;
		});
		$('#btnHidePnlVenta, #btnOutPnlVenta').on('click', function () {
			toggleSlidePanel('#pnlVenta', false);
            return false;
		});
		$('#btnHidePnlCuentas, #btnHidePnlCuentas').on('click', function () {
			toggleSlidePanel('#pnlCuentas', false);
            return false;
		});
		$('#txtImporteRecibido').on({
			keyup: function () {
				var importeRecibido = Number(($(this).val().trim().length == 0 ? '0' : $(this).val()));
				var cambioPedido = 0;
				cambioPedido =  calcularCambio(importeRecibido);
				$('#txtImporteCambio').val(cambioPedido.toFixed(2));
			},
		    mouseup: function(){
				return false;
			},
			focus: function () {
				$(this).select();
				return false;
			},
		    click: function() {
		        $(this).select();
		        return false;
		    }
		});
		$('#btnLeyendaMesas').on('click', function () {
			toggleSlideButton(this, '#pnlEstadoMesa', {
				msje_active: '<?php $translate->__('Leyenda de etapas'); ?>',
				icon_active: 'images/legend-info.png',
				msje_deactive: '<?php $translate->__('Ocultar leyenda'); ?>',
				icon_deactive: 'images/legend-info-remove.png'
			});
			return false;
		});
		$('#pnlEstadoMesa').on('click', '.tile', function () {
			var estadoMesa = $(this).attr('data-codigo');
			var selector = '#pnlMesas .tile[data-state="' + estadoMesa + '"]';
			var selectorNot = '#pnlMesas .tile[data-state!="' + estadoMesa + '"]';
			if (estadoMesa == '*')
				$('#pnlMesas .tile').show(300);
			else {
				$(selectorNot).hide(300);
				if (!$(selector).is(':visible'))
					$(selector).show(300);
			}
			$(this).siblings('.selected').removeClass('selected');
			$(this).addClass('selected');
			return false;
		});
		$('#btnCobrarPedido').on('click', function () {
			$('#form1').submit();
			return false;
		});
		$('#sliderCuentas').on('click', 'ul li:first-child + li .delete', function(event) {
			event.preventDefault();
			$(this).parent().remove();
			CalcularTotalPorCUenta();
		});
		configValidate();
		initEventGridview();
		initSlider('#sliderAmbientes', '#pnlMesas', MostrarMesas);
		initSlider('#sliderCuentas', '#pnlCuentas .modal-example-body', makeCuentaDroppable);
		$(window).resize(function () {
			resizeSlider('#sliderAmbientes', '#pnlMesas .divContent');
			resizeSlider('#sliderCuentas', '#pnlCuentas .modal-example-body', makeCuentaDroppable);
		});
	});
	function navigateTabs (anchor) {
		var href = anchor.getAttribute('href');
		$('.tabs-panel .tab').hide();
		$('.tabs-panel ' + href).show('slow', function() {
			
		});
	}
	function displayDropDownMenu (linkButton) {
		var topValue = '0';
		if ($(linkButton).hasClass('active')){
			topValue = '42px';
			$(linkButton).removeClass('active');
			$('#pnlFormaPago ul.dropdown li.active').siblings().hide(300);
		}
		else {
			topValue = '-20px';
			$(linkButton).addClass('active');
			$('#pnlFormaPago ul.dropdown li').show(300);
		}
		
		$('#pnlFormaPago ul.dropdown').animate({
			'top': topValue
		});
	}
	function configValidate () {
		$("#form1").validate({
            lang: 'es',
            rules: {
	            txtNroComprobante: {
	                required: true,
	                maxlength: 25
	            }
	        },
            showErrors: showErrorsInValidate,
            submitHandler: CobrarPedido
        });
	}
	function CobrarPedido (form) {
    	var detallePedido = '';
		var Id = $('#hdIdPrimary').val();
		var TipoSave = '00';
		var TipoSeleccion = '04';
		var IdMesa = $('#hdIdMesa').val();
		var EstadoAtencion = '07';
		var datosEnvio = '';
		detallePedido = ExtraerDetalle();
		datosEnvio = 'fnPost=fnPost&hdIdPrimary=' + Id + '&hdTipoSave=' + TipoSave + '&hdTipoSeleccion=' + TipoSeleccion + '&hdIdMesa=' + IdMesa + '&hdEstadoAtencion=' + EstadoAtencion;
		datosEnvio += $('#pnlVenta').find('input:text, select').serialize() + '&detallePedido=' + detallePedido;
		
		$.ajax({
	        type: "POST",
	        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
	        cache: false,
	        data: {
	        	fnPost: 'fnPost',
	        	hdIdPrimary: Id,
	        	hdTipoSave: TipoSave,
	        	hdTipoSeleccion: TipoSeleccion,
	        	hdIdMesa: IdMesa,
	        	hdEstadoAtencion: EstadoAtencion,
	        	detallePedido: detallePedido
	        },
	        success: function(data){
	        	var datos = eval( "(" + data + ")" );
	        	var RptaAtencion = datos.IdAtencion;
	        	var RptaVenta = datos.rpta;
	        	if (Number(RptaVenta) > 0){
					UpdateStateMesa($('#pnlMesas .tile[rel="' + IdMesa + '"]'), datos);
	        		MessageBox('<?php $translate->__('Venta realizada'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
	        			toggleSlidePanel('#pnlVenta', false);
	        			backToTables();
	        		});
	        	}
	        }
	    });
	}
	function toggleSlideButton (obj, slideSelector, params) {
		var pathIcon = '';
		var labelButton = '';
		if (!$(obj).hasClass('active')){
			pathIcon = params.icon_deactive;
			labelButton = params.msje_deactive;
			$(obj).addClass('active');
			$(slideSelector).slideDown();
		}
		else {
			pathIcon = params.icon_active;
			labelButton = params.msje_active;
			$(obj).removeClass('active');
			$(slideSelector).slideUp();
		}
		$(obj).find('.content img').attr('src', pathIcon);
		$(obj).find('.content .text').html(labelButton);
	}
	function calcularCambio (importeRecibido) {
		var totalPedido = Number($('#hdTotalPedido').val());
		var cambioPedido = 0;
		if (importeRecibido > totalPedido)
			cambioPedido = importeRecibido - totalPedido;
		else
			cambioPedido = 0;
		return cambioPedido;
	}
	function toggleSlidePanel(layout, state) {
        if (state == false) {
            if ($(layout).is(':visible')){
            	$('.control-app', parent.document).removeClass('oculto');
                $(layout).hide('slide', {'direction':'right'}, 400);
            }
        }
        else {
            if (!$(layout).is(':visible')) {
            	$('.control-app', parent.document).addClass('oculto');
                $(layout).show('slide', {'direction':'right'}, 400);
            }
        }
    }
	function resizeSlider (idLayout, parentLayout) {
        var slideItems = $(idLayout + '.slider ul li');
        var slideCount = slideItems.length;
        var slideWidth = 0;
        var sliderUlWidth = 0;
        var parentLayoutWidth = $(parentLayout).width();
        var i = 0;
        while (i < slideCount){
            slideItems[i].setAttribute("style","width:" + parentLayoutWidth + "px;");
            ++i;
        }
        slideWidth = slideItems.first().width() + 20;
        sliderUlWidth = slideCount * slideWidth;
        $(idLayout + '.slider').css({ width: slideWidth });
        $(idLayout + '.slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
    }
    function initSlider (idLayout, parentLayout, callback) {
    	var slideWidth = 0;
        resizeSlider(idLayout, parentLayout);
        
        var firstLi = $(idLayout + '.slider ul li:first-child');
        
        slideWidth = firstLi.width();
        $(idLayout + '.slider ul li:last-child').prependTo(idLayout + '.slider ul');
        firstLi = $(idLayout + '.slider ul li:first').next();
        if (typeof callback == 'function')
        	callback(firstLi.attr('data-idcontainer'));
        $(idLayout).on('click', 'a.control_prev', function () {
            $(idLayout + '.slider ul').animate({
                left: + slideWidth
            }, 200, function () {
            	var IdContainer = '0';
                $(idLayout + '.slider ul li:last-child').prependTo(idLayout + '.slider ul');
                $(idLayout + '.slider ul').css('left', '');
                if (typeof callback == 'function'){
	                IdContainer = $(idLayout + '.slider ul li:first-child').next().attr('data-idcontainer');
	                callback(IdContainer);
                }
            });
        });
        $(idLayout).on('click', 'a.control_next', function () {
            $(idLayout + '.slider ul').animate({
                left: - slideWidth
            }, 200, function () {
            	var IdContainer = '0';
                $(idLayout + '.slider ul li:first-child').appendTo(idLayout + '.slider ul');
                $(idLayout + '.slider ul').css('left', '');
                
                if (typeof callback == 'function'){
	                IdContainer = $(idLayout + '.slider ul li:first-child').next().attr('data-idcontainer');
	                callback(IdContainer);
                }
            });
        });
    }
    function makeCuentaDroppable (IdCuenta) {
    	var slider = $('#sliderCuentas ul');
		slider.on('mouseover', $('li:first-child').next().find('.slider-content'), function() {
			if (!$(this).data("init")) {
				$(this).data("init", true).droppable({
					drop: function (event, ui) {
						var html_item_section = '';
						var item = ui.draggable;
						html_item_section = '<div data-idproducto="' + item.attr('data-idproducto') + '" class="item-section">';
						html_item_section += '<span class="nombreProducto">' + item.find('.nombreProducto').text() + '</span>';
						html_item_section += '<span class="cantidad">'+item.find('.colCantidad input.inputTextInTable').val()+'</span>';
						html_item_section += '<span class="precio">'+item.find('.colSubTotal h3').text()+'</span>';
						html_item_section += '<span class="delete">&times;</span>';
						html_item_section += '</div>';
						$('#sliderCuentas ul li:first-child').next().find('.slider-content').append(html_item_section);
						
						CalcularTotalPorCUenta();
					}
				});
			}
		});
		CalcularTotalPorCUenta();
    }
    function CalcularTotalPorCUenta () {
    	var totalPorCuenta = 0;
    	var i = 0;
    	var precio = 0;
    	sliderCuentas = $('#sliderCuentas ul li:first-child').next().find('.item-section');
    	countItems = sliderCuentas.length;
    	if (countItems > 0){
	    	while(i < countItems){
	    		precio = Number($(sliderCuentas[i]).find('.precio').text());
	    		totalPorCuenta = totalPorCuenta + precio;
	    		++i;
	    	}
    	}
    	$('#lblImporteCuenta').text(totalPorCuenta.toFixed(2));
    }
    function MostrarMesas (idambiente) {
    	precargaExp('#pnlMesas .divContent', true);
    	$.ajax({
	        type: "GET",
	        url: "services/ambientes/mesas-search.php",
	        cache: false,
	        data: "tipobusqueda=ATENCION&idambiente=" + idambiente,
	        success: function(data){
	        	var datos = eval( "(" + data + ")" );
	        	var countDatos = datos.length;
	        	var i = 0;
	            var emptyMessage = '';
	            var selector = '#sliderAmbientes.slider ul li[data-idcontainer="' + idambiente + '"] .tile-area';
                var strContent = '';
                var selectedState = $('#pnlEstadoMesa .tile.selected').attr('data-codigo');
	            $(selector).html('');
	            if (countDatos > 0){
	            	while(i < countDatos){
	            		tile = $('<div class="tile dato"></div>');
	            		tile.attr({
	            			'rel' : datos[i].tm_idmesa,
	            			'data-idatencion' : datos[i].tm_idatencion,
	            			'data-state' : datos[i].ta_estadoatencion }).css('background-color', datos[i].ta_colorleyenda);
	            		strContent = '<div class="tile-content">';
			            strContent += '<div class="text-right padding10 ntp">';
			            strContent += '<h1 class="fg-white">' + datos[i].tm_codigo + '</h1>';
			            strContent += '</div></div>';
                        strContent += '<div class="brand"><span class="badge bg-dark">' + datos[i].tm_nrocomensales + '</span></div>';
                        if (selectedState != '*'){
                        	if (datos[i].ta_estadoatencion != selectedState)
                        		tile.hide();
                        }
			            tile.html(strContent).appendTo(selector);
	            		++i;
	            	}
	            }
	            else {
	            	emptyMessage = '<h2><?php $translate->__('No se encontraron resultados.'); ?></h2>';
                    $(selector).appendTo(emptyMessage);
	            }
	            precargaExp('#pnlMesas .divContent', false);
	        }
	    });
    }
	function CategoriasByDefault (obj, isSub) {
		if (!isSub)
			$('#lstSubCategorias ul li:not(:first)').remove();
		setActiveCategoria(obj);
		$('#hdIdCategoria').val('0');
        $('#hdIdSubCategoria').val('0');
		BuscarProductos('1');
	}
	function resetSelection () {
		var vista = $('#hdVista').val();
		clearSelection();
		$('#btnUnirMesas, #btnBuscarArticulos, #btnTomarCuenta').addClass('oculto');
		if (vista == 'MESAS' || vista == 'PRODUCTOS'){
			if (vista == 'MESAS'){
				$('#btnListaPedidos, #btnLeyendaMesas').removeClass('oculto');
				$('#btnViewOrder, #btnPrintOrder').addClass('oculto');
			}
			else if (vista == 'PRODUCTOS')
				$('#btnViewOrder').removeClass('oculto');
		}
		else if (vista == 'DETALLE'){
			$('#btnViewOrder').addClass('oculto');
			$('.contentPedido table tbody tr.selected').removeClass('selected');
		}
	}
	function selectOnClickMesa () {
		var EstadoMesa = '00';
		EstadoMesa = $('#hdEstadoMesa').val();
		if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02'))
			AgregarArticulos();
		else
			VerPedido('00');
	}
	function selectMesas (obj) {
		var EstadoMesa = '00';
		EstadoMesa = $(obj).attr('data-state');
		if ($('#pnlEstadoMesa').is(':visible'))
			$( "#btnLeyendaMesas" ).trigger( "click" );
		
		if($(obj).hasClass("selected")){
			$(obj).removeClass("selected");
			
			if ($('.mesas .tile.selected').length > 0){
				$('#btnClearSelection').removeClass('oculto');
				$('#btnListaPedidos, #btnLeyendaMesas').addClass('oculto');
				
				if ($('.mesas .tile.selected').length == 1){
					$('#btnViewOrder').removeClass('oculto');
					if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02'))
						$('#btnReserva, #btnBuscarArticulos').removeClass('oculto');
					else if (EstadoMesa == '07')
						$('#btnLiberarMesa, #btnPrintOrder').removeClass('oculto');
					$('#btnUnirMesas').addClass('oculto');
				}
				else {
					$('#btnTomarCuenta, #btnReserva').addClass('oculto');
					if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02')){
						$('#btnBuscarArticulos').addClass('oculto');
						$('#btnUnirMesas').removeClass('oculto');
					}
					else if (EstadoMesa == '07')
						$('#btnLiberarMesa, #btnPrintOrder').addClass('oculto');
				}
			}
			else {
				$('#btnListaPedidos, #btnLeyendaMesas').removeClass('oculto');
				$('#btnClearSelection, #btnLiberarMesa, #btnBuscarArticulos, #btnUnirMesas, #btnTomarCuenta, #btnPrintOrder, #btnViewOrder, #btnReserva').addClass('oculto');
			}
		}
		else {
			<?php 
			if ($idperfil == '4'){
			?>
			$('.mesas .tile').removeClass("selected");
			<?php
			}
			?>
			$(obj).addClass("selected");
			$('#btnClearSelection').removeClass('oculto');
			$('#btnListaPedidos, #btnLeyendaMesas').addClass('oculto');
			if ($('.mesas .tile.selected').length == 1){
				$('#btnViewOrder').removeClass('oculto');
				if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02')){
					$('#btnBuscarArticulos').removeClass('oculto');
					if (EstadoMesa == '00')
						$('#btnReserva').removeClass('oculto');
				}
				else if (EstadoMesa == '05')
					$('#btnTomarCuenta').removeClass('oculto');
				else if (EstadoMesa == '07')
					$('#btnLiberarMesa, #btnPrintOrder').removeClass('oculto');
				$('#btnUnirMesas').addClass('oculto');
			}
			else {
				if ((EstadoMesa == '00') || (EstadoMesa == '01') || (EstadoMesa == '02')){
					$('#btnBuscarArticulos').addClass('oculto');
					$('#btnUnirMesas').removeClass('oculto');
				}
				else if (EstadoMesa == '07')
					$('#btnLiberarMesa').removeClass('oculto');
				$('#btnTomarCuenta, #btnPrintOrder, #btnViewOrder, #btnReserva').addClass('oculto');
			}
		}
	}
	function selectMesasUnidas (obj) {
		if($(obj).hasClass("selected")){
			$(obj).removeClass("selected");
			if ($('#pnlMesasUnidas .tile.selected').length > 0){
				$('#btnClearSelection').removeClass('oculto');
				$('#btnListaPedidos, #btnLeyendaMesas').addClass('oculto');
				if ($('.mesas .tile.selected').length == 1){
					$('#btnBuscarArticulos, #btnViewOrder').removeClass('oculto');
					if($(obj).attr('data-state') == '00')
						$('#btnReserva').removeClass('oculto');
					else if ($(obj).attr('data-state') == '05')
						$('#btnTomarCuenta').removeClass('oculto');
					$('#btnUnirMesas').addClass('oculto');
				}
				else {
					$('#btnBuscarArticulos, #btnTomarCuenta').addClass('oculto');
				}
			}
			else {
				$('#btnClearSelection').addClass('oculto');
				$('#btnListaPedidos, #btnLeyendaMesas').removeClass('oculto');
				$('#btnBuscarArticulos').addClass('oculto');
				$('#btnUnirMesas, #btnTomarCuenta, #btnViewOrder').addClass('oculto');
			}
		}
		else {
			$(obj).siblings().removeClass("selected");
			$(obj).addClass("selected");
			$('#btnClearSelection').removeClass('oculto');
			$('#btnListaPedidos, #btnLeyendaMesas').addClass('oculto');
			if ($('#pnlMesasUnidas .tile.selected').length == 1){
				$('#btnBuscarArticulos, #btnViewOrder').removeClass('oculto');
				if($(obj).attr('data-state') == '00')
					$('#btnReserva').removeClass('oculto');
				else if ($(obj).attr('data-state') == '05')
					$('#btnTomarCuenta').removeClass('oculto');
				$('#btnUnirMesas').addClass('oculto');
			}
			else {
				$('#btnBuscarArticulos, #btnTomarCuenta, #btnViewOrder').addClass('oculto');
			}
		}
	}
	function LiberarMesas () {
		var tablesSelected = $('.mesas .tile.selected');
		var count = tablesSelected.length;
		var i = 0;
		var strMesas = '';
		var strIdAtencion = '';
		$('#hdTipoSave').val('01');
		$('#hdTipoSeleccion').val('00');
        if (count > 0){
			while(i < count){
				if (strIdAtencion.length > 0)
					strIdAtencion += ',';
				strIdAtencion += tablesSelected[i].getAttribute('data-idatencion');
				++i;
			}
			$.ajax({
		        type: "POST",
		        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
		        cache: false,
		        data: "fnPost=fnPost&strIdAtencion=" + strIdAtencion + "&hdIdPrimary=" + $('#hdIdPrimary').val() + "&hdTipoSave=01&hdEstadoAtencion=00",
		        success: function(data){
		        	var datos = eval( "(" + data + ")" );
		        	var tile = $('<div class="tile"></div>');
		        	UpdateStateMesa($(tablesSelected), datos);
		        	$('#btnLiberarMesa, #btnClearSelection #btnPrintOrder, #btnViewOrder').addClass('oculto');
		        	$('#btnLeyendaMesas, #btnListaPedidos').removeClass('oculto');
		        }
		    });
		}
		resetSelection();
	}
	function UnirMesas () {
		var tablesSelected = $('.mesas .tile.selected');
		var count = tablesSelected.length;
		var i = 0;
		var strMesas = '';
		var strIdMesas = '';
		$('#hdTipoSave').val('00');
		$('#hdTipoSeleccion').val('00');
        $('#pnlMesasUnidas h2').remove();
        if (count > 0){
			while(i < count){
				if (strIdMesas.length > 0)
					strIdMesas += ',';
				if (strMesas.length > 0)
					strMesas += ' - ';
				strIdMesas += tablesSelected[i].getAttribute('rel');
				strMesas += $(tablesSelected[i]).find('h1').text();
				++i;
			}
			$.ajax({
		        type: "POST",
		        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
		        cache: false,
		        data: "fnPost=fnPost&strListMesas=" + strIdMesas + "&hdIdPrimary=" + $('#hdIdPrimary').val() + "&hdTipoSave=00&hdTipoSeleccion=00&hdEstadoAtencion=01&hdTipoUbicacion=01",
		        success: function(data){
		        	var datos = eval( "(" + data + ")" );
		        	var tile = $('<div class="tile"></div>');
		        	var $strContent = '';
		        	UpdateStateMesa(tile, datos);
		        	tile.hide();
		        	$strContent = '<div class="tile-content"><div class="text-right padding10 ntp"><h3 class="fg-white">';
					$strContent += strMesas;
					$strContent += '</h3></div></div>';
					tile.html($strContent).appendTo('#pnlMesasUnidas').on('click', function () {
						selectOnClickMesa();
						return false;
					}).on('contextmenu', function (e) {
						selectMesasUnidas(this);
						return false;
					}).fadeIn(400);
		        }
		    });
		}
		else
			$('#pnlMesasUnidas').append('<h2><?php $translate->__('No hay mesas unidas por el momento.'); ?></h2>');
		tablesSelected.fadeOut(400, function () {
			$(this).remove();
		});
		resetSelection();
	}
	function TomarCuenta() {
		var Id = $('#hdIdPrimary').val();
		var TipoSave = '00';
		var TipoSeleccion = '03';
		var IdMesa = $('#hdIdMesa').val();
		var EstadoAtencion = '06';
		$.ajax({
	        type: "POST",
	        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
	        cache: false,
	        data: {
	        	fnPost: 'fnPost',
	        	hdIdPrimary: Id,
	        	hdTipoSave: TipoSave,
	        	hdTipoSeleccion: TipoSeleccion,
	        	hdIdMesa: IdMesa,
	        	hdEstadoAtencion: EstadoAtencion
	        },
	        success: function(data){
	        	var datos = eval( "(" + data + ")" );
	        	var RptaAtencion = datos.rpta;
	        	if (Number(RptaAtencion) > 0){
	        		UpdateStateMesa($('#pnlMesas .tile[rel="' + IdMesa + '"]'), datos);
	        		clearSelection();
	        		$('#btnListaPedidos, #btnLeyendaMesas').removeClass('oculto');
	        		$('#btnClearSelection, #btnBuscarArticulos, #btnUnirMesas, #btnTomarCuenta, #btnViewOrder, #btnReserva').addClass('oculto');
	        	}
	        }
	    });
	}
	function ExtraerDetalle () {
		var detallePedido = '';
		var listaDetalle = [];
		var idDetalle = '0';
	    var idProducto = '0';
	    var idMoneda = '0';
	    var nombreProducto = '';
	    var precio = 0;
		var cantidad = 0;
		var subtotal = 0;
		var codTipoMenuDia = '';
		var i = 0;
		
		var itemsDetalle = $('.contentPedido table tbody tr');
		var countDetalle = itemsDetalle.length;
		if (countDetalle > 0){
			while(i < countDetalle){
				idDetalle = itemsDetalle[i].getAttribute('data-iddetalle');
				idProducto = itemsDetalle[i].getAttribute('data-idproducto');
				idMoneda = itemsDetalle[i].getAttribute('data-idmoneda');
				precio = itemsDetalle[i].getAttribute('data-precio');
				codTipoMenuDia = itemsDetalle[i].getAttribute('data-tipoMenuDia');
				nombreProducto = $(itemsDetalle[i]).find('textarea[rel="txtObservaciones"]').val();
				cantidad = $(itemsDetalle[i]).find('input:text[rel="txtCantidad"]').val();
				subtotal = Number($(itemsDetalle[i]).find('input:hidden[rel="hdSubTotal"]').val()).toFixed(2);
				var detalle = new DetallePedido (idDetalle, idProducto, nombreProducto, idMoneda, cantidad, precio, subtotal, codTipoMenuDia);
				listaDetalle.push(detalle);
				++i;
			}
		}
		detallePedido = JSON.stringify(listaDetalle);
		return detallePedido;
	}
	function GuardarCambios () {
		var detallePedido = '';
		var Id = $('#hdIdPrimary').val();
		var TipoSave = '00';
		var TipoSeleccion = '01';
		var IdMesa = $('#hdIdMesa').val();
		var EstadoAtencion = '03';
		detallePedido = ExtraerDetalle();
		$.ajax({
	        type: "POST",
	        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
	        cache: false,
	        data: {
	        	fnPost: 'fnPost',
	        	hdIdPrimary: Id,
	        	hdTipoSave: TipoSave,
	        	hdTipoSeleccion: TipoSeleccion,
	        	hdIdMesa: IdMesa,
	        	hdEstadoAtencion: EstadoAtencion,
	        	hdTipoUbicacion: '00',
	        	detallePedido: detallePedido
	        },
	        success: function(data){
	        	var datos = eval( "(" + data + ")" );
	        	var RptaAtencion = datos.rpta;
	        	if (Number(RptaAtencion) > 0){
					UpdateStateMesa($('#pnlMesas .tile[rel="' + IdMesa + '"]'), datos);
	        		MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
	        			backToTables();
	        		});
	        	}
	        }
	    });
	}
	function limpiarDetalle () {
		$('.contentPedido table tbody tr').remove();
		calcularTotal();
	}
	function AgregarArticulos () {
		clearSelection();
		$('#btnListaPedidos, #btnBuscarArticulos, #btnTomarCuenta, #btnReserva, #btnUnirMesas, #btnLeyendaMesas').addClass('oculto');
		$('#btnMoreFilter, #btnViewOrder').removeClass('oculto');
		$('#pnlMesas').fadeOut(400, function () {
			if ($('#pnlEstadoMesa').is(':visible'))
				$( "#btnLeyendaMesas" ).trigger( "click" );
			$('#pnlProductos').fadeIn(400, function () {
				$('#hdVista').val('PRODUCTOS');
				if ($("#gvProductos .gridview .tile").length == 0){
					CargarFiltroCategoria('0', '#lstCategorias');
					BuscarProductos('1');
				}
			});
		});
	}
	function Reservar () {
		$('#btnListaPedidos, #btnBuscarArticulos, #btnTomarCuenta, #btnUnirMesas, #btnMoreFilter, #btnViewOrder, #btnLeyendaMesas').addClass('oculto');
		$('#pnlMesas').fadeOut(400, function () {
			$('#pnlClientes').fadeIn(400, function () {
				$('#hdVista').val('LISTACLIENTES');
				if ($("#gvClientes .gridview .tile").length == 0)
					BuscarClientes('1');
			});
		});
	}
	function VerPedido () {
		var EstadoMesa = $('#hdEstadoMesa').val();
		$('#btnMoreFilter, #btnReserva, #btnListaPedidos, #btnLeyendaMesas, #btnAddOrder, #btnBuscarArticulos, #btnViewOrder, #btnClearSelection').addClass('oculto');
		$('#btnBackToTables').removeClass('oculto');
		if ((EstadoMesa == '05') || (EstadoMesa == '06'))
			$('#btnOpcionesVenta, #btnDividirCuenta, #btnCobrarPedido').removeClass('oculto');
		else
			$('#btnGuardarCambios').removeClass('oculto');
		if ($('#pnlCategoria').is(':visible'))
			$( "#btnMoreFilter" ).trigger("click");
		if ($('#pnlMesas').is(':visible')){
			if ($('#pnlEstadoMesa').is(':visible')){
				toggleSlideButton($('#btnLeyendaMesas'), '#pnlEstadoMesa', {
					msje_active: '<?php $translate->__('Leyenda de etapas'); ?>',
					icon_active: 'images/legend-info.png',
					msje_deactive: '<?php $translate->__('Ocultar leyenda'); ?>',
					icon_deactive: 'images/legend-info-remove.png'
				});
			}
			$('#pnlMesas').fadeOut(400, function () {
				$('#pnlOrden').fadeIn(400, function () {
					$('#hdVista').val('DETALLE');
					if (monedaState == false){
						slideCurrency();
						monedaState = true;
					}
				});
			});
			$('#pnlMesas .tile.selected').removeClass('selected');
			$('#pnlMesas .tile input:checkbox').removeAttr('checked');
	        $('#pnlMesas .tile .input_spinner').hide();
		}
		else {
			if ($('#pnlCategoria').is(':visible')){
				toggleSlideButton($('#btnMoreFilter'), '#pnlCategoria', {
					msje_active: '<?php $translate->__('Mostrar filtros de b&uacute;squeda'); ?>',
					icon_active: 'images/find.png',
					msje_deactive: '<?php $translate->__('Ocultar filtros de b&uacute;squeda'); ?>',
					icon_deactive: 'images/find-remove.png'
				});
			}
			$('#pnlProductos').fadeOut(400, function () {
				$('#pnlOrden').fadeIn(400, function () {
					$('#hdVista').val('DETALLE');
					if (monedaState == false){
						slideCurrency();
						monedaState = true;
					}
				});
			});
			
		}
		//clearSelection();
		clearSelectionProductos();
	}
	function clearSelectionProductos () {
		$('#pnlProductos .tile.selected').removeClass('selected');
		$('#pnlProductos .tile input:checkbox').removeAttr('checked');
        $('#pnlProductos .tile .input_spinner').hide();
	}
	function slideCurrency () {
		$('.totalbar .currency .buttons .upCurrency').on('click', function () {
			var activeCurrency = $('.slide-currency .simbol-currency:visible');
			var prevCurrency = activeCurrency.prev();
			habilitarControl('.totalbar .currency .buttons .downCurrency', true);
			if (prevCurrency.length > 0){
				activeCurrency.slideUp();
				prevCurrency.slideDown(400, function () {
					changeCurrency(this);
					if ($(this).attr('rel') == $('.slide-currency .simbol-currency:first').attr('rel'))
						habilitarControl('.totalbar .currency .buttons .upCurrency', false);
				});
			}
			return false;
		});
		$('.totalbar .currency .buttons .downCurrency').on('click', function () {
			var activeCurrency = $('.slide-currency .simbol-currency:visible');
			var nextCurrency = activeCurrency.next();
			habilitarControl('.totalbar .currency .buttons .upCurrency', true);
			if (nextCurrency.length > 0){
				activeCurrency.slideUp();
				nextCurrency.slideDown(400, function () {
					changeCurrency(this);
					if ($(this).attr('rel') == $('.slide-currency .simbol-currency:last').attr('rel'))
						habilitarControl('.totalbar .currency .buttons .downCurrency', false);
				});
			}
			return false;
		});
	}
	function changeCurrency (obj) {
		var productsAdded = $('.contentPedido table tbody');
		var liProductsAdded = $('.contentPedido table tbody tr');
		var idsProducto = productsAdded.find('input[rel="hdIdProducto"]');
		var countProductsAdded = idsProducto.length;
		var IdCurrentMoneda = 0;
		var counterProducto = 0;
		var listProductos = '';
		var idproducto = 0;
		var arrayIdProducts = [];
		precargaExp('.page-region', true);
		if (countProductsAdded > 0){
			IdCurrentMoneda = $(obj).attr('rel');
			$('#hdIdMoneda').val(IdCurrentMoneda);
			while (counterProducto < countProductsAdded){
				idproducto = idsProducto[counterProducto].value;
				if (listProductos.length > 0)
					listProductos += ',';
				listProductos += idproducto;
				arrayIdProducts.push(idproducto);
				++counterProducto;
			}
			$.ajax({
		        type: "GET",
		        url: "services/precios/precios-search.php",
		        cache: false,
		        data: "idmoneda=" + IdCurrentMoneda + "&listProductos=" + listProductos,
		        success: function(data){
		        	var i = 0;
		        	var datos = eval( "(" + data + ")" );
		            var countDatos = datos.length;
		            var rPrecio = 0;
		            var rSubTotal = 0;
		            var rSimboloMoneda = '';
		            if (countDatos > 0){
		            	while(i < countDatos){
		            		elemProducto = $('.contentPedido table tbody tr[data-idproducto="' + datos[i].tm_idproducto + '"]');
			            	lHdIdProducto = elemProducto.find('input[rel="hdIdProducto"]');
			            	if(arrayIdProducts.indexOf( lHdIdProducto.val() )>=0){
			            		rPrecio = Number(datos[i].precio);
			            		rSimboloMoneda = datos[i].tm_simbolo;
			            		idsMoneda = lHdIdProducto.parent().find('input[rel="hdIdMoneda"]');
								cantidades = lHdIdProducto.parent().find('input[rel="txtCantidad"]');
								precios = lHdIdProducto.parent().find('input[rel="hdPrecio"]');
								subtotales = lHdIdProducto.parent().find('input[rel="hdSubTotal"]');
								simboloMoneda = lHdIdProducto.parent().find('input[rel="hdSimboloMoneda"]');
								headerPrecioProducto = lHdIdProducto.parent().parent().find('div.colPrecio h3.precio');
								headerSubTotal = lHdIdProducto.parent().parent().find('div.colSubTotal h3');
								simboloMoneda.val(rSimboloMoneda);
			            		idsMoneda.val(datos[i].tm_idmoneda);
			            		precios.val(rPrecio);
			            		rSubTotal = rPrecio * Number(cantidades.val());
			            		subtotales.val(rSubTotal.toFixed(2));
			            		headerPrecioProducto.text(rPrecio.toFixed(2));
			            		headerSubTotal.text(rSubTotal.toFixed(2));
			            	}
			            	++i;
			            }
		            }
		            calcularTotal();
		            precargaExp('.page-region', false);
		        }
		    });
		}
	}
	function backToTables () {
		limpiarDetalle();
		$('#btnListaPedidos, #btnLeyendaMesas').removeClass('oculto');
		$('#btnBackToTables, #btnBuscarArticulos, #btnUnirMesas, #btnTomarCuenta, #btnMoreFilter, #btnGuardarCambios, #btnViewOrder, #btnOpcionesVenta, #btnDividirCuenta, #btnCobrarPedido').addClass('oculto');
		
		$('#hdIdPrimary').val('0');
		$('#hdIdMesa').val('0');
		$('#hdEstadoMesa').val('00');
		if ($('#pnlProductos').is(':visible')){
			$('#pnlProductos').fadeOut(400, function () {
				if ($('#pnlCategoria').is(':visible'))
					$( "#btnMoreFilter" ).trigger( "click" );
				$('#pnlMesas').fadeIn(0, function (argument) {
					$('#hdVista').val('MESAS');
					resizeSlider('#sliderAmbientes', '#pnlMesas .divContent');
				});
			});
		}
		else {
			$('#pnlOrden').fadeOut(400, function () {
				$('#pnlMesas').fadeIn(0, function (argument) {
					$('#hdVista').val('MESAS');
					resizeSlider('#sliderAmbientes', '#pnlMesas .divContent');
				});
			});
		}
	}
	function CargarFiltroCategoria (idreferencia, idControl) {
        $(idControl + ' ul li:not(:first)').remove();
		$.ajax({
	        type: "GET",
	        url: "services/categorias/categorias-search.php",
	        cache: false,
	        data: "idref=" + idreferencia,
	        success: function(data){
	            i = 0;
	            datos = eval( "(" + data + ")" );
	            countDatos = datos.length;
	            while(i < countDatos){
                    eLi = $('<li></li>');
                    aLink = $('<a></a>')
                    aLink.attr({'href':'#', 'rel':datos[i].id});
                    content = $('<h3 class="fg-white"></h3>')
                    content.text(datos[i].value);
                    aLink.append(content).appendTo(eLi).on('click', function () {
                    	setActiveCategoria(this);
                    	if (idreferencia == '0'){
                    		$('#hdIdCategoria').val($(this).attr('rel'));
                    		$('#hdIdSubCategoria').val('0');
                    		if ($(this).attr('rel') != '0')
                    			CargarSubCategorias(this);
                    	}
                    	else
                    		$('#hdIdSubCategoria').val($(this).attr('rel'));
                        BuscarProductos('1');
                        return false;
                    });
                    $(idControl + ' ul').append(eLi);
                    ++i;
                }
	        }
	    });
	}
	function setActiveCategoria (obj) {
		$(obj).parent().parent().find('a').removeClass('active');
        $(obj).addClass('active');
	}
	function CargarSubCategorias (obj) {
		setActiveCategoria('#lstSubCategorias li:first a');
        CargarFiltroCategoria($(obj).attr('rel'), '#lstSubCategorias');
	}
	function BuscarProductos (pagina) {
        LoadProductos(TipoBusqueda, $('#hdIdCategoria').val(), $('#hdIdSubCategoria').val(), $('#txtSearch').val(), pagina);
    }
    function BuscarClientes (pagina) {
        LoadClientes('01', $('#txtSearchCliente').val(), '', pagina);
    }
    function removeArticulos () {
    	var EstadoMesa = $('#hdEstadoMesa').val();
    	$('.contentPedido table tbody tr.selected').fadeOut(400, function () {
    		var objSubTotal = $(this).find('input[rel="hdSubTotal"]');
    		var SubTotal = 0;
    		var totalPedido = 0;
    		totalPedido = Number($('#hdTotalPedido').val());
    		SubTotal = Number(objSubTotal.val());
			totalPedido = totalPedido - SubTotal;
			$('#totalDetails').text(totalPedido.toFixed(2));
			$('#hdTotalPedido').val(totalPedido.toFixed(2));
    		$(this).remove();
    	});
    	$('#pnlOrden .contentPedido table tbody tr.selected').removeClass('selected');
    	$('#btnQuitarItem, #btnClearSelection').addClass('oculto');
    	$('#btnGuardarCambios').removeClass('oculto');
    	if ((EstadoMesa == '05') || (EstadoMesa == '06'))
			$('#btnOpcionesVenta, #btnDividirCuenta, #btnCobrarPedido').removeClass('oculto');
    }
    function DetallePedido (idDetalle, idProducto, nombreProducto, idMoneda, cantidad, precio, subTotal, codTipoMenuDia) {
    	this.idDetalle = idDetalle;
    	this.idProducto = idProducto;
    	this.nombreProducto = nombreProducto;
    	this.idMoneda = idMoneda;
    	this.cantidad = cantidad;
    	this.precio = precio;
    	this.subTotal = subTotal;
    	this.codTipoMenuDia = codTipoMenuDia;
    }
    function listarDetallePedido (IdAtencion) {
    	if (IdAtencion != '0'){
    		$.ajax({
		        type: "GET",
		        url: "services/ventas/detallepedido-search.php",
		        cache: false,
		        data: "idatencion=" + IdAtencion,
		        success: function(data){
		        	var datos = eval( "(" + data + ")" );
			    	builDetallePedido('01', datos);
		        }
		    });
    	}
    }
    function addDetallePedido () {
    	Tipo = '00';
    	Origen = $("div#gvProductos > .gridview > .tile.selected");
    	builDetallePedido(Tipo, Origen);
    	$('#btnClearSelection, #btnAddOrder').addClass('oculto');
    }
	function builDetallePedido (tipo, origen) {
		var strDetalle = '';
		var idDetalle = '0';
	    var idProducto = '0';
	    var idMoneda = '0';
	    var simboloMoneda = '';
	    var nombreProducto = '';
	    var nombreCategoria = '';
	    var nombreSubCategoria = '';
	    var precio = 0;
	    var txtCantidadInTile = '';
		var cantidad = 0;
		var subtotal = 0;
		var codTipoMenuDia = '';
		var tipoMenuDia = '';
		var colorMenuDia = '';
		var colorEstado = '';
		var codEstado = '';
		var list = origen;
		var i = 0;
		var il = list.length;
		var liProductsAdded = $('.contentPedido table tbody tr');
		var idsProducto = liProductsAdded.find('input[rel="hdIdProducto"]');
		var countProductsAdded = idsProducto.length;
		var counterProducto = 0;
		var idproducto = 0;
		var arrayIdProducts = [];
		var strhtml = '';
		if (tipo != '00')
			limpiarDetalle();
		
		if (il > 0){
			if (countProductsAdded > 0){
				while (counterProducto < countProductsAdded){
					idproducto = idsProducto[counterProducto].value;
					arrayIdProducts.push(idproducto);
					++counterProducto;
				}
			}
			while (i < il){
				if (tipo == '00'){
					idProducto = list[i].getAttribute('rel');
					idMoneda = Number($(list[i]).find('.tile-status .badge .moneda').attr('rel'));
					simboloMoneda = $(list[i]).find('.tile-status .badge .moneda').text();
					nombreProducto = $(list[i]).find('.tile-status .label').text();
					nombreCategoria = $(list[i]).attr('data-nomCategoria');
					nombreSubCategoria = $(list[i]).attr('data-nomSubCategoria');
					
					colorMenuDia = $(list[i]).find('.flag_tipocarta').css('border-top-color');
					codTipoMenuDia = $(list[i]).attr('data-codTipoMenuDia');
					tipoMenuDia = $(list[i]).attr('data-tipoMenuDia');
					precio = Number($(list[i]).find('.tile-status .badge .precio').text());
					txtCantidadInTile = $(list[i]).find('.input_spinner input.inputCantidad').val();
					cantidad = txtCantidadInTile.trim().length == 0 ? 1 : Number(txtCantidadInTile);
					subtotal = precio * cantidad;
				}
				else {
					idDetalle = list[i].idDetalle;
					idProducto = list[i].idProducto;
					idMoneda = list[i].idMoneda;
					simboloMoneda = list[i].simboloMoneda;
					nombreProducto = list[i].nombreProducto;
					nombreCategoria = list[i].nombreCategoria;
					nombreSubCategoria = list[i].nombreSubCategoria;
					precio = Number(list[i].precio);
					cantidad = Number(list[i].cantidad);
					subtotal = Number(list[i].subTotal);
					codTipoMenuDia = list[i].codTipoMenuDia;
					tipoMenuDia = list[i].tipoMenuDia;
					colorMenuDia = list[i].colorMenuDia;
					codEstado = list[i].codEstado;
					colorEstado = list[i].colorEstado;
				}
				if (arrayIdProducts.indexOf(idProducto) >= 0){
			    	elemProducto = $('.contentPedido table tbody tr[data-idproducto="' + idProducto + '"]');
					cantidades = elemProducto.find('input[rel="txtCantidad"]');
					precios = elemProducto.find('input[rel="hdPrecio"]');
					subtotales = elemProducto.find('input[rel="hdSubTotal"]');
					headerSubTotal = elemProducto.find('.colSubTotal h3');
					newCantidad = Number(cantidades.val()) + cantidad;
					cantidades.val(newCantidad);
					subtotal = Number(precios.val()) * Number(cantidades.val());
					subtotales.val(subtotal.toFixed(2));
			        headerSubTotal.text(subtotal.toFixed(2));
				}
				else {
					strhtml += '<tr ';
					strhtml += 'data-iddetalle="' + idDetalle + '" ';
					strhtml += 'data-idproducto="' + idProducto + '" ';
					strhtml += 'data-idmoneda="' + idMoneda + '" ';
					strhtml += 'data-precio="' + precio.toFixed(2) + '" ';
					strhtml += 'data-tipoMenuDia="' + codTipoMenuDia + '">';
					
					strhtml += '<td class="colProducto">';
					strhtml += '<div class="input-observacion">';
					strhtml += '<button class="button-observacion"><i class="icon-pencil"></i></button>';
					strhtml += '<div class="observacion">';
					strhtml += '<div class="input-control text oculto" data-role="input-control">';
					strhtml += '<textarea rel="txtObservaciones" name="txtObservaciones[]">' + nombreProducto + '</textarea>';
					strhtml += '<button class="btn-clear" type="button"></button>';
					strhtml += '</div>';
					strhtml += '<h4 class="nombreProducto">' + nombreProducto + '</h4>';
					strhtml += '</div>';
					strhtml += '</div>';
					strhtml += '<div class="categoria">';
					strhtml += '<span class="cat">' + nombreCategoria + '</span>';
					strhtml += '<span class="subcat">' + nombreSubCategoria + '</span>';
					strhtml += '<span class="tipomenu" style="background-color: ' + colorMenuDia +'">' + tipoMenuDia + '</span>';
					strhtml += '</div>';
					strhtml += '</td>';
					strhtml += '<td class="colPrecio">';
					strhtml += '<h3 class="precio">' + precio.toFixed(2) + '</h3>';
					strhtml += '</td>';
					strhtml += '<td class="colCantidad">';
					strhtml += '<input type="hidden" rel="hdIdProducto" name="hdIdProducto[]" value="' + idProducto + '" />';
					strhtml += '<input type="hidden" rel="hdIdMoneda" name="hdIdMoneda[]" value="' + idMoneda + '" />';
					strhtml += '<input type="hidden" rel="hdSimboloMoneda" name="hdSimboloMoneda[]" value="' + simboloMoneda.trim() + '" />';
					strhtml += '<input type="hidden" rel="hdPrecio" name="hdPrecio[]" value="' + precio.toFixed(2) + '" />';
					strhtml += '<input type="hidden" rel="hdSubTotal" name="hdSubTotal[]" value="' + subtotal.toFixed(2) + '" />';
					strhtml += '<input type="text" name="txtCantidad[]" rel="txtCantidad" class="inputTextInTable" maxlength="3" value="' + cantidad + '" />';
					strhtml += '</td>';
					strhtml += '<td class="colSubTotal">';
					strhtml += '<h3>' + subtotal.toFixed(2) + '</h3>';
					strhtml += '</td>';
					strhtml += '</tr>';
				}
		      	++i;
			}
			if (strhtml.length > 0)
				$('.contentPedido table tbody').html(strhtml);
			if (tipo == '00'){
				IdMesa = $('#hdIdMesa').val();
				$.ajax({
			        type: "POST",
			        url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
			        cache: false,
			        data: {
			        	fnPost: 'fnPost',
			        	hdIdPrimary: $('#hdIdPrimary').val(),
			        	hdTipoSave: '00',
			        	hdTipoSeleccion: '02',
			        	hdIdMesa: IdMesa,
			        	hdEstadoAtencion: '02',
			        	hdTipoUbicacion: '00'
			        },
			        success: function(data){
			        	var datos = eval( "(" + data + ")" );
			        	var RptaAtencion = datos.rpta;
			        	if (Number(RptaAtencion) > 0){
			        		$('#hdIdPrimary').val(RptaAtencion);
			        		UpdateStateMesa($('.mesas .tile[rel="' + IdMesa + '"]'), datos);
			        		$.Notify({style: {background: 'green', color: 'white'}, content: "<?php $translate->__('Items agregados correctamente.'); ?>"});
			        	}
			        }
			    });
		    }
		}
		//clearSelection();
		clearSelectionProductos();
		calcularTotal();
	}
	function UpdateStateMesa (objTile, datos) {
		objTile.css({'background-color':datos.stateColor}).attr({'data-state': datos.estadoAtencion, 'data-idatencion': datos.rpta});
	}
	function selectArticulo (obj) {
		var liArticulo = $(obj);
		if (liArticulo.hasClass('selected')){
			liArticulo.removeClass('selected');
			if ($('.contentPedido table tbody tr.selected').length == 0)
				$('#btnQuitarItem, #btnClearSelection').addClass('oculto');
		}
		else {
			liArticulo.addClass('selected');
			$('#btnQuitarItem, #btnClearSelection').removeClass('oculto');
		}
	}
	function calcularTotal () {
		var i = 0;
		var list = $('#pnlOrden div.contentPedido input[rel="hdSubTotal"]');
		var il = list.length;
		var totalPedido = 0;
		var importeRecibido = Number(($('#txtImporteRecibido').val().trim().length == 0 ? '0' : $('#txtImporteRecibido').val()));
		var cambioPedido = 0;
		if (il > 0){
			while (i < il){
				totalPedido += Number(list[i].value);
				++i;
			}
		}
		$('#hdTotalPedido').val(totalPedido.toFixed(2));
		$('#totalDetails').text(totalPedido.toFixed(2));
		$('#lblMonedaVenta').text($('.simbol-currency:visible h1').text());
		$('#lblImporteVenta').text(totalPedido.toFixed(2));
		
		cambioPedido =  calcularCambio(importeRecibido);
		$('#txtImporteCambio').val(cambioPedido.toFixed(2));
	}
</script>