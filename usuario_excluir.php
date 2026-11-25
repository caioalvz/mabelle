<?php
/**
 * Recebe csrf e id por POST, muda o status do usuário para 'Baixa'
 * dentro de uma transação e redireciona para usuarios.php.
 */
// "Excluir" um usuário = marcar status 'Baixa' (exclusão lógica). A ficha continua no banco.
session_start();

// 1) Só aceita POST (abrir o endereço pelo navegador, que é GET, é recusado)
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    die("Método não permitido.");
}

// 2) Confere o token secreto (CSRF)
if (!isset($_POST["csrf"], $_SESSION["csrf"]) || !is_string($_POST["csrf"]) || !hash_equals($_SESSION["csrf"], $_POST["csrf"])) {
    http_response_code(403);
    die("Requisição inválida.");
}

// 3) Confere o id: precisa ser um número maior que zero
if (!isset($_POST["id"]) || !is_string($_POST["id"]) || !ctype_digit($_POST["id"]) || (int) $_POST["id"] < 1) {
    http_response_code(400);
    die("Requisição inválida.");
}
$id = (int) $_POST["id"];

include("php/conexao_pdo.php");

try {
    // A transação junta os passos: ou acontecem todos, ou nenhum
    $pdo->beginTransaction();

    // 4) O usuário existe?
    $comando = $pdo->prepare("SELECT id FROM usuarios WHERE id = ?");
    $comando->execute([$id]);
    $existe = $comando->fetch();

    if (!$existe) {
        $diario = $pdo->prepare("INSERT INTO auditoria_usuarios (usuario_alvo_id, acao, autor, resultado) VALUES (NULL, 'baixa', 'sistema', 'usuario inexistente')");
        $diario->execute();
        $_SESSION["flash_erro"] = "Usuário não encontrado.";
    } else {
        // 5) Dá baixa SOMENTE se ainda estiver Ativo (assim repetir não baixa duas vezes)
        $comando = $pdo->prepare("UPDATE usuarios SET status = 'Baixa', baixado_em = CURRENT_TIMESTAMP WHERE id = ? AND status = 'Ativo'");
        $comando->execute([$id]);

        if ($comando->rowCount() == 1) {
            $resultado = "sucesso";
            $_SESSION["flash"] = "Usuário excluído com sucesso.";
        } else {
            $resultado = "ja estava em baixa";
            $_SESSION["flash_erro"] = "Este usuário já havia sido excluído.";
        }

        // 6) Anota no diário (auditoria)
        $diario = $pdo->prepare("INSERT INTO auditoria_usuarios (usuario_alvo_id, acao, autor, resultado) VALUES (?, 'baixa', 'sistema', ?)");
        $diario->execute([$id, $resultado]);
    }

    $pdo->commit();
} catch (PDOException $erro) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();       // desfaz tudo se algo deu errado
    }
    error_log("Erro ao dar baixa: " . $erro->getMessage());
    $_SESSION["flash_erro"] = "Não foi possível excluir agora. Tente novamente.";
}

// 7) Volta para a lista, que mostra o aviso
header("Location: usuarios.php");
exit;
