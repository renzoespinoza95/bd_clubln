<div class="row-fluid" id="appCategory">

  <div class="span12">
    <h2>Categorías</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nueva Categoría
      </button>
    </div>

    <!-- =============================
           TABLA CATEGORY
    ============================== -->
    <table id="tablaCategory" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Color</th>
          <th>Breve</th>
          <th>Icono</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>

    <!-- =============================
           MODAL CREAR
    ============================== -->
    <div id="modalCrearCategory" class="modal hide fade">
      <div class="modal-header"><h3>Nueva Categoría</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Nombre</label>
          <div class="controls"><input class="input-xxlarge" v-model="nuevo.name"></div>
        </div>

        <div class="control-group">
          <label>Icono</label>
          <div class="controls"><input v-model="nuevo.icon"></div>
        </div>

        <div class="control-group">
          <label>Color</label>
          <div class="controls"><input v-model="nuevo.color" placeholder="#ff0000"></div>
        </div>

        <div class="control-group">
          <label>Descripción breve</label>
          <div class="controls"><input class="input-xxlarge" v-model="nuevo.brief"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crear">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- =============================
           MODAL EDITAR
    ============================== -->
    <div id="modalEditarCategory" class="modal hide fade">
      <div class="modal-header"><h3>Editar Categoría</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Nombre</label>
          <div class="controls"><input class="input-xxlarge" v-model="form.name"></div>
        </div>

        <div class="control-group">
          <label>Icono</label>
          <div class="controls"><input v-model="form.icon"></div>
        </div>

        <div class="control-group">
          <label>Color</label>
          <div class="controls"><input v-model="form.color"></div>
        </div>

        <div class="control-group">
          <label>Descripción breve</label>
          <div class="controls"><input class="input-xxlarge" v-model="form.brief"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardar">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>

<script>
new Vue({
  el: "#appCategory",
  data:{
    apphost: (typeof apphost !== 'undefined' ? apphost : ''),
    categorias:[],
    nuevo:{ name:'', icon:'', color:'#999999', brief:'' },
    form:{},
    dt:null
  },
  methods:{
    listar(){
      axios.get(`${this.apphost}/category/listar`)
      .then(r=>{
        this.categorias = r.data;

        this.$nextTick(()=>{
          if(!this.dt){
            this.dt = $('#tablaCategory').DataTable({
              dom:'frtip',
              order:[[0,'desc']]
            });

            const self=this;
            $('#tablaCategory tbody')
            .on('click','a.editar',function(){
              const id = $(this).data('id');
              const c = self.categorias.find(x=>x.id==id);
              self.abrirEditar(c);
            })
            .on('click','a.eliminar',function(){
              const id = $(this).data('id');
              const c = self.categorias.find(x=>x.id==id);
              self.eliminar(c);
            });
          }

          this.dt.clear();
          this.categorias.forEach(c=>{
            const actions = `
              <div class="btn-group">
                <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                  Opciones <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="#" class="editar" data-id="${c.id}">Editar</a></li>
                  <li><a href="#" class="eliminar" data-id="${c.id}">Eliminar</a></li>
                </ul>
              </div>
            `;
            this.dt.row.add([
              c.id,
              c.name,
              `<span style="padding:5px;background:${c.color};color:#fff">${c.color}</span>`,
              c.brief,
              c.icon,
              actions
            ]);
          });
          this.dt.draw(false);
        });
      });
    },

    abrirModalCrear(){
      this.nuevo = { name:'', icon:'', color:'#999999', brief:'' };
      $('#modalCrearCategory').modal('show');
    },

    crear(){
      axios.post(`${this.apphost}/category/crear`, this.nuevo)
      .then(()=>{
        $('#modalCrearCategory').modal('hide');
        this.listar();
      });
    },

    abrirEditar(c){
      this.form = JSON.parse(JSON.stringify(c));
      $('#modalEditarCategory').modal('show');
    },

    guardar(){
      axios.post(`${this.apphost}/category/editar`, this.form)
      .then(()=>{
        $('#modalEditarCategory').modal('hide');
        this.listar();
      });
    },

    eliminar(c){
      apprise(`¿Eliminar categoría <b>${c.name}</b>?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/category/eliminar`, { id:c.id })
        .then(()=>this.listar());
      });
    }
  },

  mounted(){
    this.listar();
  }
});
</script>
