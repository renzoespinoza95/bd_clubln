<tr id="tr_ta_id_<?php echo $tipo_acceso['ta_id'] ?>">

<td>
    <input type="checkbox" name="check_eliminar[]" value="<?php echo $tipo_acceso['ta_id'] ?>"  />
<?php  
  echo $tipo_acceso['ta_id'];
?>
</td>

<td>
<a href="#modal_edu_nombre" rel="<?php echo $tipo_acceso['ta_id'] ?>" data-toggle="modal" class="link_edu_nombre" id="link_edu_nombre_<?php echo $tipo_acceso['ta_id'] ?>">	
<?php  
  echo util::mostrar_palabra_latina($tipo_acceso['nombre']);
?>
</a>
</td>
    
<td>
<?php include "botones_acciones.php";?>   
  
</td>
</tr>