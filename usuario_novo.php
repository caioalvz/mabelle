<?php
include("php/conexao_pdo.php");

// Busca os setores ativos no banco (para preencher o select de setor)
$consulta = $pdo->query("SELECT id, nome FROM setores WHERE ativo = 1 ORDER BY nome");
$setores = $consulta->fetchAll();

// Busca os perfis ativos no banco (para preencher o select de perfil)
$consulta = $pdo->query("SELECT id, nome FROM perfis WHERE ativo = 1 ORDER BY nome");
$perfis = $consulta->fetchAll();
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

		<?php if (count($setores) == 0 || count($perfis) == 0) { ?>
			<!-- Se a tabela de setores ou de perfis estiver vazia, não dá para cadastrar -->
			<p>Cadastro indisponível: não há setores ou perfis ativos cadastrados.</p>
		<?php } else { ?>

		<form method="post" action="usuario_novo.php">
			<label for="nome">Nome</label>
			<input type="text" id="nome" name="nome" maxlength="150">

			<label for="email">E-mail</label>
			<input type="text" id="email" name="email" maxlength="150">

			<label for="setor_id">Setor</label>
			<select id="setor_id" name="setor_id">
				<option value="">Selecione...</option>
				<?php foreach ($setores as $setor) { ?>
					<option value="<?php echo $setor["id"]; ?>"><?php echo htmlspecialchars($setor["nome"]); ?></option>
				<?php } ?>
			</select>

			<label for="perfil_id">Perfil</label>
			<select id="perfil_id" name="perfil_id">
				<option value="">Selecione...</option>
				<?php foreach ($perfis as $perfil) { ?>
					<option value="<?php echo $perfil["id"]; ?>"><?php echo htmlspecialchars($perfil["nome"]); ?></option>
				<?php } ?>
			</select>

			<input type="submit" value="Cadastrar">
		</form>

		<?php } ?>
	</main>

	<footer>
		<p>Mabelle Enxovais - Todos os direitos reservados © <span id="ano"></span></p>
	</footer>

	<script src="js/script.js"></script>

</body>
</html>
