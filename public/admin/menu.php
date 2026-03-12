<?php
$administrador_id = $info_admin['administrador_id'];
$tipo_admin = (int)$info_admin['tipo_administrador_id'];
?>
<div class="container">
    <a class="brand" href="<?php echo $apphost ?>/admin/dash">
        <img src="<?php echo $varhost ?>/public/ico/logo-admin.png" alt="logo-admin" />
        CLUBLN
    </a>

      <!-- Botón lupa (a la izquierda del dropdown de usuario) -->
      <div class="btn-group pull-right" style="margin-right:8px;">
        <a href="#modalBusquedaMenu" class="btn" data-toggle="modal" title="Buscar en menús">
          <i class="icon-list-alt icon-black"></i>
        </a>
      </div>

    <div class="btn-group pull-right">
        <a href="#" data-toggle="dropdown" class="btn dropdown-toggle">
            <i class="icon-user"></i> 
            <?php echo $info_admin['nombres_apellidos']; ?>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a rel="facebox" href="<?php echo $apphost . "/editarAdministrador/" . $administrador_id ?>">Mis datos</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo $apphost . "/finAdmin"; ?>">Salir</a></li>
        </ul>
    </div>

<div class="nav-collapse">
    <ul class="nav">
        <?php 
        // Obtener los menús con sus submenús
        $menus = lista_menu_con_submenus_por_tipo_administrador_id(
            $info_admin['tipo_administrador_id']); 
        
        foreach ($menus as $menu) { ?>
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <?php echo util::mostrar_palabra_latina($menu['titulo']); ?>
                    <b class="caret"></b>
                </a>

                <?php if (!empty($menu['lista_submenu'])) { ?>
                    <ul class="dropdown-menu">
                        <?php foreach ($menu['lista_submenu'] as $submenu) { ?>
                            <li>
                                <a href="<?php echo $apphost . $submenu['url']; ?>">
                                    <?php echo util::mostrar_palabra_latina($submenu['titulo']); ?>
                                </a>
                            </li> 
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

</div>
<!-- ============== MODAL: BUSCAR OPCIONES (FIX A) ============== -->
<div id="modalBusquedaMenu" class="modal hide fade fullscreen" tabindex="-1" role="dialog" aria-hidden="true">
  <div id="appBuscadorMenus">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3><i class="icon-search"></i> Buscar opciones</h3>
    </div>

    <div class="modal-body">
      <!-- Filtro -->
      <input type="text" class="input-block-level" placeholder="Filtra por texto..."
             v-model="filtro" @input="filtrar" style="margin-bottom:10px;">

      <!-- Acordeón BS 2.3.2 (usando data-target + href void) -->
      <div class="accordion" id="accordionMenus">
        <div class="accordion-group" v-for="m in menusFiltrados" :key="m.menu_id">
          <div class="accordion-heading">
            <a class="accordion-toggle"
               data-toggle="collapse"
               :data-parent="'#accordionMenus'"
               :data-target="'#menu'+m.menu_id"
               href="javascript:void(0)">
              {{ m.titulo }}
            </a>
          </div>

          <div class="accordion-body collapse" :id="'menu'+m.menu_id">
            <div class="accordion-inner" style="padding:8px 12px;">
              <ul class="nav nav-pills nav-stacked" v-if="m.lista_submenu && m.lista_submenu.length">
                <li v-for="s in m.lista_submenu" :key="s.submenu_id" style="margin-bottom:4px;">
                  <a :href="urlCompleta(s.url)" v-bind="linkAttrs(s)">
                    {{ s.titulo }}
                  </a>
                </li>
              </ul>
              <div v-else class="muted">Sin opciones.</div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Acordeón -->
    </div>

    <div class="modal-footer">
      <a href="#" class="btn" data-dismiss="modal">Cerrar</a>
    </div>
  </div>
</div>

<script>
// Vars para el front
window.APPHOST    = <?php echo json_encode($apphost); ?>;
window.API_HOST   = APPHOST + '/api';
window.TIPO_ADMIN = <?php echo $tipo_admin; ?>;
</script>

<script>
new Vue({
  el: '#appBuscadorMenus',
  data: {
    cargando: false,
    menus: [],
    menusFiltrados: [],
    filtro: ''
  },
  created() {
    this.cargarMenus();
  },
  methods: {
    cargarMenus() {
      this.cargando = true;
      axios.get(API_HOST + '/menus/por-tipo/' + TIPO_ADMIN)
        .then(res => {
          if (res.data && res.data.ok) {
            this.menus = (res.data.menus || []).map(m => ({
              ...m,
              titulo: m.titulo,
              lista_submenu: (m.lista_submenu || []).map(s => ({
                ...s,
                titulo: s.titulo
              }))
            }));
            this.menusFiltrados = this.menus;
          }
        })
        .catch(err => {
          console.error('Error cargando menús', err);
          alert('No se pudieron cargar los menús.');
        })
        .finally(() => this.cargando = false);
    },
    filtrar() {
      const q = this.filtro.trim().toLowerCase();
      if (!q) { this.menusFiltrados = this.menus; return; }
      this.menusFiltrados = this.menus
        .map(m => {
          const matchMenu = (m.titulo || '').toLowerCase().includes(q);
          const subs = (m.lista_submenu || []).filter(s =>
            (s.titulo || '').toLowerCase().includes(q) || (s.url || '').toLowerCase().includes(q)
          );
          if (matchMenu || subs.length) {
            return { ...m, lista_submenu: subs.length ? subs : m.lista_submenu };
          }
          return null;
        })
        .filter(Boolean);
      // (opcional) colapsa abiertos después de filtrar
      this.$nextTick(() => { $('.accordion-body.in').removeClass('in').css('height','0'); });
    },
    urlCompleta(u) {
      if (!u) return APPHOST;
      return u.startsWith('http') ? u : (APPHOST + u);
    },
    linkAttrs(s) {
      const t = (s.target && s.target !== '1') ? s.target : null;
      return t ? { target: t } : {};
    }
  }
});
</script>