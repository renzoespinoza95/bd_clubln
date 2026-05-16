<div class="row-fluid" id="appBalance">
  <div class="span12">

    <h2>Balance</h2>

    <div class="form-actions">
      <button class="btn btn-warning" @click="abrirCajaChica">
        Caja Chica
      </button>

      <button class="btn btn-info" @click="abrirComprobantes">
        Comprobantes
      </button>

      <button class="btn btn-danger" @click="abrirDeudas">
        Deudas
      </button>
    </div>

    <!-- INFORME ECONOMICO -->
    <div class="well" style="background:#fff; border:1px solid #ddd; padding:20px; margin-bottom:20px;">

<!-- =========================================
     FILTROS
========================================= -->
<div style="margin-bottom:20px;">

  <div class="row-fluid">

    <!-- FECHA INICIO -->
    <div class="span3">
      <label>Fecha inicio</label>
      <input 
        type="date"
        class="input-block-level"
        v-model="filtro.fecha_inicio">
    </div>

    <!-- FECHA FIN -->
    <div class="span3">
      <label>Fecha término</label>
      <input 
        type="date"
        class="input-block-level"
        v-model="filtro.fecha_fin">
    </div>

    <!-- BOTON -->
    <div class="span2" style="padding-top:25px;">
      <button 
        class="btn btn-danger"
        style="width:100%;"
        @click="calcularBalance">
        Calcular
      </button>
    </div>

    <!-- SELECT MES -->
<div class="span4" style="padding-top:25px; text-align:right;">

  <div style="
    display:inline-block;
    position:relative;
  ">

    <!-- ICONO -->
    <i class="icon-calendar" style="
      position:absolute;
      left:12px;
      top:13px;
      color:#ffffff;
      font-size:14px;
      z-index:2;
      pointer-events:none;
    "></i>

    <select 
      class="input-xlarge"
      v-model="mesSeleccionado"
      @change="seleccionarMes"

      :style="{
        height:'42px',
        paddingLeft:'38px',
        paddingRight:'15px',

        border:'none',
        borderRadius:'10px',

        background:'linear-gradient(135deg,#ff512f 0%,#dd2476 100%)',

        color: mesSeleccionado ? '#ffffff' : '#ffe5f1',

        fontWeight:'700',
        fontSize:'14px',

        boxShadow:'0 6px 18px rgba(221,36,118,0.35)',

        outline:'none',
        cursor:'pointer',

        appearance:'none',
        WebkitAppearance:'none',
        MozAppearance:'none'
      }"

      onmouseover="
        this.style.transform='translateY(-2px)';
        this.style.boxShadow='0 10px 24px rgba(221,36,118,0.45)';
      "

      onmouseout="
        this.style.transform='translateY(0px)';
        this.style.boxShadow='0 6px 18px rgba(221,36,118,0.35)';
      "
    >

      <!-- DEFAULT -->
      <option 
        disabled
        value=""
        style="
          color:#555;
          background:#fff;
        ">
        📅 Seleccionar mes
      </option>

      <!-- MESES -->
      <option 
        v-for="m in mesesDisponibles"
        :value="m.value"
        style="
          color:#111;
          background:#fff;
        ">

        {{ m.label }}

      </option>

    </select>

  </div>

</div>

  </div>

</div>
      

  
  <div style="text-align:center; margin-bottom:30px;">

  <div style="
    display:inline-block;
    text-align:left;
    background:#ffffff;
    padding:25px 35px;
    border-radius:10px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
    border:1px solid #e5e7eb;
    min-width:500px;
  ">

    <!-- TÍTULO -->
    <h2 style="
      margin-bottom:5px;
      text-align:center;
      font-weight:600;
      color:#1f2937;
      letter-spacing:0.5px;
    ">
      INFORME ECONÓMICO MENSUAL
    </h2>

<p style="
  text-align:center;
  margin-bottom:15px;
  font-size:13px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:0.6px;
">

  <span style="color:#6b7280;">
    DEL
  </span>

  <span style="color:#ef4444;">
    {{ fechaInicioMesTexto }}
  </span>

  <span style="color:#6b7280;">
    AL
  </span>

  <span style="color:#ef4444;">
    {{ fechaFinMesTexto }}
  </span>

