const comprasMethods = {
  listar() {
    axios.get(`${this.apphost}/compra/listar`).then(r => {
      this.compras = r.data;

      this.$nextTick(() => {
        if (!this.dt) {
          this.dt = $('#tablaCompra').DataTable({
            language: typeof dt_language !== 'undefined' ? dt_language : {},
            dom: 'frtip',
            order: [[0, 'desc']]
          });

          const self = this;
          $('#tablaCompra tbody')
            .on('click', 'a.detalle', function (e) {
              e.preventDefault();
              const id = $(this).data("id");
              self.abrirDetalle(id);
            })
            .on('click', 'a.editar', function (e) {
              e.preventDefault();
              const id = $(this).data("id");
              const c = self.compras.find(x => x.compra_id == id);
              self.abrirEditar(c);
            })
            .on('click', 'a.add-items', function (e) {
              e.preventDefault();
              const id = $(this).data("id");
              const c = self.compras.find(x => x.compra_id == id);
              self.abrirAgregarProductos(c);
            })
            .on('click', 'a.eliminar', function (e) {
              e.preventDefault();
              const id = $(this).data("id");
              const c = self.compras.find(x => x.compra_id == id);
              self.eliminarCompra(c);
            });
        }

        this.dt.clear();

        this.compras.forEach(c => {
          const acciones = `
            <div class="btn-group">
              <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                Opciones <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="#" class="detalle" data-id="${c.compra_id}">Detalle</a></li>
                <li><a href="#" class="editar" data-id="${c.compra_id}">Editar</a></li>
                <li><a href="#" class="eliminar" data-id="${c.compra_id}">Eliminar</a></li>
                <li>
                  <a href="#" class="add-items" data-id="${c.compra_id}">
                    Agregar Productos
                  </a>
                </li>
              </ul>
            </div>`;

          const fechaVisual = this.formatearFechaHora(c.fecha_compra);

          this.dt.row.add([
            c.compra_id,
            c.razon_social,
            fechaVisual,
            c.total,
            acciones
          ]);
        });

        this.dt.draw(false);
      });
    });
  },

  abrirModalCrear() {
    this.nuevo = { 
      proveedor: null, 
      fecha_compra: this.fechaHoy(),
      items: [] 
    };
    $('#modalCrearCompra').modal('show');
  },

  crearCompra() {
    if (!this.nuevo.proveedor || !this.nuevo.proveedor.proveedor_id) {
      alert('Debe seleccionar proveedor');
      return;
    }

    if (this.nuevo.items.length === 0) {
      alert('Debe agregar al menos un producto');
      return;
    }

    let fechaFinal = '';
    const horaActual = this.horaActual();

    if (this.nuevo.fecha_compra) {
      fechaFinal = `${this.nuevo.fecha_compra} ${horaActual}`;
    } else {
      fechaFinal = `${this.fechaHoy()} ${horaActual}`;
    }

    axios.post(`${this.apphost}/compra/crear`, {
      proveedor_id: Number(this.nuevo.proveedor.proveedor_id),
      fecha_compra: fechaFinal,
      observaciones: '',
      items: this.nuevo.items
    }).then(() => {
      $('#modalCrearCompra').modal('hide');
      this.listar();
    });
  },

  abrirDetalle(id) {
    axios.get(`${this.apphost}/compra/detalle/${id}`).then(r => {
      this.detalle = r.data;
      $('#modalDetalleCompra').modal('show');
    });
  },

  abrirEditar(c) {
    let fechaSolo = '';
    let horaOriginal = '';

    if (c.fecha_compra) {
      const partes = c.fecha_compra.split(' ');
      fechaSolo = partes[0];
      horaOriginal = partes[1] || '';
    }

    const proveedorActual = this.proveedores.find(p => p.proveedor_id == c.proveedor_id) || null;

    this.form = {
      compra_id: c.compra_id,
      proveedor: proveedorActual,
      fecha_compra: fechaSolo,
      hora_original: horaOriginal,
      observaciones: c.observaciones || ''
    };

    $('#modalEditarCompra').modal('show');
  },

  guardarEdicion() {
    if (!this.form.proveedor || !this.form.proveedor.proveedor_id) {
      alert('Debe seleccionar proveedor');
      return;
    }

    let fechaFinal = '';
    const hora = this.form.hora_original || this.horaActual();

    if (this.form.fecha_compra) {
      fechaFinal = `${this.form.fecha_compra} ${hora}`;
    }

    const payload = {
      compra_id: this.form.compra_id,
      proveedor_id: Number(this.form.proveedor.proveedor_id),
      fecha_compra: fechaFinal,
      observaciones: this.form.observaciones
    };

    axios.post(`${this.apphost}/compra/editar`, payload)
      .then(() => {
        $('#modalEditarCompra').modal('hide');
        this.listar();
      });
  },

  eliminarCompra(c) {
    apprise(`¿Eliminar compra #${c.compra_id}?`, { confirm: true }, ok => {
      if (!ok) return;
      axios.post(`${this.apphost}/compra/eliminar`, { compra_id: c.compra_id })
        .finally(() => this.listar());
    });
  },

  cargarProveedores() {
    axios.get(`${this.apphost}/proveedor/listar`)
      .then(r => this.proveedores = r.data);
  },

  cargarProductos() {
    axios.get(`${this.apphost}/producto/listar`)
      .then(r => this.productos = r.data);
  }
};