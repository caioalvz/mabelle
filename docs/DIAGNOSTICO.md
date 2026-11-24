# Ponto 01 – Diagnóstico do módulo

Antes de mexer no projeto, olhei o que já existia e decidi o que fazer.

## 1. O que já existia

| Arquivo | Para que serve | O que vou fazer |
|---|---|---|
| `index.php`, `sobre.php`, `produto.php`, `contato.php` | Páginas do site | Não mexer |
| `login.php` | Tela de login (ainda não funciona de verdade) | Não mexer (fora do escopo) |
| `dashboard.php` | Painel com dados fixos | Só colocar um link para "Usuários" |
| `php/conexao.php` | Conexão com o banco (mysqli) | Não mexer |
| `php/teste.php` | Testa a conexão antiga | Não mexer |
| `database.sql` | Cria o banco `loja_enxoval` | Não mexer |
| `css/styles.css` | Visual do site | Só acrescentar estilos no final |
| `js/script.js` | Coloca o ano no rodapé | Não mexer |
| `QUERIES.TXT` | Rascunho de consultas | Não mexer |

Importante: o banco **não tem** tabela de usuários, setores nem perfis. Preciso criar.

O que o projeto usa: PHP, MySQL (XAMPP), HTML, CSS e JavaScript.

## 2. O que vou criar

| Arquivo novo | Para que serve |
|---|---|
| `database/sprint01_usuarios.sql` | Cria as tabelas `setores`, `perfis`, `usuarios` e `auditoria_usuarios` |
| `php/conexao_pdo.php` | Conexão nova com PDO |
| `php/config.example.php` | Exemplo para guardar usuário e senha do banco |
| `php/teste_pdo.php` | Mostra que a conexão funciona |
| `usuario_novo.php` | Formulário, conferência dos dados e gravação |
| `usuarios.php` | Lista, busca, junção das tabelas (JOIN) e pergunta de excluir |
| `usuario_excluir.php` | Marca o usuário como "Baixa" |
| `js/usuarios.js` | Ajuda no navegador (conferir campos e abrir a pergunta) |
| `tests/test_fluxo.php` | Testes automáticos |
| `docs/` | Relatório da sprint |

Estilo do código: simples. Sem classes e sem funções próprias. Cada página faz o seu trabalho do começo ao fim, com comentários.

## 3. Mapa das páginas

| Página | Como é aberta | O que faz |
|---|---|---|
| `usuarios.php` | Link normal (GET) | Mostra a lista e a busca (`?q=texto`) |
| `usuario_novo.php` | Link (GET) e formulário (POST) | Mostra o formulário e grava |
| `usuario_excluir.php` | Só formulário (POST) | Dá baixa no usuário |

## 4. Plano

Nada que existe será apagado. Só entram arquivos novos, um link no `dashboard.php` e estilos no final do `styles.css`.

## 5. Testes deste ponto

- **Positivo:** todos os arquivos da tabela acima existem e foram lidos antes de eu mudar qualquer coisa.
- **Erro/limite:** o banco atual não tem a tabela `usuarios` (o `QUERIES.TXT` fala dela, mas ela não existe). Por isso ela precisa ser criada, não suposta.
