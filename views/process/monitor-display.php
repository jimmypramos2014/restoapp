<?php
include('bussiness/mesas.php');

$IdEmpresa = 1;
$IdCentro = 1;
$counterMesa = 0;

$objMesa = new clsMesa();

$rowMesa = $objMesa->Listar('TV', $IdEmpresa, $IdCentro);
$countRowMesa = count($rowMesa);
?>
<div class="page-region bg-black">
    <div class="divContent">
        <div class="displayMonitor">
            <div class="monitorVideo">
                <div id="player"></div>
            </div>
        </div>
        <div id="pnlMesas" class="displayTables">
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
<?php
include('common/libraries-js.php');
?>
<script src="http://www.youtube.com/iframe_api"></script>
<script>
	var videoArray = ['at3FPJaAwoY', 'IxxstCcJlsc', 'dGghkjpNCQ8', 'RVmG_d3HKBA', 'NF-kLy44Hls', 'Qc9c12q3mrc', 'RcZn2-bGXqQ', 'CevxZvSJLk8', '5dbEhBKGOtY'];
    var index = 0;
    var player;

	$('document').ready(function() {
        resizeDisplay();
        $(window).resize(function () {
            resizeDisplay();
        });

        setInterval(function () {
            NotificarAtencion('NOTIFTV');
        }, 3000);
	});

    window.onYouTubeIframeAPIReady = function() {
      setVideo(0);
    }

    function resizeDisplay () {
        var heightTables = $('.displayTables').height();
        var windowHeight = document.documentElement.offsetHeight;
        var heightMonitor = 0;
        heightMonitor = windowHeight - heightTables;
        $('.displayMonitor').height(heightMonitor);
    }

	function setVideo (index) {
        if (index == videoArray.length)
            index = 0;
        player = new YT.Player('player', {
            width: 720,
            height: 480,
            videoId: videoArray[index],
            playerVars: {
                controls: 0,
                showinfo: 0,
                modestbranding: 1,
                wmode: 'transparent',
                iv_load_policy: 3
            },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    window.onPlayerReady = function(e) {
        e.target.seekTo(0);
        e.target.playVideo();
    }

    window.onPlayerStateChange = function(state) {
        if (state.data === 0) {
            index += 1;
            window.player.destroy();
            window.player == null;
            setVideo(index);
        }
    }
</script>