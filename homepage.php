<?php
include_once("./db/conexao.php");

// Processamento do formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    
    // Processamento da imagem (opcional)
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . '.' . $extensao;
        $caminhoImagem = $uploadDir . $nomeArquivo;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
            $imagem = $caminhoImagem;
        }
    }
    
    // Inserir no banco de dados
    $sql = "INSERT INTO produtos (nome, preco, descricao, imagem) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdss", $nome, $preco, $descricao, $imagem);
    
    if ($stmt->execute()) {
        $mensagem = "Produto cadastrado com sucesso!";
        // Redireciona para evitar reenvio do formulário
        header("Location: homepage.php?sucesso=1");
        exit();
    } else {
        $erro = "Erro ao cadastrar produto: " . $conn->error;
    }
}

// Consulta para listar produtos
$sql = "SELECT id, nome, descricao, preco, imagem FROM produtos ORDER BY id";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>MyOrders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <style>
    .produto-img {
      max-width: 50px;
      max-height: 50px;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <header>
    <nav class="navbar fixed-top bg-light">
      <div class="container-fluid">
        <h2 onclick="mostrarPagina('home')">MyOrders</h2>
        <div>
          <a class="navbar-brand fs-5 mx-5" href="#" onclick="mostrarPagina('produtos')">Produtos</a>
          <a class="navbar-brand fs-5 mx-5" href="#" onclick="mostrarPagina('cadastro')">Cadastro</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="container" style="margin-top: 80px;">
    <div id="home">
      <h3>Página inicial</h3>
      <p>Bem-vindo ao MyOrders!</p>
    </div>
    
    <div id="produtos" style="display: none;">
      <h3>Lista de Produtos</h3>
      <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">Produto cadastrado com sucesso!</div>
      <?php endif; ?>
      
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($linha = $resultado->fetch_object()): ?>
            <tr>
              <td><?= htmlspecialchars($linha->id) ?></td>
              <td>
                <?php if ($linha->imagem): ?>
                  <img src="<?= htmlspecialchars($linha->imagem) ?>" class="produto-img" alt="Imagem do produto">
                <?php else: ?>
                  Sem imagem
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($linha->nome) ?></td>
              <td><?= htmlspecialchars($linha->descricao) ?></td>
              <td>R$ <?= number_format($linha->preco, 2, ',', '.') ?></td>
              <td>
                <a href="editar.php?id=<?= $linha->id ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="excluir.php?id=<?= $linha->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    
    <div id="cadastro" style="display: none;">
      <h3>Cadastro de Produto</h3>
      <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
      <?php endif; ?>
      
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="nome" class="form-label">Nome do Produto</label>
          <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome" required>
        </div>
        <div class="mb-3">
          <label for="preco" class="form-label">Preço</label>
          <input type="number" step="0.01" class="form-control" id="preco" name="preco" placeholder="Digite o preço" required>
        </div>
        <div class="mb-3">
          <label for="descricao" class="form-label">Descrição</label>
          <textarea class="form-control" id="descricao" name="descricao" placeholder="Digite a descrição"></textarea>
        </div>
        <div class="mb-3">
          <label for="imagem" class="form-label">Imagem do Produto</label>
          <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
        </div>
        <button type="submit" name="cadastrar" class="btn btn-primary">Salvar</button>
      </form>
    </div>
  </main>

  <script>
    function mostrarPagina(pagina) {
      document.getElementById('home').style.display = 'none';
      document.getElementById('cadastro').style.display = 'none';
      document.getElementById('produtos').style.display = 'none';
      document.getElementById(pagina).style.display = 'block';
      
      // Se for a página de produtos, recarrega para atualizar a lista
      if (pagina === 'produtos') {
        window.location.href = 'homepage.php#produtos';
      }
    }
    
    // Verifica se há hash na URL para mostrar a página correta
    window.onload = function() {
      const hash = window.location.hash.substring(1);
      if (hash === 'produtos' || hash === 'cadastro') {
        mostrarPagina(hash);
      }
    };
  </script>
</body>
</html>