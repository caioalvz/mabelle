# Requisitos do Mabelle's Home

## Requisitos funcionais (RF)

| Código | O sistema deve permitir que... | Prioridade | Situação |
|---|---|---|---|
| RF01 | o visitante veja produtos em destaque. | Alta | Pronto |
| RF02 | o visitante veja produtos por categoria. | Alta | Pronto |
| RF03 | o visitante conheça a loja e seus contatos. | Média | Pronto |
| RF04 | o responsável cadastre um usuário. | Alta | Pronto |
| RF05 | o responsável veja os usuários ativos. | Alta | Pronto |
| RF06 | o responsável busque por nome ou e-mail. | Média | Pronto |
| RF07 | o responsável exclua um usuário após confirmar. | Alta | Pronto |
| RF08 | o usuário entre com login e senha conferidos. | Alta | Pendente |
| RF09 | o cliente use um carrinho de compras. | Alta | Pendente |
| RF10 | o visitante envie uma mensagem de contato. | Média | Pendente |

## Requisitos não funcionais (RNF)

| Código | Descrição |
|---|---|
| RNF01 | Usar PHP 8.1+ e MySQL 8 ou MariaDB 10.4+. |
| RNF02 | Usar consultas preparadas com dados digitados. |
| RNF03 | Tratar os textos exibidos com htmlspecialchars(). |
| RNF04 | Proteger os formulários que gravam dados com token CSRF. |
| RNF05 | Não mostrar detalhes técnicos de erros do banco. |
| RNF06 | A janela de exclusão deve fechar com a tecla Esc. |

## Regras de negócio (RN)

| Código | Regra |
|---|---|
| RN01 | Não pode haver dois usuários com o mesmo e-mail. |
| RN02 | O nome tem de 3 a 150 caracteres e o e-mail deve ser válido. |
| RN03 | Setor e perfil precisam existir e estar ativos. |
| RN04 | Excluir muda o status para Baixa, sem apagar o registro. |
| RN05 | Só usuários ativos aparecem na lista. |
| RN06 | Usuário em Baixa não recebe nova baixa. |
| RN07 | Cadastros, falhas de gravação e baixas vão para a auditoria. |

## Histórias de usuário


**Como** visitante, **quero** ver os produtos por categoria, **para** achar rápido o que preciso.

- Aparecem Cama, Banho e Mesa.
- Cada produto tem foto, nome e preço.
- O menu leva a cada categoria.

**Como** responsável pela loja, **quero** cadastrar usuários, **para** registrar quem trabalha em cada setor.

- Campo inválido mostra mensagem.
- E-mail repetido é recusado.
- Ao salvar, aparece o aviso de sucesso.

**Como** responsável pela loja, **quero** confirmar antes de excluir, **para** evitar erros.

- A janela mostra nome, e-mail e ID.
- Cancelar não muda nada.
- Ao confirmar, o usuário sai da lista.
