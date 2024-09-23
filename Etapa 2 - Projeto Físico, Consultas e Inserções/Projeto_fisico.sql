CREATE DATABASE brix;

CREATE TABLE setor (
    id_setor INT NOT NULL,
    nome_setor VARCHAR(50),
    CONSTRAINT setor_pk PRIMARY KEY (id_setor)
);

CREATE TABLE industria (
    id_industria INT NOT NULL,
    nome_industria VARCHAR(50),
    CONSTRAINT industria_pk PRIMARY KEY (id_industria)
);

CREATE TABLE ativo (
    ticker_ativo VARCHAR(50) NOT NULL,
    nome_ativo VARCHAR(50) NOT NULL,
    id_setor INT NOT NULL,
    id_industria INT NOT NULL,
    CONSTRAINT ativo_pk PRIMARY KEY (ticker_ativo),
    CONSTRAINT ativo_fk_setor FOREIGN KEY (id_setor) REFERENCES setor(id_setor),
    CONSTRAINT ativo_fk_industria FOREIGN KEY (id_industria) REFERENCES industria(id_industria)
);

CREATE TABLE historico (
    id_historico INT NOT NULL,
    ticker_ativo VARCHAR(50) NOT NULL,
    data_historico DATE NOT NULL,
    abertura_historico FLOAT NOT NULL,
    alto_historico FLOAT NOT NULL,
    baixo_historico FLOAT NOT NULL,
    fechamento_historico FLOAT NOT NULL,
    volume_historico INT NOT NULL,
    CONSTRAINT historico_pk PRIMARY KEY (id_historico),
    CONSTRAINT historico_fk_ativo FOREIGN KEY (ticker_ativo) REFERENCES ativo(ticker_ativo)
);

CREATE TABLE usuario (
    id_usuario INT NOT NULL,
    nome_usuario VARCHAR(50) NOT NULL,
    email_usuario VARCHAR(50) NOT NULL,
    senha_usuario VARCHAR(50) NOT NULL,
    cpf_usuario CHAR(11) NOT NULL,
    celular_usuario VARCHAR(11),
    CONSTRAINT usuario_pk PRIMARY KEY (id_usuario)
);

CREATE TABLE compra (
    id_compra INT NOT NULL,
    data_compra DATE NOT NULL,
    quantidade_compra INT NOT NULL,
    valor_unitario_compra FLOAT NOT NULL,
    id_usuario INT NOT NULL,
    ticker_ativo VARCHAR(50) NOT NULL,
    CONSTRAINT compra_pk PRIMARY KEY (id_compra),
    CONSTRAINT compra_fk_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    CONSTRAINT compra_fk_ativo FOREIGN KEY (ticker_ativo) REFERENCES ativo(ticker_ativo)
);

CREATE TABLE venda (
    id_venda INT NOT NULL,
    data_venda DATE NOT NULL,
    quantidade_venda INT NOT NULL,
    valor_unitario_venda FLOAT NOT NULL,
    id_usuario INT NOT NULL,
    ticker_ativo VARCHAR(50) NOT NULL,
    CONSTRAINT venda_pk PRIMARY KEY (id_venda),
    CONSTRAINT venda_fk_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    CONSTRAINT venda_fk_ativo FOREIGN KEY (ticker_ativo) REFERENCES ativo(ticker_ativo)
);

CREATE VIEW compra_valor_total AS
SELECT 
    id_compra,
    data_compra,
    quantidade_compra,
    valor_unitario_compra,
    id_usuario,
    ticker_ativo,
    (quantidade_compra * valor_unitario_compra) AS valor_total_compra
FROM 
    compra;

CREATE VIEW venda_valor_total AS
SELECT 
    id_venda,
    data_venda,
    quantidade_venda,
    valor_unitario_venda,
    id_usuario,
    ticker_ativo,
    (quantidade_venda * valor_unitario_venda) AS valor_total_venda
FROM 
    venda;
