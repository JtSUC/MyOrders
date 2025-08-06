<?php
// Configurações do banco de dados
$host = 'localhost'; //localhost
$usuario = 'root'; //root
$senha = ''; // sem senha, não coloque a senha
$banco = 'myorders'; //nome do banco
// Criando a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);
// Verificando se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: $conn->connect_error");
}
