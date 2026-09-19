Vue.component('v-select', VueSelect.VueSelect);

new Vue({
  el: "#appOrder",
  data: orderState,

  computed: {
    ordenCerrada() {
      return !!this.detalle.fecha_fin;
    },

    totalDetalleOrden() {
      return this.detallesOrder.reduce(
        (s, d) => s + (d.amount * d.price_item),
        0
      ).toFixed(2);
    },

    productosSelect() {
      return this.productos.map(p => ({
        product_id: p.product_id,
        price: p.price,
        label: `${p.name} - S/ ${p.price}`
      }));
    },

    totalDetalle() {
      return (this.detailForm.amount || 0) * (this.detailForm.price_item || 0);
    },

    totalItem() {
      return this.detailForm.amount * this.detailForm.price_item;
    },

    totalOrden() {
      return this.nueva.items.reduce(
        (s, i) => s + (i.amount * i.price_item),
        0
      ).toFixed(2);
    },

    totalItemNuevaOrden() {
      return (this.itemForm.amount || 0) * (this.itemForm.price_item || 0);
    },

    opcionesMesa() {
      return [
        { mesa_id: 0, label: 'DIRECTO' },
        ...this.mesas
          .filter(m => m.estado === 'DISPONIBLE')
          .map(m => ({
            mesa_id: m.mesa_id,
            label: m.nombre
          }))
      ];
    }
  },

  watch: {
    'itemForm.producto'(p) {
      if (p) {
        this.itemForm.product_id = p.product_id;
        this.itemForm.price_item = p.price;
      }
    },
    'detailForm.producto'(p) {
      if (p) {
        this.detailForm.product_id = p.product_id;
        this.detailForm.price_item = p.price;
      }
    },
    'nueva.cliente'(c) {
      if (c) {
        this.nueva.cliente_id = c.cliente_id;
        this.nueva.telefono = c.telefono || '';
      }
    },
    totalOrden(v) {
      this.nueva.total_fees = v;
    }
  },

  methods: {
    ...orderUiHelpers,
    ...orderMethods,
    ...detailMethods,
    ...clientesMethods,
    ...mesasMethods,
    ...reportesMethods
  },

  mounted() {
    this.configurarHeaderAuth();

    const fechas = this.fechaHoyFormato();
    this.reporte.fecha_inicio = fechas.ini;
    this.reporte.fecha_fin    = fechas.fin;

    this.cargarProductos();
    this.listar();
    this.cargarMesas();

    axios.get(`${this.apphost}/cliente/listar`).then(r => {
      this.clientes = r.data.map(c => ({
        ...c,
        label: `${c.dni} - ${c.nombre}`
      }));
    });

    axios.get(`${this.apphost}/tipo_pago/listar`).then(r => {
      this.tiposPago = r.data;
    });

    axios.get(`${this.apphost}/administrador/listar`).then(r => {
      this.administradores = r.data;
    });

    const self = this;
    $('#modalDetalleOrder').on('hidden', function () {
      self.listar();
    });
  }
});