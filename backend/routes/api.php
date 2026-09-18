<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\GamesController;
use App\Http\Controllers\Api\PlayersController;
use App\Http\Controllers\Api\StaffMemberController;
use App\Http\Controllers\Api\ClubDocumentController;

use App\Http\Controllers\Api\Shop\ShopProductController;
use App\Http\Controllers\Api\Shop\ShopPricingController;
use App\Http\Controllers\Api\Shop\ShopOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| News
|--------------------------------------------------------------------------
*/

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Games
|--------------------------------------------------------------------------
*/

Route::get('/fixtures', [GamesController::class, 'fixtures']);
Route::get('/results', [GamesController::class, 'results']);
Route::get('/next-match', [GamesController::class, 'nextMatch']);

Route::get('/games', [GamesController::class, 'index']);
Route::get('/games/{id}', [GamesController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Players
|--------------------------------------------------------------------------
*/

Route::get('/players', [PlayersController::class, 'index']);
Route::get('/players/{player}', [PlayersController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::get('/staff', [StaffMemberController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Club Documents
|--------------------------------------------------------------------------
*/

Route::get('/club-documents', [ClubDocumentController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Webshop
|--------------------------------------------------------------------------
|
| Public API used by the FK Radnik webshop frontend.
|
| Products:
|   GET  /api/shop/products
|   GET  /api/shop/products/{slug}
|
| Pricing:
|   POST /api/shop/pricing
|
| Orders:
|   POST /api/shop/orders
|
*/

Route::prefix('shop')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::get('/products', [
        ShopProductController::class,
        'index',
    ]);

    Route::get('/products/{slug}', [
        ShopProductController::class,
        'show',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    |
    | Calculates:
    | - subtotal
    | - season ticket discount
    | - promo voucher discount
    | - shipping
    | - final total
    |
    */

    Route::post('/pricing', [
        ShopPricingController::class,
        'calculate',
    ])->middleware('throttle:60,1');

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    |
    | Creates the order and reserves product stock.
    |
    */

    Route::post('/orders', [
        ShopOrderController::class,
        'store',
    ])->middleware('throttle:10,1');
});
