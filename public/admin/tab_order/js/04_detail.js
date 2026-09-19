const detailMethods = {
  abrirDetalle(o) {
    axios.get(`${this.apphost}/product_order/detalle/${o.product_order_id}`).then(r => {
      this.detalle = r.data.order;
      this.detallesOrder = r.data.detalles;
      $('#modalDetalleOrder').modal('show');
    });
  },

  abrirCrearDetail() {
    this.detailForm = {
      order_id: this.detalle.product_order_id,
      producto: null,
      product_id: null,
      amount: 1,
      price_item: 0
    };
    $('#modalCrearDetail').modal('show');
  },

  crearDetail() {
    axios.post(`${this.apphost}/product_order_detail/crear`, this.detailForm)
      .then(() => {
        $('#modalCrearDetail').modal('hide');
        this.abrirDetalle(this.detalle);
      });
  },

  abrirEditarDetail(d) {
    this.detailForm = JSON.parse(JSON.stringify(d));
    $('#modalEditarDetail').modal('show');
  },

  guardarDetail() {
    axios.post(`${this.apphost}/product_order_detail/editar`, this.detailForm)
      .then(() => {
        $('#modalEditarDetail').modal('hide');
        this.abrirDetalle(this.detalle);
      });
  },

  eliminarDetail(d) {
    apprise(`¿Eliminar ítem ${d.product_name}?`, { confirm: true }, ok => {
      if (!ok) return;
      axios.post(`${this.apphost}/product_order_detail/eliminar`, {
        product_order_detail_id: d.product_order_detail_id
      })
      .then(() => this.abrirDetalle(this.detalle));
    });
  },

  abrirModalAgregarItemNuevaOrden() {
    this.itemForm = {
      producto: null,
      product_id: '',
      amount: 1,
      price_item: 0
    };
    $('#modalAgregarItemNuevaOrden').modal('show');
  },

  confirmarAgregarItem() {
    const p = this.productos.find(x => x.product_id == this.itemForm.product_id);
    if (!p) {
      alert('Seleccione un producto');
      return;
    }

    if (this.itemForm.amount <= 0) {
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
  }
};