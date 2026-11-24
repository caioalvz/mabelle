# Relatório da Sprint 01 — Cadastro e gestão de usuários (v1.1.0)

Projeto: Mabelle Enxovais · Responsável: Caio Alves

Estilo do código: PHP simples, sem classes nem funções próprias; cada página faz o trabalho do início ao fim.

| Ponto | Arquivos | Teste positivo | Teste de erro/limite | Resultado | Evidência |
|---|---|---|---|---|---|
| 01 Diagnóstico | `docs/DIAGNOSTICO.md` | Inventário conferido com os arquivos reais | `usuarios` não existia no banco | Atendeu | `docs/DIAGNOSTICO.md` |
| 02 Estrutura MySQL | `database/sprint01_usuarios.sql` | Tabelas, FKs e dados fictícios; script reexecutável | E-mail repetido (erro 1062) e setor 999 (erro 1452) | Atendeu | `database/sprint01_usuarios.sql` |
| 03 PDO | `php/conexao_pdo.php`, `php/config.example.php`, `php/teste_pdo.php` | Lê `setores` | Falha simulada e banco inexistente (503, sem detalhes) | Atendeu | `php/teste_pdo.php`; teste do 503 em `docs/evidencias/ponto10_saida_testes_automaticos.txt` |
| 04 Selects | `usuario_novo.php` | Opções vêm de SELECT em registros ativos | Tabela de referência vazia bloqueia o cadastro | Atendeu | `usuario_novo.php` (SELECTs reais) |
| 05 Validação | `usuario_novo.php`, `js/usuarios.js` | T01 | T02, T04, POST manual, token CSRF inválido | Atendeu | T01, T02 e T04 no log de testes |
| 06 INSERT | `usuario_novo.php` | T01 + auditoria | T03 duplicidade, FK, T12 | Atendeu | T03 e auditoria no log de testes |
| 07 Listagem | `usuarios.php` | T05 existente | Inexistente, `%`, injeção | Atendeu | T05 e T12 no log de testes |
| 08 JOIN | `usuarios.php` | T06 | T07 renomear setor reflete na lista | Atendeu | T06 e T07 no log de testes |
| 09 Confirmação | `usuarios.php`, `js/usuarios.js`, `css/styles.css` | Modal com nome, e-mail e ID | Cancelar/Esc não enviam requisição (T08) | Atendeu | `docs/evidencias/ponto09_*.png` |
| 10 Exclusão | `usuario_excluir.php` | T09 + auditoria | T10 (GET 405, sem CSRF 403, id inválido 400), T11 repetição | Atendeu | `docs/evidencias/ponto10_*` |

## Testes T01–T12
`php tests/test_fluxo.php` executa 31 verificações em um MariaDB real (banco `mabelle_teste`), por requisições HTTP ao servidor embutido do PHP. T08 é verificado pelo HTML (botão Excluir em cada linha, botões Cancelar/Confirmar) e pelo estado do banco. O fluxo com cliques (Excluir, Cancelar, Excluir, Confirmar) foi executado em navegador, com capturas e o estado do banco após a exclusão em `docs/evidencias/`.

## Pendências e impactos
- Não há login funcional (fora do escopo): o autor da auditoria é `sistema` e não há checagem de permissão.
- O cadastro não tem senha, portanto não há hash a armazenar.
- Evidências em `docs/evidencias/`: capturas do fluxo de exclusão (Pontos 09 e 10), estado do banco após a baixa e saída dos testes automáticos. Os dados usados são fictícios.
