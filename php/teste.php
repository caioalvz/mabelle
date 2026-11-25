<?php
/**
 * Testa a conexão antiga listando as categorias.
 */
include("conexao.php");

echo "Conectado com sucesso!<br><br>";

$sql = "SELECT * FROM categorias";
$resultado = mysqli_query($conexao, $sql);

while($linha = mysqli_fetch_assoc($resultado)){
    echo $linha['id'] . " - " . $linha['nome'] . "<br>";
}

mysqli_close($conexao);
?>
