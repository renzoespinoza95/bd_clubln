const reportesMethods = {
  abrirReporteVentas() {
    this.reporteResultados = [];
    $('#modalReporteVentas').modal('show');
  },

  abrirReporteVentasAdmin() {
    this.reporteResultados = [];
    $('#modalReporteVentasAdmin').modal('show');
  },

  abrirReporteResumenVentas() {
    this.resumen = { fecha_inicio: '', fecha_fin: '' };
    $('#modalResumenVentas').modal('show');
  },

  reporteVentasPDF() {
    let ini = this.reporte.fecha_inicio.replace('T', ' ');
    let fin = this.reporte.fecha_fin.replace('T', ' ');

    window.open(
      `${this.apphost}/imp_ventas_fecha?ini=${encodeURIComponent(ini)}&fin=${encodeURIComponent(fin)}`,
      '_blank'
    );
  },

  reporteVentasExcel() {
    const { fecha_inicio, fecha_fin } = this.reporte;
    window.open(
      `${this.apphost}/imp_ventas_fecha_excel?ini=${fecha_inicio}&fin=${fecha_fin}`,
      '_blank'
    );
  },

  reporteVentasAdminPDF() {
    const { fecha_inicio, fecha_fin, admin_id } = this.reporte;
    window.open(
      `${this.apphost}/imp_ventas_fecha_admin?ini=${fecha_inicio}&fin=${fecha_fin}&admin_id=${admin_id}`,
      '_blank'
    );
  },

  reporteVentasAdminExcel() {
    let { fecha_inicio, fecha_fin, admin_id } = this.reporte;
    if (!fecha_inicio || !fecha_fin) {
      alert('Debe seleccionar fecha inicio y fin');
      return;
    }
    if (!admin_id) admin_id = 0;

    window.open(
      `${this.apphost}/imp_ventas_fecha_admin_excel?ini=${fecha_inicio}&fin=${fecha_fin}&admin_id=${admin_id}`,
      '_blank'
    );
  },

  resumenVentasPDF() {
    const { fecha_inicio, fecha_fin } = this.resumen;
    window.open(
      `${this.apphost}/imp_resumen_categoria?ini=${fecha_inicio}&fin=${fecha_fin}`,
      '_blank'
    );
  }
};