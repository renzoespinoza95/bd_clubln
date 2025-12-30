<div class="row-fluid" id="appOrder">
<!-- este es mi frontend usando boostrap2.3.2, vuejs2 modo estandalone y jquery2.0 -->
  <div class="span12">
    <h2>Órdenes</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nueva Orden
      </button>
      <button class="btn btn-info" @click="abrirModalClientes">
        <i class="icon-user icon-white"></i> Clientes
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
        <h3>Detalle de Orden #{{ detalle.product_order_id }}</h3>
      </div>
      <div class="modal-body">

        <p><b>Código:</b> {{ detalle.code }}</p>
        <p><b>Cliente:</b> {{ detalle.buyer }}</p>
        <p><b>Tipo de pago:</b> {{ detalle.tipo_pago }}</p>
        <p><b>Estado:</b> {{ detalle.status }}</p>

        <h4>Productos:</h4>

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
            <tr v-for="d in detallesOrder" :key="d.product_order_detail_id">
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
        <label>Cliente</label>
        <v-select
          :options="clientes"
          label="label"
          v-model="nueva.cliente"
        ></v-select>
      </div>

      <div class="control-group">
        <label>Tipo de pago</label>
        <div class="controls">
          <select v-model="nueva.tipo_pago_id">
            <option value="">-- Seleccione --</option>
            <option v-for="t in tiposPago" :value="t.tipo_pago_id">
              {{ t.descripcion }}
            </option>
          </select>
        </div>
      </div>



        <h4>Items</h4>

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
    <tr v-for="(i,idx) in nueva.items" :key="idx">
      <td>{{ i.product_name }}</td>
      <td>{{ i.amount }}</td>
      <td>{{ i.price_item }}</td>
      <td>{{ (i.amount*i.price_item).toFixed(2) }}</td>
      <td>
        <button class="btn btn-mini btn-danger"
                @click="nueva.items.splice(idx,1)">X</button>
      </td>
    </tr>
  </tbody>
</table>

<button class="btn btn-success" @click="abrirModalAgregarItemNuevaOrden">
  Agregar Ítem
</button>


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
        <select v-model="detailForm.product_id" @change="onProductoChange">
          <option v-for="p in productos" :value="p.product_id">
            {{ p.name }} - S/ {{ p.price }}
          </option>
        </select>


        <label>Cantidad</label>
        <input v-model.number="detailForm.amount">

        <label>Total</label>
        <input :value="totalDetalle.toFixed(2)" readonly>
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
          <option v-for="p in productos" :value="p">
            {{ p.name }} - S/ {{ p.price }}
          </option>
        </select>

        <label>Cantidad</label>
        <input v-model.number="detailForm.amount">

        <label>Total</label>
        <input :value="totalItem" readonly>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarDetail">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>


    <!-- MODAL AGREGAR ITEM (NUEVA ORDEN) -->
    <div id="modalAgregarItemNuevaOrden" class="modal hide fade">
      <div class="modal-header">
        <h3>Agregar producto orden</h3>
      </div>

      <div class="modal-body">

        <label>Producto</label>
        <select v-model="itemForm.product_id" class="input-xxlarge">
          <option disabled value="">-- Seleccione --</option>
          <option v-for="p in productos" :value="p.product_id">
            {{ p.name }} - S/ {{ p.price }}
          </option>
        </select>

        <label>Cantidad</label>
        <input type="number" v-model.number="itemForm.amount" min="1">

        <label>Precio Unitario</label>
        <input type="number" :value="itemForm.price_item" readonly>

        <label>Total</label>
        <input type="number" :value="totalItemNuevaOrden" readonly>

      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" @click="confirmarAgregarItem">
          Agregar
        </button>
        <button class="btn" data-dismiss="modal">
          Cancelar
        </button>
      </div>
    </div>


    <div id="modalClientes" class="modal hide fade">
      <div class="modal-header">
        <h3>Clientes</h3>
      </div>
      <div class="modal-body">

        <button class="btn btn-success" @click="abrirModalNuevoCliente">
          + Agregar Cliente
        </button>

        <table id="tablaClientes" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>DNI</th>
              <th>Nombre</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>

      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <div id="modalNuevoCliente" class="modal hide fade">
      <div class="modal-header">
        <h3>Nuevo Cliente</h3>
      </div>
      <div class="modal-body">

        <label>DNI</label>
        <div class="input-append">
          <input v-model="clienteForm.dni">
          <button class="btn" @click="generarDniFake">🎲</button>
        </div>

        <label>Nombre</label>
        <input v-model="clienteForm.nombre">

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarCliente">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>


    <div id="modalEditarCliente" class="modal hide fade">
      <div class="modal-header">
        <h3>Editar Cliente</h3>
      </div>
      <div class="modal-body">

        <label>DNI</label>
        <input v-model="clienteEdit.dni">

        <label>Nombre</label>
        <input v-model="clienteEdit.nombre">

        <label>Dirección</label>
        <input v-model="clienteEdit.direccion">

        <label>Teléfono</label>
        <input v-model="clienteEdit.telefono">

        <label>Email</label>
        <input v-model="clienteEdit.email">

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="actualizarCliente">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>




  </div>
