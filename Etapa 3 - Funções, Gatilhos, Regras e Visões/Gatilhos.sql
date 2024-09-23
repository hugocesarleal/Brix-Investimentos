-- Gatilho 1: Atualizar Saldo de Ações ao Registrar uma Venda
CREATE TRIGGER trigger_atualizar_saldo_venda
AFTER INSERT ON venda
FOR EACH ROW
EXECUTE FUNCTION atualizar_preco_medio(NEW.ticker_ativo);

-- Gatilho 2: Calcular e Registrar o Lucro de uma Venda
CREATE TRIGGER trigger_registrar_lucro_venda
AFTER INSERT ON venda
FOR EACH ROW
EXECUTE FUNCTION calcular_lucro_prejuizo(NEW.id_venda);

-- Gatilho 3: Impedir Venda se o Saldo de Ações for Insuficiente
CREATE TRIGGER trigger_verificar_saldo_antes_venda
BEFORE INSERT ON venda
FOR EACH ROW
EXECUTE FUNCTION verificar_saldo_acoes(NEW.id_usuario, NEW.ticker_ativo, NEW.quantidade_venda);

-- Gatilho 4: Atualizar Histórico de Preços Após uma Venda
CREATE TRIGGER trigger_atualizar_historico_preco
AFTER INSERT ON venda
FOR EACH ROW
EXECUTE FUNCTION atualizar_historico_apos_venda(NEW.ticker_ativo, NEW.data_venda);

-- Gatilho 5: Registrar Transações em um Log
CREATE TRIGGER trigger_registrar_log_transacao
AFTER INSERT ON compra OR INSERT ON venda
FOR EACH ROW
EXECUTE FUNCTION log_transacoes();
