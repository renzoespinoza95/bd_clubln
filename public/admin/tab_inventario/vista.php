<div class="row-fluid" id="appInventario">

  <div class="span12">
    <h2>Inventario</h2>

    <!-- TABLA -->
    <table id="tablaInv" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>

    <!-- MODAL DETALLE -->
    <div id="modalDetalleInv" class="modal hide fade">
      <div class="modal-header"><h3>Detalle Inventario</h3></div>
      <div class="modal-body">

        <p><b>Producto:</b> {{ detalle.inventario.producto }}</p>
        <p><b>Stock:</b> {{ detalle.inventario.stock_actual }}</p>

        <h4>Movimientos:</h4>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Origen</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="m in detalle.movimientos">
              <td>{{ m.fecha }}</td>
              <td>{{ m.tipo }}</td>
              <td>{{ m.origen }}</td>
              <td>{{ m.cantidad }}</td>
              <td>{{ m.precio_unitario }}</td>
              <td>{{ m.stock_resultante }}</td>
            </tr>
          </tbody>
        </table>

      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- MODAL CREAR -->
    <div id="modalCrearInv" class="modal hide fade">
      <div class="modal-header"><h3>Nuevo Inventario</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label>Producto</label>
          <div class="controls">
            <select v-model="nuevo.producto_id">
              <option v-for="p in productos" :value="p.product_id">{{ p.name }}</option>
            </select>
          </div>
        </div>

        <div class="control-group">
          <label>Stock Inicial</label>
          <div class="controls"><input v-model="nuevo.stock_actual"></div>
        </div>

        <div class="control-group">
          <label>Stock Mínimo</label>
          <div class="controls"><input v-model="nuevo.stock_min"></div>
        </div>

        <div class="control-group">
          <label>Stock Máximo</label>
          <div class="controls"><input v-model="nuevo.stock_max"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crear">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- MODAL EDITAR -->
    <div id="modalEditarInv" class="modal hide fade">
      <div class="modal-header"><h3>Editar Inventario</h3></div>
      <div class="modal-body">

        <p><b>Producto:</b> {{ form.producto }}</p>

        <div class="control-group">
          <label>Mínimo</label>
          <div class="controls"><input v-model="form.stock_min"></div>
        </div>

        <div class="control-group">
          <label>Máximo</label>
          <div class="controls"><input v-model="form.stock_max"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardar">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

<div id="modalAjusteInv" class="modal hide fade">
  <div class="modal-header">
    <h3>Ajuste de Inventario</h3>
  </div>

  <div class="modal-body">

    <p><b>Producto:</b> {{ ajuste.producto }}</p>
    <p><b>Stock actual:</b> {{ ajuste.stock_actual }}</p>

    <div class="control-group">
      <label>Stock real (conteo físico)</label>
      <div class="controls">
        <input type="number" v-model.number="ajuste.stock_real">
      </div>
    </div>

    <!-- PREVIEW DIFERENCIA -->
    <div v-if="ajuste.stock_real !== null" style="margin-top:10px;">
      <b>Diferencia:</b>
      <span v-if="diferencia > 0" style="color:green;">
        +{{ diferencia }} (ENTRADA)
      </span>
      <span v-else-if="diferencia < 0" style="color:red;">
        {{ diferencia }} (SALIDA)
      </span>
      <span v-else>
        0 (sin cambios)
      </span>
    </div>

  </div>

  <div class="modal-footer">
    <button class="btn btn-primary" @click="guardarAjuste">
      Guardar ajuste
    </button>
    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>
  </div>
</div>    

    <div id="modalLimitesInv" class="modal hide fade">
  <div class="modal-header">
    <h3>Establecer límites</h3>
  </div>

  <div class="modal-body">

    <p><b>Producto:</b> {{ form.producto }}</p>

    <div class="control-group">
      <label>Stock mínimo</label>
      <div class="controls">
        <input type="number" v-model.number="form.stock_min">
      </div>
    </div>

    <div class="control-group">
      <label>Stock máximo</label>
      <div class="controls">
        <input type="number" v-model.number="form.stock_max">
      </div>
    </div>

  </div>

  <div class="modal-footer">
    <button class="btn btn-primary" @click="guardarLimites">
      Guardar
    </button>
    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>
  </div>
</div>


  </div>
</div>


