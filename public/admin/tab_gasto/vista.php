<div class="row-fluid" id="appCategory">
  <div class="span12">
    <h2 class="titulo-fijo">Socios</h2>
    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Agregar categoría
      </button>
      <button class="btn btn-info" @click="abrirModalGastos">
        <i class="icon-list icon-white"></i> Gastos
      </button>
      <button class="btn btn-danger" @click="abrirModalReportes">
        <i class="icon-print icon-white"></i> Reportes
      </button>
    </div>
    <table id="tablaCategory" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Color</th>
          <th>Prioridad</th>
          <th>Participa reparto</th>
          <th>% Socio</th>
          <th>% Propietario</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
<!-- MODAL CREAR CATEGORY -->
<div id="modalCrearCategory" class="modal hide fade">
  <div class="modal-header">
    <h3>Nueva Categoría</h3>
  </div>
  <div class="modal-body">
    <div class="control-group">
      <label>Nombre</label>
      <div class="controls">
        <input v-model="nuevo.name" class="input-xxlarge">
      </div>
    </div>
    <div class="control-group">
      <label>Descripción</label>
      <div class="controls">
        <input v-model="nuevo.brief" class="input-xxlarge">
      </div>
    </div>
    <div class="control-group">
      <label>Color</label>
      <div class="controls">
        <input type="color" v-model="nuevo.color">
      </div>
    </div>
    <div class="control-group" style="padding: 4px;">
      <label class="checkbox">
        <input type="checkbox" v-model="nuevo.participa_reparto">
        Participa reparto
      </label>
    </div>
    <div class="control-group" v-if="nuevo.participa_reparto">
      <label>% Socio</label>
      <div class="controls">
        <input type="number" v-model.number="nuevo.porcentaje_socio">
      </div>
    </div>
    <div class="control-group" v-if="nuevo.participa_reparto">
      <label>% Propietario</label>
      <div class="controls">
        <input type="number" v-model.number="nuevo.porcentaje_propietario">
      </div>
    </div>
    <div class="control-group">
      <label>Activo</label>
      <div class="controls">
        <select v-model="nuevo.is_activo">
          <option :value="1">SI</option>
          <option :value="0">NO</option>
        </select>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" @click="crearCategory">
      Guardar
    </button>
    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>
  </div>
</div>
<!-- MODAL EDITAR -->
<div id="modalEditarCategory" class="modal hide fade">
  <div class="modal-header">
    <h3>Editar Categoría</h3>
  </div>
  <div class="modal-body">
    <div class="control-group">
      <label>Nombre</label>
      <div class="controls">
        <input v-model="form.name" class="input-xxlarge">
      </div>
    </div>
    <div class="control-group">
      <label>Descripción</label>
      <div class="controls">
        <input v-model="form.brief" class="input-xxlarge">
      </div>
    </div>
    <div class="control-group">
      <label>Color</label>
      <div class="controls">
        <input type="color" v-model="form.color">
      </div>
    </div>
    <div class="control-group" style="padding: 4px;">
      <label class="checkbox">
        <input type="checkbox" v-model="form.participa_reparto">
        Participa reparto
      </label>
    </div>
    <div class="control-group" v-if="form.participa_reparto">
      <label>% Socio</label>
      <div class="controls">
        <input type="number" v-model.number="form.porcentaje_socio">
      </div>
    </div>
    <div class="control-group" v-if="form.participa_reparto">
      <label>% Propietario</label>
      <div class="controls">
        <input type="number" v-model.number="form.porcentaje_propietario">
      </div>
    </div>
    <div class="control-group">
      <label>Activo</label>
      <div class="controls">
        <select v-model="form.is_activo">
          <option :value="1">SI</option>
          <option :value="0">NO</option>
        </select>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" @click="guardarEdicion">
      Guardar
    </button>
    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>
  </div>
</div>
<!-- MODAL GASTOS -->
<div id="modalGastos" class="modal hide fade" style="width:900px;margin-left:-450px">
  <div class="modal-header">
    <h3>Gastos por Categoría</h3>
  </div>
  <div class="modal-body">
    <button class="btn btn-success" @click="abrirModalCrearGasto">
      Agregar gasto
    </button>
    <table id="tablaGastos" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Categoría</th>
          <th>Concepto</th>
          <th>Monto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<!-- MODAL CREAR GASTO -->
<div id="modalCrearGasto" class="modal hide fade">
  <div class="modal-header">
    <h3>Nuevo gasto</h3>
  </div>
  <div class="modal-body">
    <div class="control-group">
      <label>Categoría</label>
      <div class="controls">
        <select v-model="nuevoGasto.rubro_category_id">
          <option
          v-for="c in categoriasActivas"
          :value="c.id"
          >
          {{c.name}}
        </option>
      </select>
    </div>
  </div>
  <div class="control-group">
    <label>Tipo de costo</label>
    <div class="controls">
      <v-select
      :options="tiposCosto"
      label="nombre"
      :reduce="o => o.tipo_costo_category_id"
      v-model="nuevoGasto.tipo_costo_category_id">
    </v-select>
  </div>
