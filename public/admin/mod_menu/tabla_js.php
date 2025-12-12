<script type="text/javascript">

$('.link_eliminar_menu').click(function(e){
  e.preventDefault();
  var menu_id = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el menu_id:' + menu_id, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(init);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarMenu" ?>',
                        dataType: "html",
                        data: 'menu_id=' + menu_id,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_menu_id_' + menu_id).fadeOut();
                        }
                    });
            //--
             }
    //--
    });
  
});  

$(function () {

  var $modal      = $('#modalOrdenMenu');
  var $inputOrden = $('#inputOrdenMenu');
  var $btnGuardar = $('#btnGuardarOrdenMenu');
  var $btnActivo;                      // botón que abrió el modal

  /* 1. Mostrar modal con el valor actual */
  $(document).on('click', '.btn-orden-menu', function () {
      $btnActivo = $(this);
      $inputOrden.val($btnActivo.data('orden'));
      $modal.modal('show');
  });

  /* 2. Enviar POST y actualizar la UI */
  $btnGuardar.on('click', function () {

      var nuevoOrden = parseInt($inputOrden.val(), 10);
      if (isNaN(nuevoOrden) || nuevoOrden < 1) {
          alert('Ingrese un número válido.');
          return;
      }

      var payload = {
          menu_id : $btnActivo.data('menu-id'),
          orden   : nuevoOrden
      };

      $.ajax({
          url         : apphost + '/admin/actualizarOrdenMenu',
          type        : 'POST',
          contentType : 'application/json; charset=utf-8',
          dataType    : 'json',
          data        : JSON.stringify(payload),
          success     : function (resp) {
              if (resp.success) {
                  // actualiza atributo y texto
                  $btnActivo
                      .data('orden', nuevoOrden)
                      .contents().filter(function () { return this.nodeType === 3; }).remove(); // quita texto viejo
                  $btnActivo.append(' ' + nuevoOrden);       // agrega texto nuevo
                  msg_exito();
              } else {
                  // alert('No se pudo actualizar.');
                msg_error();
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