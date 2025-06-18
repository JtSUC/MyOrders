<?php
include 'conexao.php';

$sql = "SELECT id, nome, preco FROM cad_produto";
$resultado = $con->query($sql);

$produtos = [];

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $produtos[] = $row;
    }
}

echo json_encode($produtos); // Envia os dados em JSON
?>
