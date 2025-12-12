<?php
$titulo = vari('TITULO_PAG_WEB');

$privacy = $apphost . "/privacy";
$terms = $apphost . "/terms";
$pagina = $apphost . "/pag/1";
$contacto = $apphost . "/contacto";

$baseFullParr = $varhost . "/" . rtrim(vari('PICS_PARR_WEB_FULL'), '/'); // p.ej. pics/parr_web/full

function render_slider_parr($parrafo, $baseFullParr) {
    $slides = [];
    foreach (['url_img01','url_img02'] as $campo) {
        if (!empty($parrafo[$campo])) {
            $src = $baseFullParr.'/'.$parrafo[$campo];
            $alt = htmlspecialchars($parrafo['titulo'] ?? 'Imagen', ENT_QUOTES, 'UTF-8');
            $slides[] = '<div class="slide"><img loading="lazy" src="'.$src.'" alt="'.$alt.'"></div>';
        }
    }
    if (!$slides) {
        // Sin imágenes: opcional mostrar un placeholder (o devolver vacío)
        return '<div class="parr-slider"><div class="slide"><img src="images/pic01.jpg" alt="Placeholder"></div></div>';
    }
    return '<div class="parr-slider">'.implode("\n", $slides).'</div>';
}

$detalle_pagina = infoPagwebPorClave($clave_txt);

$titulo_pagina = $detalle_pagina['titulo'];

$url_fondo = $varhost . "/pics/pag_web_full/" . $detalle_pagina['url_img01'];

// dd($url_fondo);
//dd($detalle_pagina);

?>
<!DOCTYPE HTML>
<html>
	<?php
	include "header.php";
	?>
	<body class="landing">

	<?php		
		$mAlt = 'class="alt"';
		include "nav.php";
	?>

		<!-- Banner -->
			<section id="banner">
				<i class="icon fa-diamond"></i>
				<h2>BARSI</h2>
				<p>
				<?php echo $titulo_pagina ?></p>
				<ul class="actions">
					<li><a href="#" class="button big special">EMPEZAR</a></li>
				</ul>
			</section>

		<!-- One -->
			<section id="one" class="wrapper style1">
				<div class="inner">
<?php
$lista_parrafos = listarParrwebPorClave($clave_txt);
foreach($lista_parrafos as $parrafo) {
?>
			<article class="feature" style="flex-direction:column!important; align-items:stretch!important;">
			  <?php echo render_slider_parr($parrafo, $baseFullParr); ?>

			  <div class="content" 
			       style="width:100%!important;">
			    <h2><?php echo $parrafo['titulo'] ?></h2>
			    <?php echo $parrafo['contenido'] ?>
			  </div>
			</article>

<?php
}
?>				

				</div>
			</section>

		<!-- Footer -->
		<?php 
		include "footer.php";
		?>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="assets/js/main.js"></script>
<?php
	boot::slick();
?>					
<script>
  jQuery(function($){
    // Inicializa todos los sliders de los artículos (hay uno por parrweb)
    $('.parr-slider').not('.slick-initialized').slick({
      dots: true,
      arrows: true,
      autoplay: true,
      autoplaySpeed: 4000,
      adaptiveHeight: true,
      infinite: false
    });
  });
</script>
<style>
  /* Ajustes visuales */
  .parr-slider { width:100%; margin:0 0 1.5em 0; }
  .parr-slider .slide { text-align:center; }
  .parr-slider img { display:inline-block; max-width:100%; height:auto; }
  #banner {
  	background-image: url("images/overlay.png"), url("<?php echo $url_fondo ?>");
  }
</style>

	</body>
</html>