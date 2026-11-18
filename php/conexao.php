<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "loja_enxoval";

$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

if(!$conexao){
    die("Erro na conexao: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8");

?>
