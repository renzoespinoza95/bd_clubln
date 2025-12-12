<?php
$barsi_inicio = $apphost . "/barsi/inicio";
$barsi_privacidad = $apphost . "/barsi/privacidad"; 
$barsi_terminos = $apphost . "/barsi/terminos";
?>

		<!-- Header -->
			<header id="header" <?php echo $mAlt ?>>
				<h1><a href="index.html">Barsi</a></h1>
				<a href="#nav">Informes</a>
			</header>

		<!-- Nav -->
			<nav id="nav">
				<ul class="links">
					<li><a href="<?php echo $barsi_inicio ?>">Presentacion</a></li>
					<li><a href="<?php echo $barsi_privacidad ?>">Privacidad</a></li>
					<li><a href="<?php echo $barsi_terminos ?>">Terminos</a></li>
				</ul>
			</nav>