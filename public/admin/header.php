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
    boot::vuejs2();
    boot::vue_select();
    boot::apprise();
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
    h2::todohost($apphost, $varhost);
    boot::datatables();
    boot::summernote();
?>		    
  </head>
<style>
  /* Bootstrap 2.3.2: el modal debe estar sobre el backdrop */
  .modal { position: fixed; z-index: 1060 !important; }
  .modal.fade.in { z-index: 1060 !important; }
  .modal-backdrop { z-index: 1050 !important; }

  /* opcional: si tienes algún overlay de facebox, mantenlo debajo */
  #facebox, #facebox .popup { z-index: 1020 !important; }
  #facebox_overlay { z-index: 1015 !important; }
</style>
<script>
  $(function () {
    var $m = $('#modalBusquedaMenu');
    // En BS 2.3 el evento es 'show'
    $m.on('show', function () {
      $(this).appendTo('body');   // evita stacking context del contenedor
    });
    // Limpieza por si quedaron backdrops huérfanos en algún cierre previo
    $m.on('hidden', function () {
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
    });
  });
</script>

  