</p>

    <hr style="margin:15px 0; border-color:#f1f5f9;">

    <div class="row-fluid">

      <!-- INGRESOS -->
      <div class="span6" style="padding-right:15px; border-right:1px solid #f1f5f9;">
        <h4 style="color:#10b981; font-weight:600; margin-bottom:10px;">
          INGRESOS
        </h4>

        <p style="color:#6b7280;">Ventas (empresa)</p>
        <h3 style="color:#059669; margin-bottom:10px;">
          S/ {{ fmt(resumenMes.ventas) }}
        </h3>

        <p style="color:#6b7280;">Caja chica</p>
        <h4 style="margin-bottom:10px;">
          S/ {{ fmt(resumenMes.caja_ing) }}
        </h4>

        <p style="color:#6b7280;">Facturados</p>
        <h4>
          S/ {{ fmt(resumenMes.facturado_ing) }}
        </h4>
      </div>

      <!-- EGRESOS -->
      <div class="span6" style="padding-left:15px;">
        <h4 style="color:#ef4444; font-weight:600; margin-bottom:10px;">
          EGRESOS
        </h4>

        <p style="color:#6b7280;">Caja chica</p>
        <h4 style="color:#dc2626; margin-bottom:10px;">
          S/ {{ fmt(resumenMes.caja_eg) }}
        </h4>

        <p>Pago de deudas</p>
        <h4 style="color:#c0392b;">
          S/ {{ fmt(resumenMes.pago_deuda) }}
        </h4>        

        <p style="color:#6b7280;">Compras</p>
        <h4 style="margin-bottom:10px;">
          S/ {{ fmt(resumenMes.compras) }}
        </h4>

        <p style="color:#6b7280;">Facturados</p>
        <h4>
          S/ {{ fmt(resumenMes.facturado_eg) }}
        </h4>
      </div>

    </div>
  </div>

</div>



  <hr>

  <!-- =========================
       DETALLE INGRESOS
  ========================== -->
  <h4>INGRESOS POR CATEGORÍA</h4>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Categoría</th>
        <th>Venta Total</th>
        <th>% Prop</th>
        <th>Empresa</th>
        <th>% Socio</th>
        <th>Socio</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="i in resumenMes.detalle_ingresos">
        <td>{{ i.name }}</td>
        <td>S/ {{ fmt(i.total_venta) }}</td>
        <td>{{ i.porcentaje_propietario }}%</td>
        <td style="color:#27ae60;">S/ {{ fmt(i.ingreso_empresa) }}</td>
        <td>{{ i.porcentaje_socio }}%</td>
        <td style="color:#2980b9;">S/ {{ fmt(i.ingreso_socio) }}</td>
      </tr>
    </tbody>
  </table>

  <!-- =========================
       DETALLE EGRESOS
  ========================== -->
  <h4>EGRESOS POR CATEGORÍA</h4>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Categoría</th>
        <th>Gasto Total</th>
        <th>Empresa</th>
        <th>Socio</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="e in resumenMes.detalle_egresos">
        <td>{{ e.name }}</td>
        <td>S/ {{ fmt(e.total_gasto) }}</td>
        <td style="color:#c0392b;">S/ {{ fmt(e.gasto_empresa) }}</td>
        <td style="color:#8e44ad;">S/ {{ fmt(e.gasto_socio) }}</td>
      </tr>
    </tbody>
  </table>

  <!-- =========================
       UTILIDAD POR CATEGORÍA
  ========================== -->
  <h4>UTILIDAD POR SOCIO (CATEGORÍA)</h4>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Categoría</th>
        <th>Utilidad Empresa</th>
        <th>Utilidad Socio</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="u in resumenMes.detalle_utilidad">
        <td>{{ u.name }}</td>
        <td :style="{color: u.utilidad_empresa >= 0 ? '#27ae60' : '#c0392b'}">
          S/ {{ fmt(u.utilidad_empresa) }}
        </td>
        <td :style="{color: u.utilidad_socio >= 0 ? '#2980b9' : '#c0392b'}">
          S/ {{ fmt(u.utilidad_socio) }}
        </td>
      </tr>
    </tbody>
  </table>

  <hr>

  <!-- =========================
       TOTALES
  ========================== -->
  <div style="text-align:center;">

    <h3>
      TOTAL INGRESOS:
      <span style="color:#27ae60;">
        S/ {{ fmt(resumenMes.total_ingresos) }}
      </span>
    </h3>

    <h3>
      TOTAL EGRESOS:
      <span style="color:#c0392b;">
        S/ {{ fmt(resumenMes.total_egresos) }}
      </span>
    </h3>

    <h2>
      UTILIDAD:
      <span :style="{color: resumenMes.utilidad >= 0 ? '#27ae60' : '#c0392b'}">
        S/ {{ fmt(resumenMes.utilidad) }}
      </span>
    </h2>

  </div>

