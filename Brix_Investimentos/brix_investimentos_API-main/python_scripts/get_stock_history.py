import mysql.connector
from mysql.connector import Error
import yfinance as yf
import json
from datetime import datetime, timedelta
import pytz

# Configuração do banco de dados
config = {
    'user': 'root',
    'password': '10182016Ab@',
    'host': '127.0.0.1',
    'database': 'novo'
}

# Função para obter dados históricos de ações usando yfinance
def obter_dados_historicos_minuto(ticker):
    timezone = pytz.timezone('America/Sao_Paulo')
    agora = datetime.now(timezone)
    
    # Calcula o início e o fim do dia de negociação
    inicio_do_dia = agora.replace(hour=10, minute=30, second=0, microsecond=0)
    fim_do_dia = agora.replace(hour=17, minute=0, second=0, microsecond=0)

    inicio = inicio_do_dia
    fim = fim_do_dia

    ativo = yf.Ticker(ticker)
    hist = ativo.history(start=inicio, end=fim, interval='1m')  # Intervalo de 1 minuto

    dados_historicos = []
    for index, row in hist.iterrows():
        dados_historicos.append({
            'ticker': ticker,
            'data_ativo': index.to_pydatetime(),
            'open': row['Open'],
            'low': row['Low'],
            'high': row['High'],
            'close': row['Close'],
            'volume': row['Volume']
        })
    
    return dados_historicos

# Função para inserir dados no banco de dados
def inserir_dados(connection, dados):
    cursor = connection.cursor()
    cursor.execute("START TRANSACTION")
    for dado in dados:
        cursor.execute("""
        INSERT INTO historico_preco_ativos (ticker, data_ativo, open, low, high, close, volume)
        VALUES (%s, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE open=%s, low=%s, high=%s, close=%s, volume=%s
        """, (
            dado['ticker'], dado['data_ativo'], dado['open'], dado['low'], 
            dado['high'], dado['close'], dado['volume'], 
            dado['open'], dado['low'], dado['high'], dado['close'], dado['volume']
        ))
    connection.commit()

# Lista de tickers para obter dados
tickers = [
    'AAPL', 'GOOGL', 'MSFT', 'AMZN', 'META', 'TSLA', 'NFLX', 'IBM', 
    'ORCL', 'BA', 'NVDA', 'INTC', 'CSCO', 'WMT', 'DIS', 'BABA', 
    'PFE', 'MRK', 'XOM', 'CVX'
]

# Conectar ao banco de dados e inserir os dados
connection = None

try:
    connection = mysql.connector.connect(**config)
    if connection.is_connected():
        dados_historicos = []
        for ticker in tickers:
            dados_historicos.extend(obter_dados_historicos_minuto(ticker))

        inserir_dados(connection, dados_historicos)
        
        # Imprimir os dados gerados em formato JSON
        print(json.dumps(dados_historicos, default=str))

except Error as e:
    print("Erro ao conectar ao MySQL", e)
finally:
    if connection is not None and connection.is_connected():
        connection.close()
