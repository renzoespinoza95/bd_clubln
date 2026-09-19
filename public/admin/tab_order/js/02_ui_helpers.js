const orderUiHelpers = {
  fechaHoyFormato() {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    return {
      ini: `${yyyy}-${mm}-${dd}T00:00`,
      fin: `${yyyy}-${mm}-${dd}T23:59`,
      dia: `${yyyy}-${mm}-${dd}`
    };
  },

  configurarHeaderAuth() {
    const jwt = localStorage.getItem('jwt');
    if (jwt && window.axios) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${jwt}`;
    }
  }
};