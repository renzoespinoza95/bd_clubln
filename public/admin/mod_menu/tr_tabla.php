<tr id="tr_menu_id_<?php echo $menu['menu_id'] ?>">

<td>
<?php  
  echo $menu['menu_id'];
?>
</td>

<td>
<?php  
  echo util::mostrar_palabra_latina($menu['titulo']);
?>
</td>

<td>
    <button class="btn btn-info btn-orden-menu"
            data-menu-id="<?php echo $menu['menu_id']; ?>"
            data-orden="<?php echo $menu['orden']; ?>">
      <i class="fa fa-arrows"></i> <?php echo $menu['orden']; ?>
    </button>
</td>

<td>
<?php  
  echo $menu['descripcion'];
?>
</td>    
    
<td>
<?php include "botones_acciones.php";?>   
  
</td>
</tr>