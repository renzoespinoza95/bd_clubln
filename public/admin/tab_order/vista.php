<div class="row-fluid" id="appOrder">

  <div class="span12">
    <h2>Órdenes</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nueva Orden
      </button>
    </div>

    <table id="tablaOrder" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Código</th>
          <th>Cliente</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Total</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>

    <!-- ===============================
         MODAL DETALLE (incluye CRUD detail)
    ================================ -->
    <div id="modalDetalleOrder" class="modal hide fade">
      <div class="modal-header">
        <h3>Detalle de Orden #{{ detalle.id }}</h3>
      </div>
      <div class="modal-body">

        <p><b>Código:</b> {{ detalle.code }}</p>
        <p><b>Cliente:</b> {{ detalle.buyer }}</p>
        <p><b>Dirección:</b> {{ detalle.address }}</p>
        <p><b>Estado:</b> {{ detalle.status }}</p>

        <h4>Items:</h4>

        <!-- Tabla detalles -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Total</th>
              <th>Acc.</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="d in detallesOrder" :key="d.id">
              <td>{{ d.product_name }}</td>
              <td>{{ d.amount }}</td>
              <td>{{ d.price_item }}</td>
              <td>{{ (d.amount * d.price_item).toFixed(2) }}</td>
              <td>
                <button class="btn btn-mini btn-primary" @click="abrirEditarDetail(d)">Editar</button>
                <button class="btn btn-mini btn-danger" @click="eliminarDetail(d)">X</button>
              </td>
            </tr>
          </tbody>
        </table>

        <button class="btn btn-success" @click="abrirCrearDetail">Agregar Ítem</button>

      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- MODAL NUEVA ORDEN -->
    <div id="modalCrearOrder" class="modal hide fade">
      <div class="modal-header"><h3>Nueva Orden</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Código</label>
          <div class="controls"><input v-model="nuevo.code"></div>
        </div>

        <div class="control-group">
          <label>Cliente</label>
          <div class="controls"><input v-model="nuevo.buyer"></div>
        </div>

        <div class="control-group">
          <label>Dirección</label>
          <div class="controls"><input class="input-xxlarge" v-model="nuevo.address"></div>
        </div>

        <div class="control-group">
          <label>Total</label>
          <div class="controls"><input v-model="nuevo.total_fees"></div>
        </div>

        <div class="control-group">
          <label>Estado</label>
          <div class="controls">
            <select v-model="nuevo.status">
              <option>WAITING</option>
              <option>PAID</option>
              <option>SENT</option>
              <option>CANCELLED</option>
            </select>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearOrder">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- MODAL EDITAR ORDEN -->
    <div id="modalEditarOrder" class="modal hide fade">
      <div class="modal-header"><h3>Editar Orden</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Cliente</label>
          <div class="controls"><input v-model="form.buyer"></div>
        </div>

        <div class="control-group">
          <label>Dirección</label>
          <div class="controls"><input v-model="form.address" class="input-xxlarge"></div>
        </div>

        <div class="control-group">
          <label>Estado</label>
          <div class="controls">
            <select v-model="form.status">
              <option>WAITING</option>
              <option>PAID</option>
              <option>SENT</option>
              <option>CANCELLED</option>
            </select>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarOrder">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- --- CRUD DETALLES (Modal Crear / Editar) ---- -->

    <!-- Crear Detalle -->
    <div id="modalCrearDetail" class="modal hide fade">
      <div class="modal-header"><h3>Agregar Ítem</h3></div>
      <div class="modal-body">
        <label>Producto</label>
        <select v-model="detailForm.product_id">
          <option v-for="p in productos" :value="p.id">{{ p.name }}</option>
        </select>

        <label>Cantidad</label>
        <input v-model.number="detailForm.amount">

        <label>Precio</label>
        <input v-model.number="detailForm.price_item">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearDetail">Agregar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- Editar Detalle -->
    <div id="modalEditarDetail" class="modal hide fade">
      <div class="modal-header"><h3>Editar Ítem</h3></div>
      <div class="modal-body">
        <label>Producto</label>
        <select v-model="detailForm.product_id">
          <option v-for="p in productos" :value="p.id">{{ p.name }}</option>
        </select>

        <label>Cantidad</label>
        <input v-model.number="detailForm.amount">

        <label>Precio</label>
        <input v-model.number="detailForm.price_item">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarDetail">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>

