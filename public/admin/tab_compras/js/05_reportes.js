const reportesMethods = {
  abrirReporteFechas() {
    this.filtro = { fecha_ini: '', fecha_fin: '' };
    $('#modalReporteFechas').modal('show');
  },

  imprimirReporte() {
    const { fecha_ini, fecha_fin } = this.filtro;
    window.open(
      `${this.apphost}/imp_compras_fecha?ini=${fecha_ini}&fin=${fecha_fin}`,
      '_blank'
    );
  },

  descargarExcel() {
    const { fecha_ini, fecha_fin } = this.filtro;
    window.location =
      `${this.apphost}/imp_compras_fecha_excel?ini=${fecha_ini}&fin=${fecha_fin}`;
  }
};