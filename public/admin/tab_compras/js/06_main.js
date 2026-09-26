new Vue({
  el: '#appCompra',
  data: comprasState,
  computed: {
    totalNuevo() {
      return this.nuevo.items.reduce((s, it) => s + (Number(it.cantidad) * Number(it.costo_unitario)), 0);
    },
    productosSelectCompra() {
      return this.productos.map(p => ({
        product_id: p.product_id,
        name: p.name,
        price: p.price,
        label: p.name
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
        // Precarga el precio base como sugerencia de costo unitario
        this.itemTemp.costo_unitario = parseFloat(p.price || 0);
      } else {
        this.itemTemp.product_id = null;
        this.itemTemp.costo_unitario = 0;
      }
    }
  },
  mounted() {
    this.cargarProveedores();
    this.cargarProductos();
    this.listar();
  }
});