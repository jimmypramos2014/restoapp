<?php

include('bussiness/monedas.php');

$objData = new clsMoneda();

$counter = 0;

$rs = $objData->Listar('L', '');

$count = count($rs);

?>

<form id="form1" name="form1" method="post">

    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />

    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />

    <input type="hidden" id="hdPage" name="hdPage" value="1" />

    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0">

    <input type="hidden" id="hdFoto" name="hdFoto" value="no-set">

    <div class="page-region">

        <div id="pnlMoneda" class="inner-page">

            <div class="listview">

            <?php if ($count > 0): ?>

            <?php for ($counter=0; $counter < $count; $counter++): ?>

            <a href="#" class="list">

                <div class="list-content">

                    <img src="images/onenote2013icon.png" class="icon">

                    <div class="data">

                        <span class="list-title">Word 2013</span>

                        <div class="progress-bar small" data-role="progress-bar" data-value="75"><div class="bar bg-cyan" style="width: 75%;"></div></div>

                    </div>

                </div>

            </a>

            <?php endfor; ?>

            <?php else: ?>

            <h2><?php $translate->__('No se encontraron monedas'); ?></h2>

            <?php endif; ?>

            </div>

        </div>

    </div>

</form>

<?php

include('common/libraries-js.php');

?>