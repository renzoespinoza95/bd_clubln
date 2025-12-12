<?php

class tipo_acceso {

public static function lista_tipo_acceso_paginacion($start, $end)
{
$query = <<<EOF
SELECT * FROM tipo_acceso
        ORDER BY ta_id DESC
        LIMIT $start, $end
EOF;
 
$res= DB::query($query);
return $res;          
}


public static function lista_tipo_acceso_edu_combo()
{
$query = <<<EOF
SELECT * FROM tipo_acceso
        ORDER BY ta_id DESC      
EOF;
 
$listado = DB::query($query);

$res = array();

__::each($listado, function($item) use (&$res) {
    
    $agregar = array();
    
    $agregar['ta_id'] = (int)$item['ta_id'];  
    $agregar['nombre'] = utf8_encode($item['nombre']);
      
    array_push($res, $agregar);
}); 

return json_encode($res); 
}


  public static function lista_tipo_acceso_edu($start, $end)
{
$query = <<<EOF
SELECT * FROM tipo_acceso
        ORDER BY ta_id DESC
        LIMIT $start, $end     
EOF;
 
$listado = DB::query($query);

$res = array();

__::each($listado, function($item) use (&$res) {
    
    $agregar = array();    
        
    $agregar['ta_id'] = (int)$item['ta_id'];  
    $agregar['nombre'] = utf8_encode($item['nombre']);      
    
    array_push($res, $agregar);
}); 

return json_encode($res); 
}


public static function lista_tipo_acceso()
{
$query = <<<EOF
SELECT * FROM tipo_acceso
        ORDER BY ta_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_tipo_acceso_por_ta_id($ta_id)
{
$query = <<<EOF
SELECT * FROM tipo_acceso
WHERE ta_id = $ta_id
        ORDER BY ta_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_tipo_acceso_por_nombre($nombre)
{
$query = <<<EOF
SELECT * FROM tipo_acceso
WHERE nombre LIKE '%$nombre%'
        ORDER BY ta_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}


 public static  function agregar_tipo_acceso(
$nombre

)
{
    DB::insert('tipo_acceso', array(    
      'nombre' => $nombre 
));    
    $res = DB::insertId();    
    return $res;
}            
 public static function detalle_tipo_acceso($ta_id)
{            
    $query = <<<EOF
SELECT * FROM tipo_acceso WHERE ta_id = $ta_id
EOF;

    $res = DB::queryFirstRow($query);
    return $res;    
} 

public static function editar_tipo_acceso($ta_id, $nombre_campo, $valor)
{
  DB::update('tipo_acceso', array(
  $nombre_campo => $valor
  ), "ta_id=%s", $ta_id);
}  

public static function eliminar_tipo_acceso($ta_id)
{   
    DB::delete('tipo_acceso', 'ta_id=%s', $ta_id);
} 

public static function cantidad_tipo_acceso($qw = null)
{
$query = <<<EOF
SELECT count(*) as cant FROM tipo_acceso $qw 
EOF;

    $res = DB::queryFirstRow($query);
    return $res['cant'];    
}
//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++  
}


