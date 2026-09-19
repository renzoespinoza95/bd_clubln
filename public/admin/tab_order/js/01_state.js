const orderState = {
  apphost: (typeof apphost !== 'undefined' ? apphost : ''),
  ordenes: [],
  productos: [],
  mesas: [],
  form: {},
  detalle: {},
  resumen: {
    fecha_inicio: '',
    fecha_fin: ''
  },
  detallesOrder: [],
  administradores: [],
  reporte: {
    fecha_inicio: '',
    fecha_fin: '',
    admin_id: ''
  },
  reporteResultados: [],
  clientes: [],
  clienteForm: { dni: '', nombre: '' },
  clienteEdit: {},
  dtClientes: null,

  detailForm: {
    order_id: null,
    producto: null,
    product_id: null,
    amount: 1,
    price_item: 0
  },
  itemForm: {
    producto: null,
    product_id: null,
    amount: 1,
    price_item: 0
  },
  dt: null,
  cajaActual: null,
  caja_id: null,
  tiposPago: [],
  nueva: {
    cliente_id: null,
    cliente: null,
    telefono: '',
    total_fees: 0,
    items: [],
    tipo_pago_id: null,
    mesa: null
  }
};