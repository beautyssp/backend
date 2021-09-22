<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Subcategories\SubcategoriesController;
use App\Http\Controllers\Warehouses\WarehousesController;
use App\Http\Controllers\Categories\CategoriesController;
use App\Http\Controllers\Suppliers\SuppliersController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Bills\BillsController;
use App\Http\Controllers\Scaner\ScanerController;
use App\Models\Warehouses;
use App\Models\Products;
use App\Models\Supplier;
use App\Models\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/getUser', function(Request $request){
        return response()->json([
            'user' => $request->user()
        ]);
    });

    Route::get('/desktop', function(){
        return response()->json([
            'data' => [
                'warehouses' => count(Warehouses::all()),
                'suppliers' => count(Supplier::all()),
                'products' => count(Products::all()),
                'clients' => count(Client::all()),
            ]
        ]);
    });
    
    Route::prefix('/warehouses')->group(function () {
        Route::delete('/{id}', [WarehousesController::class, 'delete'])->name('deleteWarehouse');
        Route::get('', [WarehousesController::class, 'index'])->name('listWarehouse');
        Route::post('/find', [WarehousesController::class, 'search'])->name('findWarehouse');
        Route::post('/create', [WarehousesController::class, 'create'])->name('addWarehouse');
    });
    
    Route::prefix('/suppliers')->group(function () {
        Route::delete('/{id}', [SuppliersController::class, 'delete'])->name('deleteSupplier');
        Route::get('', [SuppliersController::class, 'index'])->name('listSupplier');
        Route::post('/find', [SuppliersController::class, 'search'])->name('findSupplier');
        Route::post('/create', [SuppliersController::class, 'create'])->name('addSupplier');
    });
    
    Route::prefix('/clients')->group(function () {
        Route::delete('/{id}', [ClientsController::class, 'delete'])->name('deleteClient');
        Route::get('', [ClientsController::class, 'index'])->name('listClient');
        Route::post('/find', [ClientsController::class, 'search'])->name('findClient');
        Route::post('/create', [ClientsController::class, 'create'])->name('addClient');
    });
    
    Route::prefix('/categories')->group(function () {
        Route::delete('/{id}', [CategoriesController::class, 'delete'])->name('deleteCategory');
        Route::get('', [CategoriesController::class, 'index'])->name('listCategory');
        Route::post('/find', [CategoriesController::class, 'search'])->name('findCategory');
        Route::post('/create', [CategoriesController::class, 'create'])->name('addCategory');
    });
    
    Route::prefix('/subcategories')->group(function () {
        Route::delete('/{id}', [SubcategoriesController::class, 'delete'])->name('deleteSubcategory');
        Route::get('', [SubcategoriesController::class, 'index'])->name('listSubcategory');
        Route::post('/find', [SubcategoriesController::class, 'search'])->name('findSubcategory');
        Route::post('/create', [SubcategoriesController::class, 'create'])->name('addSubcategory');
    });
    
    Route::prefix('/products')->group(function () {
        Route::post('/masive', [ProductsController::class, 'masive'])->name('masiveTemplate');
        Route::get('', [ProductsController::class, 'index'])->name('listProducts');
        Route::post('/find', [ProductsController::class, 'search'])->name('findProduct');
        Route::post('/create', [ProductsController::class, 'create'])->name('addProducts');
        Route::post('/changeWarehouse', [ProductsController::class, 'changeWarehouse'])->name('changeWarehouse');
        Route::delete('/{id}', [ProductsController::class, 'delete'])->name('deleteProducts');
    });

    Route::prefix('/bills')->group(function () {
        Route::post('/create', [BillsController::class, 'create'])->name('addBill');
    });

    Route::post('/scaner/register', [ScanerController::class, 'register'])->name('registerScan');
});

Route::get('/barcode/{id}', [ProductsController::class, 'barcode'])->name('getBarcode');

