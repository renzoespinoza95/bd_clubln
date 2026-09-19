const comprasState = {
  apphost: typeof apphost !== 'undefined' ? apphost : '',
  compras: [],
  compraAdd: { compra_id: null, items_nuevos: [] },
  proveedores: [],
  itemsExistentes: [],
  productos: [],
  nuevo: {
    proveedor: null,
    fecha_compra: '',
    items: []
  },
  form: {
    compra_id: null,
    proveedor: null,
    fecha_compra: '',
    observaciones: ''
  },
  filtro: { fecha_ini: '', fecha_fin: '' },
  detalle: { cabecera: {}, detalle: [] },
  itemTemp: {
    producto: null,
    product_id: null,
    costo_unitario: 0,
    cantidad: 1
  },
  dt: null
};