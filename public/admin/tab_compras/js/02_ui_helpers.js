const comprasUiHelpers = {
  fechaHoy() {
    const d = new Date();
    const year  = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day   = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  },

  horaActual() {
    const d = new Date();
    const h = String(d.getHours()).padStart(2, '0');
    const m = String(d.getMinutes()).padStart(2, '0');
    const s = String(d.getSeconds()).padStart(2, '0');
    return `${h}:${m}:${s}`;
  },

  formatearFechaHora(fechaStr) {
    if (!fechaStr) return '';

    // Maneja strings en formato 'YYYY-MM-DD HH:mm:ss' o ISO
    const partes = fechaStr.split(' ');
    if (partes.length < 2) {
      return fechaStr;
    }

    const [anio, mes, dia] = partes[0].split('-');
    const [hh, mm] = partes[1].split(':');

    let horas = parseInt(hh, 10);
    const minutos = mm;
    const ampm = horas >= 12 ? 'PM' : 'AM';

    horas = horas % 12;
    horas = horas ? horas : 12; // Las 00:00 pasa a ser 12

    return `${dia}/${mes}/${anio} ${horas}:${minutos} ${ampm}`;
  },

  liberarFocusVueSelect() {
    $(document).off('focusin.modal');
  }
};