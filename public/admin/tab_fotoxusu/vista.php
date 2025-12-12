<script>
function formDataToDebug(fd){
  const out = {};
  for (const [k,v] of fd.entries()) {
    if (v instanceof File) { out[k] = { __file__: true, name: v.name, size: v.size, type: v.type }; }
    else { out[k] = v; }
  }
  return out;
}

axios.interceptors.request.use(cfg=>{
  let data = cfg.data;
  try { if (data instanceof FormData) data = formDataToDebug(data); } catch(e){}
  console.log('[REQ]', cfg.method?.toUpperCase(), cfg.url, data);
  return cfg;
});
axios.interceptors.response.use(
  res => { console.log('[RES]', res.status, res.config.url, res.data); return res; },
  err => { console.log('[ERR]', err?.response?.status, err?.config?.url, err?.response?.data); return Promise.reject(err); }
);

// Registrar componente global de Vue-Select
if (window.VueSelect && window.Vue) {
  Vue.component('v-select', VueSelect.VueSelect);
}
</script>

<div class="row-fluid" id="appFotoUsu">
  <div class="span12">
    <h2>Fotos por Usuario</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nueva Foto
      </button>
    </div>

    <table id="tablaFotoUsu" class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Santo</th>
          <th>Me gusta</th>
          <th>Válido</th>
          <th>Fecha creación</th>
          <th>Imagen</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody><!-- DataTables --></tbody>
    </table>

    <!-- Modal Detalle -->
    <div id="modalDetalleFotoUsu" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Detalle</h3></div>
      <div class="modal-body">
        <p><strong>ID:</strong> {{ detalle.fotoxusu_id }}</p>
        <p><strong>Usuario:</strong> {{ detalle.usu_nom }}</p>
        <p><strong>Santo:</strong> {{ detalle.santo_id }}</p>
        <p><strong>Me gusta:</strong> {{ detalle.me_gusta }}</p>
        <p><strong>Válido:</strong> {{ detalle.is_valido == 1 ? 'Sí' : 'No' }}</p>
        <p><strong>Fecha creación:</strong> {{ detalle.fecha_creacion }}</p>
        <div class="thumbnail" v-if="detalle.fotoxusu_id">
          <img :src="urlImg(detalle.fotoxusu_id)" alt="foto" style="max-width:100%;">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- Modal Crear -->
    <div id="modalCrearFotoUsu" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Nueva Foto</h3></div>
      <div class="modal-body">
        <div class="control-group">
          <label class="control-label">Usuario</label>
          <div class="controls">
            <input v-model="nuevo.usu_nom" class="input-xxlarge" placeholder="Nombre de usuario">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Santo <span> – {{ getSantoText(nuevo.santo_id) }}</span></label>
          <div class="controls">
            <!-- Reemplazo Select2 → Vue-Select -->
            <v-select
              :options="santos"
              label="text"
              :reduce="o => o.id"
              v-model="nuevo.santo_id"
              placeholder="Seleccione Santo…"
            ></v-select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Válido <span> – {{ getValidoText(nuevo.is_valido) }}</span></label>
          <div class="controls">
            <v-select
              :options="validoOptions"
              label="text"
              :reduce="o => o.id"
              v-model="nuevo.is_valido"
              placeholder="¿Válido?"
            ></v-select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Imagen (JPG/PNG/GIF)</label>
          <div class="controls">
            <input type="file" id="fileCrearImg" accept="image/*">
            <div id="prevCrear" class="thumbnail" style="margin-top:8px; display:none;">
              <img id="prevCrearImg" src="" style="max-width:100%;">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crear">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- Modal Editar -->
    <div id="modalEditarFotoUsu" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Editar Foto</h3></div>
      <div class="modal-body">
        <div class="control-group">
          <label class="control-label">Usuario</label>
          <div class="controls">
            <input v-model="form.usu_nom" class="input-xxlarge">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Santo <span> – {{ getSantoText(form.santo_id) }}</span></label>
          <div class="controls">
            <v-select
              :options="santos"
              label="text"
              :reduce="o => o.id"
              v-model="form.santo_id"
              placeholder="Seleccione Santo…"
            ></v-select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Válido <span> – {{ getValidoText(form.is_valido) }}</span></label>
          <div class="controls">
            <v-select
              :options="validoOptions"
              label="text"
              :reduce="o => o.id"
              v-model="form.is_valido"
              placeholder="¿Válido?"
            ></v-select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Reemplazar Imagen (opcional)</label>
          <div class="controls">
            <input type="file" id="fileEditarImg" accept="image/*">
            <div class="thumbnail" style="margin-top:8px;">
              <img :src="form.fotoxusu_id ? urlImg(form.fotoxusu_id) : ''" style="max-width:100%;">
            </div>
            <div id="prevEditar" class="thumbnail" style="margin-top:8px; display:none;">
              <img id="prevEditarImg" src="" style="max-width:100%;">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarEdicion">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