</div>

    <!-- =========================
        DETALLE INGRESOS
    ========================== -->
    <div id="modalIngresos" class="modal hide fade">
      <div class="modal-header"><h3>Detalle Ingresos</h3></div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr v-for="i in ingresos">
            <td>{{i.fecha}}</td>
            <td>{{i.tipo}}</td>
            <td>{{i.monto}}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- =========================
        DETALLE EGRESOS
    ========================== -->
    <div id="modalEgresos" class="modal hide fade">
      <div class="modal-header"><h3>Detalle Egresos</h3></div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr v-for="e in egresos">
            <td>{{e.fecha}}</td>
            <td>{{e.tipo}}</td>
            <td>{{e.monto}}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- =========================
        DEUDAS
    ========================== -->
    <div id="modalDeudas" class="modal hide fade">
      <div class="modal-header">
        <h3>Deudas de la empresa</h3>
      </div>

      <div class="modal-body">

        <table id="tablaDeudas" class="table table-bordered">

          <thead>
            <tr>
              <th>ID</th>
              <th>Entidad</th>
              <th>Tipo</th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Saldo</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="d in deudas">
              <td>{{ d.deuda_id }}</td>
              <td>
                {{ d.tipo_entidad == 'PERSONA'
                ? d.nombre_persona
                : d.proveedor_nombre }}
              </td>

              <td>{{ d.tipo_entidad }}</td>
              <td>{{ d.monto_total }}</td>
              <td>{{ d.pagado }}</td>
              <td :style="{color: d.saldo_pendiente > 0 ? 'red' : 'green'}">
                {{ d.saldo_pendiente }}
              </td>

              <td>
                <button class="btn btn-mini" @click="abrirPagar(d)">
                  Pagar
                </button>
              </td>
            </tr>
          </tbody>

        </table>

        <button class="btn btn-success" @click="abrirCrearDeuda">
          Nueva deuda
        </button>

      </div>
    </div>

    <!-- REGISTRAR DEUDA -->
    <div id="modalCrearDeuda" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Registrar Deuda</h3>
  </div>

  <div class="modal-body">

    <!-- TIPO -->
    <div class="control-group">
      <label class="control-label">Tipo:</label>
      <div class="controls">
        <label class="radio inline">
          <input type="radio" value="PERSONA" v-model="deuda.tipo_entidad"> Persona
        </label>
        <label class="radio inline">
          <input type="radio" value="PROVEEDOR" v-model="deuda.tipo_entidad"> Proveedor
        </label>
      </div>
    </div>

    <!-- PERSONA -->
    <div class="control-group" v-if="deuda.tipo_entidad=='PERSONA'">
      <label class="control-label">Nombre:</label>
      <div class="controls">
        <input type="text" class="input-xlarge" v-model="deuda.nombre_persona">
      </div>
    </div>

    <!-- PROVEEDOR -->
    <div class="control-group" v-if="deuda.tipo_entidad=='PROVEEDOR'">
      <label class="control-label">Proveedor:</label>
      <div class="controls">

        <v-select
          :options="proveedores"
          label="nombre"
          v-model="deuda.proveedor_obj"
          placeholder="Seleccione proveedor">
        </v-select>

      </div>
    </div>

    <!-- MONTO -->
    <div class="control-group">
      <label class="control-label">Monto total:</label>
      <div class="controls">
        <input type="number" class="input-medium" v-model="deuda.monto_total">
      </div>
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="control-group">
      <label class="control-label">Descripción:</label>
      <div class="controls">
        <textarea class="input-xlarge" v-model="deuda.descripcion"></textarea>
      </div>
    </div>

  </div>

  <div class="modal-footer">
    <button class="btn btn-primary" @click="guardarDeuda">
      Guardar
    </button>
    <button class="btn" data-dismiss="modal">
      Cancelar
    </button>
  </div>
