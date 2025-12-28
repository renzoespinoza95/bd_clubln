<!-- jQuery UI 1.10.3 (SORTABLE compatible con jQuery 2.0) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/css/base/jquery.ui.all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<div class="row-fluid" id="appMenu">
  <div class="span12">

    <h2>Lista de Menu</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="nuevoMenu">
        <i class="icon-plus icon-white"></i> Agregar
      </button>
    </div>

    <!-- ===========================
         TABLA MENÚ
    ============================ -->
    <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Orden</th>
            <th>Tipo admin</th>
            <th>Submenús</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <!-- 👇 tbody sortable -->
        <tbody ref="tbodyMenu">
          <tr v-for="m in menus" :key="m.menu_id" :data-id="m.menu_id">
            <td>{{ m.menu_id }}</td>
            <td>{{ m.titulo }}</td>

            <!-- ORDEN (solo icono visual) -->
            <td style="cursor:move">
              <span class="label label-info">⇅</span>
            </td>

            <td>{{ m.tipo_admin }}</td>

            <td>
              <button class="btn btn-mini btn-primary" @click="abrirSubmenus(m)">
                ☰ ({{ m.total_submenus }})
              </button>
            </td>

            <td>
              <div class="btn-group">
                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
                  ⚙ <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="#" @click.prevent="editarMenu(m)">Editar</a></li>
                  <li><a href="#" @click.prevent="eliminarMenu(m)">Eliminar</a></li>
                </ul>
              </div>
            </td>
          </tr>
        </tbody>
      </table>


    <!-- ===========================
         MODAL MENÚ
    ============================ -->
    <div class="modal hide fade" id="modalMenu">
      <div class="modal-header">
        <h3>{{ form.menu_id ? 'Editar Menú' : 'Nuevo Menú' }}</h3>
      </div>
      <div class="modal-body">

        <label>Título</label>
        <input class="input-xxlarge" v-model="form.titulo">

        <label>Tipo Administrador</label>
        <select class="input-xlarge" v-model="form.tipo_administrador_id">
          <option disabled value="">-- Seleccione --</option>
          <option v-for="t in tipos" :value="t.tipo_administrador_id">
            {{ t.descripcion }}
          </option>
        </select>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarMenu">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- ===========================
         MODAL SUBMENÚS
    ============================ -->
    <div class="modal hide fade" id="modalSubmenus">
      <div class="modal-header">
        <h3>Submenús de {{ menuActual.titulo }}</h3>
      </div>
      <div class="modal-body">

        <button class="btn btn-success btn-mini" @click="nuevoSubmenu">
          <i class="icon-plus icon-white"></i> Agregar Submenú
        </button>

        <table class="table table-bordered table-striped" style="margin-top:10px">
            <thead>
              <tr>
                <th>Título</th>
                <th>URL</th>
                <th>Orden</th>
                <th>Target</th>
                <th>Acciones</th>
              </tr>
            </thead>

            <!-- 👇 sortable -->
            <tbody ref="tbodySubmenu">
              <tr v-for="s in submenus" :key="s.submenu_id" :data-id="s.submenu_id">
                <td>{{ s.titulo }}</td>
                <td>{{ s.url }}</td>

                <!-- indicador visual -->
                <td class="drag-handle" style="cursor:move;text-align:center">
                  <span class="label label-info">⇅</span>
                </td>


                <td>{{ s.target }}</td>

                <td>
                  <button class="btn btn-mini" @click="editarSubmenu(s)">Editar</button>
                  <button class="btn btn-mini btn-danger" @click="eliminarSubmenu(s)">Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>


      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- ===========================
         MODAL SUBMENÚ FORM
    ============================ -->
    <div class="modal hide fade" id="modalSubmenuForm">
      <div class="modal-header">
        <h3>{{ formSub.submenu_id ? 'Editar Submenú' : 'Nuevo Submenú' }}</h3>
      </div>
      <div class="modal-body">

        <label>Título</label>
        <input class="input-xlarge" v-model="formSub.titulo">

        <label>URL</label>
        <input class="input-xlarge" v-model="formSub.url">

        <label>Orden</label>
        <input class="input-mini" v-model="formSub.orden">

        <label>Target</label>
        <input class="input-mini" v-model="formSub.target">

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarSubmenu">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>

