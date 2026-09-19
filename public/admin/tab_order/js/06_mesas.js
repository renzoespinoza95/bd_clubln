const mesasMethods = {
  abrirModalMesas() {
    axios.get(`${this.apphost}/mesa/listar`).then(r => {
      this.mesas = r.data;
      $('#modalMesas').modal('show');
    });
  },

  cargarMesas() {
    axios.get(`${this.apphost}/mesa/listar`).then(r => {
      this.mesas = r.data;
    });
  },

  confirmarLiberarMesa(mesa) {
    apprise(
      `¿Deseas liberar la mesa <b>${mesa.nombre}</b>?<br>Se verificará si tiene pedidos pendientes.`,
      { confirm: true },
      ok => {
        if (!ok) return;
        this.liberarMesa(mesa);
      }
    );
  },

  liberarMesa(mesa) {
    axios.post(`${this.apphost}/ventas/liberarMesaOcupada`, {
      mesa_id: mesa.mesa_id
    })
    .then(r => {
      if (r.data.status !== 'ok') {
        apprise(r.data.msg || 'No se pudo liberar la mesa');
        return;
      }
      apprise('Mesa liberada correctamente');
      this.cargarMesas();
    })
    .catch(e => {
      apprise(e.response?.data?.msg || 'Error al liberar mesa');
    });
  }
};