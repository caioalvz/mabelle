-- Sprint 01 - Gestao de usuarios (MySQL 8 / MariaDB 10.4+)
-- Reexecutavel: usa IF NOT EXISTS e INSERT IGNORE. Nao altera tabelas existentes.
CREATE DATABASE IF NOT EXISTS loja_enxoval CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja_enxoval;

CREATE TABLE IF NOT EXISTS setores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    setor_id INT NOT NULL,
    perfil_id INT NOT NULL,
    status ENUM('Ativo','Baixa') NOT NULL DEFAULT 'Ativo',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    baixado_em TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT uq_usuarios_email UNIQUE (email),
    CONSTRAINT fk_usuarios_setor FOREIGN KEY (setor_id) REFERENCES setores(id),
    CONSTRAINT fk_usuarios_perfil FOREIGN KEY (perfil_id) REFERENCES perfis(id),
    INDEX idx_usuarios_status (status),
    INDEX idx_usuarios_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auditoria_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_alvo_id INT NULL,
    acao VARCHAR(30) NOT NULL,
    autor VARCHAR(100) NOT NULL DEFAULT 'sistema',
    data_hora TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resultado VARCHAR(200) NOT NULL,
    INDEX idx_auditoria_alvo (usuario_alvo_id)
) ENGINE=InnoDB;

-- Dados ficticios de homologacao
INSERT IGNORE INTO setores (id, nome) VALUES (1,'Vendas'),(2,'Estoque'),(3,'Atendimento');
INSERT IGNORE INTO perfis (id, nome) VALUES (1,'Administrador'),(2,'Operador'),(3,'Consulta');
INSERT IGNORE INTO usuarios (nome, email, setor_id, perfil_id) VALUES
('Ana Teste','ana.teste@exemplo.com',1,1),
('Bruno Teste','bruno.teste@exemplo.com',2,2),
('Carla Teste','carla.teste@exemplo.com',3,3);

-- Verificacoes (rode manualmente):
--   SELECT u.nome, s.nome AS setor, p.nome AS perfil FROM usuarios u
--     JOIN setores s ON s.id = u.setor_id JOIN perfis p ON p.id = u.perfil_id;
--   INSERT INTO usuarios (nome,email,setor_id,perfil_id) VALUES ('X','ana.teste@exemplo.com',1,1); -- deve falhar (UNIQUE)
--   INSERT INTO usuarios (nome,email,setor_id,perfil_id) VALUES ('X','x@exemplo.com',999,1);      -- deve falhar (FK)
