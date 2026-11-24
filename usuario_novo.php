<?php
session_start();
include("php/conexao_pdo.php");

// Token secreto da sessão (protege o formulário contra envios de outros sites)
if (!isset($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

// Busca os setores e perfis ativos no banco (para os selects)
$consulta = $pdo->query("SELECT id, nome FROM setores WHERE ativo = 1 ORDER BY nome");
$setores = $consulta->fetchAll();
$consulta = $pdo->query("SELECT id, nome FROM perfis WHERE ativo = 1 ORDER BY nome");
$perfis = $consulta->fetchAll();

// Variáveis que o formulário usa (começam vazias)
$nome = "";
$email = "";
$setor_id = "";
$perfil_id = "";
$erros = [];          // guarda uma mensagem de erro para cada campo com problema
$mensagem_erro = "";  // erro geral (ex.: sessão expirada)

// Só valida quando o formulário foi enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Confere o token secreto
    if (!isset($_POST["csrf"]) || !is_string($_POST["csrf"]) || !hash_equals($_SESSION["csrf"], $_POST["csrf"])) {
        http_response_code(403);
        $mensagem_erro = "Sessão expirada. Recarregue a página e tente novamente.";
    } else {

        // Pega cada campo (se não veio ou não é texto, fica vazio)
        if (isset($_POST["nome"]) && is_string($_POST["nome"])) {
            $nome = trim($_POST["nome"]);
        }
        if (isset($_POST["email"]) && is_string($_POST["email"])) {
            $email = mb_strtolower(trim($_POST["email"]));
        }
        if (isset($_POST["setor_id"]) && is_string($_POST["setor_id"])) {
            $setor_id = trim($_POST["setor_id"]);
        }
        if (isset($_POST["perfil_id"]) && is_string($_POST["perfil_id"])) {
            $perfil_id = trim($_POST["perfil_id"]);
        }

        // Valida o nome
        if ($nome == "") {
            $erros["nome"] = "Informe o nome.";
        } elseif (mb_strlen($nome) < 3 || mb_strlen($nome) > 150) {
            $erros["nome"] = "O nome deve ter entre 3 e 150 caracteres.";
        }

        // Valida o e-mail
        if ($email == "") {
            $erros["email"] = "Informe o e-mail.";
        } elseif (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros["email"] = "Informe um e-mail válido.";
        }

        // Valida o setor: precisa ser número E existir (ativo) no banco
        if ($setor_id == "") {
            $erros["setor_id"] = "Selecione um setor.";
        } elseif (!ctype_digit($setor_id)) {
            $erros["setor_id"] = "Setor inválido.";
        } else {
            $comando = $pdo->prepare("SELECT id FROM setores WHERE id = ? AND ativo = 1");
            $comando->execute([$setor_id]);
            if (!$comando->fetch()) {
                $erros["setor_id"] = "Setor inválido.";
            }
        }

        // Valida o perfil do mesmo jeito
        if ($perfil_id == "") {
            $erros["perfil_id"] = "Selecione um perfil.";
        } elseif (!ctype_digit($perfil_id)) {
            $erros["perfil_id"] = "Perfil inválido.";
        } else {
            $comando = $pdo->prepare("SELECT id FROM perfis WHERE id = ? AND ativo = 1");
            $comando->execute([$perfil_id]);
            if (!$comando->fetch()) {
                $erros["perfil_id"] = "Perfil inválido.";
            }
        }

        // Se não tem nenhum erro, grava no banco
        if (count($erros) == 0) {
            try {
                // Os ? são "lugares vazios": os valores vão separados, então nunca viram comando SQL
                $comando = $pdo->prepare("INSERT INTO usuarios (nome, email, setor_id, perfil_id) VALUES (?, ?, ?, ?)");
                $comando->execute([$nome, $email, $setor_id, $perfil_id]);
                $novo_id = $pdo->lastInsertId();

                // Anota no diário (auditoria): sem senha e sem dados pessoais, só o id
                $diario = $pdo->prepare("INSERT INTO auditoria_usuarios (usuario_alvo_id, acao, autor, resultado) VALUES (?, 'cadastro', 'sistema', 'sucesso')");
                $diario->execute([$novo_id]);

                // Guarda um aviso e volta para a lista (assim F5 não cadastra de novo)
                $_SESSION["flash"] = "Usuário cadastrado com sucesso.";
                header("Location: usuarios.php");
                exit;
            } catch (PDOException $erro) {
                error_log("Erro ao cadastrar usuario: " . $erro->getMessage());   // detalhe só no log
                $codigo = 0;
                if (isset($erro->errorInfo[1])) {
                    $codigo = $erro->errorInfo[1];
                }
                $resultado = "falha";
                if ($codigo == 1062) {                       // 1062 = valor repetido (e-mail UNIQUE)
                    $erros["email"] = "Este e-mail já está cadastrado.";
                    $resultado = "email duplicado";
                } elseif ($codigo == 1452) {                 // 1452 = chave estrangeira inválida
                    $erros["setor_id"] = "Setor ou perfil inválido.";
                    $resultado = "vinculo invalido";
                } else {
                    $mensagem_erro = "Não foi possível salvar agora. Tente novamente.";
                }
                // Anota a falha no diário (se o próprio diário falhar, segue sem avisar)
                try {
                    $diario = $pdo->prepare("INSERT INTO auditoria_usuarios (usuario_alvo_id, acao, autor, resultado) VALUES (NULL, 'cadastro', 'sistema', ?)");
                    $diario->execute([$resultado]);
                } catch (PDOException $erro2) {
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title>Mabelle - Novo usuário</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>

	<header>
		<div class="topo">
			<div class="logo"><a class="logo-link" href="index.php">Ma<span>belle</span></a></div>
			<nav>
				<ul>
					<li><a href="index.php">Início</a></li>
					<li><a href="produto.php">Produtos</a></li>
					<li><a href="usuarios.php">Usuários</a></li>
					<li><a href="dashboard.php">Dashboard</a></li>
				</ul>
			</nav>
		</div>
	</header>

	<main>
		<h1>Novo usuário</h1>

		<?php if ($mensagem_erro != "") { ?>
			<div class="aviso erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
		<?php } ?>

		<?php if (count($setores) == 0 || count($perfis) == 0) { ?>
			<p>Cadastro indisponível: não há setores ou perfis ativos cadastrados.</p>
		<?php } else { ?>

		<form id="form-usuario" method="post" action="usuario_novo.php" novalidate>
			<input type="hidden" name="csrf" value="<?php echo $_SESSION["csrf"]; ?>">

			<label for="nome">Nome</label>
			<input type="text" id="nome" name="nome" maxlength="150" value="<?php echo htmlspecialchars($nome); ?>">
			<?php if (isset($erros["nome"])) { ?><span class="erro-campo"><?php echo $erros["nome"]; ?></span><?php } ?>

			<label for="email">E-mail</label>
			<input type="text" id="email" name="email" maxlength="150" value="<?php echo htmlspecialchars($email); ?>">
			<?php if (isset($erros["email"])) { ?><span class="erro-campo"><?php echo $erros["email"]; ?></span><?php } ?>

			<label for="setor_id">Setor</label>
			<select id="setor_id" name="setor_id">
				<option value="">Selecione...</option>
				<?php foreach ($setores as $setor) { ?>
					<option value="<?php echo $setor["id"]; ?>" <?php if ($setor["id"] == $setor_id) { echo "selected"; } ?>><?php echo htmlspecialchars($setor["nome"]); ?></option>
				<?php } ?>
			</select>
			<?php if (isset($erros["setor_id"])) { ?><span class="erro-campo"><?php echo $erros["setor_id"]; ?></span><?php } ?>

			<label for="perfil_id">Perfil</label>
			<select id="perfil_id" name="perfil_id">
				<option value="">Selecione...</option>
				<?php foreach ($perfis as $perfil) { ?>
					<option value="<?php echo $perfil["id"]; ?>" <?php if ($perfil["id"] == $perfil_id) { echo "selected"; } ?>><?php echo htmlspecialchars($perfil["nome"]); ?></option>
				<?php } ?>
			</select>
			<?php if (isset($erros["perfil_id"])) { ?><span class="erro-campo"><?php echo $erros["perfil_id"]; ?></span><?php } ?>

			<input type="submit" value="Cadastrar">
		</form>

		<?php } ?>
	</main>

	<footer>
		<p>Mabelle Enxovais - Todos os direitos reservados © <span id="ano"></span></p>
	</footer>

	<script src="js/script.js"></script>
	<script src="js/usuarios.js"></script>
</body>
</html>
