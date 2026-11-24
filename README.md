# 🌸 Mabelle

Site para apresentação de produtos para consumidores em sua maioria predominante sendo de mulheres de 30 a 45 anos que buscam praticidade para encontrar itens de cama, mesa e banho de maneira descomplicada e de fácil acessibilidade. O site visa resolver as barreiras geográficas atuais da loja, que somente vende para consumidores locais.

---

## 🛠️ Tecnologias Utilizadas

Este projeto foi desenvolvido utilizando as seguintes tecnologias e ferramentas:

* **Front-end:** HTML5, CSS3, JavaScript
* **Controle de Versão:** Git e GitHub
* **Ambiente / Ferramentas:** XAMMP, MySQL

---

## 📁 Organização de Pastas

A estrutura de diretórios do projeto está organizada da seguinte forma:

```text
mabelle/
├── 📁 assets/             # Arquivos estáticos (imagens, ícones, fontes)
│   ├── 📁 css/            # Folhas de estilo globais e componentizadas
│   ├── 📁 js/             # Scripts e comportamentos JavaScript
│   └── 📁 img/            # Imagens e mídias do projeto
├── 📁 components/         # Componentes reutilizáveis (se aplicável)
├── 📁 includes/           # Partes reutilizáveis de código (Header, Footer, etc.)
├── 📄 index.php           # Página principal da aplicação
├── 📄 .gitignore          # Arquivos e pastas ignorados pelo Git
└── 📄 README.md           # Documentação do projeto
```

---

## 👥 Módulo de usuários (Sprint 01)

Cadastro, listagem, busca e exclusão lógica de usuários em PHP e MySQL. Esta seção acompanha as entregas: cada ponto concluído é registrado abaixo.

### Andamento

| Ponto | Entrega | Situação |
|---|---|---|
| 01 | Diagnóstico do módulo | Concluído |
| 02 | Estrutura MySQL | Concluído |
| 03 | Conexão PDO | Concluído |
| 04 | Selects de setor e perfil | Concluído |
| 05 | Validação do formulário | Concluído |
| 06 | Gravação do usuário | Concluído |
| 07 | Listagem e busca | Concluído |
| 08 | JOIN na listagem | Concluído |
| 09 | Confirmação de exclusão | Concluído |
| 10 | Exclusão segura | Concluído |

- **Ponto 01 – Diagnóstico do módulo:** Levantamento do que já existia no projeto e plano de alteração (`docs/DIAGNOSTICO.md`).
- **Ponto 02 – Estrutura MySQL:** Script `database/sprint01_usuarios.sql` com as tabelas `setores`, `perfis`, `usuarios` e `auditoria_usuarios`, chaves estrangeiras e dados fictícios.
- **Ponto 03 – Conexão PDO:** `php/conexao_pdo.php` cria a variável `$pdo`; falhas de conexão são tratadas sem expor detalhes ao visitante.
- **Ponto 04 – Selects de setor e perfil:** O formulário `usuario_novo.php` carrega as opções de setor e perfil direto do banco.
- **Ponto 05 – Validação do formulário:** Conferência dos campos no navegador (`js/usuarios.js`) e no servidor (PHP), com token CSRF.
- **Ponto 06 – Gravação do usuário:** INSERT preparado, com mensagens próprias para e-mail duplicado e setor/perfil inválido, e registro na auditoria.
- **Ponto 07 – Listagem e busca:** `usuarios.php` lista os usuários ativos e permite buscar por nome ou e-mail (`?q=`).
- **Ponto 08 – JOIN na listagem:** A listagem junta `usuarios`, `setores` e `perfis` para exibir os nomes de setor e perfil.
- **Ponto 09 – Confirmação de exclusão:** Janela de confirmação com os botões Cancelar e Confirmar, aberta pela coluna de ações.
- **Ponto 10 – Exclusão segura:** `usuario_excluir.php` aceita apenas POST com token CSRF, marca o usuário como `Baixa` dentro de uma transação e registra a ação na auditoria. Inclui testes automáticos e o relatório da sprint.

### Banco de dados

O script `database/sprint01_usuarios.sql` cria o banco `loja_enxoval` (caso não exista) e as tabelas do módulo:

| Tabela | Conteúdo |
|---|---|
| `setores` | Setores da loja (nome único) |
| `perfis` | Perfis de acesso (nome único) |
| `usuarios` | Nome, e-mail único, `setor_id`, `perfil_id`, `status` (`Ativo` ou `Baixa`) e datas |
| `auditoria_usuarios` | Registro de cada ação: usuário alvo, ação, autor, data/hora e resultado |

As tabelas `usuarios` → `setores` e `usuarios` → `perfis` são ligadas por chave estrangeira. O script pode ser executado mais de uma vez, pois usa `IF NOT EXISTS` e `INSERT IGNORE`.

