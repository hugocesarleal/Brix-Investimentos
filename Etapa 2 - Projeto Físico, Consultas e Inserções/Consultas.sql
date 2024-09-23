-- Seleciona todos os ativos com seus respectivos nomes de setor e indústria
SELECT a.ticker_ativo, a.nome_ativo, s.nome_setor, i.nome_industria
FROM ativo a
JOIN setor s ON a.id_setor = s.id_setor
JOIN industria i ON a.id_industria = i.id_industria;

-- Seleciona o histórico completo de um ativo específico, ordenado por data
SELECT h.*
FROM historico h
WHERE h.ticker_ativo = 'TICKER'
ORDER BY h.data_historico;

-- Calcula a média dos preços de abertura e fechamento de um ativo em um período específico
SELECT 
    h.ticker_ativo,
    AVG(h.abertura_historico) AS media_abertura,
    AVG(h.fechamento_historico) AS media_fechamento
FROM historico h
WHERE h.ticker_ativo = 'TICKER'
  AND h.data_historico BETWEEN 'DATA_INICIO' AND 'DATA_FINAL'
GROUP BY h.ticker_ativo;

-- Calcula o valor total das compras e vendas feitas por cada usuário
SELECT 
    u.nome_usuario,
    u.email_usuario,
    SUM(cv.valor_total_compra) AS total_compras,
    SUM(vv.valor_total_venda) AS total_vendas
FROM usuario u
LEFT JOIN compra_valor_total cv ON u.id_usuario = cv.id_usuario
LEFT JOIN venda_valor_total vv ON u.id_usuario = vv.id_usuario
GROUP BY u.nome_usuario, u.email_usuario;

-- Seleciona os 5 ativos mais negociados por volume em um período específico
SELECT 
    h.ticker_ativo,
    SUM(h.volume_historico) AS total_volume
FROM historico h
WHERE h.data_historico BETWEEN 'DATA_INICIO' AND 'DATA_FINAL'
GROUP BY h.ticker_ativo
ORDER BY total_volume DESC
LIMIT 5;

-- Lista os usuários com o número total de compras e vendas realizadas
SELECT 
    u.nome_usuario,
    COUNT(DISTINCT c.id_compra) AS total_compras,
    COUNT(DISTINCT v.id_venda) AS total_vendas
FROM usuario u
LEFT JOIN compra c ON u.id_usuario = c.id_usuario
LEFT JOIN venda v ON u.id_usuario = v.id_usuario
GROUP BY u.nome_usuario;

-- Calcula os preços médios, mínimos e máximos de fechamento para cada ativo
SELECT 
    h.ticker_ativo,
    AVG(h.fechamento_historico) AS media_preco,
    MIN(h.fechamento_historico) AS minimo_preco,
    MAX(h.fechamento_historico) AS maximo_preco
FROM historico h
GROUP BY h.ticker_ativo;

-- Seleciona os setores com a maior média de desempenho dos ativos, medida pelos preços de fechamento
SELECT 
    s.nome_setor,
    AVG(h.fechamento_historico) AS media_performance
FROM historico h
JOIN ativo a ON h.ticker_ativo = a.ticker_ativo
JOIN setor s ON a.id_setor = s.id_setor
GROUP BY s.nome_setor
ORDER BY media_performance DESC;