</div>
<div class="control-group">
  <label>Fecha</label>
  <div class="controls">
    <input type="date" v-model="nuevoGasto.fecha">
  </div>
</div>
<div class="control-group">
  <label>Concepto</label>
  <div class="controls">
    <input v-model="nuevoGasto.concepto" class="input-xxlarge">
  </div>
</div>
<div class="control-group">
  <label>Monto</label>
  <div class="controls">
    <input type="number" step="0.01" v-model.number="nuevoGasto.monto">
  </div>
</div>
</div>
<div class="modal-footer">
  <button class="btn btn-primary" @click="crearGasto">
    Guardar
  </button>
  <button class="btn" data-dismiss="modal">
    Cancelar
  </button>
</div>
</div>


<div id="modalEditarGasto" class="modal hide fade">

  <div class="modal-header">
    <h3>Editar gasto</h3>
  </div>

  <div class="modal-body">


    <div class="control-group">
      <label>Categoría</label>

      <div class="controls">

        <select v-model="formGasto.rubro_category_id">

          <option
          v-for="c in categoriasActivas"
          :value="c.id">

          {{c.name}}

        </option>

      </select>

    </div>

  </div>


  <div class="control-group">

    <label>Tipo de costo</label>

    <div class="controls">

      <v-select
      :options="tiposCosto"
      label="nombre"
      :reduce="o => o.tipo_costo_category_id"
      v-model="formGasto.tipo_costo_category_id">
    </v-select>

  </div>

</div>


<div class="control-group">

  <label>Fecha</label>

  <div class="controls">
    <input type="date" v-model="formGasto.fecha">
  </div>

</div>


<div class="control-group">

  <label>Concepto</label>

  <div class="controls">
    <input v-model="formGasto.concepto" class="input-xxlarge">
  </div>

</div>


<div class="control-group">

  <label>Monto</label>

  <div class="controls">
    <input type="number" step="0.01" v-model.number="formGasto.monto">
  </div>

</div>


</div>


<div class="modal-footer">

  <button class="btn btn-primary" @click="guardarEdicionGasto">
    Guardar
  </button>

  <button class="btn" data-dismiss="modal">
    Cancelar
  </button>

</div>

</div>


<div id="modalReportes" class="modal hide fade">

  <div class="modal-header">
    <h3>Reportes</h3>
  </div>

  <div class="modal-body">

    <div class="control-group">
      <label>Fecha inicio</label>
      <div class="controls">
        <input type="date" v-model="reporte.ini">
      </div>
    </div>

    <div class="control-group">
      <label>Fecha término</label>
      <div class="controls">
        <input type="date" v-model="reporte.fin">
      </div>
    </div>

  </div>

  <div class="modal-footer">

    <button class="btn btn-primary" @click="generarPDF">
      PDF
    </button>

    <button class="btn btn-warning" @click="cerrarDia">
      Cerrar día
    </button>

    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>

  </div>

</div>