</div>

<script>
Vue.component('v-select', VueSelect.VueSelect);
</script>
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

    clientes:[],
    clienteForm:{ dni:'', nombre:'' },
    clienteEdit:{},
    dtClientes:null,

    detailForm:{},
    itemForm:{ product_id:null, amount:1, price_item:0 },
    dt:null,
    cajaActual: null,
    caja_id: null,
    tiposPago: [],
    nueva:{
      cliente_id:null,
      buyer:'',
      address:'',
      total_fees:0,
      items:[],
      tipo_pago_id:null
    },
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
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.abrirDetalle(o);
            })
            .on('click','a.editar',function(){
              const id = $(this).data("id");
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.abrirEditar(o);
            })
            .on('click','a.eliminar',function(){
              const id = $(this).data("id");
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.eliminar(o);
            });
          }

          this.dt.clear();
          this.ordenes.forEach(o=>{
            const actions = `
              <div class="btn-group">
                <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="#" class="detalle" data-id="${o.product_order_id}">Detalle</a></li>
                  <li><a href="#" class="editar"  data-id="${o.product_order_id}">Editar</a></li>
                  <li><a href="#" class="eliminar" data-id="${o.product_order_id}">Eliminar</a></li>
                </ul>
              </div>`;
            this.dt.row.add([ o.product_order_id, o.code, o.buyer, o.status, o.fecha, o.total_fees, actions ]);
          });
          this.dt.draw(false);
        });
      });
    },

    abrirModalCrear() {
      axios.get(`${this.apphost}/auth/administrador-actual`).then(r=>{

        const caja = r.data.caja;

        if(caja.estado !== 'ABIERTA'){
          apprise('La caja de este usuario está cerrada');
          return;
        }

        // ✅ caja abierta
        this.cajaActual = caja;
        this.caja_id = caja.caja_id;

        // preparar orden
        this.nueva = {
          cliente_id:null,
          buyer:'',
          address:'',
          total_fees:0,
          items:[],
          caja_id: caja.caja_id
        };

        $('#modalCrearOrder').modal('show');

      }).catch(()=>{
        apprise('No se pudo verificar el estado de la caja');
      });
    },


    abrirModalClientes(){
      $('#modalClientes').modal('show');
      this.listarClientes();
    },

    abrirModalAgregarItemNuevaOrden(){
      this.itemForm = {
        product_id: '',
        amount: 1,
        price_item: 0
      };
      $('#modalAgregarItemNuevaOrden').modal('show');
    },


    crearOrden(){
      if(!this.nueva.cliente_id){
        apprise('Seleccione un cliente');
        return;
      }

      if(this.nueva.items.length === 0){
        apprise('Agregue al menos un ítem');
        return;
      }

      axios.post(`${this.apphost}/product_order/crear`,{
        buyer: this.nueva.buyer,
        address: this.nueva.address,
        total_fees: this.nueva.total_fees,
        caja_id: this.nueva.caja_id,
        items: this.nueva.items
      }).then(()=>{

        $('#modalCrearOrder').modal('hide');
        this.listar();

      }).catch(()=>{
        apprise('Error al crear la orden');
      });
    },


    listarClientes(){
      axios.get(`${this.apphost}/cliente/listar`).then(r=>{
        this.clientes = r.data;

        this.$nextTick(()=>{
          if(!this.dtClientes){
            this.dtClientes = $('#tablaClientes').DataTable({
                   language: dt_language,
                   scrollX: true,
                   dom: 'frtip',
                   order:[[0,'desc']]
                 });
          }

          this.dtClientes.clear();
          this.clientes.forEach(c=>{
            this.dtClientes.row.add([
              c.dni,
              c.nombre,
              `<button class="btn btn-mini btn-primary editar" data-id="${c.cliente_id}">Editar</button>`
            ]);
          });
          this.dtClientes.draw(false);

          const self=this;
          $('#tablaClientes').off().on('click','.editar',function(){
            const id=$(this).data('id');
            self.abrirEditarCliente(
              self.clientes.find(x=>x.cliente_id==id)
            );
          });
        });
      });
    },

    generarDniFake(){
      const letras = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
      let dni = '';
      for(let i=0;i<8;i++){
        dni += Math.random()<0.7
          ? Math.floor(Math.random()*10)
          : letras[Math.floor(Math.random()*letras.length)];
      }
      this.clienteForm.dni = dni;
    },

    guardarCliente(){
      if(!this.clienteForm.dni || !this.clienteForm.nombre){
        alert('DNI y Nombre son obligatorios');
        return;
      }

      axios.post(`${this.apphost}/cliente/crear`, this.clienteForm)
      .then(()=>{
        $('#modalNuevoCliente').modal('hide');
        this.listarClientes();
      });
    },

    actualizarCliente(){
      axios.post(`${this.apphost}/cliente/editar`, this.clienteEdit)
      .then(()=>{
        $('#modalEditarCliente').modal('hide');
        this.listarClientes();
      });
    },

    abrirEditarCliente(c){
      this.clienteEdit = JSON.parse(JSON.stringify(c));
      $('#modalEditarCliente').modal('show');
    },


    crearOrder(){
      if(!this.nueva.cliente_id){
        alert('Seleccione cliente');
        return;
      }

      if(this.nueva.items.length === 0){
        alert('Agregue al menos un ítem');
        return;
      }

      axios.post(`${this.apphost}/product_order/crear`,{
        cliente_id: this.nueva.cliente_id,
        buyer: this.nueva.buyer,
        total_fees: this.totalOrden,
        items: this.nueva.items
      }).then(()=>{
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
      axios.get(`${this.apphost}/product_order/detalle/${o.product_order_id}`).then(r=>{
        this.detalle     = r.data.order;
        this.detallesOrder = r.data.detalles;
        $('#modalDetalleOrder').modal('show');
      });
    },

    abrirModalItem(){
      this.itemForm={ product_id:null, amount:1, price_item:0 };
      $('#modalAgregarItem').modal('show');
    },

    agregarItem(){
      const p = this.productos.find(
        x=>x.product_id==this.itemForm.product_id
      );

      this.nueva.items.push({
        product_id: p.product_id,
        product_name: p.name,
        amount: this.itemForm.amount,
        price_item: p.price
      });

      $('#modalAgregarItem').modal('hide');
    },

    eliminar(o){
      apprise(`¿Eliminar orden #${o.product_order_id}?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/product_order/eliminar`,{ product_order_id: o.product_order_id })
        .finally(()=>this.listar());
      });
    },

    // ----------- CRUD DETALLES -------------
    abrirCrearDetail(){
      this.detailForm = {
        order_id: this.detalle.product_order_id,
        product_id: null,
        amount: 1,
        price_item: 0
      };
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

    abrirModalItem(){
      this.itemForm = { product_id:null, amount:1, price_item:0 };
      $('#modalCrearDetail').modal('show');
    },

    eliminarDetail(d){
      apprise(`¿Eliminar ítem ${d.product_name}?`, {confirm:true}, ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/product_order_detail/eliminar`,{
          product_order_detail_id: d.product_order_detail_id
        })
        .then(()=>this.abrirDetalle(this.detalle));
      });
    },

    confirmarAgregarItem(){
      const p = this.productos.find(
        x => x.product_id == this.itemForm.product_id
      );

      if(!p){
        alert('Seleccione un producto');
        return;
      }

      if(this.itemForm.amount <= 0){
        alert('Cantidad inválida');
        return;
      }

      this.nueva.items.push({
        product_id: p.product_id,
        product_name: p.name,
        amount: this.itemForm.amount,
        price_item: p.price
      });

      $('#modalAgregarItemNuevaOrden').modal('hide');
    },


    abrirModalNuevoCliente(){
      this.clienteForm = { dni:'', nombre:'' };
      $('#modalNuevoCliente').modal('show');
    },    

    cargarProductos(){
        axios.get(`${this.apphost}/product/listar`)
        .then(r => this.productos = r.data);
    },
    onProductoChange(){
      const p = this.productos.find(
        x => x.product_id == this.detailForm.product_id
      );
      if(p){
        this.detailForm.price_item = p.price;
      }
    }
  },

  mounted(){
    // 🔐 restaurar JWT en Axios
    const jwt = localStorage.getItem('jwt');
    if(jwt){
      axios.defaults.headers.common['Authorization'] = `Bearer ${jwt}`;
    }
    
    this.cargarProductos();
    this.listar();

    axios.get(`${this.apphost}/cliente/listar`)
      .then(r=>{
        this.clientes = r.data.map(c => ({
          ...c,
          label: `${c.dni} - ${c.nombre}`
        }));
    });

    axios.get(`${this.apphost}/tipo_pago/listar`)
    .then(r => this.tiposPago = r.data);  
  },

  watch:{
    // cuando cambias producto en detalle
    'detailForm.product'(p){
      if(p){
        this.detailForm.price_item = p.price;
      }
    },

    // cuando seleccionas cliente
    'nueva.cliente'(c){
      if(c){
        this.nueva.cliente_id = c.cliente_id;
        this.nueva.buyer = c.nombre;
        this.nueva.address = c.direccion || '';
      }
    },

    'itemForm.product_id'(id){
      const p = this.productos.find(x => x.product_id == id);
      if(p){
        this.itemForm.price_item = p.price;
      }
    },

    // cuando cambia el total calculado
    totalOrden(v){
      this.nueva.total_fees = v;
    }
  },

  computed:{
    totalDetalle(){
      return (this.detailForm.amount || 0) *
             (this.detailForm.price_item || 0);
    },

    totalItem(){
      return this.detailForm.amount * this.detailForm.price_item;
    },

    totalOrden(){
      return this.nueva.items.reduce(
        (s,i)=> s + (i.amount * i.price_item),
        0
      ).toFixed(2);
    },
    
    totalItemNuevaOrden(){
      return (this.itemForm.amount || 0) *
             (this.itemForm.price_item || 0);
    }
  }

});
</script>
