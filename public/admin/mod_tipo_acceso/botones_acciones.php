  <div class="btn-group">
      <a class="btn btn-primary" href="#">
        <i class="icon-cog icon-white"></i>
      </a>
      <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a rel="facebox" 
          href="<?php echo $apphost . "/admin/editarTipoAcceso/" . $tipo_acceso['ta_id']?>"><i class="icon-pencil"></i> Editar</a></li>
        <li>
          <a class ="link_eliminar_tipo_acceso"
           href="<?php echo $tipo_acceso['ta_id'] ?>"><i class="icon-trash"></i> Eliminar</a></li>            
      </ul>
</div>