const appFotoUsu = new Vue({
  el: '#appFotoUsu',
  data: {
    apphost: apphost, // definida en tu layout
    dt: null,
    listado: [],
    detalle: {},
    nuevo: { usu_nom: '', santo_id: null, is_valido: 1 },
    form:  { fotoxusu_id: null, usu_nom: '', santo_id: null, is_valido: 1 },
    santos: [
      { id: 'santa rosa',        text: 'santa rosa' },
      { id: 'sr de los milagros', text: 'sr de los milagros' },
      { id: 'san martin de porres', text: 'san martin de porres' }
    ],
    validoOptions: [
      { id: 1, text: 'Sí (1)' },
      { id: 0, text: 'No (0)' }
    ]
  },
  methods: {
    urlImg(id){ return `${this.apphost}/pics/fotos/${id}.jpg?` + Date.now(); },
    bloquear(msg){ $.blockUI({ message: `<h4>${msg}</h4>`, css:{border:'none',padding:'15px',background:'#000',opacity:.6,color:'#fff'} }); },
    desbloquear(){ $.unblockUI(); },

    getSantoText(val){
      const it = this.santos.find(x => x.id === val);
      return it ? it.text : '';
    },
    getValidoText(val){
      const it = this.validoOptions.find(x => String(x.id) === String(val));
      return it ? it.text : '';
    },

    listar(){
      this.bloquear('Cargando...');
      axios.get(`${this.apphost}/fotoxusu/listar`)
        .then(r=>{
          this.listado = r.data;
          this.$nextTick(()=>{
            if(!this.dt){
              this.dt = $('#tablaFotoUsu').DataTable({ 
                scrollX: true, 
                scrollY: '500px',
                dom: 'frtip', 
                order:[[0,'desc']] });
              const self = this;
              $('#tablaFotoUsu tbody')
                .on('click','a.detalle-item', function(e){ e.preventDefault(); const id = $(this).data('id'); const it = self.listado.find(x=>x.fotoxusu_id==id); if(it) self.abrirModalDetalle(it); })
                .on('click','a.editar-item',  function(e){ e.preventDefault(); const id = $(this).data('id'); const it = self.listado.find(x=>x.fotoxusu_id==id); if(it) self.abrirModalEditar(it); })
                .on('click','a.eliminar-item', function(e){ e.preventDefault(); const id = $(this).data('id'); const it = self.listado.find(x=>x.fotoxusu_id==id); if(it) self.eliminar(it); });
            }
            this.dt.clear();
            this.listado.forEach(row=>{
              const imgTag = `<img src="${this.urlImg(row.fotoxusu_id)}" style="height:40px;width:auto">`;
              const actions = `
                <div class="btn-group">
                  <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                    Opciones <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#" class="detalle-item" data-id="${row.fotoxusu_id}">Detalle</a></li>
                    <li><a href="#" class="editar-item" data-id="${row.fotoxusu_id}">Editar</a></li>
                    <li><a href="#" class="eliminar-item" data-id="${row.fotoxusu_id}">Eliminar</a></li>
                  </ul>
                </div>`;
              this.dt.row.add([
                row.fotoxusu_id,
                row.usu_nom || '',
                row.santo_id || '',
                row.me_gusta ?? 0,
                row.is_valido == 1 ? 'Sí' : 'No',
                row.fecha_creacion || '',
                imgTag,
                actions
              ]);
            });
            this.dt.draw(false);
          });
        })
        .finally(this.desbloquear);
    },

    abrirModalDetalle(item){ this.detalle = Object.assign({}, item); $('#modalDetalleFotoUsu').modal('show'); },

    abrirModalCrear(){
      this.nuevo = { usu_nom:'', santo_id:null, is_valido:1 };
      this.$nextTick(()=>{
        // Preview de imagen
        $('#fileCrearImg').off('change').on('change', e=>{
          const f = e.target.files && e.target.files[0];
          if(!f){ $('#prevCrear').hide(); return; }
          const r = new FileReader();
          r.onload = ev=>{ $('#prevCrearImg').attr('src', ev.target.result); $('#prevCrear').show(); };
          r.readAsDataURL(f);
        });
        $('#modalCrearFotoUsu').modal('show');
      });
    },

    normSanto(v){
      // Acepta string o objeto {id,text}
      return (v && typeof v === 'object') ? v.id : v;
    },
    normValido(v){
      return parseInt(v, 10);
    },
    getSantoText(val){
      const id = (val && typeof val === 'object') ? val.id : val;
      const it = this.santos.find(x => x.id === id);
      return it ? it.text : '';
    },

    crear(){
      if(!this.nuevo.usu_nom.trim()) return apprise('Escribe el nombre de usuario');

      const santo = this.normSanto(this.nuevo.santo_id);
      if(!santo) return apprise('Selecciona un Santo');

      const fd = new FormData();
      fd.append('usu_nom', this.nuevo.usu_nom);
      fd.append('santo_id', santo);                      // ← normalizado
      fd.append('is_valido', this.normValido(this.nuevo.is_valido)); // ← número

      const file = document.getElementById('fileCrearImg')?.files?.[0];
      if(file) fd.append('img_file', file);

      this.bloquear('Creando…');
      axios.post(`${this.apphost}/fotoxusu/crear`, fd, {
        headers: { 'Content-Type':'multipart/form-data' }
      })
      .then(()=>{ $('#modalCrearFotoUsu').modal('hide'); apprise('¡Creado!'); })
      .finally(()=>{ this.desbloquear(); this.listar(); });
    },

    guardarEdicion(){
      if(!this.form.usu_nom.trim()) return apprise('Escribe el nombre de usuario');

      const santo = this.normSanto(this.form.santo_id);
      if(!santo) return apprise('Selecciona un Santo');

      const fd = new FormData();
      fd.append('fotoxusu_id', this.form.fotoxusu_id);
      fd.append('usu_nom', this.form.usu_nom);
      fd.append('santo_id', santo);                       // ← normalizado
      fd.append('is_valido', this.normValido(this.form.is_valido)); // ← número

      const file = document.getElementById('fileEditarImg')?.files?.[0];
      if(file) fd.append('img_file', file);

      this.bloquear('Actualizando…');
      axios.post(`${this.apphost}/fotoxusu/editar`, fd, {
        headers: { 'Content-Type':'multipart/form-data' }
      })
      .then(()=>{ $('#modalEditarFotoUsu').modal('hide'); apprise('¡Actualizado!'); })
      .finally(()=>{ this.desbloquear(); this.listar(); });
    },

    abrirModalEditar(item){
      this.form = Object.assign({}, item);
      this.$nextTick(()=>{
        // Preview de imagen reemplazo
        $('#fileEditarImg').off('change').on('change', e=>{
          const f = e.target.files && e.target.files[0];
          if(!f){ $('#prevEditar').hide(); return; }
          const r = new FileReader();
          r.onload = ev=>{ $('#prevEditarImg').attr('src', ev.target.result); $('#prevEditar').show(); };
          r.readAsDataURL(f);
        });
        $('#modalEditarFotoUsu').modal('show');
      });
    },

    guardarEdicion(){
      if(!this.form.usu_nom.trim()) return apprise('Escribe el nombre de usuario');
      if(!this.form.santo_id) return apprise('Selecciona un Santo');

      const fd = new FormData();
      fd.append('fotoxusu_id', this.form.fotoxusu_id);
      fd.append('usu_nom', this.form.usu_nom);
      fd.append('santo_id', this.form.santo_id);
      fd.append('is_valido', this.form.is_valido);
      const file = document.getElementById('fileEditarImg').files[0];
      if(file) fd.append('img_file', file);

      this.bloquear('Actualizando…');
      axios.post(`${this.apphost}/fotoxusu/editar`, fd, { headers: { 'Content-Type':'multipart/form-data' } })
        .then(()=>{ $('#modalEditarFotoUsu').modal('hide'); apprise('¡Actualizado!'); })
        .finally(()=>{ this.desbloquear(); this.listar(); });
    },

    eliminar(item){
      apprise(`¿Eliminar foto de <b>${item.usu_nom}</b> (#${item.fotoxusu_id})?`, {confirm:true}, ok=>{
        if(!ok) return;
        this.bloquear('Eliminando…');
        axios.post(`${this.apphost}/fotoxusu/eliminar`, { fotoxusu_id: item.fotoxusu_id })
          .finally(()=>{ this.desbloquear(); this.listar(); });
      });
    }
  },
  mounted(){ this.listar(); }
});
</script>
