<?php
/**
 * Cria a conexão PDO na variável $pdo.
 * Se a conexão falhar, responde 503 sem mostrar detalhes.
 */
// Conexão com o MySQL usando PDO. As páginas fazem: include("php/conexao_pdo.php");
// Depois disso, a variável $pdo já está pronta para usar.

// 1) Valores padrão do XAMPP
$host = "localhost";
$banco = "loja_enxoval";
$usuario = "root";
$senha = "";

// 2) Se existir o arquivo php/config.local.php, ele troca os valores acima
if (file_exists(__DIR__ . "/config.local.php")) {
    include __DIR__ . "/config.local.php";
}

// 3) Usado só pelos testes automáticos (banco de teste separado)
if (getenv("MABELLE_DB_NAME")) {
    $banco = getenv("MABELLE_DB_NAME");
}

// 4) Tenta conectar. Se falhar, mostra uma mensagem simples (sem segredos)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);          // erro vira exceção
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);     // linhas viram ['nome' => 'Ana']
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);                  // consulta preparada de verdade
} catch (PDOException $erro) {
    error_log("Erro de conexao: " . $erro->getMessage());                   // detalhe só no log do servidor
    http_response_code(503);
    die("Serviço indisponível. Tente novamente em instantes.");
}
