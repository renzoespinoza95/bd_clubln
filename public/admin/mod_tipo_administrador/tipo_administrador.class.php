<?php

class tipo_administrador {

public static function lista_tipo_administrador_paginacion($start, $end)
{
    $query_select = self::query_select();
    
$query = <<<EOF
$query_select
        ORDER BY tipo_administrador_id DESC
        LIMIT $start, $end
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_tipo_administrador_edu_combo()
{
$query = <<<EOF
SELECT * FROM tipo_administrador
        ORDER BY tipo_administrador_id DESC
EOF;
 
$listado = DB::query($query);

$res = array();

__::each($listado, function($item) use (&$res) {
    
    $agregar = array();
    
    $agregar['tipo_administrador_id'] = (int)$item['tipo_administrador_id'];
    $agregar['descripcion'] = utf8_encode($item['descripcion']);
    $agregar['is_activo'] = (int)$item['is_activo'];   
      
    array_push($res, $agregar);
}); 

return json_encode($res); 
}

public static function lista_tipo_administrador()
{
$query = <<<EOF
SELECT * FROM tipo_administrador
        ORDER BY tipo_administrador_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

 public static  function agregar_tipo_administrador(
$descripcion, 
$is_activo 
)
{
    DB::insert('tipo_administrador', array(
      'descripcion' => $descripcion,
      'is_activo' => $is_activo
));    
    $res = DB::insertId();    
    return $res;
}            
 public static function detalle_tipo_administrador($tipo_administrador_id)
{            
    $query_select = self::query_select();
    $query = <<<EOF
$query_select
WHERE tipo_administrador_id = $tipo_administrador_id
EOF;

    $res = DB::queryFirstRow($query);
    return $res;    
} 

public static function editar_tipo_administrador($tipo_administrador_id, $nombre_campo, $valor)
{
  DB::update('tipo_administrador', array(
  $nombre_campo => $valor
  ), "tipo_administrador_id=%s", $tipo_administrador_id);
}  

public static function eliminar_tipo_administrador($tipo_administrador_id)
{   
    DB::delete('tipo_administrador', 'tipo_administrador_id=%s', $tipo_administrador_id);
} 

public static function cantidad_tipo_administrador($qw = null)
{
$query = <<<EOF
SELECT count(*) as cant FROM tipo_administrador $qw 
EOF;

    $res = DB::queryFirstRow($query);
    return $res['cant'];    
}

public static function is_activo_tipo_administrador($tipo_administrador_id, $valor)
{

  DB::update("tipo_administrador", array(
    'is_activo' => $valor 
  ),"tipo_administrador_id=%s", $tipo_administrador_id);

}

public static function query_select()
{

$query = <<<EOF
Select
  tipo_administrador.tipo_administrador_id,
  tipo_administrador.descripcion,
  tipo_administrador.is_activo,
  tipo_administrador.submenu_inicio,
  sub.titulo
From
  tipo_administrador
LEFT JOIN
(
SELECT * FROM submenu
) sub
ON
sub.submenu_id = tipo_administrador.submenu_inicio
EOF;

return $query;

}

//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++  
}


