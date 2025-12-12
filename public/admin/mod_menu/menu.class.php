<?php

class menu {

public static function lista_menu_clasico()
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_menu()
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_menu_por_tipo_administrador_id($tipo_administrador_id)
{
  $query_select = self::query_select();
$query = <<<EOF
$query_select
WHERE 
menu.tipo_administrador_id = $tipo_administrador_id
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
  menu.orden,
  tipo_administrador.tipo_administrador_id,
  tipo_administrador.descripcion as tipo_administrador_descripcion
From
  tipo_administrador Inner Join
  menu On menu.tipo_administrador_id = tipo_administrador.tipo_administrador_id
  WHERE
  menu.padre = $padre
        ORDER BY menu.menu_id ASC
EOF;
 
$res= DB::query($query);
return $res;          
}

public static function lista_menu_tipo_administrador($tipo_administrador_id)
{
$query = <<<EOF
Select
  menu.menu_id,
  menu.titulo,
  menu.orden,
  tipo_administrador.tipo_administrador_id,
  tipo_administrador.descripcion as tipo_administrador_descripcion
From
  tipo_administrador Inner Join
  menu On menu.tipo_administrador_id = tipo_administrador.tipo_administrador_id
  WHERE menu.tipo_administrador_id = $tipo_administrador_id 
        ORDER BY menu.menu_id ASC
EOF;
 
$res= DB::query($query);
return $res;          
}  

public static function detalle_menu($menu_id)
{            
    $query = <<<EOF
    SELECT * FROM menu WHERE menu_id = $menu_id
EOF;

    $res = DB::queryFirstRow($query);
    return $res;    
}

public static function agregar_menu($titulo,$tipo_administrador_id)
{
    DB::insert('menu', array(
      'titulo' => $titulo,
      'padre' => 0,
      'orden' => 99,
	  'tipo_administrador_id' => $tipo_administrador_id
    ));    
    $res = DB::insertId();    
    return $res;
} 

public static function editar_menu($menu_id, $nombre_campo, $valor)
{
  DB::update('menu', array(
  $nombre_campo => $valor
  ), "menu_id=%s", $menu_id);
} 

public static function eliminar_menu($menu_id)
{
    DB::delete('submenu', 'menu_id=%s', $menu_id);
    DB::delete('menu', 'menu_id=%s', $menu_id);
}

public static function query_select()
{

$query = <<<EOF
Select
  menu.menu_id,
  menu.titulo,
  menu.orden,
  tipo_administrador.tipo_administrador_id,
  tipo_administrador.descripcion,
  tipo_administrador.is_activo,
  tipo_administrador.submenu_inicio
From
  menu Inner Join
  tipo_administrador On tipo_administrador.tipo_administrador_id =
    menu.tipo_administrador_id
EOF;

return $query;

}

public static function lista_menu_con_submenus_por_tipo_administrador_id($tipo_administrador_id) {
        $query = <<<EOF
        SELECT 
            menu.menu_id, 
            menu.titulo, 
            menu.orden, 
            menu.tipo_administrador_id, 
            submenu.submenu_id, 
            submenu.titulo AS submenu_titulo, 
            submenu.url, 
            submenu.orden AS submenu_orden, 
            submenu.target
        FROM menu 
        INNER JOIN submenu ON menu.menu_id = submenu.menu_id
        WHERE 
        menu.tipo_administrador_id = $tipo_administrador_id
        ORDER BY menu.orden, submenu.orden
EOF;

        // Ejecutar la consulta
        $res = DB::query($query);

        // Estructurar los datos en un array asociativo
        $menus = [];
        foreach ($res as $row) {
            $menu_id = $row['menu_id'];

            // Si el menú aún no está en el array, agregarlo
            if (!isset($menus[$menu_id])) {
                $menus[$menu_id] = [
                    'menu_id' => $row['menu_id'],
                    'titulo' => $row['titulo'],
                    'orden' => $row['orden'],
                    'tipo_administrador_id' => $row['tipo_administrador_id'],
                    'lista_submenu' => []
                ];
            }

            // Agregar el submenú a la lista de submenús del menú correspondiente
            $menus[$menu_id]['lista_submenu'][] = [
                'submenu_id' => $row['submenu_id'],
                'titulo' => $row['submenu_titulo'],
                'url' => $row['url'],
                'orden' => $row['submenu_orden'],
                'target' => $row['target']
            ];
        }

        // Devolver los menús estructurados como array indexado
        return array_values($menus);
    }

//+++ FIN CLASS +++
}