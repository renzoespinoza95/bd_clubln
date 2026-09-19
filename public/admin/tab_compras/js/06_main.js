new Vue({
  el: '#appCompra',
  data: comprasState,
  computed: {
    totalNuevo() {
      return this.nuevo.items.reduce((s, it) => s + (it.cantidad * it.costo_unitario), 0);
    },
    productosSelectCompra() {
      return this.productos.map(p => ({
        product_id: p.product_id,
        label: `${p.name}`
      }));
    }
  },
  methods: {
    ...comprasUiHelpers,
    ...comprasMethods,
    ...itemsMethods,
    ...reportesMethods
  },
  watch: {
    'itemTemp.producto'(p) {
      if (p) {
        this.itemTemp.product_id = p.product_id;
      }
    }
  },
  mounted() {
    this.cargarProveedores();
    this.cargarProductos();
    this.listar();
  }
});