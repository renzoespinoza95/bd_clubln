<!DOCTYPE html>
<!-- este es mi pagina login  -->
<html lang="es" ng-app="loginApp">
<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Login</title>      
      <base href="<?php echo $mBase ?>">
      <link rel="stylesheet" type="text/css" href="css/login.css?version=<?php echo $version ?>">
      <?php
      boot::jquery2();
      boot::vuejs2();
      boot::apprise();
      boot::favicon();
      boot::font_awesome();
      h2::todohost($apphost, $varhost);
      ?>
</head>
<body>

  <form class="login" @submit.prevent="submit" id="appLogin">
    <fieldset>
      <legend class="legend">
        <img src="images/logo_login.png" alt="Logo" class="login-logo">
      </legend>


      <div class="input" :class="{'focused': usuarioFocused}">
        <input type="text" placeholder="Usuario" required
               v-model="usuario"
               @focus="onFocus('usuario')"
               @blur="onBlur('usuario')">
        <span><i class="fa fa-envelope-o"></i></span>
      </div>

      <div class="input" :class="{'focused': clavelFocused}">
        <input type="password" placeholder="Password" required
               v-model="clavel"
               @focus="onFocus('clavel')"
               @blur="onBlur('clavel')">
        <span><i class="fa fa-lock"></i></span>
      </div>

      <button type="submit" class="submit">
        <i class="fa" :class="{'fa-long-arrow-right': !success, 'fa-check': success}"></i>
      </button>
    </fieldset>
  </form>


<script>
  new Vue({
    el: '#appLogin',
    data: {
      usuario: '',
      clavel: '',
      usuarioFocused: false,
      clavelFocused: false,
      success: false,
      apphost: apphost
    },
    methods: {
      onFocus(field) {
        if (field === 'usuario') this.usuarioFocused = true;
        if (field === 'clavel') this.clavelFocused = true;
      },
      onBlur(field) {
        if (field === 'usuario') this.usuarioFocused = false;
        if (field === 'clavel') this.clavelFocused = false;
      },
      submit() {
        const postData = {
          usuario: this.usuario,
          clavel: this.clavel
        };

        axios.post(`${this.apphost}/loginVault`, postData)
        .then(response => {
          const data = response.data;
          if (data.status && data.status === 'ok') {
            // 1) Guarda el JWT en localStorage
            localStorage.setItem('jwt', data.token);
            // 2) Configura el header Authorization por defecto en Axios
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            // 3) Logueo en consola (opcional)
            console.log('Token recibido:', data.token);
            // 4) Redirige al dashboard
            window.location.href = `${this.apphost}/admin/dash`;
          } else {
            // Si status !== 'ok', muestra alerta
            apprise("Por favor, verifica tus credenciales");
          }
        })
        .catch(error => {
          // Manejo de errores en la petición
          console.error('Error al enviar los datos:', error);
          apprise("Error al comunicar con el servidor. Por favor, intenta más tarde");
        });

      }
    }
  });
</script>
</body>
</html>
