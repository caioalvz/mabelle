<?php
/**
 * Testes automáticos T01 a T12 do módulo de usuários.
 */
// Testes T01 a T12 contra um MySQL/MariaDB de TESTE (banco mabelle_teste, criado e apagado aqui).
// Requisitos: MySQL rodando em localhost com usuário root sem senha (padrão do XAMPP).
// Uso: php tests/test_fluxo.php

$ok = 0;
$falhas = 0;
/**
 * Conta e mostra o resultado de uma verificação.
 *
 * @param string $nome     Descrição do teste.
 * @param bool   $condicao Verdadeiro se passou.
 * @return void
 */
function verificar($nome, $condicao)
{
    global $ok, $falhas;
    if ($condicao) { $ok++; echo "[OK]    $nome\n"; } else { $falhas++; echo "[FALHA] $nome\n"; }
}

// ---- 1) Cria o banco de teste a partir do script SQL do projeto ----
$admin = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
$admin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$admin->exec("DROP DATABASE IF EXISTS mabelle_teste");
$sql = file_get_contents(__DIR__ . "/../database/sprint01_usuarios.sql");
$sql = str_replace("loja_enxoval", "mabelle_teste", $sql);
$linhas = [];
foreach (explode("\n", $sql) as $linha) {
    if (strpos(trim($linha), "--") !== 0) { $linhas[] = $linha; }   // tira comentários
}
foreach (explode(";", implode("\n", $linhas)) as $comando) {
    if (trim($comando) != "") { $admin->exec($comando); }
}
$db = new PDO("mysql:host=localhost;dbname=mabelle_teste;charset=utf8mb4", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// ---- 2) Sobe o servidor do PHP apontando para o banco de teste ----
$raiz = realpath(__DIR__ . "/..");
$porta = 8794;
$ambiente = getenv();
$ambiente["MABELLE_DB_NAME"] = "mabelle_teste";
$pasta = sys_get_temp_dir();
$processo = proc_open([PHP_BINARY, "-S", "127.0.0.1:$porta", "-t", $raiz],
    [1 => ["file", "$pasta/mabelle_t.out", "w"], 2 => ["file", "$pasta/mabelle_t.err", "w"]], $pipes, $raiz, $ambiente);
usleep(1500000);

$cookie = "";
// Faz uma requisição e devolve [status, corpo, redirecionamento]
/**
 * Faz uma requisição ao servidor de teste.
 *
 * @param string $metodo  GET ou POST.
 * @param string $caminho Página chamada.
 * @param array  $dados   Campos do formulário.
 * @return array [status, corpo, redirecionamento]
 */
function chamar($metodo, $caminho, $dados = [])
{
    global $porta, $cookie;
    $cabecalhos = "Content-Type: application/x-www-form-urlencoded\r\n";
    if ($cookie != "") { $cabecalhos .= "Cookie: $cookie\r\n"; }
    $contexto = stream_context_create(["http" => [
        "method" => $metodo, "content" => http_build_query($dados), "header" => $cabecalhos,
        "ignore_errors" => true, "follow_location" => 0]]);
    $corpo = @file_get_contents("http://127.0.0.1:$porta/$caminho", false, $contexto);
    $status = 0; $destino = "";
    foreach ($http_response_header ?? [] as $linha) {
        if (preg_match("#^HTTP/\S+ (\d+)#", $linha, $m)) { $status = (int) $m[1]; }
        if (stripos($linha, "Location:") === 0) { $destino = trim(substr($linha, 9)); }
        if (stripos($linha, "Set-Cookie:") === 0) { $cookie = explode(";", trim(substr($linha, 11)))[0]; }
    }
    return [$status, (string) $corpo, $destino];
}
/**
 * Lê o token CSRF de uma página.
 *
 * @param string $caminho Página com formulário.
 * @return string Token, ou vazio se não houver.
 */
function pegar_token($caminho)
{
    $r = chamar("GET", $caminho);
    preg_match('/name="csrf" value="([a-f0-9]+)"/', $r[1], $m);
    return $m[1] ?? "";
}
/**
 * Executa uma consulta de contagem.
 *
 * @param PDO    $db  Conexão.
 * @param string $sql Consulta SELECT COUNT.
 * @return int
 */
function contar($db, $sql) { return (int) $db->query($sql)->fetchColumn(); }

$token = pegar_token("usuario_novo.php");
$valido = ["csrf" => $token, "nome" => "Maria Silva", "email" => "Maria@Exemplo.com", "setor_id" => "1", "perfil_id" => "2"];

// T01 - cadastro válido
$antes = contar($db, "SELECT COUNT(*) FROM usuarios");
$r = chamar("POST", "usuario_novo.php", $valido);
verificar("T01 cadastro válido grava um único registro e redireciona", $r[0] == 302 && contar($db, "SELECT COUNT(*) FROM usuarios") == $antes + 1);
verificar("T01 e-mail salvo em minúsculas e status Ativo", contar($db, "SELECT COUNT(*) FROM usuarios WHERE email='maria@exemplo.com' AND status='Ativo'") == 1);
verificar("T01 auditoria de cadastro registrada", contar($db, "SELECT COUNT(*) FROM auditoria_usuarios WHERE acao='cadastro' AND resultado='sucesso'") == 1);

// T02 - campos inválidos
$r = chamar("POST", "usuario_novo.php", ["email" => "abc", "nome" => ""] + $valido);
verificar("T02 e-mail inválido e nome vazio recusados com mensagem", strpos($r[1], "Informe o nome.") !== false && strpos($r[1], "e-mail válido") !== false);
verificar("T02 banco não recebeu registro", contar($db, "SELECT COUNT(*) FROM usuarios") == $antes + 1);

// T03 - e-mail repetido
$r = chamar("POST", "usuario_novo.php", $valido);
verificar("T03 e-mail repetido avisa e não duplica", strpos($r[1], "já está cadastrado") !== false && contar($db, "SELECT COUNT(*) FROM usuarios") == $antes + 1);
verificar("T03 mensagem não mostra SQL", stripos($r[1], "SQLSTATE") === false && stripos($r[1], "INSERT") === false);

// T04 - setor/perfil inexistente (POST manual)
$r = chamar("POST", "usuario_novo.php", ["setor_id" => "999", "email" => "z@exemplo.com"] + $valido);
verificar("T04 setor 999 bloqueado no servidor", strpos($r[1], "Setor inválido") !== false);
$r = chamar("POST", "usuario_novo.php", ["perfil_id" => "1 OR 1=1", "email" => "y@exemplo.com"] + $valido);
verificar("T04 perfil não numérico bloqueado", strpos($r[1], "Perfil inválido") !== false);
$r = chamar("POST", "usuario_novo.php", ["csrf" => "errado"] + $valido);
verificar("formulário sem token válido é recusado (403)", $r[0] == 403);

// T05 - busca
$r = chamar("GET", "usuarios.php?q=bruno");
verificar("T05 busca por nome existente", strpos($r[1], "Bruno Teste") !== false && strpos($r[1], "Carla Teste") === false);
$r = chamar("GET", "usuarios.php?q=carla.teste@");
verificar("T05 busca por e-mail", strpos($r[1], "Carla Teste") !== false);
$r = chamar("GET", "usuarios.php?q=zzzz");
verificar("T05 busca inexistente mostra mensagem", strpos($r[1], "Nenhum usuário encontrado") !== false);

// T06 - JOIN
$r = chamar("GET", "usuarios.php?q=ana");
verificar("T06 JOIN mostra setor e perfil corretos", strpos($r[1], "<td>Vendas</td>") !== false && strpos($r[1], "<td>Administrador</td>") !== false);

// T07 - alterar referência
$db->exec("UPDATE setores SET nome='Comercial' WHERE id=1");
$r = chamar("GET", "usuarios.php?q=ana");
verificar("T07 JOIN mostra o novo nome do setor", strpos($r[1], "<td>Comercial</td>") !== false);

// T12 - caracteres especiais e injeção
$r = chamar("GET", "usuarios.php?q=" . urlencode("' OR '1'='1"));
verificar("T12 injeção na busca não lista todos", strpos($r[1], "Nenhum usuário encontrado") !== false);
$r = chamar("GET", "usuarios.php?q=" . urlencode("%"));
verificar("T12 curinga % tratado como texto", strpos($r[1], "Nenhum usuário encontrado") !== false);
$token = pegar_token("usuario_novo.php");
chamar("POST", "usuario_novo.php", ["csrf" => $token, "nome" => "<b>Rob'); DROP TABLE usuarios;--", "email" => "rob@exemplo.com", "setor_id" => "1", "perfil_id" => "1"]);
$r = chamar("GET", "usuarios.php?q=rob");
verificar("T12 nome com HTML/SQL é salvo como texto e escapado", strpos($r[1], "&lt;b&gt;Rob") !== false && contar($db, "SELECT COUNT(*) FROM usuarios") > 0);

// T08 - cancelar não faz nenhuma requisição (o botão Cancelar só esconde o modal no navegador)
$ana = (int) $db->query("SELECT id FROM usuarios WHERE nome='Ana Teste'")->fetchColumn();
verificar("T08 sem confirmar, Ana continua Ativa", $db->query("SELECT status FROM usuarios WHERE id=$ana")->fetchColumn() == "Ativo");
$r = chamar("GET", "usuarios.php");
verificar("cada linha da lista traz o botão Excluir com o id do usuário", strpos($r[1], 'class="botao btn-excluir"') !== false && strpos($r[1], 'data-id="' . $ana . '"') !== false);
verificar("modal traz o botão Cancelar e o botão Confirmar", strpos($r[1], 'id="modal-cancelar"') !== false && strpos($r[1], 'value="Confirmar"') !== false);

// T10 - tentativas inválidas de exclusão
$r = chamar("GET", "usuario_excluir.php?id=$ana");
verificar("T10 GET recusado (405)", $r[0] == 405);
$r = chamar("POST", "usuario_excluir.php", ["id" => $ana]);
verificar("T10 POST sem CSRF recusado (403)", $r[0] == 403);
$token = pegar_token("usuarios.php");
$r = chamar("POST", "usuario_excluir.php", ["csrf" => $token, "id" => "abc"]);
verificar("T10 id inválido recusado (400)", $r[0] == 400);
verificar("T10 dados preservados", $db->query("SELECT status FROM usuarios WHERE id=$ana")->fetchColumn() == "Ativo");

// T09 - confirmar
$r = chamar("POST", "usuario_excluir.php", ["csrf" => $token, "id" => $ana]);
verificar("T09 baixa: status Baixa e baixado_em preenchido", $db->query("SELECT status FROM usuarios WHERE id=$ana")->fetchColumn() == "Baixa"
    && $db->query("SELECT baixado_em FROM usuarios WHERE id=$ana")->fetchColumn() !== null);
$r = chamar("GET", "usuarios.php?q=ana");
verificar("T09 usuário sai da lista de ativos", strpos($r[1], "Ana Teste") === false);
verificar("T09 auditoria registra a baixa", contar($db, "SELECT COUNT(*) FROM auditoria_usuarios WHERE acao='baixa' AND usuario_alvo_id=$ana AND resultado='sucesso'") == 1);

// T11 - repetir
$r = chamar("POST", "usuario_excluir.php", ["csrf" => $token, "id" => $ana]);
$r = chamar("GET", "usuarios.php");
verificar("T11 repetição mostra aviso controlado", strpos($r[1], "já havia sido excluído") !== false);
verificar("T11 não houve segunda baixa", contar($db, "SELECT COUNT(*) FROM auditoria_usuarios WHERE acao='baixa' AND resultado='sucesso'") == 1);

// Banco indisponível
proc_terminate($processo);
$ambiente["MABELLE_DB_NAME"] = "banco_que_nao_existe";
$processo = proc_open([PHP_BINARY, "-S", "127.0.0.1:" . ($porta + 1), "-t", $raiz],
    [1 => ["file", "$pasta/mabelle_t2.out", "w"], 2 => ["file", "$pasta/mabelle_t2.err", "w"]], $pipes, $raiz, $ambiente);
usleep(1500000);
$porta = $porta + 1;
$cookie = "";
$r = chamar("GET", "usuarios.php");
verificar("banco indisponível: resposta 503 sem detalhes técnicos", $r[0] == 503 && stripos($r[1], "banco_que_nao_existe") === false && stripos($r[1], "SQLSTATE") === false);
proc_terminate($processo);

$admin->exec("DROP DATABASE IF EXISTS mabelle_teste");
echo "\nResumo: $ok ok, $falhas falhas\n";
exit($falhas > 0 ? 1 : 0);