<script>
new Vue({
  el:"#appOrder",
  data:{
    apphost: (typeof apphost !== 'undefined' ? apphost : ''),
    ordenes:[],
    productos:[],

    nuevo:{code:'',buyer:'',address:'',total_fees:0,status:'WAITING'},
    form:{},
    detalle:{},
    detallesOrder:[],

    detailForm:{},
    dt:null
  },

  methods:{
    listar(){
      axios.get(`${this.apphost}/product_order/listar`).then(r=>{
        this.ordenes=r.data;

        this.$nextTick(()=>{
          if(!this.dt){
            this.dt = $('#tablaOrder').DataTable({
              dom:'frtip', order:[[0,'desc']]
            });

            const self=this;
            $('#tablaOrder tbody')
            .on('click','a.detalle',function(){
              const id = $(this).data("id");
              const o = self.ordenes.find(x=>x.id==id);
              self.abrirDetalle(o);
            })
            .on('click','a.editar',function(){
              const id = $(this).data("id");
              const o = self.ordenes.find(x=>x.id==id);
              self.abrirEditar(o);
            })
            .on('click','a.eliminar',function(){
              const id = $(this).data("id");
              const o = self.ordenes.find(x=>x.id==id);
              self.eliminar(o);
            });
          }

          this.dt.clear();
          this.ordenes.forEach(o=>{
            const actions = `
              <div class="btn-group">
                <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="#" class="detalle" data-id="${o.id}">Detalle</a></li>
                  <li><a href="#" class="editar"  data-id="${o.id}">Editar</a></li>
                  <li><a href="#" class="eliminar" data-id="${o.id}">Eliminar</a></li>
                </ul>
              </div>`;
            this.dt.row.add([ o.id, o.code, o.buyer, o.status, o.fecha, o.total_fees, actions ]);
          });
          this.dt.draw(false);
        });
      });
    },

    abrirModalCrear(){  
      this.nuevo={code:'',buyer:'',address:'',total_fees:0,status:'WAITING'};
      $('#modalCrearOrder').modal('show');
    },

    crearOrder(){
      axios.post(`${this.apphost}/product_order/crear`, this.nuevo)
      .then(()=>{
        $('#modalCrearOrder').modal('hide');
        this.listar();
      });
    },

    abrirEditar(o){
      this.form = JSON.parse(JSON.stringify(o));
      $('#modalEditarOrder').modal('show');
    },

    guardarOrder(){
      axios.post(`${this.apphost}/product_order/editar`, this.form)
      .then(()=>{
        $('#modalEditarOrder').modal('hide');
        this.listar();
      });
    },

    abrirDetalle(o){
      axios.get(`${this.apphost}/product_order/detalle/${o.id}`).then(r=>{
        this.detalle     = r.data.order;
        this.detallesOrder = r.data.detalles;
        $('#modalDetalleOrder').modal('show');
      });
    },

    eliminar(o){
      apprise(`¿Eliminar orden #${o.id}?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/product_order/eliminar`,{id:o.id})
        .finally(()=>this.listar());
      });
    },

    // ----------- CRUD DETALLES -------------
    abrirCrearDetail(){
      this.detailForm={ order_id:this.detalle.id, product_id:'', amount:1, price_item:0 };
      $('#modalCrearDetail').modal('show');
    },

    crearDetail(){
      axios.post(`${this.apphost}/product_order_detail/crear`, this.detailForm)
      .then(()=>{
        $('#modalCrearDetail').modal('hide');
        this.abrirDetalle(this.detalle);
      });
    },

    abrirEditarDetail(d){
      this.detailForm = JSON.parse(JSON.stringify(d));
      $('#modalEditarDetail').modal('show');
    },

    guardarDetail(){
      axios.post(`${this.apphost}/product_order_detail/editar`, this.detailForm)
      .then(()=>{
        $('#modalEditarDetail').modal('hide');
        this.abrirDetalle(this.detalle);
      });
    },

    eliminarDetail(d){
      apprise(`¿Eliminar ítem ${d.product_name}?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/product_order_detail/eliminar`,{id:d.id})
        .then(()=>this.abrirDetalle(this.detalle));
      });
    },

    cargarProductos(){
      axios.get(`${this.apphost}/product/listar`)
      .then(r => this.productos = r.data);
    }
  },

  mounted(){
    this.cargarProductos();
    this.listar();
  }
});
</script>
