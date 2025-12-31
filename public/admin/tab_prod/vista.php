<div class="row-fluid" id="appProduct">
<!-- este es mi frontend usando boostrap2.3.2, vuejs2 modo estandalone y jquery2.0 -->
  <div class="span12">
    <h2>Productos</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nuevo Producto
      </button>

      <!-- 🟦 NUEVO BOTÓN PARA AÑADIR CATEGORÍA -->
      <button class="btn btn-info" style="margin-left:10px" @click="abrirModalCrearCategoria">
        <i class="icon-plus icon-white"></i> Nueva Categoría
      </button>

      <button class="btn btn-warning" style="margin-left:10px" @click="abrirReporteProductos">
        <i class="icon-print icon-white"></i> Reporte Productos
      </button>
    </div>

    <!-- ===========================
          TABLA PRODUCTOS
    ============================ -->
    <table id="tablaProduct" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Categorías</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>

    <!-- ===========================
          MODAL DETALLE
    ============================ -->
    <div id="modalDetalleProduct" class="modal hide fade">
      <div class="modal-header"><h3>Detalle del Producto</h3></div>
      <div class="modal-body">
        <p><strong>ID:</strong> {{ detalle.product_id  }}</p>
        <p><strong>Nombre:</strong> {{ detalle.name }}</p>
        <p><strong>Precio:</strong> S/ {{ detalle.price }}</p>
        <p><strong>Stock:</strong> {{ detalle.stock }}</p>
        <p><strong>Descripción:</strong> {{ detalle.description }}</p>

        <h4>Categorías:</h4>
        <ul>
          <li v-for="c in detalle.categories">{{ c.descripcion }}</li>
        </ul>

        <h4>Imágenes:</h4>
        <img v-for="img in detalle.images"
             :src="img.image" style="width:80px;margin:5px;border:1px solid #ccc;">
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- ===========================
          MODAL CREAR PRODUCTO
    ============================ -->
    <div id="modalCrearProduct" class="modal hide fade">
      <div class="modal-header"><h3>Nuevo Producto</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Nombre</label>
          <div class="controls"><input v-model="nuevo.name" class="input-xxlarge"></div>
        </div>

        <div class="control-group">
          <label>Precio</label>
          <div class="controls"><input v-model="nuevo.price"></div>
        </div>

        <div class="control-group">
          <label>Categorías</label>
          <v-select multiple :options="categorias"
                    label="descripcion"
                    v-model="nuevo.categorias"
                    class="input-xxlarge">
          </v-select>
        </div>

        <div class="control-group">
          <label>Descripción</label>
          <div class="controls">
            <textarea v-model="nuevo.description" class="input-xxlarge"></textarea>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearProduct">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- ===========================
          MODAL EDITAR PRODUCTO
    ============================ -->
    <div id="modalEditarProduct" class="modal hide fade">
      <div class="modal-header"><h3>Editar Producto</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Nombre</label>
          <div class="controls"><input v-model="form.name" class="input-xxlarge"></div>
        </div>

        <div class="control-group">
          <label>Precio</label>
          <div class="controls"><input v-model="form.price"></div>
        </div>

        <div class="control-group">
          <label>Categorías</label>
          <v-select
            multiple
            :options="categorias"
            label="descripcion"
            v-model="form.categorias"
            track-by="category_id"
            class="input-xxlarge">
          </v-select>

        </div>

        <div class="control-group">
          <label>Descripción</label>
          <div class="controls">
            <textarea v-model="form.description" class="input-xxlarge"></textarea>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarEdicion">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>


    <!-- ===========================
      MODAL CREAR CATEGORIA
      =========================== -->
      <div id="modalCrearCategoria" class="modal hide fade">
        <div class="modal-header"><h3>Nueva Categoría</h3></div>
        <div class="modal-body">

          <div class="control-group">
            <label>Nombre</label>
            <div class="controls">
              <input v-model="catNueva.name" class="input-xxlarge">
            </div>
          </div>

          <div class="control-group">
            <label>Ícono (archivo o nombre)</label>
            <div class="controls">
              <input v-model="catNueva.icon">
            </div>
          </div>

          <div class="control-group">
            <label>Color</label>
            <div class="controls">
              <input v-model="catNueva.color" placeholder="#ff0000">
            </div>
          </div>

          <div class="control-group">
            <label>Descripción breve</label>
            <div class="controls">
              <input v-model="catNueva.brief" class="input-xxlarge">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" @click="crearCategoria">Crear</button>
          <button class="btn" data-dismiss="modal">Cancelar</button>
        </div>
      </div>


  </div>
