# Telas, rotas e fluxos

## Mapa de telas

| Tela | Arquivo | Quem acessa | O que faz |
|---|---|---|---|
| Início | index.php | Todos | Banner e destaques |
| Produtos | produto.php | Todos | Produtos por categoria |
| Sobre | sobre.php | Todos | Missão e valores |
| Contato | contato.php | Todos | Formulário e contatos |
| Login | login.php | Todos | Campos de acesso |
| Dashboard | dashboard.php | Todos* | Resumo com dados fixos |
| Usuários | usuarios.php | Todos* | Lista, busca e exclusão |
| Novo usuário | usuario_novo.php | Todos* | Cadastro |

\* Deveriam ser restritas, mas o login ainda não funciona.


## Arquivos que processam dados

| Arquivo | Método | Recebe | Faz | Vai para |
|---|---|---|---|---|
| usuario_novo.php | POST | csrf, nome, email, setor_id, perfil_id | Valida e grava | usuarios.php, ou mostra o erro |
| usuarios.php | GET | q | Filtra a lista | mesma tela |
| usuario_excluir.php | POST | csrf, id | Dá baixa e audita | usuarios.php |

## Mapa de navegação

```text
Início (index.php)
 |-- Produtos (produto.php)
 |-- Sobre (sobre.php)
 |-- Contato (contato.php)
 `-- Dashboard (dashboard.php)
      |-- Usuários (usuarios.php)
      |    `-- Novo usuário (usuario_novo.php)
      `-- Sair -> Login (login.php)
```

## Fluxo de cadastro

1. A pessoa preenche o formulário.
2. O JavaScript confere os campos. Se houver erro, o formulário não é enviado.
3. O PHP confere o token e os campos. Se houver erro, mostra a mensagem.
4. O usuário é gravado, a ação vai para a auditoria e a lista aparece.

## Fluxo de exclusão

1. A pessoa clica em Excluir.
2. A janela pede confirmação. Cancelar ou Esc não alteram nada.
3. `usuario_excluir.php` confere o método POST, o token e o ID.
4. O status muda para Baixa, a ação vai para a auditoria e a lista mostra um aviso.

## Capturas

As imagens das telas estão na pasta [telas](telas/).
