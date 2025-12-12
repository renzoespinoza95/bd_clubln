<?php
boot::start();
boot::jquery2();
boot::apprise();
$redir = $apphost . "/loginVault";
?>
    
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo variables_sistema::variable_sistema("TITULO_SITIO"); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

  <body>
  </body>        
<script type="text/javascript">

$(document).ready(function() {
	termino_sesion('Su sesion ha terminado');
}); 

 
function termino_sesion(string, args) {
apprise(string, args, function(r) {
	if(r) {
    location.href = "<?php echo $redir ?>";
	} 
});
}
</script>