<?php
session_start();
include("php/conexao_pdo.php");

// Token secreto da sessão (vai escondido no formulário de exclusão)
if (!isset($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

// Avisos guardados por outras páginas (ex.: "Usuário cadastrado com sucesso")
$aviso_ok = "";
$aviso_erro = "";
if (isset($_SESSION["flash"])) {
    $aviso_ok = $_SESSION["flash"];
    unset($_SESSION["flash"]);          // mostra só uma vez
}
if (isset($_SESSION["flash_erro"])) {
    $aviso_erro = $_SESSION["flash_erro"];
    unset($_SESSION["flash_erro"]);
}

// Texto digitado na busca (se não digitou nada, fica vazio)
$busca = "";
if (isset($_GET["q"]) && is_string($_GET["q"])) {
    $busca = mb_substr(trim($_GET["q"]), 0, 100);
}

// No LIKE, % e _ são curingas. Colocamos ! na frente para valerem como letras normais.
$texto = str_replace(["!", "%", "_"], ["!!", "!%", "!_"], $busca);
$parametro = "%" . $texto . "%";

// Busca os usuários ativos. Usuários com status 'Baixa' não aparecem.
// JOIN = juntar tabelas. Cada ON diz como ligar:
//   usuarios.setor_id  ->  setores.id   (traz o NOME do setor)
//   usuarios.perfil_id ->  perfis.id    (traz o NOME do perfil)
// u, s e p são apelidos para escrever menos.
$comando = $pdo->prepare(
    "SELECT u.id, u.nome, u.email, s.nome AS setor, p.nome AS perfil
     FROM usuarios u
     INNER JOIN setores s ON s.id = u.setor_id
     INNER JOIN perfis p ON p.id = u.perfil_id
     WHERE u.status = 'Ativo'
       AND (u.nome LIKE ? ESCAPE '!' OR u.email LIKE ? ESCAPE '!')
     ORDER BY u.nome"
);
$comando->execute([$parametro, $parametro]);
$usuarios = $comando->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title>Mabelle - Usuários</title>
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
		<h1>Usuários</h1>

		<?php if ($aviso_ok != "") { ?>
			<div class="aviso sucesso"><?php echo htmlspecialchars($aviso_ok); ?></div>
		<?php } ?>
		<?php if ($aviso_erro != "") { ?>
			<div class="aviso erro"><?php echo htmlspecialchars($aviso_erro); ?></div>
		<?php } ?>

		<form method="get" action="usuarios.php">
			<label for="q">Buscar por nome ou e-mail</label>
			<input type="text" id="q" name="q" maxlength="100" value="<?php echo htmlspecialchars($busca); ?>">
			<input type="submit" value="Buscar">
		</form>

		<p><a class="botao" href="usuario_novo.php">Novo usuário</a></p>

		<?php if (count($usuarios) == 0) { ?>
			<p>Nenhum usuário encontrado.</p>
		<?php } else { ?>
			<table>
				<tr>
					<th>Nome</th>
					<th>E-mail</th>
					<th>Setor</th>
					<th>Perfil</th>
					<th>Ações</th>
				</tr>
				<?php foreach ($usuarios as $usuario) { ?>
				<tr>
					<td><?php echo htmlspecialchars($usuario["nome"]); ?></td>
					<td><?php echo htmlspecialchars($usuario["email"]); ?></td>
					<td><?php echo htmlspecialchars($usuario["setor"]); ?></td>
					<td><?php echo htmlspecialchars($usuario["perfil"]); ?></td>
					<td>
						<!-- type="button": só abre a pergunta, não envia nada -->
						<button type="button" class="botao btn-excluir"
							data-id="<?php echo (int) $usuario["id"]; ?>"
							data-nome="<?php echo htmlspecialchars($usuario["nome"]); ?>"
							data-email="<?php echo htmlspecialchars($usuario["email"]); ?>">Excluir</button>
					</td>
				</tr>
				<?php } ?>
			</table>
		<?php } ?>

		<!-- Pergunta de confirmação (começa escondida) -->
		<div id="modal-excluir" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-titulo" hidden>
			<form method="post" action="usuario_excluir.php" class="modal-caixa">
				<input type="hidden" name="csrf" value="<?php echo $_SESSION["csrf"]; ?>">
				<input type="hidden" name="id" id="modal-id" value="">
				<h2 id="modal-titulo">Deseja realmente excluir este usuário?</h2>
				<p>Usuário: <strong id="modal-nome"></strong> (<span id="modal-email"></span>) - ID <span id="modal-id-texto"></span></p>
				<button type="button" class="botao" id="modal-cancelar">Cancelar</button>
				<input type="submit" value="Confirmar">
			</form>
		</div>
	</main>

	<footer>
		<p>Mabelle Enxovais - Todos os direitos reservados © <span id="ano"></span></p>
	</footer>

	<script src="js/script.js"></script>
	<script src="js/usuarios.js"></script>
</body>
</html>
