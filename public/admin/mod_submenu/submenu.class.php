<?php
class submenu {

public static function lista_submenu_paginacion($start, $end)
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
Order By
  submenu.submenu_id Desc
        LIMIT $start, $end
EOF;
 
$res= DB::query($query);
return $res;          
}
public static function lista_submenu_edu_combo()
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu,  
  submenu.target 
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id 
Order By
  submenu.submenu_id Desc
EOF;
 
$listado = DB::query($query);

$res = array();

__::each($listado, function($item) use (&$res) {
    
    $agregar = array();
    
    $agregar['submenu_id'] = (int)$item['submenu_id'];
    $agregar['titulo'] = utf8_encode($item['titulo']);
    $agregar['url'] = utf8_encode($item['url']);
    $agregar['orden'] = (int)$item['orden'];
    $agregar['titulo_menu'] = utf8_encode($item['titulo_menu']);  
    $agregar['target'] = utf8_encode($item['target']);
      
    array_push($res, $agregar);
}); 

return json_encode($res); 
}

  public static function lista_submenu_edu($start, $end)
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu, 
  submenu.target 
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id 
Order By
  submenu.submenu_id Desc
        LIMIT $start, $end     
EOF;
 
$listado = DB::query($query);

$res = array();

__::each($listado, function($item) use (&$res) {
    
    $agregar = array();
    
    $agregar['submenu_id'] = (int)$item['submenu_id'];
    $agregar['titulo'] = utf8_encode($item['titulo']);
    $agregar['url'] = utf8_encode($item['url']);
    $agregar['orden'] = (int)$item['orden'];
    $agregar['titulo_menu'] = utf8_encode($item['titulo_menu']);   
    $agregar['target'] = utf8_encode($item['target']);
      

    array_push($res, $agregar);
}); 

return json_encode($res); 
}

public static function lista_submenu()
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu,
  submenu.target 
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id 
        ORDER BY submenu.orden ASC
EOF;

$res = DB::query($query);

return $res;          
}

public static function lista_submenu_por_titulo($titulo)
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
WHERE submenu.titulo LIKE '%$titulo%'
        ORDER BY submenu.submenu_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_submenu_por_url($url)
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu,
  submenu.target
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id 
WHERE submenu.url LIKE '%$url%'
        ORDER BY submenu.submenu_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_submenu_por_orden($orden)
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu,
  submenu.target 
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id 
        ORDER BY submenu.submenu_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_submenu_por_menu_id($menu_id)
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
WHERE submenu.menu_id = $menu_id
        ORDER BY submenu.submenu_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_submenu_por_target($target)
{
$query = <<<EOF
Select
  submenu.submenu_id,
  submenu.titulo,
  submenu.url,
  submenu.orden,
  menu.titulo As titulo_menu,
  submenu.target 
From
  submenu Inner Join
  menu On submenu.menu_id = menu.menu_id
WHERE submenu.target LIKE '%$target%'
        ORDER BY submenu.submenu_id DESC
EOF;
 
$res= DB::query($query);
return $res;          
}

 public static  function agregar_submenu(
$titulo,
$url,
$menu_id,
$target

)
{
    DB::insert('submenu', array(
      'titulo' => $titulo,
      'url' => $url,   
      'menu_id' => $menu_id,
      'target' => $target           
     
));    
    $res = DB::insertId();   

    //DB::query("CALL sp_generar_orden_default_submenu($res)");
    return $res;
}            
 public static function detalle_submenu($submenu_id)
{          
$query_select = self::query_select();  
    $query = <<<EOF
$query_select
WHERE submenu_id = $submenu_id
EOF;

    $res = DB::queryFirstRow($query);
    return $res;    
} 

public static function editar_submenu($submenu_id, $campo, $valor)
{
  DB::update('submenu', array(
  $campo => $valor
  ), "submenu_id=%s", $submenu_id);
}  

public static function eliminar_submernu($submenu_id)
{   
    DB::delete('submenu', 'submenu_id=%s', $submenu_id);
} 

public static function cantidad_submenu()
{
$query = <<<EOF
SELECT count(*) as cant 
FROM submenu
EOF;

    $res = DB::queryFirstRow($query);
    return $res['cant'];    
}

public static function cantidad_submenu_por_menu_id($menu_id)
{
$query = <<<EOF
SELECT count(*) as cant 
FROM submenu
WHERE
submenu.menu_id = $menu_id
EOF;

    $res = DB::queryFirstRow($query);
    return $res['cant'];    
}

public static function lista_menu_solo_padres($tipo_administrador_id)
{
$query = <<<EOF
Select
  menu.menu_id,
  menu.titulo,
  menu.orden,
  menu.padre,
  menu.tipo_administrador_id  
From 
  menu 
  WHERE
  menu.padre = 0   
  AND
  menu.tipo_administrador_id = 1   
  ORDER BY menu.menu_id ASC  
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_menu_por_padre($padre)
{
$query = <<<EOF
Select
  menu.menu_id,
  menu.titulo,
  menu.orden  
From 
  menu 
  WHERE
  menu.padre = $padre
        ORDER BY menu.menu_id ASC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_target()
{
  $lista = array();

  $item = array(
    'cod_target' => '_self',
    'descripcion' => '_self'
  );
  array_push($lista, $item);

  $item = array(
    'cod_target' => '_blank',
    'descripcion' => '_blank'
  );
  array_push($lista, $item);

  return $lista;
}

public static function eliminar_submenu($submenu_id)
{   
    DB::delete('submenu', 'submenu_id=%s', $submenu_id);
} 

public static function query_select()
{

$query = <<<EOF
Select
  menu.menu_id,
  menu.titulo as menu_titulo,
  menu.orden,
  menu.tipo_administrador_id,
  submenu.submenu_id,
  submenu.titulo As submenu_titulo,
  submenu.url,
  submenu.orden As submenu_orden,
  submenu.target
From
  menu Inner Join
  submenu On menu.menu_id = submenu.menu_id
EOF;

return $query;

}

//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++  
}


