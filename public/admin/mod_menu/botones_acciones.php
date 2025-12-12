<?php
$url_buscar = $apphost . "/mud/buscarSubmenuCriterio?cbo_filtro=menu_id&txt_criterio=" . $menu['menu_id'];
$cant_submenu = submenu::cantidad_submenu_por_menu_id($menu['menu_id']);
$cant_submenu = "(" . $cant_submenu . ")";
?>
<a class="btn btn-info tt" href="<?php echo $url_buscar ?>" title="Lista de submenu">
    <i class="icon-white icon-th-list">
    </i>
    <?php echo $cant_submenu; ?>
</a>

    <div class="btn-group">
      <a class="btn btn-primary" href="#">
        <i class="icon-cog icon-white"></i>
      </a>
      <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a rel="facebox" 
          href="<?php echo $apphost . "/admin/editarMenu/" . $menu['menu_id'] ?>"><i class="icon-pencil"></i> Editar</a></li>
        <li>
          <a class ="link_eliminar_menu"
           href="<?php echo $menu['menu_id'] ?>"><i class="icon-trash"></i> Eliminar</a></li>            
      </ul>
</div>