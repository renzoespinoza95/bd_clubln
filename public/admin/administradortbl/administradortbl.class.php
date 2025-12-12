<?php

class administradortbl {  

public static function lista_administradores()
{

$query_select = self::query_select();    
   $query=<<<EOF
$query_select
EOF;

$res =  DB::query($query);  
  return $res;  
}

public static function detalle_administrador($administrador_id)
{
  $query =<<<EOF
            SELECT * FROM administradortbl
            WHERE administrador_id = $administrador_id
EOF;
  
    $res = DB::queryFirstRow($query);
    return $res;

}

public static function agregar_administrador(
                $nombres_apellidos,
                $email,
                $password,
                $fecha_creacion,
                $fecha_ultimo_acceso,
                $is_activo,
				$tipo_administrador_id           
				)
{
    DB::insert('administradortbl', array(
        'nombres_apellidos' => $nombres_apellidos,
        'email' => $email,
        'clavel' => $password,
        'fecha_creacion' => $fecha_creacion,
        'fecha_ultimo_acceso' => $fecha_ultimo_acceso,
        'is_activo' => $is_activo,
    	'tipo_administrador_id' =>$tipo_administrador_id,   
	));
    
    $res = DB::insertId();
    
    return $res;    
}

public static function editar_administrador($administrador_id, $nombre_campo, $valor)
{
  DB::update('administradortbl', array(
  $nombre_campo => $valor
  ), "administrador_id=%s", $administrador_id);
} 

public static function eliminar_administradortbl($administrador_id)
{   
    DB::delete('administradortbl', 'administrador_id=%s', $administrador_id);
} 

public static function is_activo_administradortbl($administrador_id, $valor)
{

  DB::update("administradortbl", array(
    'is_activo' => $valor 
  ),"administrador_id=%s", $administrador_id);

}

public static function accesos_usuario($administrador_id) 
{
    $detalle_administrador = administradortbl::detalle_administrador($administrador_id);
    $tipo_administrador_id = $detalle_administrador['tipo_administrador_id'];
    
    if($tipo_administrador_id == 1) {
        $menu_administrador = menu::lista_menu();
    } else {
        $menu_administrador = menu::lista_menu_tipo_administrador($tipo_administrador_id);
    }
    
     
    $res = array();
    
    __::each($menu_administrador, function($item) use (&$res, &$tipo_administrador_id) {
    
            $agregar = array();

            $agregar['menu_id'] = (int)$item['menu_id'];    
            $agregar['titulo'] = $item['titulo']; 
            $agregar['orden'] = (int)$item['orden'];

            if($tipo_administrador_id == 1 ) {
                $agregar['lista_submenu'] = submenu::lista_submenu(" WHERE submenu.menu_id = " . $item['menu_id']);
            } else {
                $agregar['lista_submenu'] = submenu::lista_submenu_tipo_administrador(
                    $item['tipo_administrador_id'], 
                    $item['menu_id']);
            }
            

            array_push($res, $agregar);
    }); 

    return $res;  
}


public static function query_select()
{

$query = <<<EOF
Select
  administradortbl.administrador_id,
  administradortbl.nombres_apellidos,
  administradortbl.email,
  administradortbl.clavel,
  administradortbl.fecha_creacion,
  administradortbl.fecha_ultimo_acceso,
  administradortbl.is_activo,
  administradortbl.tipo_administrador_id,
  tipo_administrador.descripcion,
  tipo_administrador.submenu_inicio,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  submenu.menu_id,
  submenu.target
From
  administradortbl Inner Join
  tipo_administrador On administradortbl.tipo_administrador_id =
    tipo_administrador.tipo_administrador_id Inner Join
  submenu On tipo_administrador.submenu_inicio = submenu.submenu_id
EOF;

return $query;

}


//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++  
}