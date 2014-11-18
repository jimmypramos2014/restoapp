<div id="pnlConfig" class="page-region">
	<div class="divload">
		<div class="moduloTwoPanel">
			<div class="colTwoPanel1">
				<h1 class="title-config"><?php $translate->__('Configuraci&oacute;n'); ?></h1>
				<div class="links-config">
					<ul>
						<li><a data-tab="tab1" href="?pag=settings&subpag=moneda" class="active"><h2><?php $translate->__('Moneda'); ?></h2></a></li>
						<li><a data-tab="tab2" href="?pag=settings&subpag=impuestos"><h2><?php $translate->__('Impuestos'); ?></h2></a></li>
						<li><a data-tab="tab3" href="?pag=settings&subpag=tipos-comprobante"><h2><?php $translate->__('Tipos de comprobante'); ?></h2></a></li>
						<li><a data-tab="tab4" href="?pag=settings&subpag=medios-pago"><h2><?php $translate->__('Medios de pago'); ?></h2></a></li>
						<li><a data-tab="tab5" href="?pag=settings&subpag=documentos-identidad"><h2><?php $translate->__('Documentos de identidad'); ?></h2></a></li>
						<li><a data-tab="tab6" href="?pag=settings&subpag=ubicaciones"><h2><?php $translate->__('Ubicaciones'); ?></h2></a></li>
						<li><a data-tab="tab7" href="?pag=settings&subpag=usuarios-perfiles"><h2><?php $translate->__('Usuarios y perfiles'); ?></h2></a></li>
						<li><a data-tab="tab8" href="?pag=settings&subpag=migraciones"><h2><?php $translate->__('Migraciones'); ?></h2></a></li>
					</ul>
				</div>
			</div>
			<div class="colTwoPanel2">
				<div class="panels">
					<iframe data-tab="tab1" src="?pag=settings&subpag=moneda" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="100%"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include('common/libraries-js.php');
?>
<script>
	$(document).ready(function(){
		$('.links-config ul').on('click', 'a', function () {
			$('.links-config a').removeClass('active');
			$(this).addClass('active');
			navigateInFrame(this);
			return false;
		});
	});
	function navigateInFrame (alink) {
		var url = alink.getAttribute('href');
		var tab = alink.getAttribute('data-tab');
		var iframe = '<iframe data-tab="' + tab + '" src="' + url + '" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="100%"></iframe>';
		$('.panels iframe').hide();
		if ($('.panels > iframe[data-tab="' + tab + '"]').length == 0){
			$(iframe).appendTo('.panels').load(function () {
				blockLoadWin(false);
                $(this).contents().find("body, body *").on('click', function(event) {
                    window.top.hideAllSlidePanels();
                });
			});
		}
		else
			$('.panels > iframe[data-tab="' + tab + '"]').show();
	}
</script>