### Conexão com o banco

`php/conexao_pdo.php` usa PDO com exceções ativadas e consultas preparadas reais. Os valores padrão são `localhost`, banco `loja_enxoval`, usuário `root` e senha vazia (padrão do XAMPP). Para outro ambiente, o arquivo `php/config.local.php` (modelo em `php/config.example.php`, ignorado pelo Git) substitui esses valores. Se a conexão falha, a página responde com status 503 e uma mensagem genérica; o detalhe técnico vai para o log do servidor. `php/teste_pdo.php` exibe o resultado de uma consulta simples e uma simulação de falha tratada.

Requisitos: PHP 8.1+ com `pdo_mysql` e MySQL 8 ou MariaDB 10.4+ (XAMPP).

### Cadastro de usuário

`usuario_novo.php` monta os campos Setor e Perfil a partir das tabelas `setores` e `perfis`, exibindo somente os registros ativos. Os textos vindos do banco são escapados com `htmlspecialchars` antes de aparecer na página.

### Validação

Os dados do formulário passam por duas conferências. No navegador, `js/usuarios.js` verifica nome, e-mail, setor e perfil e mostra a mensagem abaixo de cada campo. No servidor, o PHP repete as regras (nome de 3 a 150 caracteres, e-mail válido, setor e perfil existentes) e confere um token CSRF guardado na sessão. A conferência do navegador melhora o uso; a do servidor é a que vale.

### Gravação

Os dados são gravados com `INSERT` preparado (`?`), de modo que o valor digitado nunca vira parte do comando SQL. O erro 1062 do MySQL (e-mail duplicado) e o erro 1452 (setor ou perfil inexistente) viram mensagens próprias; qualquer outro erro mostra uma mensagem genérica. Cada tentativa, com sucesso ou falha, é registrada em `auditoria_usuarios`. Após gravar, a página redireciona para a listagem com um aviso de sucesso.

### Listagem e busca

`usuarios.php` exibe os usuários com `status = 'Ativo'`, ordenados por nome. O campo de busca filtra por nome ou e-mail com `LIKE`; os caracteres `%`, `_` e `!` digitados são tratados como texto comum. Sem resultados, a página mostra "Nenhum usuário encontrado.".

### JOIN

A listagem usa `INNER JOIN` com as condições `usuarios.setor_id = setores.id` e `usuarios.perfil_id = perfis.id`. Guardar o nome do setor ou do perfil dentro de `usuarios` duplicaria a informação: renomear um setor deixaria usuários com valores divergentes. Com a chave estrangeira, o nome existe em um único lugar.

### Confirmação de exclusão

Cada linha da lista tem o botão Excluir (`type="button"`), que abre uma janela de confirmação com nome, e-mail e ID do usuário. O botão Cancelar e a tecla Esc fecham a janela sem enviar nada; somente Confirmar envia o formulário. A janela usa `role="dialog"` e `aria-modal` para leitores de tela.

### Exclusão

`usuario_excluir.php` aceita somente POST (outros métodos recebem 405), exige o token CSRF (403 se inválido) e valida o ID (400 se inválido). A baixa é lógica: o `UPDATE` troca `status` para `Baixa` e preenche `baixado_em`, com a condição `AND status = 'Ativo'` para que uma segunda tentativa não gere nova baixa. A baixa e o registro de auditoria ficam na mesma transação (`beginTransaction`, `commit`, `rollBack`).

### Rotas e arquivos

| Rota | Função |
|---|---|
| `usuarios.php` | Lista de usuários ativos (SELECT + JOIN), busca e janela de confirmação |
| `usuario_novo.php` | Formulário, validação (navegador e PHP) e INSERT |
| `usuario_excluir.php` | Baixa lógica, apenas POST com token CSRF |
| `php/conexao_pdo.php` | Conexão PDO (variável `$pdo`) |
| `js/usuarios.js` | Validação no navegador e janela de exclusão |

### Testes

`tests/test_fluxo.php` executa os testes T01 a T12 contra um banco temporário (`mabelle_teste`), criado a partir do script SQL e apagado ao final. O MySQL precisa estar em execução. Comando: `php tests/test_fluxo.php`. O relatório da sprint está em `docs/RELATORIO_SPRINT01.md`.

### Pacote

`git archive --format=zip -o mabelle-sprint01.zip HEAD` gera um ZIP apenas com os arquivos versionados, sem o `php/config.local.php`.

### Organização dos scripts SQL

Os dois scripts SQL do projeto ficam na pasta `database/`: `database.sql` (banco `loja_enxoval` e tabelas originais da loja) e `sprint01_usuarios.sql` (módulo de usuários). O `database.sql` ficava na raiz do projeto e foi movido para `database/` ao final da sprint, sem alteração de conteúdo.
