-- Regra 1: Impedir Exclusão de Usuários com Transações
CREATE OR REPLACE RULE impedir_exclusao_usuario_com_transacoes AS
ON DELETE TO usuario
WHERE EXISTS (
    SELECT 1 FROM compra WHERE compra.id_usuario = OLD.id_usuario
    UNION ALL
    SELECT 1 FROM venda WHERE venda.id_usuario = OLD.id_usuario
)
DO INSTEAD NOTHING;

-- Regra 2: Prevenir Venda de Mais Ações do que o Usuário Possui
CREATE OR REPLACE RULE prevenir_venda_excessiva AS
ON INSERT TO venda
WHERE (SELECT SUM(quantidade_compra) - COALESCE(SUM(quantidade_venda), 0)
       FROM compra LEFT JOIN venda ON compra.ticker_ativo = venda.ticker_ativo
       WHERE compra.id_usuario = NEW.id_usuario AND compra.ticker_ativo = NEW.ticker_ativo)
       < NEW.quantidade_venda
DO INSTEAD NOTHING;

-- Regra 3: Atualizar Preço Médio das Ações no Momento da Compra
CREATE OR REPLACE RULE atualizar_preco_medio AS
ON INSERT TO compra
DO ALSO
UPDATE ativo
SET preco_medio = (
    SELECT AVG(valor_unitario_compra)
    FROM compra
    WHERE compra.ticker_ativo = NEW.ticker_ativo
)
WHERE ativo.ticker_ativo = NEW.ticker_ativo;

-- Regra 4: Registrar Log de Transações
CREATE OR REPLACE RULE log_transacoes AS
ON INSERT TO compra OR INSERT TO venda
DO ALSO
INSERT INTO log_transacoes (id_usuario, ticker_ativo, tipo_transacao, quantidade, valor_unitario, data)
VALUES (
    CASE WHEN TG_OP = 'INSERT' AND TG_TABLE_NAME = 'compra' THEN NEW.id_usuario ELSE NEW.id_usuario END,
    CASE WHEN TG_OP = 'INSERT' AND TG_TABLE_NAME = 'compra' THEN NEW.ticker_ativo ELSE NEW.ticker_ativo END,
    TG_TABLE_NAME,
    CASE WHEN TG_OP = 'INSERT' AND TG_TABLE_NAME = 'compra' THEN NEW.quantidade_compra ELSE NEW.quantidade_venda END,
    CASE WHEN TG_OP = 'INSERT' AND TG_TABLE_NAME = 'compra' THEN NEW.valor_unitario_compra ELSE NEW.valor_unitario_venda END,
    CASE WHEN TG_OP = 'INSERT' AND TG_TABLE_NAME = 'compra' THEN NEW.data_compra ELSE NEW.data_venda END
);

-- Regra 5: Atualizar Histórico de Preços ao Registrar uma Venda
CREATE OR REPLACE RULE atualizar_historico_apos_venda AS
ON INSERT TO venda
DO ALSO
UPDATE historico
SET fechamento_historico = NEW.valor_unitario_venda
WHERE ticker_ativo = NEW.ticker_ativo AND data_historico = NEW.data_venda;