</div>

<script>
Vue.component('v-select', VueSelect.VueSelect);

new Vue({
  el: '#appProduct',
  data:{
    apphost: (typeof apphost !== 'undefined' ? apphost : ''),
    productos: [],
    catNueva: { name:'', icon:'', color:'#999999', brief:'' },
    categorias: [],
    nuevo: { name:'', price:0, description:'', categorias:[] },
    form: {},
    detalle:{},
    dt:null
  },
  methods:{
    listar(){
      axios.get(`${this.apphost}/product/listar`).then(r=>{
        this.productos = r.data;
        this.$nextTick(()=>{

          if(!this.dt){
            this.dt = $('#tablaProduct').DataTable({
              dom:'frtip', order:[[0,'desc']]
            });

            const self=this;
            $('#tablaProduct tbody')
              .on('click','a.detalle',function(e){
                const id = $(this).data("id");
                const p = self.productos.find(x => x.product_id == id);
                self.abrirDetalle(p);
              })
              .on('click','a.editar',function(e){
                const id = $(this).data("id");
                const p = self.productos.find(x => x.product_id == id);
                self.abrirEditar(p);
              })
              .on('click','a.eliminar',function(e){
                const id = $(this).data("id");
                const p = self.productos.find(x => x.product_id == id);
                self.eliminar(p);
              });
          }

          this.dt.clear();
          this.productos.forEach(p=>{
            const actions = `
               <div class="btn-group">
                 <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
                 <ul class="dropdown-menu">
                   <li><a href="#" class="detalle" data-id="${p.product_id}">Detalle</a></li>
                   <li><a href="#" class="editar"  data-id="${p.product_id}">Editar</a></li>
                   <li><a href="#" class="eliminar" data-id="${p.product_id}">Eliminar</a></li>
                 </ul>
               </div>`;
            this.dt.row.add([
              p.product_id, p.name, p.price, p.stock,
              p.categories_names, actions
            ]);
          });
          this.dt.draw(false);

        });
      });
    },

    abrirModalCrear(){
      this.nuevo = {name:'',price:0,description:'',categorias:[]};
      $('#modalCrearProduct').modal('show');
    },

    crearProduct(){
      axios.post(`${this.apphost}/product/crear`, this.nuevo)
      .then(()=>{ $('#modalCrearProduct').modal('hide'); this.listar(); });
    },

    abrirEditar(p){
      this.form = {
        product_id: p.product_id,
        name: p.name,
        price: p.price,
        description: p.description || '',
        categorias: []   // 👈 vacío primero
      };

      $('#modalEditarProduct').modal('show');

      this.$nextTick(() => {
        this.form.categorias = this.categorias.filter(c =>
          p.categories_ids.includes(c.category_id)
        );
      });
    },

    guardarEdicion(){
      axios.post(`${this.apphost}/product/editar`, this.form)
      .then(()=>{ $('#modalEditarProduct').modal('hide'); this.listar(); });
    },

    abrirDetalle(p){
      axios.get(`${this.apphost}/product/detalle/${p.product_id}`).then(r=>{
        this.detalle = r.data;
        $('#modalDetalleProduct').modal('show');
      });
    },

    eliminar(p){
      apprise(`¿Eliminar producto <b>${p.name}</b>?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/product/eliminar`, { product_id: p.product_id })
        .finally(()=>this.listar());
      });
    },

    cargarCategorias(){
      axios.get(`${this.apphost}/product/listar_categorias`)
      .then(r=>this.categorias=r.data);
    },

    abrirModalCrearCategoria(){
      this.catNueva = { name:'', icon:'', color:'#999999', brief:'' };
      $('#modalCrearCategoria').modal('show');
    },

    crearCategoria(){
      if(!this.catNueva.name.trim()){
        apprise("Escribe un nombre para la categoría");
        return;
      }

      axios.post(`${this.apphost}/product/categoria_crear`, this.catNueva)
      .then(() => {
          $('#modalCrearCategoria').modal('hide');
          apprise("Categoría creada correctamente");

          // recargar categorías para el v-select
          this.cargarCategorias();
      });
    },

    abrirReporteProductos(){
      const url = `${this.apphost}/imp_lista_prod`;
      window.open(url, '_blank');
    }

  },

  mounted(){
    this.cargarCategorias();
    this.listar();
  }

});
</script>
