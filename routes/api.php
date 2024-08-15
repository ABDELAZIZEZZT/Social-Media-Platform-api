<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\blogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function (Request $request) {
    return response()->json(User::all());
});

Route::middleware(['guest'])->group(function () {
    Route::post('register',[AuthController::class,'registration']);
    Route::post('login',[AuthController::class,'login'])->name('login');
});
Route::group(['middleware' => ['verifyToken']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('/blogs', [BlogController::class, 'index']);


Route::middleware('auth:api')->group(function () {
    Route::get('/myblogs', [BlogController::class,'show']);
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::PUT('/blogs/update/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    Route::get('/oneblog/forupdate/{id}', [BlogController::class, 'oneBlog']);


    Route::get('/user', function (Request $request) { return $request->user();});
    Route::put('/user/{id}', [AuthController::class, 'update']);
});

Route::get('/', function () {
    return response()->json(['error' => 'please login'], 401);
})->name('unauth');