</div>
</div>
<script>
  Vue.component('v-select', VueSelect.VueSelect);
  new Vue({
    el:'#appCategory',
    data:{
      apphost:(typeof apphost!='undefined'?apphost:''),
      categorias:[],
      categoriasActivas:[],
      gastos:[],
      nuevo:{
        name:'',
        brief:'',
        color:'#000000',
        priority:0,
        participa_reparto:false,
        porcentaje_socio:60,
        porcentaje_propietario:40,
        is_activo:1
      },
      reporte:{
        ini:'',
        fin:''
      },
      form:{},
      nuevoGasto:{
        rubro_category_id:null,
        tipo_costo_category_id:null,
        fecha:'',
        concepto:'',
        monto:null
      },     


      formGasto:{
        gasto_rubro_id:null,
        rubro_category_id:null,
        tipo_costo_category_id:null,
        fecha:'',
        concepto:'',
        monto:null
      },

      dt:null,
      dtGastos:null,
      tiposCosto:[],
    },
    methods:{
      bloquear(msg){
        $.blockUI({
          message:'<h4>'+msg+'</h4>'
        })
      },
      desbloquear(){
        $.unblockUI()
      },
      hoy(){
        let d=new Date()
        let m=('0'+(d.getMonth()+1)).slice(-2)
        let day=('0'+d.getDate()).slice(-2)
        return d.getFullYear()+"-"+m+"-"+day
      },
      listarCategorias(){
        axios.get(`${this.apphost}/reg/category/listar`)
        .then(r=>{
          this.categorias=r.data
          this.$nextTick(()=>{
            if(!this.dt){
              this.dt=$("#tablaCategory").DataTable({
                language:dt_language
              })
            }
            this.dt.clear()
            this.categorias.forEach(c=>{
              let participa=c.participa_reparto==1
              ?'<span class="label label-success">SI</span>'
              :'<span class="label">NO</span>'
              let activo=c.is_activo==1
              ?'<span class="label label-success">SI</span>'
              :'<span class="label label-important">NO</span>'
              let acciones=`
              <a href="#" class="editarCategoria" data-id="${c.id}">
              Editar
              </a>
              `
              this.dt.row.add([
                c.id,
                c.name,
                c.color,
                c.priority,
                participa,
                c.porcentaje_socio,
                c.porcentaje_propietario,
                activo,
                acciones
              ])
            })
            this.dt.draw(false)
          })
        })
      },
      cargarCategoriasActivas(){
        axios.get(`${this.apphost}/reg/category/activas`)
        .then(r=>{
          this.categoriasActivas=r.data
        })
      },
      abrirModalCrear(){

        this.nuevo = {
          name:'',
          brief:'',
          color:'#000000',
          priority:0,
          participa_reparto:false,
          porcentaje_socio:60,
          porcentaje_propietario:40,
          is_activo:1
        }

        $("#modalCrearCategory").modal("show")

      },

      abrirModalEditarGasto(g){

        this.formGasto = Object.assign({},g)

        $("#modalEditarGasto").modal("show")

      },

      crearCategory(){
        axios.post(`${this.apphost}/reg/category/crear`,this.nuevo)
        .then(()=>{
          $("#modalCrearCategory").modal("hide")
          this.listarCategorias()
          this.cargarCategoriasActivas()
        })
      },
      abrirModalEditar(c){
        this.form=Object.assign({},c)
        $("#modalEditarCategory").modal("show")
      },
      guardarEdicion(){
        axios.post(`${this.apphost}/reg/category/editar`,this.form)
        .then(()=>{
          $("#modalEditarCategory").modal("hide")
          this.listarCategorias()
          this.cargarCategoriasActivas()
        })
      },

      guardarEdicionGasto(){

        axios.post(`${this.apphost}/reg/pos_gasto_rubro/editar`,this.formGasto)
        .then(()=>{

          $("#modalEditarGasto").modal("hide")

          this.listarGastos()

        })

      },

      listarGastos(){
        axios.get(`${this.apphost}/reg/pos_gasto_rubro/listar`)
        .then(r=>{
          this.gastos = r.data
          this.$nextTick(()=>{
            if(!this.dtGastos){
              this.dtGastos = $("#tablaGastos").DataTable({
                language: dt_language
              })
            }
            this.dtGastos.clear()
            this.gastos.forEach(g=>{
              let acciones = `
      <a href="#" class="editarGasto" data-id="${g.gasto_rubro_id}">
      Editar
      </a>
              `
              this.dtGastos.row.add([
                g.gasto_rubro_id,
                g.fecha,
                g.category_nombre,
                g.concepto,
                g.monto,
                acciones
              ])
            })
            this.dtGastos.draw(false)
          })
        })
      },
      cargarTiposCosto(){
        axios.get(`${this.apphost}/reg/pos_tipo_costo_category/listar`)
        .then(r=>{
          this.tiposCosto = r.data.data;
        })
      },
      abrirModalGastos(){
        this.listarGastos()
        $("#modalGastos").modal("show")
      },
      abrirModalCrearGasto(){
        this.nuevoGasto={
          rubro_category_id:null,
          tipo_costo_category_id:null,
          fecha:this.hoy(),
          concepto:'',
          monto:null
        };
        $("#modalCrearGasto").modal("show")
      },
      crearGasto() {

        axios.post(`${this.apphost}/reg/pos_gasto_rubro/crear`, {
          rubro_category_id: this.nuevoGasto.rubro_category_id,
          tipo_costo_category_id: this.nuevoGasto.tipo_costo_category_id,
          fecha: this.nuevoGasto.fecha,
          concepto: this.nuevoGasto.concepto,
          monto: this.nuevoGasto.monto
        })
        .then(() => {

          $('#modalCrearGasto').modal('hide');
          this.listarGastos();

        })
        .catch(err => {

          console.error(err);

        });

      },
      generarPDF(){

        let url = `${this.apphost}/reg/reportes/utilidad?ini=${this.reporte.ini}&fin=${this.reporte.fin}`

        window.open(url)

      },
      cerrarDia(){

        if(!confirm("¿Seguro que desea ejecutar el cierre del día?")){
          return
        }

        axios.post(`${this.apphost}/reg/pos_rubro/cerrar_dia`)
        .then(()=>{

          alert("Cierre diario ejecutado correctamente")

        })

      },
      abrirModalReportes(){

        this.reporte.ini=this.hoy()
        this.reporte.fin=this.hoy()

        $("#modalReportes").modal("show")

      }
    },
    mounted() {

      this.listarCategorias();
      this.cargarCategoriasActivas();
      this.cargarTiposCosto();

      $("#tablaCategory").on("click", ".editarCategoria", (e) => {
        let id = $(e.currentTarget).data("id");
        let cat = this.categorias.find(c => c.id == id);
        this.abrirModalEditar(cat);
      });

      $("#tablaGastos").on("click", ".editarGasto", (e) => {
        let id = $(e.currentTarget).data("id");
        let gasto = this.gastos.find(g => g.gasto_rubro_id == id);
        console.log(gasto);
            // aquí luego abrirás modal editar gasto
      });

      $("#tablaGastos").on("click",".editarGasto",(e)=>{

        let id=$(e.currentTarget).data("id")

        let gasto=this.gastos.find(g=>g.gasto_rubro_id==id)

        this.abrirModalEditarGasto(gasto)

      });

    }
  })
</script>