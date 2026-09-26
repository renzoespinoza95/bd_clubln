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

  onSeleccionarProductoTemp(prod) {
    if (prod) {
      this.itemTemp.product_id = prod.product_id;
      // Asigna el precio base como sugerencia de costo unitario si está disponible
      this.itemTemp.costo_unitario = parseFloat(prod.price || prod.costo || 0);
    } else {
      this.itemTemp.product_id = null;
      this.itemTemp.costo_unitario = 0;
    }
  },

  confirmarAgregarItemCompra() {
    if (!this.itemTemp.producto || !this.itemTemp.producto.product_id) {
      apprise('Seleccione un producto');
      return;
    }

    if (this.itemTemp.cantidad <= 0) {
      apprise('Cantidad inválida');
      return;
    }

    const nombreProducto = this.itemTemp.producto.name || this.itemTemp.producto.label;

    this.nuevo.items.push({
      product_id: this.itemTemp.producto.product_id,
      producto: nombreProducto,
      cantidad: Number(this.itemTemp.cantidad),
      costo_unitario: parseFloat(this.itemTemp.costo_unitario || 0)
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