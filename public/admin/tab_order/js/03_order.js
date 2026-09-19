const orderMethods = {
  listar() {
    axios.get(`${this.apphost}/product_order/listar`).then(r => {
      this.ordenes = r.data;

      this.$nextTick(() => {
        if (!this.dt) {
          this.dt = $('#tablaOrder').DataTable({
            language: typeof dt_language !== 'undefined' ? dt_language : {},
            dom: 'frtip',
            order: [[0, 'desc']]
          });

          const self = this;
          $('#tablaOrder tbody')
            .on('click', 'a.detalle', function () {
              const id = $(this).data("id");
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.abrirDetalle(o);
            })
            .on('click', 'a.editar', function () {
              const id = $(this).data("id");
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.abrirEditar(o);
            })
            .on('click', 'a.eliminar', function () {
              const id = $(this).data("id");
              const o = self.ordenes.find(x => x.product_order_id == id);
              self.eliminar(o);
            })
            .on('click', 'a.liberar', function () {
              const id = $(this).data('id');
              apprise('¿Liberar esta mesa?', { confirm: true }, ok => {
                if (!ok) return;
                axios.post(`${self.apphost}/product_order/liberar_mesa`, {
                  product_order_id: id
                }).then(() => {
                  self.listar();
                  self.cargarMesas();
                });
              });
            });
        }

        this.dt.clear();
        this.ordenes.forEach(o => {
          let extra = '';
          if (o.modo_order_id === 2) {
            extra = `
              <li>
                <a href="#" class="liberar" data-id="${o.product_order_id}">
                  Liberar mesa
                </a>
              </li>`;
          }

          const actions = `
            <div class="btn-group">
              <button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown">
                Opciones <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a href="#" class="detalle" data-id="${o.product_order_id}">
                    Detalle
                  </a>
                </li>
                ${extra}
                <li class="divider"></li>
                <li>
                  <a href="#" class="eliminar" data-id="${o.product_order_id}">
                    Eliminar
                  </a>
                </li>
              </ul>
            </div>`;

          const modoTxt = o.modo_order_id === 2
            ? `<span class="label label-important">${o.modo_order}</span>`
            : `<span class="label label-success">${o.modo_order}</span>`;

          this.dt.row.add([
            o.product_order_id,
            o.serial,
            o.cliente,
            o.mesa_nombre || '—',
            modoTxt,
            o.administrador || '—',
            o.tipo_pago || '—',
            o.fecha,
            o.total_fees,
            actions
          ]);
        });
        this.dt.draw(false);
      });
    });
  },

  filtrarMesaPedido() {
    if (this.dt) {
      this.dt.search('mesa pedido').draw();
      $('#tablaOrder_filter input').val('mesa pedido').focus();
    }
  },

  restablecerFiltroInicio() {
    if (this.dt) {
      this.dt.search('').order([[0, 'desc']]).draw();
      $('#tablaOrder_filter input').val('').focus();
    }
  },

  abrirModalCrear() {
    axios.get(`${this.apphost}/auth/administrador-actual`).then(r => {
      const caja = r.data.caja;
      if (caja.estado !== 'ABIERTA') {
        apprise('La caja de este usuario está cerrada');
        return;
      }

      this.cajaActual = caja;
      this.caja_id = caja.caja_id;

      this.nueva = {
        cliente_id: null,
        cliente: null,
        buyer: '',
        address: '',
        total_fees: 0,
        items: [],
        tipo_pago_id: null,
        caja_id: caja.caja_id,
        mesa: null
      };

      $('#modalCrearOrder').modal('show');
    }).catch(() => {
      apprise('No se pudo verificar el estado de la caja');
    });
  },

  crearOrder() {
    if (!this.nueva.cliente_id) {
      apprise('Debe seleccionar un cliente');
      return;
    }

    if (!this.nueva.mesa) {
      apprise('Debe seleccionar una mesa o DIRECTO');
      return;
    }

    if (this.nueva.items.length === 0) {
      apprise('Agregue al menos un ítem');
      return;
    }

    if (!this.nueva.tipo_pago_id) {
      apprise('Seleccione tipo de pago');
      return;
    }

    const mesa_id = this.nueva.mesa.mesa_id;

    axios.post(`${this.apphost}/product_order/crear`, {
      cliente_id: this.nueva.cliente_id,
      phone: this.nueva.telefono || '',
      comment: '',
      total_fees: this.totalOrden,
      tipo_pago_id: this.nueva.tipo_pago_id,
      mesa_id: mesa_id,
      items: this.nueva.items
    })
    .then(() => {
      $('#modalCrearOrder').modal('hide');
      this.listar();
      this.cargarMesas();
    })
    .catch(e => {
      const r = e.response?.data;
      if (r && r.status === 'error' && r.msg === 'Stock insuficiente') {
        apprise(
          'Stock insuficiente.<br><br>' +
          '<b>Producto:</b> ' + r.producto + '<br>' +
          '<b>Stock actual:</b> ' + r.stock_actual + '<br>' +
          '<b>Cantidad solicitada:</b> ' + r.cantidad_solicitada
        );
        return;
      }
      apprise(r?.msg || 'Error al crear la orden');
    });
  },

  abrirEditar(o) {
    this.form = JSON.parse(JSON.stringify(o));
    $('#modalEditarOrder').modal('show');
  },

  guardarOrder() {
    axios.post(`${this.apphost}/product_order/editar`, this.form)
      .then(() => {
        $('#modalEditarOrder').modal('hide');
        this.listar();
      });
  },

  eliminar(o) {
    apprise(`¿Eliminar orden #${o.product_order_id}?`, { confirm: true }, ok => {
      if (!ok) return;
      axios.post(`${this.apphost}/product_order/eliminar`, { product_order_id: o.product_order_id })
        .finally(() => this.listar());
    });
  },

  cargarProductos() {
    axios.get(`${this.apphost}/product/listar`)
      .then(r => this.productos = r.data);
  }
};