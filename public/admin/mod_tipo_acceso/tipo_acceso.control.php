<?php

$app->get('/admin/listaTipoAcceso', 'g_admin_listaTipoAcceso');
function g_admin_listaTipoAcceso()
{
  include DEFINITION;
  login_admin::autentificar_administrador();    
  include $path_public . "/admin/mod_tipo_acceso/inicio.php";
} 


$app->get('/admin/agregarTipoAcceso', 'g_admin_agregarTipoAcceso');
function g_admin_agregarTipoAcceso()
{
  include DEFINITION;
  login_admin::autentificar_administrador();
  
  include $path_public . "/admin/mod_tipo_acceso/agregar_tipo_acceso.php";
}
    
$app->post('/admin/agregarTipoAcceso','p_admin_agregarTipoAcceso');
function p_admin_agregarTipoAcceso()
{
  include DEFINITION;
  login_admin::autentificar_administrador();
 
  $post_enviado = array_map('trim', $_POST);
  extract($post_enviado); 
 
  tipo_acceso::agregar_tipo_acceso(
  util::guardar_palabra_latina($txt_nombre)
);
  
  $app->redirect($apphost . '/admin/listaTipoAcceso');
}

$app->post('/admin/buscarTipoAcceso','p_admin_buscarTipoAcceso');
function p_admin_buscarTipoAcceso()
{
  include DEFINITION;  
 
  $post_enviado = array_map('trim', $_POST);
  extract($post_enviado); 

  include $path_public . "/admin/mod_tipo_acceso/inicio_buscar.php";
}

$app->get('/admin/editarTipoAcceso/:ta_id', 'g_admin_editarTipoAcceso');
function g_admin_editarTipoAcceso($ta_id)
{
  include DEFINITION;
  login_admin::autentificar_administrador();
  $detalle_tipo_acceso = tipo_acceso::detalle_tipo_acceso($ta_id);  
  include $path_public . "/admin/mod_tipo_acceso/tipo_acceso_editar.php";
} 

           
$app->post('/admin/editarTipoAcceso','p_admin_editarTipoAcceso');
function p_admin_editarTipoAcceso()
{
  include DEFINITION;  
  
  $post_enviado = array_map('trim', $_POST);
  extract($post_enviado);
  
  tipo_acceso::editar_tipo_acceso($txt_ta_id, 'nombre' , util::guardar_palabra_latina($txt_nombre)); 

 $app->redirect($apphost . '/admin/listaTipoAcceso');
 
}  

$app->post('/admin/eliminarTipoAcceso', 'p_admin_eliminarTipoAcceso');
function p_admin_eliminarTipoAcceso()
{
  include DEFINITION;
  
  $post_enviado = array_map('trim', $_POST);
  extract($post_enviado); 
  
 tipo_acceso::eliminar_tipo_acceso($ta_id);
}


$app->post('/admin/eliminarVariosTipoAcceso','p_eliminarVariosTipoAcceso');
function p_eliminarVariosTipoAcceso()
{
  include DEFINITION;
   
  extract($_POST); 
  
  
  __::each($info, function($item) {
      
      tipo_acceso::eliminar_tipo_acceso($item);            
      
  });
  
}


$app->post('/admin/eduTipoAccesoNombre','p_admin_eduTipoAccesoNombre');   
function p_admin_eduTipoAccesoNombre()
{
  include DEFINITION;
 
  $post_enviado = array_map('trim', $_POST);
  extract($post_enviado); 

  tipo_acceso::editar_tipo_acceso($txt_edu_ta_id, "nombre", 
    util::guardar_palabra_latina($txt_edu_nombre));
  
 }