<script>
new Vue({
  el:'#appMenu',
  data:{
    apphost: apphost,
    menus:[],
    tipos:[],
    submenus:[],
    menuActual:{},
    form:{},
    formSub:{}
  },

  methods:{
    listarMenus(){
      axios.get(this.apphost+'/menu/listar')
        .then(r=>this.menus=r.data);
    },

    cargarTipos(){
      axios.get(this.apphost+'/tipo-administrador/listar')
        .then(r=>this.tipos=r.data);
    },

    nuevoMenu(){
      this.form={ titulo:'', tipo_administrador_id:'' };
      $('#modalMenu').modal('show');
    },

    editarMenu(m){
      this.form=JSON.parse(JSON.stringify(m));
      $('#modalMenu').modal('show');
    },

    guardarMenu(){
      axios.post(this.apphost+'/menu/guardar',this.form)
        .then(()=>{ $('#modalMenu').modal('hide'); this.listarMenus(); });
    },

    eliminarMenu(m){
      if(!confirm('¿Eliminar menú?')) return;
      axios.post(this.apphost+'/menu/eliminar',{ menu_id:m.menu_id })
        .then(()=>this.listarMenus());
    },

    cambiarOrden(m,delta){
      axios.post(this.apphost+'/menu/cambiar-orden',{
        menu_id:m.menu_id,
        delta:delta
      }).then(()=>this.listarMenus());
    },

    abrirSubmenus(m){
        this.menuActual = m;

        axios.get(this.apphost + '/submenu/listar/' + m.menu_id)
          .then(r => {
            this.submenus = r.data;

            this.$nextTick(() => {

              // mostrar modal primero
              $('#modalSubmenus').modal('show');

              // esperar a que Bootstrap termine de mostrarlo
              $('#modalSubmenus').one('shown', () => {

                const self = this;
                const $tbody = $(self.$refs.tbodySubmenu);

                // 🔥 MUY IMPORTANTE: destruir sortable previo
                if ($tbody.hasClass('ui-sortable')) {
                  $tbody.sortable('destroy');
                }

                // 🔥 inicializar sortable YA visible
                $tbody.sortable({
                  axis: 'y',
                  handle: '.drag-handle',
                  cursor: 'move',
                  helper: 'clone',
                  tolerance: 'pointer',

                  update: function () {
                    let orden = [];

                    $tbody.find('tr').each(function (index) {
                      orden.push({
                        submenu_id: $(this).data('id'),
                        orden: index + 1
                      });
                    });

                    axios.post(self.apphost + '/submenu/actualizar-orden', {
                      orden: orden
                    });
                  }
                });

              });

            });

          });
      },
    nuevoSubmenu(){
      this.formSub={ menu_id:this.menuActual.menu_id, orden:1, target:'_self' };
      $('#modalSubmenuForm').modal('show');
    },

    editarSubmenu(s){
      this.formSub=JSON.parse(JSON.stringify(s));
      $('#modalSubmenuForm').modal('show');
    },

    guardarSubmenu(){
      axios.post(this.apphost+'/submenu/guardar',this.formSub)
        .then(()=>{
          $('#modalSubmenuForm').modal('hide');
          this.abrirSubmenus(this.menuActual);
        });
    },

    eliminarSubmenu(s){
      if(!confirm('¿Eliminar submenú?')) return;
      axios.post(this.apphost+'/submenu/eliminar',{ submenu_id:s.submenu_id })
        .then(()=>this.abrirSubmenus(this.menuActual));
    }
  },

  mounted(){
    this.cargarTipos();
    this.listarMenus();

    this.$nextTick(() => {
      const self = this;

      $(this.$refs.tbodyMenu).sortable({
        axis: "y",
        cursor: "move",
        update: function () {

          // Obtener nuevo orden visual
          let orden = [];
          $(self.$refs.tbodyMenu).find("tr").each(function (index) {
            orden.push({
              menu_id: $(this).data("id"),
              orden: index + 1   // 👈 posición = orden
            });
          });

          // Enviar al backend
          axios.post(self.apphost + '/menu/actualizar-orden', {
            orden: orden
          }).then(() => {
            self.listarMenus();
          });
        }
      });
    });
  }

});
</script>
