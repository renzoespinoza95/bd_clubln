<tr id="tr_submenu_id_<?php echo $submenu['submenu_id'] ?>">

<td>
<?php  
  echo $submenu['submenu_id'];
?>
</td>

<td>
    <a href="#modal_edu_titulo" 
    rel="<?php echo $submenu['submenu_id'] ?>" 
    data-toggle="modal" class="link_edu_titulo" 
    id="link_edu_titulo_<?php echo $submenu['submenu_id'] ?>">	
    <?php  
      echo util::mostrar_palabra_latina($submenu['submenu_titulo']);
    ?>
    </a>
</td>

<td>
<?php  
  echo util::mostrar_palabra_latina($submenu['menu_titulo']);
?>
</td>

<td>	
<?php  
  echo util::mostrar_palabra_latina($submenu['url']);
?>
</td>

<td>
  <button class="btn btn-info btn-orden" data-submenu-id="<?php echo $submenu['submenu_id'] ?>">
    <i class="fa fa-arrows"></i> 
    <?php echo $submenu['submenu_orden'] ?> 
  </button>  
</td>



<td>	
<?php  
  echo util::mostrar_palabra_latina($submenu['target']);
?>
</td>
    
<td>
<?php include "botones_acciones.php";?>   
  
</td>
</tr>