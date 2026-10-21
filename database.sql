CREATE DATABASE IF NOT EXISTS loja_enxoval;
USE loja_enxoval;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    categoria_id INT NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    endereco TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_nascimento DATE,
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'Pendente',
    valor_total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('percentual', 'fixo')),
    valor DECIMAL(10, 2) NOT NULL, 
    valor_minimo DECIMAL(10, 2) DEFAULT 
    data_validade DATE NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);
    
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cnpj VARCHAR(11) UNIQUE NOT NULL,
    razao_social VARCHAR(180) UNIQUE NOT NULL, 
    nome_fantasia VARCHAR(180),
    endereco TEXT,
    telefone VARCHAR(20)
)

INSERT INTO categorias (nome, descricao) VALUES
('Cama', 'Jogos de lençol, edredons, fronhas, cobertores e colchas.'),
('Mesa', 'Toalhas de mesa, jogos americanos, trilhos e guardanapos.'),
('Banho', 'Toalhas de banho, rosto, pisos, roupões e tapetes.');

INSERT INTO clientes (nome, email, telefone, endereco, data_nascimento) VALUES
('Alberto Gomes', 'alberto@gmail.com', '8199999998', 'Rua do Futuro, 18 - Prazeres', '1990-04-27'),
('Breno Fernandes', 'breno@gmail.com', '8199999997', 'Rua do Passado, 45 - Cabo de Santo Agostinho', '1987-03-15'),
('Carlos Souza', 'carlos@gmail.com', '8199999996', 'Rua do Presente, 134 - Pontezinha', '2001-04-14'),
('Dagoberto Silva', 'dagoberto@gmail.com', '8199999995', 'Rua do Destino, 453 - Prazeres', '1990-06-23'),
('Emílio Pereira', 'emilio@gmail.com', '8199999994', 'Rua do Futuro, 606 - Bomba do Hemetério', '2000-11-02');

INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id) VALUES
('Lençol Vermelho Casal', 'Um lençol vermelho para cama de casal', 50, 25, 1);

