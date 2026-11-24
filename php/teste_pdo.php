<?php
// Demonstração do Ponto 03.
// Abra: php/teste_pdo.php               -> lê um dado real do banco
//       php/teste_pdo.php?simular_falha=1 -> mostra uma falha tratada
header("Content-Type: text/plain; charset=utf-8");

if (isset($_GET["simular_falha"])) {
    try {
        // porta 1 não tem MySQL: vai dar erro de propósito
        $pdo = new PDO("mysql:host=127.0.0.1;port=1;dbname=x", "x", "senha_secreta");
    } catch (PDOException $erro) {
        echo "Falha simulada tratada: Serviço indisponível.\n";   // o usuário não vê detalhes
    }
    exit;
}

include("conexao_pdo.php");
$consulta = $pdo->query("SELECT COUNT(*) AS total FROM setores");
$linha = $consulta->fetch();
echo "Conexão PDO OK. Setores cadastrados: " . $linha["total"] . "\n";