<script>
new Vue({
  el:"#appInventario",
  data:{
    apphost:(typeof apphost!=='undefined'?apphost:''),
    inventario:[],
    productos:[],
    detalle:{inventario:{},movimientos:[]},
    nuevo:{ producto_id:null, stock_actual:0, stock_min:0, stock_max:999 },
    form:{},
    ajuste:{
  inventario_id:null,
  product_id:null,
  producto:'',
  stock_actual:0,
  stock_real:null
},
    dt:null
  },

  methods:{

    cargarProductos(){
      axios.get(`${this.apphost}/inventario/productos`)
           .then(r=>this.productos=r.data);
    },

    listar(){
      axios.get(`${this.apphost}/inventario/listar`).then(r=>{
        this.inventario = r.data;

        this.$nextTick(()=>{

          if(!this.dt){
            this.dt = $('#tablaInv').DataTable({
              dom:'frtip'
            });

            const self=this;
            $("#tablaInv tbody")
              .on("click","a.detalle",function(){
                const id=$(this).data("id");
                const item=self.inventario.find(x=>x.inventario_id==id);
                self.verDetalle(item);
              })
              .on("click","a.editar",function(){
                const id=$(this).data("id");
                const item=self.inventario.find(x=>x.inventario_id==id);
                self.abrirEditar(item);
              })
              .on("click","a.ajuste",function(){
                const id=$(this).data("id");
                const item=self.inventario.find(x=>x.inventario_id==id);
                self.abrirAjuste(item);
              })              
              .on("click","a.limites",function(){
                const id = $(this).data("id");
                const item = self.inventario.find(x => x.inventario_id == id);
                self.abrirLimites(item);
              })
              .on("click","a.eliminar",function(){
                const id=$(this).data("id");
                const item=self.inventario.find(x=>x.inventario_id==id);
                self.eliminar(item);
              });
          }

          this.dt.clear();
          this.inventario.forEach(i=>{
            const acciones = `
              <div class="btn-group">
                <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                  Opciones <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a href="#" class="detalle" data-id="${i.inventario_id}">
                      Detalle
                    </a>
                  </li>                  
                  <li>
                    <a href="#" class="ajuste" data-id="${i.inventario_id}">
                      Ajustar stock
                    </a>
                  </li>              
                </ul>
              </div>`;

            const iconoStock = i.stock_actual > i.stock_min
              ? `<i class="fa fa-thumbs-up" style="color:green"></i>`
              : `<i class="fa fa-thumbs-down" style="color:red"></i>`;

            const stockTxt = `
              ${i.stock_actual}
              &nbsp;${iconoStock}
            `;
  


            this.dt.row.add([
              i.inventario_id,
              i.producto,
              stockTxt,
              acciones
            ]);
          });
          this.dt.draw(false);

        });
      });
    },

    abrirLimites(i){
      this.form = JSON.parse(JSON.stringify(i));
      $('#modalLimitesInv').modal('show');
    },

    guardarLimites(){
      axios.post(`${this.apphost}/inventario/limites`,{
        inventario_id: this.form.inventario_id,
        stock_min: this.form.stock_min,
        stock_max: this.form.stock_max
      }).then(()=>{
        $('#modalLimitesInv').modal('hide');
        this.listar();
      });
    },


    verDetalle(i){
      axios.get(`${this.apphost}/inventario/detalle/${i.inventario_id}`).then(r=>{
        this.detalle = r.data;
        $("#modalDetalleInv").modal("show");
      });
    },

    abrirCrear(){
      this.nuevo={producto_id:null,stock_actual:0,stock_min:0,stock_max:999};
      $("#modalCrearInv").modal("show");
    },

    crear(){
      axios.post(`${this.apphost}/inventario/crear`,this.nuevo)
           .then(()=>{ $("#modalCrearInv").modal("hide"); this.listar(); });
    },
    abrirAjuste(i){
      this.ajuste = {
        inventario_id: i.inventario_id,
        product_id: i.product_id,
        producto: i.producto,
        stock_actual: parseInt(i.stock_actual),
        stock_real: parseInt(i.stock_actual)
      };
      $("#modalAjusteInv").modal("show");
    },

    guardarAjuste(){

      if(this.ajuste.stock_real === null){
        alert("Ingresa stock real");
        return;
      }

      axios.post(`${this.apphost}/inventario/ajuste`,{
        product_id: this.ajuste.product_id,
        stock_real: this.ajuste.stock_real
      })
      .then(r=>{
        $("#modalAjusteInv").modal("hide");
        this.listar();
      });
    },    

    abrirEditar(i){
      this.form = JSON.parse(JSON.stringify(i));
      $("#modalEditarInv").modal("show");
    },

    guardar(){
      axios.post(`${this.apphost}/inventario/editar`,this.form)
           .then(()=>{ $("#modalEditarInv").modal("hide"); this.listar(); });
    },

    eliminar(i){
      apprise(`¿Eliminar inventario del producto <b>${i.producto}</b>?`,{confirm:true},ok=>{
        if(!ok) return;
        axios.post(`${this.apphost}/inventario/eliminar`,{inventario_id:i.inventario_id})
             .finally(()=>this.listar());
      });
    }

  },

  mounted(){
    this.cargarProductos();
    this.listar();
  },
  computed:{
    diferencia(){
      if(this.ajuste.stock_real === null) return 0;
      return this.ajuste.stock_real - this.ajuste.stock_actual;
    }
  },
});
</script>
