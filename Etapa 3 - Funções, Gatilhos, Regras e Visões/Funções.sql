-- Função 1: Calcular Valor Total das Ações de um Usuário
CREATE OR REPLACE FUNCTION calcular_valor_total_acoes(id_usuario INT)
RETURNS FLOAT AS $$
DECLARE
    valor_total FLOAT;
BEGIN
    SELECT SUM((c.quantidade_compra - COALESCE(v.quantidade_venda, 0)) * a.preco_medio)
    INTO valor_total
    FROM compra c
    LEFT JOIN venda v ON c.ticker_ativo = v.ticker_ativo AND c.id_usuario = v.id_usuario
    JOIN ativo a ON c.ticker_ativo = a.ticker_ativo
    WHERE c.id_usuario = id_usuario;
    RETURN COALESCE(valor_total, 0);
END;
$$ LANGUAGE plpgsql;

-- Função 2: Calcular Lucro ou Prejuízo de uma Venda Específica
CREATE OR REPLACE FUNCTION calcular_lucro_prejuizo(id_venda INT)
RETURNS FLOAT AS $$
DECLARE
    lucro_prejuizo FLOAT;
BEGIN
    SELECT (v.valor_unitario_venda - c.valor_unitario_compra) * v.quantidade_venda
    INTO lucro_prejuizo
    FROM venda v
    JOIN compra c ON v.ticker_ativo = c.ticker_ativo AND v.id_usuario = c.id_usuario
    WHERE v.id_venda = id_venda
    LIMIT 1;
    RETURN lucro_prejuizo;
END;
$$ LANGUAGE plpgsql;

-- Função 3: Verificar Saldo de Ações Antes de Realizar uma Venda
CREATE OR REPLACE FUNCTION verificar_saldo_acoes(id_usuario INT, ticker_ativo VARCHAR, quantidade INT)
RETURNS BOOLEAN AS $$
DECLARE
    saldo INT;
BEGIN
    SELECT SUM(c.quantidade_compra) - COALESCE(SUM(v.quantidade_venda), 0)
    INTO saldo
    FROM compra c
    LEFT JOIN venda v ON c.ticker_ativo = v.ticker_ativo AND c.id_usuario = v.id_usuario
    WHERE c.id_usuario = id_usuario AND c.ticker_ativo = ticker_ativo;
    
    IF saldo >= quantidade THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Função 4: Atualizar Preço Médio de um Ativo
CREATE OR REPLACE FUNCTION atualizar_preco_medio(ticker_ativo VARCHAR)
RETURNS VOID AS $$
BEGIN
    UPDATE ativo
    SET preco_medio = (
        SELECT AVG(valor_unitario_compra)
        FROM compra
        WHERE ticker_ativo = ticker_ativo
    )
    WHERE ticker_ativo = ticker_ativo;
END;
$$ LANGUAGE plpgsql;

-- Função 5: Retornar Ranking dos Usuários por Lucro Total
CREATE OR REPLACE FUNCTION ranking_usuarios_lucro()
RETURNS TABLE(nome_usuario VARCHAR, lucro_total FLOAT) AS $$
BEGIN
    RETURN QUERY
    SELECT u.nome_usuario, SUM((v.valor_unitario_venda - c.valor_unitario_compra) * v.quantidade_venda) AS lucro_total
    FROM usuario u
    JOIN venda v ON u.id_usuario = v.id_usuario
    JOIN compra c ON v.ticker_ativo = c.ticker_ativo AND v.id_usuario = c.id_usuario
    GROUP BY u.nome_usuario
    ORDER BY lucro_total DESC;
END;
$$ LANGUAGE plpgsql;