</div>
    <!-- PAGAR -->
    <div id="modalPagar" class="modal hide fade">

  <!-- HEADER -->
  <div class="modal-header" style="border-bottom:1px solid #eee;">
    <h3 style="margin:0; font-weight:600; color:#1f2937;">
      💳 Pagar deuda
    </h3>
  </div>

  <!-- BODY -->
  <div class="modal-body" style="padding:20px 25px;">

    <!-- INFO -->
    <div style="
      background:#f9fafb;
      border:1px solid #e5e7eb;
      border-radius:8px;
      padding:15px;
      margin-bottom:20px;
    ">

      <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span style="color:#6b7280;">Total deuda</span>
        <strong style="color:#111827;">
          S/ {{ fmt(deudaSel.monto_total) }}
        </strong>
      </div>

      <div style="display:flex; justify-content:space-between;">
        <span style="color:#6b7280;">Saldo pendiente</span>
        <strong style="color:#dc2626; font-size:16px;">
          S/ {{ fmt(deudaSel.saldo_pendiente) }}
        </strong>
      </div>

    </div>

    <!-- MONTO -->
    <div class="control-group" style="margin-bottom:15px;">
      <label style="color:#374151;">Monto a pagar</label>
      <div class="controls">
        <input 
          type="number"
          class="input-block-level"
          v-model="pago.monto"
          placeholder="Ingrese monto"
          style="
            height:38px;
            border-radius:6px;
            border:1px solid #d1d5db;
          ">
      </div>
    </div>

    <!-- MEDIO PAGO -->
    <div class="control-group">
      <label style="color:#374151;">Medio de pago</label>
      <div class="controls">
        <select 
          class="input-block-level"
          v-model="pago.medio_pago"
          style="
            height:38px;
            border-radius:6px;
            border:1px solid #d1d5db;
          ">
          <option value="">Seleccione</option>
          <option value="EFECTIVO">💵 EFECTIVO</option>
          <option value="BANCO">🏦 BANCO</option>
        </select>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <div class="modal-footer" style="border-top:1px solid #eee;">

    <button 
      class="btn"
      data-dismiss="modal"
      style="
        border-radius:6px;
        padding:6px 15px;
      ">
      Cancelar
    </button>

    <button 
      class="btn btn-success"
      @click="pagar"
      style="
        border-radius:6px;
        padding:6px 15px;
        font-weight:600;
      ">
      💰 Pagar
    </button>

  </div>

</div>

    <!-- CAJA CHICA -->
    <div id="modalCaja" class="modal hide fade">
      <div class="modal-header">
        <h3>Movimientos Caja Chica</h3>
      </div>

      <div class="modal-body">

        <table id="tablaCaja" class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Categoría</th>
              <th>Descripción</th>
              <th>Monto</th>
            </tr>
          </thead>

          <tbody>
          </tbody>
        </table>

        <hr>

        <p><b>Total ingresos:</b> S/ {{ totalIngresos }}</p>
        <p><b>Total egresos:</b> S/ {{ totalEgresos }}</p>

      </div>

      <div class="modal-footer">
        <button class="btn btn-success" @click="abrirRegistrarCaja">
          Agregar
        </button>
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

