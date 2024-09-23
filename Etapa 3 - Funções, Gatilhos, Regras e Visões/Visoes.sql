-- Visão 1: Histórico de Preços de Ações com Nome do Ativo
CREATE VIEW vw_historico_precos AS
SELECT h.ticker_ativo, a.nome_ativo, h.data_historico, h.abertura_historico, h.alto_historico, h.baixo_historico, h.fechamento_historico, h.volume_historico
FROM historico h
JOIN ativo a ON h.ticker_ativo = a.ticker_ativo;

-- Visão 2: Saldo de Ações dos Usuários
CREATE VIEW vw_saldo_acoes AS
SELECT u.nome_usuario, a.nome_ativo, SUM(c.quantidade_compra) - COALESCE(SUM(v.quantidade_venda), 0) AS saldo
FROM usuario u
JOIN compra c ON u.id_usuario = c.id_usuario
LEFT JOIN venda v ON u.id_usuario = v.id_usuario AND c.ticker_ativo = v.ticker_ativo
JOIN ativo a ON c.ticker_ativo = a.ticker_ativo
GROUP BY u.nome_usuario, a.nome_ativo;

-- Visão 3: Lucro ou Prejuízo das Vendas
CREATE VIEW vw_lucro_prejuizo AS
SELECT u.nome_usuario, a.nome_ativo, SUM((v.valor_unitario_venda - c.valor_unitario_compra) * v.quantidade_venda) AS lucro_prejuizo
FROM venda v
JOIN compra c ON v.id_usuario = c.id_usuario AND v.ticker_ativo = c.ticker_ativo
JOIN usuario u ON v.id_usuario = u.id_usuario
JOIN ativo a ON v.ticker_ativo = a.ticker_ativo
GROUP BY u.nome_usuario, a.nome_ativo;

-- Visão 4: Transações de Compras e Vendas de um Usuário
CREATE VIEW vw_transacoes_usuario AS
SELECT u.nome_usuario, a.nome_ativo, 'Compra' AS tipo_transacao, c.data_compra AS data_transacao, c.quantidade_compra AS quantidade, c.valor_unitario_compra AS valor_unitario
FROM compra c
JOIN usuario u ON c.id_usuario = u.id_usuario
JOIN ativo a ON c.ticker_ativo = a.ticker_ativo
UNION ALL
SELECT u.nome_usuario, a.nome_ativo, 'Venda' AS tipo_transacao, v.data_venda AS data_transacao, v.quantidade_venda AS quantidade, v.valor_unitario_venda AS valor_unitario
FROM venda v
JOIN usuario u ON v.id_usuario = u.id_usuario
JOIN ativo a ON v.ticker_ativo = a.ticker_ativo;

-- Visão 5: Ranking dos Ativos Mais Negociados
CREATE VIEW vw_ranking_ativos AS
SELECT a.nome_ativo, SUM(c.quantidade_compra + COALESCE(v.quantidade_venda, 0)) AS total_negociado
FROM ativo a
LEFT JOIN compra c ON a.ticker_ativo = c.ticker_ativo
LEFT JOIN venda v ON a.ticker_ativo = v.ticker_ativo
GROUP BY a.nome_ativo
ORDER BY total_negociado DESC;
