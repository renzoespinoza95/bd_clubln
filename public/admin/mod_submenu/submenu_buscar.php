<div id="modal_buscar" class="modal hide fade fullscreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Buscar Submenu</h3>
  </div>
  <div class="modal-body">
<?php
boot::uniform();
?>
<div class="row-fluid">

<div class="span12">

<div class="tabbable" style="margin-bottom: 18px;">
<!-- INICIO ENCABEZADOS -->
       <ul class="nav nav-tabs">
        <li class="active">
          <a href="#tab2" data-toggle="tab">titulo</a>
        </li>        
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Filtros <b class="caret"></b></a>
          <ul class="dropdown-menu">            
            <li><a href="#tab3" data-toggle="tab">url</a></li>
            <li><a href="#tab4" data-toggle="tab">menu_id</a></li>
            <li><a href="#tab5" data-toggle="tab">target</a></li>
          </ul>
        </li>
      </ul>
<!-- FIN ENCABEZADOS --> 

<!-- INICIO FORMULARIOS -->     
        <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
<!-- INICIO TAB2 -->
          <div class="tab-pane active" id="tab2">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarSubmenu">
             <label>buscar por titulo</label>
             <input type="text" id="cbo_filtro" name="cbo_filtro" value="TITULO" class="span2" readonly="true" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
      </form>
          </div>
<!-- INICIO TAB3 -->
          <div class="tab-pane" id="tab3">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarSubmenu">
             <label>buscar por url</label>
             <input type="text" id="cbo_filtro"
             readonly="true" name="cbo_filtro" value="URL" class="span2" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
      </form>
          </div>
<!-- INICIO TAB4 -->
          <div class="tab-pane" id="tab4">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarSubmenu">
             <label>buscar por menu_id</label>
             <input type="text" id="cbo_filtro"
             readonly="true" name="cbo_filtro" value="MENU_ID" class="span2" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
      </form>
          </div>
<!-- INICIO TAB5 -->
          <div class="tab-pane" id="tab5">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarSubmenu">
             <label>buscar por target</label>
             <input type="text" id="cbo_filtro" name="cbo_filtro"
             readonly="true" value="TARGET" class="span2" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
      </form>
          </div>                 

        </div>
      </div>
<!-- FIN FORMULARIOS -->
</form> 


                
</div>
</div>

 <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-mail-reply"></i> Cancelar</button>    
  </div>

<!-- -->    
  </div>

</div>

