<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RelatorioDiversoController;

// Grupo de rotas com prefixo 'v1' e middleware de autenticação JWT
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');

    // API Resources para CRUDs
    Route::apiResource('cliente', 'App\Http\Controllers\ClienteController');
    Route::apiResource('cadastrar-ativos', 'App\Http\Controllers\CadastrarAtivoController');
    Route::apiResource('compras', 'App\Http\Controllers\ComprasController');
    Route::apiResource('vendas', 'App\Http\Controllers\VendasController');
    Route::get('relatorio-diversos', [RelatorioDiversoController::class, 'index']);
    // Corrigir rota para obter o histórico de preços de ações
    Route::post('get-stock-history', [StockController::class, 'getStockHistory']);
    Route::get('get-stock-history', [StockController::class, 'getStockHistory']);
});

// Rotas públicas para registro e login
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');
