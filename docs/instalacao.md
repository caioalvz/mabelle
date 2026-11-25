# Instalação do Mabelle's Home

## Requisitos
- XAMPP com PHP 8.1 ou superior e MySQL 8 ou MariaDB 10.4+
- Navegador atualizado

## Passo a passo
1. No XAMPP Control Panel, clique em **Start** no Apache e no MySQL.
2. Copie a pasta `mabelle` para `C:\xampp\htdocs`.
3. Acesse http://localhost/phpmyadmin
4. Em **Importar**, envie `database/database.sql` e execute (apenas uma vez).
5. Importe `database/sprint01_usuarios.sql`.
6. Acesse http://localhost/mabelle

## Configuração
Para usar outros dados de acesso, copie `php/config.example.php` para `php/config.local.php`.

| Variável | Valor padrão no XAMPP |
|---|---|
| `$host` | localhost |
| `$banco` | loja_enxoval |
| `$usuario` | root |
| `$senha` | (vazio) |

## Teste
Abra http://localhost/mabelle/php/teste_pdo.php. Deve aparecer "Conexão PDO OK. Setores cadastrados: 3".

Testes automáticos (com o MySQL ligado): `php tests/test_fluxo.php`

## Problemas comuns

| Erro | Causa | Solução |
|---|---|---|
| Serviço indisponível | MySQL parado ou acesso errado | Ligue o MySQL e confira os dados |
| Table doesn't exist | Script não importado | Importe os dois arquivos |
| Table already exists | `database.sql` importado de novo | Não importe outra vez |
| Apache não inicia | Porta 80 ocupada | Feche o programa ou mude a porta |
