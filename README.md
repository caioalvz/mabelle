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
| 06 | Gravação do usuário | Pendente |
| 07 | Listagem e busca | Pendente |
| 08 | JOIN na listagem | Pendente |
| 09 | Confirmação de exclusão | Pendente |
| 10 | Exclusão segura | Pendente |

- **Ponto 01 – Diagnóstico do módulo:** Levantamento do que já existia no projeto e plano de alteração (`docs/DIAGNOSTICO.md`).
- **Ponto 02 – Estrutura MySQL:** Script `database/sprint01_usuarios.sql` com as tabelas `setores`, `perfis`, `usuarios` e `auditoria_usuarios`, chaves estrangeiras e dados fictícios.
- **Ponto 03 – Conexão PDO:** `php/conexao_pdo.php` cria a variável `$pdo`; falhas de conexão são tratadas sem expor detalhes ao visitante.
- **Ponto 04 – Selects de setor e perfil:** O formulário `usuario_novo.php` carrega as opções de setor e perfil direto do banco.
- **Ponto 05 – Validação do formulário:** Conferência dos campos no navegador (`js/usuarios.js`) e no servidor (PHP), com token CSRF.

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
