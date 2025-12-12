<?php
 
 class login_admin{
 
public static function informacion_administrador_por_id($administrador_id)
{        
$query_select = administradortbl::query_select();                                                                                     
  $query = <<<EOF
$query_select
WHERE
  administradortbl.administrador_id = '$administrador_id' AND
  administradortbl.is_activo = 1
EOF;

    $res = DB::queryFirstRow($query);   
    
    return $res;
}

public static function informacion_administrador_por_email($email)
{                                                                         
  $query =<<<EOF
  SELECT * from administradortbl
             WHERE is_activo = true
             AND email = '$email';
EOF;

  $res = DB::queryFirstRow($query);           
  return $res;           
}

public static function existe_administrador($email)
{                                                                 
$query =<<<EOF
        SELECT COUNT(*) from administradortbl
        WHERE is_activo = true
        AND email = '$email';
EOF;

  $res = DB::queryFirstRow($query);
  return $res;          
}

public static function existe_usuario($email)
{                                                                 
$query =<<<EOF
      SELECT COUNT(*) from usuariostbl
      WHERE is_delete = false
      AND email = '$email';
EOF;

  $res = DB::queryFirstRow($query);
  return $res;          
}


public static function informacion_usuario_por_id($usuario_id)
{                                                               
            
$query = <<<EOF

      SELECT * FROM clientestbl
      WHERE is_delete = false
      AND cliente_id = '$usuario_id'
EOF;
            
  $res = DB::queryFirstRow($query);
  return $res;
}

public static function informacion_usuario_por_email($email)
{

  $query = <<<EOF
       SELECT * FROM usuariostbl
       WHERE is_delete = false
       AND email = '$email';  
EOF;

$res = DB::queryFirstRow($query);
return $res;
}


public static function cantidad_visitas()
{
$res = variables_sistema::detalle_variables_sistema("VISITAS");

return $res['valor']; 
}

public static function agregar_visita()
{
  $cant_visitas = login_admin::cantidad_visitas();
  DB::update('variables_sistema', array(
    'valor' => $cant_visitas + 1
  ),'nombre_variable=%s','VISITAS');
}

public static function imagen_vacio()
{
  include DEFINITION;
  $imagen = $varhost_data_web . "/adm/imagen_vacio.png";
  
  $aplicar = <<<EOF
    <img width="50px" alt="imagen-vacio" src="$imagen">
EOF;
    echo $aplicar;    
    n();
} 

public static function autentificar_administrador()
{
  include DEFINITION;
 
  if(empty($sesion_admin_administrador_id)) {
    include $path_public . "/admin/fin_sesion.php";
    exit;
  }

  return $sesion_admin_administrador_id;
}

public static function verificar_datos_usuarios($email, $clavel)
{

$query = <<<EOF
SELECT COUNT(administrador_id) AS cant 
FROM administradortbl 
WHERE email = %s AND clavel = %s;
EOF;

$res = DB::queryFirstRow($query, $email, $clavel);
return $res['cant'];
}


//==========
// FIN CLASS                   
//========== 
 
 }