<div id="modalRegistrarCaja" class="modal hide fade">

  <!-- HEADER -->
  <div class="modal-header" style="border-bottom:1px solid #eee;">
    <h3 style="margin:0; font-weight:600; color:#1f2937;">
      💼 Registrar Caja Chica
    </h3>
  </div>

  <!-- BODY -->
  <div class="modal-body" style="padding:20px 25px;">

    <!-- BLOQUE PRINCIPAL -->
    <div style="
      background:#f9fafb;
      border:1px solid #e5e7eb;
      border-radius:10px;
      padding:18px;
      margin-bottom:20px;
    ">

      <!-- TIPO -->
      <div class="control-group" style="margin-bottom:15px;">
        <label style="color:#374151;">Tipo</label>
        <div>
          <label class="radio inline">
            <input type="radio" value="INGRESO" v-model="caja.tipo"> 💰 INGRESO
          </label>
          <label class="radio inline">
            <input type="radio" value="EGRESO" v-model="caja.tipo"> 💸 EGRESO
          </label>
        </div>
      </div>

      <!-- CATEGORIA -->
      <div class="control-group">
        <label style="color:#374151;">Categoría</label>
        <div>
          <label class="radio">
            <input type="radio" value="CAJA_CHICA" v-model="caja.categoria">
            Caja chica
          </label>
          <label class="radio">
            <input type="radio" value="FACTURADO" v-model="caja.categoria">
            Facturado
          </label>
        </div>
      </div>

    </div>

    <!-- =========================
        FACTURADO
    ========================== -->
    <div v-if="caja.categoria=='FACTURADO'" style="
      background:#ffffff;
      border:1px dashed #d1d5db;
      border-radius:10px;
      padding:15px;
      margin-bottom:20px;
    ">

      <h4 style="margin-bottom:10px; color:#374151;">
        🧾 Datos del comprobante
      </h4>

      <div class="control-group">
        <label>Tipo comprobante</label>
        <label class="radio inline">
          <input type="radio" value="BOLETA" v-model="caja.tipo_comprobante"> BOLETA
        </label>
        <label class="radio inline">
          <input type="radio" value="FACTURA" v-model="caja.tipo_comprobante"> FACTURA
        </label>
      </div>

      <div class="control-group">
        <label>Número</label>
        <input type="text" class="input-block-level" v-model="caja.numero">
      </div>

      <!-- CLIENTE -->
      <div v-if="caja.tipo=='INGRESO'" class="control-group">
        <label>Cliente</label>
        <v-select
          :options="clientes"
          label="nombre"
          v-model="caja.cliente_obj"
          placeholder="Seleccione cliente">
        </v-select>
      </div>

      <!-- PROVEEDOR -->
      <div v-if="caja.tipo=='EGRESO'" class="control-group">
        <label>Proveedor</label>
        <v-select
          :options="proveedores"
          label="nombre"
          v-model="caja.proveedor_obj"
          placeholder="Seleccione proveedor">
        </v-select>
      </div>

    </div>

    <!-- GENERALES -->
    <div class="control-group">
      <label>Descripción</label>
      <input 
        type="text" 
        class="input-block-level"
        v-model="caja.descripcion"
        style="height:36px; border-radius:6px;">
    </div>

    <div class="control-group">
      <label>Monto</label>
      <input 
        type="number" 
        class="input-block-level"
        v-model="caja.monto"
        style="height:36px; border-radius:6px;">
    </div>

    <div class="control-group">
      <label>Fecha</label>
      <input 
        type="date" 
        class="input-block-level"
        v-model="caja.fecha"
        style="height:36px; border-radius:6px;">
    </div>

  </div>

  <!-- FOOTER -->
  <div class="modal-footer" style="border-top:1px solid #eee;">

    <button 
      class="btn"
      data-dismiss="modal"
      style="
        border-radius:6px;
        padding:6px 15px;
      ">
      Cancelar
    </button>

    <button 
      class="btn btn-primary"
      @click="guardarCaja"
      style="
        border-radius:6px;
        padding:6px 15px;
        font-weight:600;
      ">
      💾 Guardar
    </button>

  </div>

</div>

    <!-- COMPROBANTES -->
    <div id="modalComprobantes" class="modal hide fade">
  <div class="modal-header">
    <h3>Comprobantes</h3>
  </div>

  <div class="modal-body">

    <table id="tablaComprobantes" class="table table-bordered">

      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Tipo</th>
          <th>Número</th>
          <th>Cliente / Proveedor</th>
          <th>Monto</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="c in comprobantes">
          <td>{{ c.comprobante_id }}</td>
          <td>{{ formatearFecha(c.fecha) }}</td>

          <td>{{ c.tipo_comprobante }}</td>

          <td>{{ c.numero }}</td>

          <td>
            {{ c.tipo_entidad == 'CLIENTE'
                ? c.cliente_nombre
                : c.proveedor_nombre }}
          </td>

          <td>{{ c.monto }}</td>

        </tr>
      </tbody>

    </table>

  </div>

  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cerrar</button>
  </div>
</div>

    <!-- CREAR COMPROBANTE -->
    <div id="modalCrearComprobante" class="modal hide fade">
      <div class="modal-header"><h3>Registrar comprobante</h3></div>
      <div class="modal-body">

        <select v-model="comp.tipo">
          <option>BOLETA</option>
          <option>FACTURA</option>
          <option>RECIBO</option>
        </select>

        <input v-model="comp.numero">
        <input v-model="comp.cliente">
        <input v-model="comp.monto">

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarComp">Guardar</button>
      </div>
    </div>

  </div>
