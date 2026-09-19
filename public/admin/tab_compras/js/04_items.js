const itemsMethods = {
  abrirModalAgregarItemCompra() {
    this.itemTemp = {
      producto: null,
      product_id: null,
      costo_unitario: 0,
      cantidad: 1
    };

    this.$nextTick(() => {
      this.liberarFocusVueSelect();
      $('#modalAgregarItemCompra').modal('show');
    });
  },

  confirmarAgregarItemCompra() {
    if (!this.itemTemp.producto) {
      apprise('Seleccione un producto');
      return;
    }

    if (this.itemTemp.cantidad <= 0) {
      apprise('Cantidad inválida');
      return;
    }

    this.nuevo.items.push({
      product_id: this.itemTemp.producto.product_id,
      producto: this.itemTemp.producto.label,
      cantidad: this.itemTemp.cantidad,
      costo_unitario: this.itemTemp.costo_unitario
    });

    $('#modalAgregarItemCompra').modal('hide');
  },

  quitarItem(it) {
    this.nuevo.items = this.nuevo.items.filter(x => x !== it);
  },

  abrirAgregarProductos(c) {
    this.compraAdd = {
      compra_id: c.compra_id,
      items_nuevos: []
    };

    axios.get(`${this.apphost}/compra/items/${c.compra_id}`)
      .then(r => {
        this.itemsExistentes = r.data;
        $('#modalAddItems').modal('show');
      });
  },

  agregarItemAdd() {
    this.compraAdd.items_nuevos.push({
      product_id: null,
      cantidad: 1,
      costo_unitario: 0
    });
  },

  quitarItemAdd(it) {
    this.compraAdd.items_nuevos = this.compraAdd.items_nuevos.filter(x => x !== it);
  },

  guardarAddItems() {
    axios.post(`${this.apphost}/compra/agregar-items`, {
      compra_id: this.compraAdd.compra_id,
      items: this.compraAdd.items_nuevos
    }).then(() => {
      $('#modalAddItems').modal('hide');
      this.listar();
    });
  }
};