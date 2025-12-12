<script type="text/javascript">
           
$('.link_eliminar_submenu').click(function(e){
  e.preventDefault();
  var submenu_id = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el submenu_id:' + submenu_id, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(config_blockui);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarSubmenu" ?>',
                        dataType: "html",
                        data: 'submenu_id=' + submenu_id,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_submenu_id_' + submenu_id).fadeOut();                                                       
                        }
                    });
            //--
             }
    //--
    });
  
});  

/* tabla_js.php  (o donde cargues tus scripts) */
$(function () {

  var $modal       = $('#modalOrdenSubmenu');
  var $inputOrden  = $('#inputOrden');
  var $btnGuardar  = $('#btnGuardarOrden');
  var $btnActivo;              // botón que disparó el modal

  /* 1.  Mostrar modal con valor actual */
  $(document).on('click', '.btn-orden', function () {
      $btnActivo   = $(this);
      var orden    = $btnActivo.data('orden');
      $inputOrden.val(orden);
      $modal.modal('show');
  });

  /* 2.  Enviar POST y refrescar UI */
  $btnGuardar.on('click', function () {

      var nuevoOrden = parseInt($inputOrden.val(), 10);
      if (isNaN(nuevoOrden) || nuevoOrden < 1) {
          alert('Ingrese un número válido');
          return;
      }

      var payload = {
          submenu_id : $btnActivo.data('submenu-id'),
          orden      : nuevoOrden
      };

      $.ajax({
          url         : apphost + '/admin/actualizarOrdenSubmenu',
          type        : 'POST',
          contentType : 'application/json; charset=utf-8',
          dataType    : 'json',
          data        : JSON.stringify(payload),

          success : function (resp) {
              if (resp.success) {
                  /* Actualiza atributo y texto del botón */
                  $btnActivo.data('orden', nuevoOrden)
                            .find('.fa')             // deja el ícono
                            .next()                  // nodo de texto
                            .remove();               // bórralo para reemplazar

                  $btnActivo.append(' ' + nuevoOrden);
              } else {
                  alert('No se pudo actualizar.');
              }
              $modal.modal('hide');
          },
          error : function () {
              alert('Error de comunicación con el servidor.');
          }
      });
  });

});

</script>  