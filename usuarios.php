<?php
session_start();
include("php/conexao_pdo.php");

// Avisos guardados por outras páginas (ex.: "Usuário cadastrado com sucesso")
$aviso_ok = "";
if (isset($_SESSION["flash"])) {
    $aviso_ok = $_SESSION["flash"];
    unset($_SESSION["flash"]);          // mostra só uma vez
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
$comando = $pdo->prepare(
    "SELECT id, nome, email, setor_id, perfil_id
     FROM usuarios
     WHERE status = 'Ativo'
       AND (nome LIKE ? ESCAPE '!' OR email LIKE ? ESCAPE '!')
     ORDER BY nome"
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
					<th>Setor (id)</th>
					<th>Perfil (id)</th>
				</tr>
				<?php foreach ($usuarios as $usuario) { ?>
				<tr>
					<td><?php echo htmlspecialchars($usuario["nome"]); ?></td>
					<td><?php echo htmlspecialchars($usuario["email"]); ?></td>
					<td><?php echo $usuario["setor_id"]; ?></td>
					<td><?php echo $usuario["perfil_id"]; ?></td>
				</tr>
				<?php } ?>
			</table>
		<?php } ?>
	</main>

	<footer>
		<p>Mabelle Enxovais - Todos os direitos reservados © <span id="ano"></span></p>
	</footer>

	<script src="js/script.js"></script>
</body>
</html>
