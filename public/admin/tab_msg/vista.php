<div class="row-fluid" id="appMsg">
  <div class="span12">
    <h2>Mensajes</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrear">
        <i class="icon-plus icon-white"></i> Nuevo Mensaje
      </button>
    </div>

    <table id="tablaMsg" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Santo</th>
          <th>Me gusta</th>
          <th>Validez</th>
          <th>Creación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody><!-- DataTables --></tbody>
    </table>

    <!-- Modal Detalle -->
    <div id="modalDetalleMsg" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Detalle del Mensaje</h3></div>
      <div class="modal-body">
        <p><strong>ID:</strong> {{ detalle.msg_id }}</p>
        <p><strong>Usuario:</strong> {{ detalle.usu_nom }}</p>
        <p><strong>Santo:</strong> {{ detalle.santo_nombre || detalle.santo_id || '-' }}</p>
        <p><strong>Me gusta:</strong> {{ detalle.me_gusta }}</p>
        <p><strong>Validez:</strong> {{ detalle.is_valido ? 'Válido' : 'No válido' }}</p>
        <p><strong>Creación:</strong> {{ detalle.fecha_creacion }}</p>
        <hr>
        <div>
          <h4>Contenido</h4>
          <div v-html="detalle.contenido_rem" style="border:1px solid #ddd; padding:10px; background:#fafafa;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- Modal Crear -->
    <div id="modalCrearMsg" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Nuevo Mensaje</h3></div>
      <div class="modal-body">
        <div class="control-group">
          <label class="control-label">Usuario</label>
          <div class="controls">
            <input v-model="nuevo.usu_nom" class="input-xxlarge" placeholder="Nombre del usuario">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">¿Válido?
            <span class="lbl-seleccion lbl-valido-crear muted"></span>
          </label>
          <div class="controls">
            <select id="selValidoCrear" style="width:100%"></select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Santo
            <span class="lbl-seleccion lbl-santo-crear muted"></span>
          </label>
          <div class="controls">
            <select id="selSantoCrear" style="width:100%"></select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Me gusta</label>
          <div class="controls">
            <input v-model.number="nuevo.me_gusta" type="number" min="0" class="input-small">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Contenido</label>
          <div class="controls">
            <div id="summerCrear"></div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearMsg">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- Modal Editar -->
    <div id="modalEditarMsg" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Editar Mensaje</h3></div>
      <div class="modal-body">

        <div class="control-group">
          <label class="control-label">Usuario</label>
          <div class="controls">
            <input v-model="form.usu_nom" class="input-xxlarge">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">¿Válido?
            <span class="lbl-seleccion lbl-valido-editar muted"></span>
          </label>
          <div class="controls">
            <select id="selValidoEditar" style="width:100%"></select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Santo
            <span class="lbl-seleccion lbl-santo-editar muted"></span>
          </label>
          <div class="controls">
            <select id="selSantoEditar" style="width:100%"></select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Me gusta</label>
          <div class="controls">
            <input v-model.number="form.me_gusta" type="number" min="0" class="input-small">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Contenido</label>
          <div class="controls">
            <div id="summerEditar"></div>
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
const appMsg = new Vue({
  el: '#appMsg',
  data: {
    apphost: apphost,
    msgs: [],
    detalle: {},
    nuevo: { usu_nom:'', is_valido:0, santo_id:null, me_gusta:0, contenido_rem:'' },
    form:  {},
    dt: null,
    opcionesValidez: [
      { id: 1, text: 'Válido' },
      { id: 0, text: 'No válido' }
    ],
    // ENUM de texto (ids = values exactos del ENUM)
    opcionesSantos: [
      { id: 'santa rosa',           text: 'Santa Rosa' },
      { id: 'sr de los milagros',   text: 'Sr de los Milagros' },
      { id: 'san martin de porres', text: 'San Martín de Porres' }
    ]
  },
  methods: {
    /** Helpers Select2: agrega el texto seleccionado junto al label */
    _syncSelect2Label($select, labelSelector){
      const txt = $select.find('option:selected').text() || '';
      $(labelSelector).text(txt ? '— ' + txt : '');
    },
    _initSelect2EnModal(selId, opts, modalSel, labelSel, vModelGetterSetter){
      const $s = $(selId);
      $s.empty();
      opts.forEach(o => $s.append(new Option(o.text, o.id)));
      $s.select2({ allowClear:true, placeholder:'Seleccione…', dropdownParent: $(modalSel) });

      const vmValue = vModelGetterSetter('get');
      if (vmValue !== null && vmValue !== undefined) $s.val(String(vmValue)).trigger('change.select2');

      $s.off('change').on('change', e=>{
        const val = $(e.target).val();
        // NO parseInt para santo_id (es string). El setter se encarga.
        vModelGetterSetter('set', val !== null ? val : null);
        this._syncSelect2Label($s, labelSel);
      });

      this._syncSelect2Label($s, labelSel);
    },

    /** LISTAR con DataTables */
    listar(){
      $.blockUI({message:'<h4>Cargando mensajes…</h4>', css:{border:'none',padding:'12px',background:'#000',opacity:.7,color:'#fff'}});
      axios.get(`${this.apphost}/msg/listar`)
        .then(r=>{
          this.msgs = r.data||[];
          this.$nextTick(()=>{
            if(!this.dt){
              this.dt = $('#tablaMsg').DataTable({ scrollX:true, dom:'frtip', order:[[0,'desc']] });
              const self = this;
              $('#tablaMsg tbody')
                .on('click','a.detalle-msg', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.msgs.find(x=>x.msg_id==id);
                  if(row) self.abrirModalDetalle(row);
                })
                .on('click','a.editar-msg', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.msgs.find(x=>x.msg_id==id);
                  if(row) self.abrirModalEditar(row);
                })
                .on('click','a.eliminar-msg', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.msgs.find(x=>x.msg_id==id);
                  if(row) self.eliminarMsg(row);
                });
            }
            this.dt.clear();
            this.msgs.forEach(m=>{
              const actions = `
                <div class="btn-group">
                  <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">Opciones <span class="caret"></span></button>
                  <ul class="dropdown-menu">
                    <li><a href="#" class="detalle-msg"  data-id="${m.msg_id}">Detalle</a></li>
                    <li><a href="#" class="editar-msg"   data-id="${m.msg_id}">Editar</a></li>
                    <li><a href="#" class="eliminar-msg" data-id="${m.msg_id}">Eliminar</a></li>
                  </ul>
                </div>`;
              this.dt.row.add([
                m.msg_id,
                m.usu_nom||'',
                (m.santo_nombre || m.santo_id || '-') + '',
                m.me_gusta||0,
                m.is_valido ? 'Sí' : 'No',
                m.fecha_creacion || '',
                actions
              ]);
            });
            this.dt.draw(false);
          });
        })
        .finally(()=> $.unblockUI());
    },

    abrirModalDetalle(row){ this.detalle = row; $('#modalDetalleMsg').modal('show'); },

    // CREAR
    abrirModalCrear(){
      this.nuevo = { usu_nom:'', is_valido:0, santo_id:null, me_gusta:0, contenido_rem:'' };

      this.$nextTick(()=>{
        this._initSelect2EnModal(
          '#selValidoCrear', this.opcionesValidez, '#modalCrearMsg', '.lbl-valido-crear',
          (mode,val)=> mode==='get'? this.nuevo.is_valido : (this.nuevo.is_valido = parseInt(val)) // num
        );
        this._initSelect2EnModal(
          '#selSantoCrear', this.opcionesSantos, '#modalCrearMsg', '.lbl-santo-crear',
          (mode,val)=> mode==='get'? this.nuevo.santo_id : (this.nuevo.santo_id = val || null)     // string
        );

        $('#summerCrear').summernote({
          height: 200,
          callbacks: { onChange: (contents)=> { this.nuevo.contenido_rem = contents; } }
        }).summernote('code', this.nuevo.contenido_rem);

        $('#modalCrearMsg').modal('show');
      });
    },
    crearMsg(){
      if(!this.nuevo.usu_nom.trim()) return apprise('Escribe el nombre del usuario');
      if(!this.nuevo.contenido_rem || !this.nuevo.contenido_rem.trim()) return apprise('Escribe el contenido');

      $.blockUI({message:'<h4>Creando…</h4>', css:{border:'none',padding:'12px',background:'#000',opacity:.7,color:'#fff'}});
      const fd = new FormData();
      fd.append('usu_nom', this.nuevo.usu_nom);
      fd.append('is_valido', this.nuevo.is_valido); // 0/1
      if(this.nuevo.santo_id!=null) fd.append('santo_id', this.nuevo.santo_id); // string enum
      fd.append('me_gusta', this.nuevo.me_gusta||0);
      fd.append('contenido_rem', this.nuevo.contenido_rem);

      axios.post(`${this.apphost}/msg/crear`, fd, { headers:{'Content-Type':'multipart/form-data'} })
        .then(()=>{ $('#modalCrearMsg').modal('hide'); apprise('¡Creado!'); })
        .finally(()=>{ $.unblockUI(); this.listar(); });
    },

    // EDITAR
    abrirModalEditar(row){
      this.form = Object.assign({}, row);

      this.$nextTick(()=>{
        this._initSelect2EnModal(
          '#selValidoEditar', this.opcionesValidez, '#modalEditarMsg', '.lbl-valido-editar',
          (mode,val)=> mode==='get'? this.form.is_valido : (this.form.is_valido = parseInt(val))
        );
        this._initSelect2EnModal(
          '#selSantoEditar', this.opcionesSantos, '#modalEditarMsg', '.lbl-santo-editar',
          (mode,val)=> mode==='get'? this.form.santo_id : (this.form.santo_id = val || null)
        );

        $('#summerEditar').summernote({
          height: 200,
          callbacks: { onChange: (contents)=> { this.form.contenido_rem = contents; } }
        }).summernote('code', this.form.contenido_rem||'');

        $('#modalEditarMsg').modal('show');
      });
    },
    guardarEdicion(){
      if(!this.form.usu_nom.trim()) return apprise('Escribe el nombre del usuario');
      if(!this.form.contenido_rem || !this.form.contenido_rem.trim()) return apprise('Escribe el contenido');

      $.blockUI({message:'<h4>Actualizando…</h4>', css:{border:'none',padding:'12px',background:'#000',opacity:.7,color:'#fff'}});
      const fd = new FormData();
      fd.append('msg_id', this.form.msg_id);
      fd.append('usu_nom', this.form.usu_nom);
      fd.append('is_valido', this.form.is_valido?1:0);
      if(this.form.santo_id!=null) fd.append('santo_id', this.form.santo_id);
      fd.append('me_gusta', this.form.me_gusta||0);
      fd.append('contenido_rem', this.form.contenido_rem||'');

      axios.post(`${this.apphost}/msg/editar`, fd, { headers:{'Content-Type':'multipart/form-data'} })
        .then(()=>{ $('#modalEditarMsg').modal('hide'); apprise('¡Actualizado!'); })
        .finally(()=>{ $.unblockUI(); this.listar(); });
    },

    // ELIMINAR
    eliminarMsg(row){
      apprise(`¿Eliminar mensaje <b>#${row.msg_id}</b>?`, {confirm:true}, ok=>{
        if(!ok) return;
        $.blockUI({message:'<h4>Eliminando…</h4>', css:{border:'none',padding:'12px',background:'#000',opacity:.7,color:'#fff'}});
        axios.post(`${this.apphost}/msg/eliminar`, { msg_id: row.msg_id })
          .finally(()=>{ $.unblockUI(); this.listar(); });
      });
    }
  },
  mounted(){ this.listar(); }
});
</script>
