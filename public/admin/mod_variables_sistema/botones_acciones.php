 <?php
  
// Enlace “Editar Variables Sistema”
echo '<a ' . h2::parseAttributes([
    'href'  => $apphost . "/admin/editarVariablesSistema/" . $variables_sistema['nombre_variable'],
    'class' => 'btn btn-info',
    'rel'   => 'facebox'
]) . '>'
    . '<i class="icon-white icon-pencil"></i>'
  . '</a>';

// Enlace “Eliminar Variables Sistema”
echo '<a ' . h2::parseAttributes([
    'href'  => $variables_sistema['nombre_variable'],
    'class' => 'link_eliminar_variables_sistema btn btn-info'
]) . '>'
    . '<i class="icon-white icon-remove"></i>'
  . '</a>';  
 
  ?>