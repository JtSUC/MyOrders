    function mostrarPagina(pagina) {
      document.getElementById('home').style.display = 'none';
      document.getElementById('cadastro').style.display = 'none';
      document.getElementById('produtos').style.display = 'none';
      document.getElementById(pagina).style.display = 'block';
    }