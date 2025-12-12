<?php
boot::vue_select();
?>
<!-- ================== PAGWEB + PARRWEB (Maestro/Detalle) ================== -->
<div class="row-fluid" id="appPagweb">
  <div class="span12">
    <h2>Páginas Web</h2>

    <div class="form-actions">
      <button class="btn btn-success" @click="abrirModalCrearPagweb">
        <i class="icon-plus icon-white"></i> Nueva Página
      </button>
    </div>

    <!-- Tabla Pagweb -->
    <table id="tablaPagweb" class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Clave</th>
          <th>Título</th>
          <th>Meta 01</th>
          <th>Meta 02</th>
          <th>Img 01</th>
          <th>Img 02</th>
          <th>Mini</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody><!-- filas vía DataTables --></tbody>
    </table>

    <!-- Sección detalle PARRWEB del Pagweb seleccionado -->
    <div v-if="pagwebSel" class="well">
      <h3>
        Parrweb de: {{ pagwebSel.clave_txt || pagwebSel.titulo }}
      </h3>


      <div class="form-actions">
        <button class="btn btn-info" @click="abrirModalCrearParrwebPara(pagwebSel)">
          <i class="icon-plus icon-white"></i> Nuevo Parrweb para esta Página
        </button>
      </div>

      <table id="tablaParrweb" class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Clave</th>
            <th>Video01</th>
            <th>Video02</th>
            <th>Img01</th>
            <th>Img02</th>
            <th>Mini</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody><!-- filas vía DataTables --></tbody>
      </table>
    </div>

    <!-- ============== Modales PAGWEB ============== -->

    <!-- Detalle Pagweb -->
    <div id="modalDetallePagweb" class="modal hide fade fullscreen" tabindex="-1">
      <div class="modal-header"><h3>Detalle de Página</h3></div>

<div class="modal-body">
  <p><strong>ID:</strong> {{ detallePagweb.pagweb_id }}</p>
  <p><strong>Clave:</strong> {{ detallePagweb.clave_txt }}</p>
  <p><strong>Título:</strong> {{ detallePagweb.titulo }}</p>
  <p><strong>Meta 01:</strong> {{ detallePagweb.metatag01 }}</p>
  <p><strong>Meta 02:</strong> {{ detallePagweb.metatag02 }}</p>

  <hr>
  <h4>Imágenes</h4>

  <!-- URL_IMG01 -->
  <form @submit.prevent="subirImgPag('url_img01')">
    <label>Imagen 01 (campo: url_img01)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniPagweb('url_img01')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniPagweb('url_img01')" alt="mini pagweb 01">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_pag_url_img01" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 01
        </button>
      </div>
    </div>
  </form>

  <hr>

  <!-- URL_IMG02 -->
  <form @submit.prevent="subirImgPag('url_img02')">
    <label>Imagen 02 (campo: url_img02)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniPagweb('url_img02')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniPagweb('url_img02')" alt="mini pagweb 02">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_pag_url_img02" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 02
        </button>
      </div>
    </div>
  </form>
</div>


      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- Crear Pagweb -->
    <div id="modalCrearPagweb" class="modal hide fade fullscreen" tabindex="-1">
      <div class="modal-header"><h3>Nueva Página</h3></div>
      <div class="modal-body">
        <div class="control-group">
          <label class="control-label">Clave *</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.clave_txt" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Título *</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.titulo" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Meta 01</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.metatag01" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Meta 02</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.metatag02" class="input-xxlarge">
          </div>
        </div>
        <!-- Campos de imagen (URL) -->
        <div class="control-group">
          <label class="control-label">URL Imagen 01</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.url_img01" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">URL Imagen 02</label>
          <div class="controls">
            <input v-model.trim="nuevoPagweb.url_img02" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearPagweb">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- Editar Pagweb -->
    <div id="modalEditarPagweb" class="modal hide fade" tabindex="-1">
      <div class="modal-header"><h3>Editar Página</h3></div>
      <div class="modal-body">
        <div class="control-group">
          <label class="control-label">Clave *</label>
          <div class="controls">
            <input v-model.trim="formPagweb.clave_txt" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Título *</label>
          <div class="controls">
            <input v-model.trim="formPagweb.titulo" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Meta 01</label>
          <div class="controls">
            <input v-model.trim="formPagweb.metatag01" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Meta 02</label>
          <div class="controls">
            <input v-model.trim="formPagweb.metatag02" class="input-xxlarge">
          </div>
        </div>
        <!-- Imágenes -->
        <div class="control-group">
          <label class="control-label">URL Imagen 01</label>
          <div class="controls">
            <input v-model.trim="formPagweb.url_img01" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">URL Imagen 02</label>
          <div class="controls">
            <input v-model.trim="formPagweb.url_img02" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarEdicionPagweb">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- ============== Modales PARRWEB ============== -->

    <!-- Detalle Parrweb -->
    <div id="modalDetalleParr" class="modal hide fade fullscreen" tabindex="-1">
      <div class="modal-header"><h3>Detalle del Parrweb</h3></div>
      

