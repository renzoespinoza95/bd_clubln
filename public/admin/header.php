<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo variables_sistema::variable_sistema("TITULO_SITIO"); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?php echo isset($mBase) ? $mBase : '' ?>">

<style>
  body {
    padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
  }
</style>

<?php    
    boot::start();
    boot::favicon();
    boot::font_awesome();
    boot::jquery2();
    boot::js();
    boot::facebox();
    boot::apprise();
    boot::block_ui();
    boot::perso();
    boot::notify();
    h2::todohost($apphost, $varhost, $apihost);
?>		    
  </head>