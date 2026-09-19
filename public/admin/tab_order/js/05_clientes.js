const clientesMethods = {
  abrirModalClientes() {
    $('#modalClientes').modal('show');
    this.listarClientes();
  },

  listarClientes() {
    axios.get(`${this.apphost}/cliente/listar`).then(r => {
      this.clientes = r.data.map(c => ({
        ...c,
        label: `${c.dni} - ${c.nombre}`
      }));

      this.$nextTick(() => {
        if (!this.dtClientes) {
          this.dtClientes = $('#tablaClientes').DataTable({
            language: typeof dt_language !== 'undefined' ? dt_language : {},
            scrollX: true,
            dom: 'frtip',
            order: [[0, 'desc']]
          });
        }

        this.dtClientes.clear();
        this.clientes.forEach(c => {
          this.dtClientes.row.add([
            c.dni,
            c.nombre,
            `<button class="btn btn-mini btn-primary editar" data-id="${c.cliente_id}">Editar</button>`
          ]);
        });
        this.dtClientes.draw(false);

        const self = this;
        $('#tablaClientes').off().on('click', '.editar', function () {
          const id = $(this).data('id');
          self.abrirEditarCliente(self.clientes.find(x => x.cliente_id == id));
        });
      });
    });
  },

  abrirModalNuevoCliente() {
    this.clienteForm = { dni: '', nombre: '' };
    $('#modalNuevoCliente').modal('show');
  },

  generarDniFake() {
    const letras = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    let dni = '';
    for (let i = 0; i < 8; i++) {
      dni += Math.random() < 0.7
        ? Math.floor(Math.random() * 10)
        : letras[Math.floor(Math.random() * letras.length)];
    }
    this.clienteForm.dni = dni;
  },

  guardarCliente() {
    if (!this.clienteForm.dni || !this.clienteForm.nombre) {
      alert('DNI y Nombre son obligatorios');
      return;
    }

    axios.post(`${this.apphost}/cliente/crear`, this.clienteForm)
      .then(() => {
        $('#modalNuevoCliente').modal('hide');
        this.listarClientes();
      });
  },

  abrirEditarCliente(c) {
    this.clienteEdit = JSON.parse(JSON.stringify(c));
    $('#modalEditarCliente').modal('show');
  },

  actualizarCliente() {
    axios.post(`${this.apphost}/cliente/editar`, this.clienteEdit)
      .then(() => {
        $('#modalEditarCliente').modal('hide');
        this.listarClientes();
      });
  }
};