<div class="modal-body">
  <p><strong>ID:</strong> {{ detalleParr.parrweb_id }}</p>
  <p><strong>Clave:</strong> {{ detalleParr.clave_txt || '—' }}</p>
  <p><strong>Título:</strong> {{ detalleParr.titulo }}</p>
  <p><strong>Pagweb ID:</strong> {{ detalleParr.pagweb_id }}</p>
  <hr>
  <div><strong>Contenido (HTML):</strong></div>
  <div v-html="detalleParr.contenido"></div>
  <hr>
  <p><strong>Videos:</strong>
    <br>V1: {{ detalleParr.url_video01 || '—' }}
    <br>V2: {{ detalleParr.url_video02 || '—' }}
    <br>V3: {{ detalleParr.url_video03 || '—' }}
    <br>V4: {{ detalleParr.url_video04 || '—' }}
  </p>

  <hr>
  <h4>Imágenes</h4>

  <!-- IMG 01 -->
  <form @submit.prevent="subirImgParr('url_img01')">
    <label>Imagen 01 (campo: url_img01)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniParrweb('url_img01')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniParrweb('url_img01')" alt="mini parr 01">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_parr_url_img01" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 01
        </button>
      </div>
    </div>
  </form>

  <hr>

  <!-- IMG 02 -->
  <form @submit.prevent="subirImgParr('url_img02')">
    <label>Imagen 02 (campo: url_img02)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniParrweb('url_img02')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniParrweb('url_img02')" alt="mini parr 02">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_parr_url_img02" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 02
        </button>
      </div>
    </div>
  </form>

  <hr>

  <!-- IMG 03 -->
  <form @submit.prevent="subirImgParr('url_img03')">
    <label>Imagen 03 (campo: url_img03)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniParrweb('url_img03')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniParrweb('url_img03')" alt="mini parr 03">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_parr_url_img03" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 03
        </button>
      </div>
    </div>
  </form>

  <hr>

  <!-- IMG 04 -->
  <form @submit.prevent="subirImgParr('url_img04')">
    <label>Imagen 04 (campo: url_img04)</label>
    <div class="row-fluid">
      <div class="span4">
        <div v-if="miniParrweb('url_img04')" class="thumbnail" style="max-height:150px;overflow:hidden">
          <img :src="miniParrweb('url_img04')" alt="mini parr 04">
        </div>
        <div v-else class="muted">Sin imagen</div>
      </div>
      <div class="span8">
        <input type="file" id="file_parr_url_img04" accept="image/*" class="input-block-level" />
        <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">
          Subir Imagen 04
        </button>
      </div>
    </div>
  </form>
