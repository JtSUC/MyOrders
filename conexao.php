<?php
$host = "localhost";         // ou 127.0.0.1
$usuario = "root";           // usuário padrão do XAMPP/WAMP
$senha = "";                 // geralmente vazio no local
$banco = "myorders";         // nome do banco que aparece no phpMyAdmin

$con = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($con->connect_error) {
    die("Erro na conexão: " . $con->connect_error);
}
?>
