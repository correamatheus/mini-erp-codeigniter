<?php
$servername = "db"; // Nome do serviço do Docker Compose
$username = "root";
$password = "root";
$database = "mini_erp";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Checar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão bem-sucedida ao banco de dados!";

$conn->close();
?>