</div>

<script>
Vue.component('v-select', VueSelect.VueSelect);
new Vue({
el:'#appBalance',
data:{
  balances:[],
  apphost: (typeof apphost !== 'undefined' ? apphost : ''),
  ingresos:[],
  mesSeleccionado:'',
  mesesDisponibles:[],
  fechaInicioMesTexto:'',
  fechaFinMesTexto:'',
  deuda:{
    tipo_entidad:'PERSONA',
    nombre_persona:'',
    proveedor_id:null,
    proveedor_obj:null,
    monto_total:'',
    descripcion:''
  },
  resumenMes:{},
  fechaInicioMes:'',
  fechaHoy:'',
  filtro:{
      fecha_inicio:'',
      fecha_fin:''
    },
    resumen:{
      ventas:0,
      caja_ing:0,
      facturado:0,
      caja_eg:0,
      comision:0,
      compras:0,
      total_ingresos:0,
      total_egresos:0,
      utilidad:0
  },  
  cajaMovimientos:[],
  clientes:[],
  caja:{
      tipo:'INGRESO',
      categoria:'CAJA_CHICA',
      tipo_comprobante:'',
      numero:'',
      cliente_obj:null,
      proveedor_obj:null,
      descripcion:'',
      monto:'',
      fecha:''
  },
  totalIngresos:0,
  totalEgresos:0,  
  proveedores:[],
  egresos:[],
  saldoAnterior: 0,
  deudas:[],
  comprobantes:[],

  gen:{anio:2026,mes:5},
  resumen:{},

  deuda:{},
  deudaSel:{},
  pago:{},

  caja:{},
  comp:{}
},

methods:{
mesNombre(m){
  return ['','Enero','Febrero','Marzo','Abril','Mayo'][m];
},

calcularInforme(){

  if(!this.filtro.fecha_inicio || !this.filtro.fecha_fin){
    alert('Ingrese fechas');
    return;
  }

  axios.post(this.apphost + '/LF4f/balance/resumen', this.filtro)
  .then(r=>{
    this.resumen = r.data;
  });

},

fmt(n){
  return parseFloat(n || 0).toLocaleString('es-PE', {
    minimumFractionDigits:2
  });
},

cargarInformeActual(){

  const hoy = new Date();

  const yyyy = hoy.getFullYear();

  const mm = String(
    hoy.getMonth() + 1
  ).padStart(2,'0');

  const dd = String(
    hoy.getDate()
  ).padStart(2,'0');

  const fecha_inicio = `${yyyy}-${mm}-01`;

  const fecha_fin = `${yyyy}-${mm}-${dd}`;

  this.filtro.fecha_inicio = fecha_inicio;
  this.filtro.fecha_fin = fecha_fin;

  this.mesSeleccionado = `${yyyy}-${mm}`;

  this.actualizarTextoFechas(
    fecha_inicio,
    fecha_fin
  );

  axios.post(this.apphost + '/LF4f/balance/resumen', {

    fecha_inicio,
    fecha_fin

  })
  .then(r=>{

    this.resumenMes = r.data;

  });

},

formatearFecha(f){
  if(!f) return '';
  const d = new Date(f);
  const dia = String(d.getDate()).padStart(2,'0');
  const mes = String(d.getMonth()+1).padStart(2,'0');
  return `${dia}/${mes}`;
},

abrirComprobantes(){

  axios.get(this.apphost + '/LF4f/comprobante/listar')
  .then(r=>{

    this.comprobantes = r.data;

    this.$nextTick(()=>{

      if ($.fn.DataTable.isDataTable('#tablaComprobantes')) {
        $('#tablaComprobantes').DataTable().destroy();
      }

      $('#tablaComprobantes').DataTable({
                language: (typeof dt_language !== 'undefined' ? dt_language : undefined),
                scrollX: true,
                dom: 'frtip',
                order: [[0,'desc']]
              });

    });

    $('#modalComprobantes').modal('show');

  });

},

calcularBalance(){

    if(!this.filtro.fecha_inicio || !this.filtro.fecha_fin){
      alert('Seleccione fechas');
      return;
    }

    // 🔥 BLOCK UI
    $.blockUI({
      message: '<h4>Procesando...</h4>',
      css: { 
        border: 'none', 
        padding: '15px', 
        backgroundColor: '#000', 
        color: '#fff',
        borderRadius: '8px'
      }
    });

    axios.post(this.apphost + '/LF4f/balance/resumen', {
      fecha_inicio: this.filtro.fecha_inicio,
      fecha_fin: this.filtro.fecha_fin
    })
    .then(r=>{
      this.resumenMes = r.data;
    })
    .finally(()=>{
      $.unblockUI();
    });

  },

abrirDeudas(){

  axios.get(this.apphost + '/LF4f/deuda/listar')
  .then(r=>{
    this.deudas = r.data;

    this.$nextTick(()=>{

      if ($.fn.DataTable.isDataTable('#tablaDeudas')) {
        $('#tablaDeudas').DataTable().destroy();
      }

      $('#tablaDeudas').DataTable({
                language: (typeof dt_language !== 'undefined' ? dt_language : undefined),
                scrollX: true,
                dom: 'frtip',
                order: [[0,'desc']]
              });

    });

    $('#modalDeudas').modal('show');
  });

},
getFechaHoy(){
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth()+1).padStart(2,'0');
  const dd = String(hoy.getDate()).padStart(2,'0');
  return `${yyyy}-${mm}-${dd}`;
},  

  abrirRegistrarCaja(){

    $('#modalCaja').modal('hide');

    this.caja = {
      tipo:'INGRESO',
      categoria:'CAJA_CHICA',
      descripcion:'',
      monto:'',
      fecha: this.getFechaHoy() // 👈 AQUÍ
    };

    $('#modalRegistrarCaja').modal('show');

  },

  cargarClientes(){
    axios.get(this.apphost + '/LF4f/cliente/listar')
    .then(r=>{
      this.clientes = r.data || [];
    })
    .catch(err=>{
      console.error('Error clientes', err);
      this.clientes = [];
    });
  },  

  guardarCaja(){

  // IDs dinámicos
  if(this.caja.tipo=='INGRESO'){
    this.caja.cliente_id = this.caja.cliente_obj?.cliente_id || null;
  }

  if(this.caja.tipo=='EGRESO'){
    this.caja.proveedor_id = this.caja.proveedor_obj?.proveedor_id || null;
  }

  axios.post(this.apphost + '/LF4f/caja_chica/crear', this.caja)
  .then(()=>{
    $('#modalRegistrarCaja').modal('hide');

    this.abrirCajaChica();

  });

},

abrirCrearDeuda(){

  this.deuda = {
    tipo_entidad: 'PERSONA',
    nombre_persona: '',
    proveedor_id: null,
    proveedor_obj: null,
    monto_total: '',
    descripcion: ''
  };

  $('#modalCrearDeuda').modal('show');

},

guardarDeuda(){

  if(this.deuda.tipo_entidad=='PROVEEDOR'){
    this.deuda.proveedor_id = this.deuda.proveedor_obj?.proveedor_id || null;
  }

  axios.post(this.apphost + '/LF4f/deuda/crear', this.deuda)
  .then(()=>{
    $('#modalCrearDeuda').modal('hide');
    this.listarDeudas();
  });

},
generarMeses(){

  const meses = [
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre'
  ];

  const hoy = new Date();

  const anio = 2026;

  const mesActual = hoy.getFullYear() === 2026
    ? hoy.getMonth() + 1
    : 12;

  this.mesesDisponibles = [];

  for(let i = 1; i <= mesActual; i++){

    this.mesesDisponibles.push({

      value: `${anio}-${String(i).padStart(2,'0')}`,

      label: `${meses[i - 1]} - ${anio}`

    });

  }

},

seleccionarMes(){

  if(!this.mesSeleccionado){
    return;
  }

  const partes = this.mesSeleccionado.split('-');

  const anio = partes[0];
  const mes  = partes[1];

  const inicio = `${anio}-${mes}-01`;

  const ultimoDia = new Date(anio, mes, 0).getDate();

  const fin = `${anio}-${mes}-${String(ultimoDia).padStart(2,'0')}`;

  this.filtro.fecha_inicio = inicio;
  this.filtro.fecha_fin = fin;

  this.actualizarTextoFechas(
    inicio,
    fin
  );

  this.calcularBalance();

},

actualizarTextoFechas(inicio, fin){

  const meses = [
    'ENERO',
    'FEBRERO',
    'MARZO',
    'ABRIL',
    'MAYO',
    'JUNIO',
    'JULIO',
    'AGOSTO',
    'SEPTIEMBRE',
    'OCTUBRE',
    'NOVIEMBRE',
    'DICIEMBRE'
  ];

  const fi = new Date(inicio + 'T00:00:00');
  const ff = new Date(fin + 'T00:00:00');

  this.fechaInicioMesTexto =
  `${String(fi.getDate()).padStart(2,'0')} ${meses[fi.getMonth()]}`;

this.fechaFinMesTexto =
  `${String(ff.getDate()).padStart(2,'0')} ${meses[ff.getMonth()]}`;

},
cargarProveedores(){
  axios.get(this.apphost + '/LF4f/proveedor/listar')
  .then(r=>{
    this.proveedores = r.data;
  });
},
abrirPagar(d){

  this.deudaSel = d;

  // limpiar pago
  this.pago = {
    monto: '',
    medio_pago: 'EFECTIVO'
  };

  // cerrar modal de deudas
  $('#modalDeudas').modal('hide');

  // abrir modal pagar
  $('#modalPagar').modal('show');

},

pagar(){

  axios.post(this.apphost + '/LF4f/deuda/pagar', {
    deuda_id: this.deudaSel.deuda_id,
    monto: this.pago.monto,
    medio_pago: this.pago.medio_pago
  })
  .then(()=>{

    // cerrar pagar
    $('#modalPagar').modal('hide');

    // refrescar lista
    this.listarDeudas();

    // volver a abrir deudas
    $('#modalDeudas').modal('show');

  });

},

abrirCrearComprobante(){
  $('#modalCrearComprobante').modal('show');
},

guardarComp(){
  axios.post('/comprobante/crear',this.comp);
},

abrirCajaChica(){

  this.bloquear('Cargando caja chica…');

  console.log('🚀 iniciar abrirCajaChica');

  axios.get(this.apphost + '/LF4f/caja_chica/listar')
  .then(r=>{

    console.log('📦 respuesta axios');
    console.log('📊 registros:', r.data.length);

    this.cajaMovimientos = r.data;

    this.$nextTick(()=>{

      // 🔥 inicializar SOLO UNA VEZ
      if (!this.dtCaja) {

        console.log('⚙️ creando DataTable');

        this.dtCaja = $('#tablaCaja').DataTable({
          language: (typeof dt_language !== 'undefined' ? dt_language : undefined),
          scrollX: true,
          dom: 'frtip',
          order: [[0,'desc']]
        });

      }

      console.log('♻️ limpiando tabla');
      this.dtCaja.clear();

      console.log('➕ agregando filas');

      this.cajaMovimientos.forEach(c => {

        this.dtCaja.row.add([
          c.caja_chica_id,
          c.fecha,
          c.tipo,
          c.categoria,
          c.descripcion,
          'S/ ' + parseFloat(c.monto).toFixed(2)
        ]);

      });

      console.log('🎯 dibujando tabla');
      this.dtCaja.draw(false);

    });

    $('#modalCaja').modal('show');

  })
  .catch(err=>{
    console.error('❌ ERROR:', err);
  })
  .finally(()=>{
    $.unblockUI();
  });

},

verDetalle(){
  axios.get(this.apphost +'/balance/ingresos').then(r=>this.ingresos=r.data);
  axios.get(this.apphost +'/balance/egresos').then(r=>this.egresos=r.data);
  $('#modalIngresos').modal('show');
},

bloquear(msg){
  $.blockUI({
    message: `<h4>${msg}</h4>`,
    css: { 
      border: 'none', 
      padding: '15px', 
      backgroundColor: '#000', 
      color: '#fff',
      borderRadius: '8px'
    }
  });
},

listarDeudas(){
  axios.get(this.apphost + '/LF4f/deuda/listar')
  .then(r=>{
    this.deudas = r.data;
  });
}
},

mounted(){
  this.cargarProveedores();
  this.generarMeses();
  this.cargarClientes(); 
  this.cargarInformeActual();
}
});
</script>