</div>



      <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

    <!-- Crear Parrweb -->
    <div id="modalCrearParr" class="modal hide fade fullscreen" tabindex="-1">
      <div class="modal-header"><h3>Nuevo Parrweb</h3></div>
      <div class="modal-body">
        <!-- Select2 de Pagweb -->
         <div class="control-group">
          <label class="control-label">Página destino</label>
          <div class="controls">
            <!-- Campo solo lectura mostrando el título y el ID -->
            <input class="input-xxlarge" :value="destPagweb ? destPagweb.text : ''" disabled>
            <!-- Guardamos el id fijo para el POST -->
            <input type="hidden" v-model.number="nuevoParr.pagweb_id">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Clave</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.clave_txt" class="input-xxlarge" placeholder="identificador opcional">
          </div>
        </div>


        <div class="control-group">
          <label class="control-label">Título *</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.titulo" class="input-xxlarge">
          </div>
        </div>

        <!-- Summernote -->
        <div class="control-group">
          <label class="control-label">Contenido (HTML)</label>
          <div class="controls">
            <div id="summerContenidoCrear" class="summernote"></div>
          </div>
        </div>

        <!-- Videos -->
        <div class="control-group">
          <label class="control-label">Video 01</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_video01" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 02</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_video02" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 03</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_video03" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 04</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_video04" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>

        <!-- Imágenes -->
        <div class="control-group">
          <label class="control-label">Imagen 01</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_img01" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 02</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_img02" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 03</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_img03" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 04</label>
          <div class="controls">
            <input v-model.trim="nuevoParr.url_img04" class="input-xxlarge" placeholder="https://...">
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="crearParrweb">Crear</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <!-- Editar Parrweb -->
    <div id="modalEditarParr" class="modal hide fade fullscreen" tabindex="-1">
      <div class="modal-header"><h3>Editar Parrweb</h3></div>
      <div class="modal-body">
        <!-- Select2 de Pagweb -->
        <div class="control-group">
          <label class="control-label">
            Página destino
            <small v-if="formParr && formParr.pagweb_id">
              ({{ obtenerClavePagweb(formParr.pagweb_id) }})
            </small>
          </label>
          <div class="controls">
            <v-select
              :options="opcionesPagweb"
              label="text"
              v-model="selectedPagwebEditar"
              :clearable="true"
              placeholder="Seleccione Página…">
            </v-select>

          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Clave</label>
          <div class="controls">
            <input v-model.trim="formParr.clave_txt" class="input-xxlarge" placeholder="identificador opcional">
          </div>
        </div>


        <div class="control-group">
          <label class="control-label">Título *</label>
          <div class="controls">
            <input v-model.trim="formParr.titulo" class="input-xxlarge">
          </div>
        </div>

        <!-- Summernote -->
        <div class="control-group">
          <label class="control-label">Contenido (HTML)</label>
          <div class="controls">
            <div id="summerContenidoEditar"></div>
          </div>
        </div>

        <!-- Videos -->
        <div class="control-group">
          <label class="control-label">Video 01</label>
          <div class="controls">
            <input v-model.trim="formParr.url_video01" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 02</label>
          <div class="controls">
            <input v-model.trim="formParr.url_video02" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 03</label>
          <div class="controls">
            <input v-model.trim="formParr.url_video03" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Video 04</label>
          <div class="controls">
            <input v-model.trim="formParr.url_video04" class="input-xxlarge">
          </div>
        </div>

        <!-- Imágenes -->
        <div class="control-group">
          <label class="control-label">Imagen 01</label>
          <div class="controls">
            <input v-model.trim="formParr.url_img01" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 02</label>
          <div class="controls">
            <input v-model.trim="formParr.url_img02" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 03</label>
          <div class="controls">
            <input v-model.trim="formParr.url_img03" class="input-xxlarge">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Imagen 04</label>
          <div class="controls">
            <input v-model.trim="formParr.url_img04" class="input-xxlarge">
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="guardarEdicionParrweb">Guardar</button>
        <button class="btn" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

    <div id="modalImgsPag" class="modal hide fade" tabindex="-1">
  <div class="modal-header"><h3>Imágenes de Página</h3></div>
  <div class="modal-body">
    <p class="muted">
      Página: <strong>{{ uploadPag?.titulo || ('#' + (uploadPag?.pagweb_id || '')) }}</strong>
    </p>

    <!-- IMG 01 -->
    <form @submit.prevent="subirImgPagDesdeModal('url_img01')">
      <div class="control-group">
        <label class="control-label">Imagen 01 ({{ uploadPag.url_img01 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickPag($event, 'preview01')">
          <div v-if="uploadPag.preview01" class="mt-2">
            <img :src="uploadPag.preview01" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 01</button>
        </div>
      </div>
    </form>

    <hr>

    <!-- IMG 02 -->
    <form @submit.prevent="subirImgPagDesdeModal('url_img02')">
      <div class="control-group">
        <label class="control-label">Imagen 02 ({{ uploadPag.url_img02 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickPag($event, 'preview02')">
          <div v-if="uploadPag.preview02" class="mt-2">
            <img :src="uploadPag.preview02" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 02</button>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cerrar</button>
  </div>
</div>


<div id="modalImgsPag" class="modal hide fade" tabindex="-1">
  <div class="modal-header"><h3>Imágenes de Página</h3></div>
  <div class="modal-body">
    <p class="muted">
      Página: <strong>{{ uploadPag?.titulo || ('#' + (uploadPag?.pagweb_id || '')) }}</strong>
    </p>

    <!-- IMG 01 -->
    <form @submit.prevent="subirImgPagDesdeModal('url_img01')">
      <div class="control-group">
        <label class="control-label">Imagen 01 ({{ uploadPag.url_img01 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickPag($event, 'preview01')">
          <div v-if="uploadPag.preview01" class="mt-2">
            <img :src="uploadPag.preview01" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 01</button>
        </div>
      </div>
    </form>

    <hr>

    <!-- IMG 02 -->
    <form @submit.prevent="subirImgPagDesdeModal('url_img02')">
      <div class="control-group">
        <label class="control-label">Imagen 02 ({{ uploadPag.url_img02 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickPag($event, 'preview02')">
          <div v-if="uploadPag.preview02" class="mt-2">
            <img :src="uploadPag.preview02" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 02</button>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cerrar</button>
  </div>
</div>

<div id="modalImgsParr" class="modal hide fade" tabindex="-1">
  <div class="modal-header"><h3>Imágenes del Parrweb</h3></div>
  <div class="modal-body">
    <p class="muted">
      Parrweb: <strong>#{{ uploadParr?.parrweb_id }}</strong> — {{ uploadParr?.titulo || '' }}
    </p>

    <form @submit.prevent="subirImgParrDesdeModal('url_img01')">
      <div class="control-group">
        <label class="control-label">Imagen 01 ({{ uploadParr.url_img01 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickParr($event, 'preview01')">
          <div v-if="uploadParr.preview01" class="mt-2">
            <img :src="uploadParr.preview01" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 01</button>
        </div>
      </div>
    </form>

    <hr>

    <form @submit.prevent="subirImgParrDesdeModal('url_img02')">
      <div class="control-group">
        <label class="control-label">Imagen 02 ({{ uploadParr.url_img02 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickParr($event, 'preview02')">
          <div v-if="uploadParr.preview02" class="mt-2">
            <img :src="uploadParr.preview02" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 02</button>
        </div>
      </div>
    </form>

    <hr>

    <form @submit.prevent="subirImgParrDesdeModal('url_img03')">
      <div class="control-group">
        <label class="control-label">Imagen 03 ({{ uploadParr.url_img03 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickParr($event, 'preview03')">
          <div v-if="uploadParr.preview03" class="mt-2">
            <img :src="uploadParr.preview03" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 03</button>
        </div>
      </div>
    </form>

    <hr>

    <form @submit.prevent="subirImgParrDesdeModal('url_img04')">
      <div class="control-group">
        <label class="control-label">Imagen 04 ({{ uploadParr.url_img04 || '—' }})</label>
        <div class="controls">
          <input type="file" accept="image/*" @change="onPickParr($event, 'preview04')">
          <div v-if="uploadParr.preview04" class="mt-2">
            <img :src="uploadParr.preview04" style="max-width:200px;max-height:120px">
          </div>
          <button type="submit" class="btn btn-primary btn-small" style="margin-top:8px">Subir Imagen 04</button>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cerrar</button>
  </div>
</div>



  </div>
</div>

<script>
  Vue.component('v-select', VueSelect.VueSelect);
</script>
<style>
  /* Z-index para dropdown dentro de Bootstrap 2.3.2 modal */
  .modal .vs__dropdown-menu { z-index: 2050; }
  /* Que ocupe todo el ancho en los .controls */
  .controls .v-select { width: 100%; }
</style>


<script>
/* global apphost, $, axios, apprise */
const appPagweb = new Vue({
  el: '#appPagweb',
  data: {
    apphost: apphost,

    // Maestro
    pagwebs: [],
    dtPag: null,
    pagwebSel: null,           // objeto seleccionado para ver parrs
    detallePagweb: {},
    nuevoPagweb: { clave_txt:'', titulo:'', metatag01:'', metatag02:'', url_img01:'', url_img02:'' },
    formPagweb: {},

    // Detalle (Parrweb)
    parrs: [],
    dtParr: null,
    destPagweb: null, 
    detalleParr: {},
    nuevoParr: {
      pagweb_id: null,
      titulo: '',
      contenido: '',
      url_video01:'', url_video02:'', url_video03:'', url_video04:'',
      url_img01:'', url_img02:'', url_img03:'', url_img04:''
    },
    formParr: {},

    // Opciones Select2 Pagweb
    opcionesPagweb: [],  // [{id, text}]
    uploadPag: {
  pagweb_id: null,
  titulo: '',
  url_img01: '',
  url_img02: '',
  preview01: '',
  preview02: '',
  file01: null,
  file02: null
},
uploadParr: {
  parrweb_id: null,
  titulo: '',
  url_img01: '',
  url_img02: '',
  url_img03: '',
  url_img04: '',
  preview01: '',
  preview02: '',
  preview03: '',
  preview04: '',
  file01: null,
  file02: null,
  file03: null,
  file04: null
},

  },
  methods: {
    // Util: bloquear UI rápido
    lock(msg='Procesando…') {
      $.blockUI({ message: `<h4>${msg}</h4>`,
        css:{border:'none',padding:'15px',background:'#000',opacity:.6,color:'#fff'} });
    },
    unlock(){ $.unblockUI(); },

    // ------------------ Maestro: PAGWEB ------------------
    listarPagweb() {
      this.lock('Cargando páginas…');
      axios.get(`${this.apphost}/pagweb/listar`)
        .then(r => {
          this.pagwebs = r.data || [];
          this.$nextTick(() => {
            // Inicializa / reinicializa DataTable
            if (!this.dtPag) {
              this.dtPag = $('#tablaPagweb').DataTable({
                scrollX: true,
                scrollY: '300px',
                dom: 'frtip',
                order: [[0,'desc']]
              });

              const self = this;
              $('#tablaPagweb tbody')
                .on('click','a.detalle-pag', function(e){
                  e.preventDefault();
                  const id = $(this).data('id'); 
                  const row = self.pagwebs.find(x => +x.pagweb_id === +id);
                  if(row) self.abrirModalDetallePagweb(row);
                })
                .on('click','a.editar-pag', function(e){
                  e.preventDefault();
                  const id = $(this).data('id'); 
                  const row = self.pagwebs.find(x => +x.pagweb_id === +id);
                  if(row) self.abrirModalEditarPagweb(row);
                })
                .on('click','a.eliminar-pag', function(e){
                  e.preventDefault();
                  const id = $(this).data('id'); 
                  const row = self.pagwebs.find(x => +x.pagweb_id === +id);
                  if(row) self.eliminarPagweb(row);
                })
                .on('click', 'a.parr-pag', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.pagwebs.find(x => +x.pagweb_id === +id);
                  if(row) self.abrirModalCrearParrwebPara(row);
                })
                .on('click','a.imgs-pag', (e) => {
                  e.preventDefault();
                  const id = $(e.currentTarget).data('id');
                  const row = this.pagwebs.find(x => +x.pagweb_id === +id);
                  if (row) this.abrirModalImgsPag(row);
                })
                .on('click','a.ver-parrs', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.pagwebs.find(x => +x.pagweb_id === +id);
                  if(row) self.mostrarParrsDe(row);
                });
            }
            // Render filas
            this.dtPag.clear();



            this.pagwebs.forEach(p => {
              const tituloClickable = `<a href="#" class="ver-parrs" data-id="${p.pagweb_id}">${this.escapeHtml(p.titulo || '')}</a>`;
              const miniRel = p.img01_mini || p.img02_mini || '';
              const miniHtml = miniRel
                ? `<img src="${this.apphost}/${miniRel.replace(/^\/+/, '')}" style="max-width:80px;max-height:60px">`
                : '<span class="muted">—</span>';

              const actions = `
                <div class="btn-group">
                  <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                    Opciones <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#" class="detalle-pag" data-id="${p.pagweb_id}">Detalle</a></li>
                    <li><a href="#" class="editar-pag"  data-id="${p.pagweb_id}">Editar</a></li>
                    <li><a href="#" class="eliminar-pag" data-id="${p.pagweb_id}">Eliminar</a></li>
                    <li><a href="#" class="imgs-pag" data-id="${p.pagweb_id}">Imágenes…</a></li>
                    <li class="divider"></li>
                    <li><a href="#" class="parr-pag"    data-id="${p.pagweb_id}"><strong>Parrweb…</strong></a></li>
                  </ul>
                </div>`;
              this.dtPag.row.add([
                p.pagweb_id,
                this.escapeHtml(p.clave_txt || ''),
                tituloClickable,
                this.escapeHtml(p.metatag01 || ''),
                this.escapeHtml(p.metatag02 || ''),
                this.escapeHtml(p.url_img01 || ''),
                this.escapeHtml(p.url_img02 || ''),
                miniHtml,
                actions
              ]);
            });
            this.dtPag.draw(false);
          });
        })
        .finally(this.unlock);
    },

    abrirModalImgsPag(row) {
  this.uploadPag = {
    pagweb_id: row.pagweb_id,
    titulo: row.clave_txt || row.titulo || '',
    url_img01: row.url_img01 || '',
    url_img02: row.url_img02 || '',
    preview01: '',
    preview02: '',
    file01: null,
    file02: null
  };
  $('#modalImgsPag').modal('show');
},

abrirModalImgsParr(row) {
  this.uploadParr = {
    parrweb_id: row.parrweb_id,
    titulo: row.clave_txt || row.titulo || '',
    url_img01: row.url_img01 || '',
    url_img02: row.url_img02 || '',
    url_img03: row.url_img03 || '',
    url_img04: row.url_img04 || '',
    preview01: '',
    preview02: '',
    preview03: '',
    preview04: '',
    file01: null, file02: null, file03: null, file04: null
  };
  $('#modalImgsParr').modal('show');
},

onPickPag(evt, targetPreview) {
  const f = evt.target.files[0]; if (!f) return;
  const reader = new FileReader();
  reader.onload = e => { this.uploadPag[targetPreview] = e.target.result; };
  reader.readAsDataURL(f);
  if (targetPreview === 'preview01') this.uploadPag.file01 = f;
  if (targetPreview === 'preview02') this.uploadPag.file02 = f;
},

onPickParr(evt, targetPreview) {
  const f = evt.target.files[0]; if (!f) return;
  const reader = new FileReader();
  reader.onload = e => { this.uploadParr[targetPreview] = e.target.result; };
  reader.readAsDataURL(f);
  if (targetPreview === 'preview01') this.uploadParr.file01 = f;
  if (targetPreview === 'preview02') this.uploadParr.file02 = f;
  if (targetPreview === 'preview03') this.uploadParr.file03 = f;
  if (targetPreview === 'preview04') this.uploadParr.file04 = f;
},


async subirImgPagDesdeModal(campo) {
  const id = this.uploadPag.pagweb_id;
  const file = (campo === 'url_img01') ? this.uploadPag.file01 : this.uploadPag.file02;
  if (!file) return apprise('Selecciona una imagen');
  const fd = new FormData();
  fd.append('pagweb_id', id);
  fd.append('campo', campo);
  fd.append('archivo', file);
  try {
    await axios.post(`${this.apphost}/pagweb/subir_img`, fd, { headers:{'Content-Type':'multipart/form-data'} });
    apprise('Imagen subida');
    $('#modalImgsPag').modal('hide');
    this.listarPagweb(); // refresca mini columna
  } catch(e) {
    apprise('Error al subir');
  }
},

async subirImgParrDesdeModal(campo) {
  const id = this.uploadParr.parrweb_id;
  const mapa = { 'url_img01':'file01', 'url_img02':'file02', 'url_img03':'file03', 'url_img04':'file04' };
  const file = this.uploadParr[mapa[campo]];
  if (!file) return apprise('Selecciona una imagen');
  const fd = new FormData();
  fd.append('parrweb_id', id);
  fd.append('campo', campo);
  fd.append('archivo', file);
  try {
    await axios.post(`${this.apphost}/parrweb/subir_img`, fd, { headers:{'Content-Type':'multipart/form-data'} });
    apprise('Imagen subida');
    $('#modalImgsParr').modal('hide');
    if (this.pagwebSel) this.listarParrwebPorPag(this.pagwebSel.pagweb_id); // refresca mini
  } catch(e) {
    apprise('Error al subir');
  }
},


    // Abre el detalle y guarda rutas de imágenes desde vari()
    abrirModalDetallePagweb(p) {
      axios.get(`${this.apphost}/pagweb/detalle/${p.pagweb_id}`)
        .then(r => {
          if (r.data && r.data.data) {
            this.detallePagweb = r.data.data;
            // rutas publicadas por el backend (vari('PICS_PAG_WEB_MINI/FULL'))
            this.detallePagweb.__pics = r.data.pics || { mini:'', full:'' };
            $('#modalDetallePagweb').modal('show');
          } else {
            apprise('No se pudo cargar el detalle');
          }
        });
    },


    abrirModalCrearPagweb() {
      this.nuevoPagweb = { clave_txt:'', titulo:'', metatag01:'', metatag02:'', url_img01:'', url_img02:'' };
      $('#modalCrearPagweb').modal('show');
    },
    crearPagweb() {
      if(!this.nuevoPagweb.clave_txt || !this.nuevoPagweb.titulo){
        return apprise('Completa Clave y Título');
      }
      this.lock('Creando página…');
      axios.post(`${this.apphost}/pagweb/crear`, this.nuevoPagweb)
        .then(() => {
          $('#modalCrearPagweb').modal('hide');
          apprise('¡Creado!');
          this.listarPagweb();
        })
        .finally(this.unlock);
    },
    abrirModalEditarPagweb(p) {
      this.formPagweb = Object.assign({}, p);
      $('#modalEditarPagweb').modal('show');
    },
    guardarEdicionPagweb() {
      if(!this.formPagweb.clave_txt || !this.formPagweb.titulo){
        return apprise('Completa Clave y Título');
      }
      this.lock('Actualizando página…');
      axios.post(`${this.apphost}/pagweb/editar`, this.formPagweb)
        .then(() => {
          $('#modalEditarPagweb').modal('hide');
          apprise('¡Actualizado!');
          this.listarPagweb();
          // refrescar detalle si estaba visible
          if(this.pagwebSel && this.pagwebSel.pagweb_id === this.formPagweb.pagweb_id){
            this.pagwebSel = Object.assign({}, this.formPagweb);
          }
        })
        .finally(this.unlock);
    },
    eliminarPagweb(p) {
      apprise(`¿Eliminar página <b>#${p.pagweb_id}</b>?`, {confirm:true}, ok => {
        if(!ok) return;
        this.lock('Eliminando…');
        axios.post(`${this.apphost}/pagweb/eliminar`, { pagweb_id: p.pagweb_id })
          .then(() => {
            apprise('Eliminado');
            if(this.pagwebSel && +this.pagwebSel.pagweb_id === +p.pagweb_id){
              this.pagwebSel = null;
              this.destruirParrTable();
            }
            this.listarPagweb();
          })
          .finally(this.unlock);
      });
    },

    // ------------------ Detalle: PARRWEB ------------------
    mostrarParrsDe(p) {
      this.pagwebSel = p;
      this.listarParrwebPorPag(p.pagweb_id);
    },
    destruirParrTable(){
      if(this.dtParr){
        this.dtParr.clear().destroy();
        this.dtParr = null;
      }
      this.parrs = [];
    },
    listarParrwebPorPag(pagweb_id) {
      this.lock('Cargando parrweb…');
      axios.get(`${this.apphost}/parrweb/listar/${pagweb_id}`)
        .then(r => {
          this.parrs = r.data || [];
          this.$nextTick(() => {
            if(!this.dtParr){
              this.dtParr = $('#tablaParrweb').DataTable({
                scrollX:true,
                scrollY: '300px',
                dom:'frtip',
                order:[[0,'desc']]
              });
              const self = this;
              $('#tablaParrweb tbody')
                .on('click','a.detalle-parr', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.parrs.find(x => +x.parrweb_id === +id);
                  if(row) self.abrirModalDetalleParr(row);
                })
                .on('click','a.editar-parr', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.parrs.find(x => +x.parrweb_id === +id);
                  if(row) self.abrirModalEditarParr(row);
                })
                .on('click','a.imgs-parr', (e) => {
                  e.preventDefault();
                  const id = $(e.currentTarget).data('id');
                  const row = this.parrs.find(x => +x.parrweb_id === +id);
                  if (row) this.abrirModalImgsParr(row);
                })
                .on('click','a.eliminar-parr', function(e){
                  e.preventDefault();
                  const id = $(this).data('id');
                  const row = self.parrs.find(x => +x.parrweb_id === +id);
                  if(row) self.eliminarParr(row);
                });
            }           


            this.dtParr.clear();
            this.parrs.forEach(x => {
              const miniRel =
                x.img01_mini || x.img02_mini || x.img03_mini || x.img04_mini || '';
              const miniHtml = miniRel
                ? `<img src="${this.apphost}/${miniRel.replace(/^\/+/, '')}" style="max-width:80px;max-height:60px">`
                : '<span class="muted">—</span>';

              const actions = `
                <div class="btn-group">
                  <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                    Opciones <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#" class="detalle-parr"  data-id="${x.parrweb_id}">Detalle</a></li>
                    <li><a href="#" class="editar-parr"   data-id="${x.parrweb_id}">Editar</a></li>
                    <li><a href="#" class="eliminar-parr" data-id="${x.parrweb_id}">Eliminar</a></li>
                    <li><a href="#" class="imgs-parr" data-id="${x.parrweb_id}">Imágenes…</a></li>
                  </ul>
                </div>`;
              this.dtParr.row.add([
                x.parrweb_id,
                this.escapeHtml(x.titulo || ''),
                this.escapeHtml(x.clave_txt || ''),   
                this.escapeHtml(x.url_video01 || ''),
                this.escapeHtml(x.url_video02 || ''),
                this.escapeHtml(x.url_img01 || ''),
                this.escapeHtml(x.url_img02 || ''),
                miniHtml,
                actions
              ]);
            });
            this.dtParr.draw(false);
          });
        })
        .finally(this.unlock);
    },

    abrirModalDetalleParr(row) {
      axios.get(`${this.apphost}/parrweb/detalle/${row.parrweb_id}`)
        .then(r => {
          if (r.data && r.data.data) {
            this.detalleParr = r.data.data;
            this.detalleParr.__pics = r.data.pics || { mini:'', full:'' };
            $('#modalDetalleParr').modal('show');
          } else {
            apprise('No se pudo cargar el detalle');
          }
        });
    },


    // ----- Crear Parrweb desde pagweb (dropdown "Parrweb") -----
    abrirModalCrearParrwebPara(pagweb) {
      // Debe venir desde una fila o desde pagwebSel
      if (!pagweb || !pagweb.pagweb_id) {
        return apprise('Primero elige una página (pagweb).');
      }

      // Fijamos destino
      this.destPagweb = {
        id: pagweb.pagweb_id,
        text: (pagweb.clave_txt || pagweb.titulo || '').trim()
      };


      // Prepara el objeto de creación con el pagweb_id bloqueado
      this.nuevoParr = {
        pagweb_id: this.destPagweb.id,
        titulo: '',
        clave_txt: '',
        contenido: '',
        url_video01:'', url_video02:'', url_video03:'', url_video04:'',
        url_img01:'', url_img02:'', url_img03:'', url_img04:''
      };

      this.$nextTick(() => {
        // Summernote fresco
        $('#summerContenidoCrear').summernote('destroy');
        $('#summerContenidoCrear').summernote({
          height: 180,
          callbacks: {
            onChange: (contents) => { this.nuevoParr.contenido = contents; }
          }
        }).summernote('code', this.nuevoParr.contenido || '');

        $('#modalCrearParr').modal('show');
      });
    },

    crearParrweb() {
      if(!this.nuevoParr.pagweb_id) return apprise('Seleccione la Página destino.');
      if(!this.nuevoParr.titulo) return apprise('Escriba un título.');

      this.lock('Creando parrweb…');
      axios.post(`${this.apphost}/parrweb/crear`, this.nuevoParr)
        .then(() => {
          $('#modalCrearParr').modal('hide');
          apprise('¡Creado!');
          if (this.pagwebSel && this.pagwebSel.pagweb_id === this.nuevoParr.pagweb_id) {
            this.listarParrwebPorPag(this.pagwebSel.pagweb_id);
          }
        })
        .finally(this.unlock);
    },

    // ----- Editar Parrweb -----
    abrirModalEditarParr(row) {
      this.cargarOpcionesPagweb(() => {
        this.formParr = Object.assign({}, row);
        this.$nextTick(() => {
          // Summernote
          $('#summerContenidoEditar').summernote('destroy');
          $('#summerContenidoEditar').summernote({
            height: 180,
            callbacks: {
              onChange: (contents) => { this.formParr.contenido = contents; }
            }
          }).summernote('code', this.formParr.contenido || '');

          $('#modalEditarParr').modal('show');
        });
      });
    },

    textoPagweb(id) {
      const o = this.opcionesPagweb.find(x => x.id === id);
      if (!o) return '';
      // prioriza clave; si no hay, usa text (que ya incluye [id])
      return o.clave_txt || o.text || '';
    },


    guardarEdicionParrweb() {
      if(!this.formParr.pagweb_id) return apprise('Seleccione la Página destino.');
      if(!this.formParr.titulo) return apprise('Escriba un título.');

      this.lock('Actualizando parrweb…');
      axios.post(`${this.apphost}/parrweb/editar`, this.formParr)
        .then(() => {
          $('#modalEditarParr').modal('hide');
          apprise('¡Actualizado!');
          if(this.pagwebSel) this.listarParrwebPorPag(this.pagwebSel.pagweb_id);
        })
        .finally(this.unlock);
    },

    eliminarParr(row) {
      apprise(`¿Eliminar parrweb <b>#${row.parrweb_id}</b>?`, {confirm:true}, ok => {
        if(!ok) return;
        this.lock('Eliminando…');
        axios.post(`${this.apphost}/parrweb/eliminar`, { parrweb_id: row.parrweb_id })
          .then(() => {
            apprise('Eliminado');
            if(this.pagwebSel) this.listarParrwebPorPag(this.pagwebSel.pagweb_id);
          })
          .finally(this.unlock);
      });
    },

    // ------ Utilidades ------
    cargarOpcionesPagweb(done){
      // cache simple
      axios.get(`${this.apphost}/pagweb/listar`)
        .then(r => {
          const rows = r.data || [];
          this.opcionesPagweb = (r.data || []).map(x => ({
            id: Number(x.pagweb_id),
            clave_txt: (x.clave_txt || '').trim(),
            titulo: (x.titulo || '').trim(),
            // lo que verá el v-select:
            text: (x.clave_txt || x.titulo || '').trim()
          }));

          done && done();
        });
    },
    escapeHtml(s) {
      return String(s).replace(/[&<>"'`=\/]/g, function(c){
        return {
          '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'
        }[c];
      });
    },

    obtenerClavePagweb(id) {
      const o = this.opcionesPagweb.find(p => Number(p.id) === Number(id));
      return o ? (o.clave_txt || o.titulo || '') : '';
    },


    // Devuelve la URL MINI para mostrar el preview en el modal
    miniPagweb(campo) {
      const fname = this.detallePagweb && this.detallePagweb[campo] ? this.detallePagweb[campo] : '';
      const base  = this.detallePagweb && this.detallePagweb.__pics ? this.detallePagweb.__pics.mini : '';
      if (!fname || !base) return '';
      // arma: {apphost}/{carpeta_mini}/{filename}
      return `${this.apphost}/${String(base).replace(/^\/+/, '')}/${fname}`;
    },


    miniParrweb(campo){
      const fname = this.detalleParr && this.detalleParr[campo] ? this.detalleParr[campo] : '';
      const base  = this.detalleParr && this.detalleParr.__pics ? this.detalleParr.__pics.mini : '';
      if (!fname || !base) return '';
      return `${this.apphost}/${String(base).replace(/^\/+/, '')}/${fname}`;
    },


    // Subida Pagweb + refresco de detalle
    async subirImgPag(campo){
      if(!this.detallePagweb || !this.detallePagweb.pagweb_id){
        return apprise('No hay página seleccionada');
      }
      const inputId = `file_pag_${campo}`;
      const file = document.getElementById(inputId)?.files?.[0];
      if(!file) return apprise('Selecciona una imagen');

      this.lock('Subiendo imagen…');
      try{
        const fd = new FormData();
        fd.append('pagweb_id', this.detallePagweb.pagweb_id);
        fd.append('campo', campo); // 'url_img01' o 'url_img02'
        fd.append('archivo', file);

        const r = await axios.post(`${this.apphost}/pagweb/subir_img`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if(r.data && r.data.filename){
          // Actualiza rápido en memoria
          this.detallePagweb[campo] = r.data.filename;
          apprise('Imagen subida');

          // 🔁 Refrescar detalle desde backend (incluye __pics con vari())
          const det = await axios.get(`${this.apphost}/pagweb/detalle/${this.detallePagweb.pagweb_id}`);
          if(det.data && det.data.data){
            this.detallePagweb = det.data.data;
            this.detallePagweb.__pics = det.data.pics || { mini:'', full:'' };
          }

          // Opcional: refrescar tabla maestro
          this.listarPagweb();
        }

        // Limpia input file
        document.getElementById(inputId).value = '';
      } catch(e){
        apprise('Error al subir');
      } finally {
        this.unlock();
      }
    },

    // Subida Parrweb + refresco de detalle
    async subirImgParr(campo){
      if(!this.detalleParr || !this.detalleParr.parrweb_id){
        return apprise('No hay parrweb seleccionado');
      }
      const inputId = `file_parr_${campo}`;
      const file = document.getElementById(inputId)?.files?.[0];
      if(!file) return apprise('Selecciona una imagen');

      this.lock('Subiendo imagen…');
      try{
        const fd = new FormData();
        fd.append('parrweb_id', this.detalleParr.parrweb_id);
        fd.append('campo', campo); // 'url_img01'..'url_img04'
        fd.append('archivo', file);

        const r = await axios.post(`${this.apphost}/parrweb/subir_img`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if(r.data && r.data.filename){
          // Actualiza rápido en memoria
          this.detalleParr[campo] = r.data.filename;
          apprise('Imagen subida');

          // 🔁 Refrescar detalle desde backend (incluye __pics con vari())
          const det = await axios.get(`${this.apphost}/parrweb/detalle/${this.detalleParr.parrweb_id}`);
          if(det.data && det.data.data){
            this.detalleParr = det.data.data;
            this.detalleParr.__pics = det.data.pics || { mini:'', full:'' };
          }

          // Si estás viendo la lista de parrs de una página, recárgala
          if(this.pagwebSel) {
            this.listarParrwebPorPag(this.pagwebSel.pagweb_id);
          }
        }

        // Limpia input file
        document.getElementById(inputId).value = '';
      } catch(e){
        apprise('Error al subir');
      } finally {
        this.unlock();
      }
    },
  },
  mounted() {
    this.listarPagweb();
  },
  computed: {
    selectedPagwebEditar: {
      get() {
        const id = this.formParr?.pagweb_id == null ? null : Number(this.formParr.pagweb_id);
        return this.opcionesPagweb.find(o => Number(o.id) === id) || null;
      },
      set(val) {
        this.formParr.pagweb_id = val ? Number(val.id) : null;
      }
    }
  }
});
</script>
