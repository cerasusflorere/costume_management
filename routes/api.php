<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 区分一覧取得
Route::get('/informations/sections', 'App\Http\Controllers\InformationController@index_section')->name('imformation.index_section');

// 区分詳細取得
Route::get('/informations/sections/{id}', 'App\Http\Controllers\InformationController@show_section')->name('imformation.show_section');

// 区分投稿
Route::post('/informations/sections', 'App\Http\Controllers\InformationController@store_section')->name('imformation.store_section');

// 区分更新
Route::post('/informations/sections/{id}', 'App\Http\Controllers\InformationController@update_section')->name('imformation.update_section');

// 区分削除
Route::delete('/informations/sections/{id}', 'App\Http\Controllers\InformationController@destroy_section')->name('imformation.destroy_section');

// 登場人物一覧取得
Route::get('/informations/characters', 'App\Http\Controllers\InformationController@index_character')->name('imformation.index_character');

// 登場人物詳細取得
Route::get('/informations/characters/{id}', 'App\Http\Controllers\InformationController@show_character')->name('imformation.show_character');

// 登場人物投稿
Route::post('/informations/characters', 'App\Http\Controllers\InformationController@store_character')->name('imformation.store_character');

// 登場人物更新
Route::post('/informations/characters/{id}', 'App\Http\Controllers\InformationController@update_character')->name('imformation.update_character');

// 登場人物削除
Route::delete('/informations/characters/{id}', 'App\Http\Controllers\InformationController@destroy_character')->name('imformation.destroy_character');

// 持ち主一覧取得
Route::get('/informations/owners', 'App\Http\Controllers\InformationController@index_owner')->name('information.index_owner');

// 持ち主詳細取得
Route::get('/informations/owners/{id}', 'App\Http\Controllers\InformationController@show_owner')->name('imformation.show_owner');

// 持ち主登録
Route::post('/informations/owners', 'App\Http\Controllers\InformationController@store_owner')->name('information.store_owner');

// 持ち主更新
Route::post('/informations/owners/{id}', 'App\Http\Controllers\InformationController@update_owner')->name('information.update_owner');

// 持ち主削除
Route::delete('/informations/owners/{id}', 'App\Http\Controllers\InformationController@destroy_owner')->name('imformation.destroy_owner');

// 衣装分類一覧取得
Route::get('/informations/classes', 'App\Http\Controllers\InformationController@index_class')->name('information.index_class');

// 持ち主詳細取得
Route::get('/informations/classes/{id}', 'App\Http\Controllers\InformationController@show_class')->name('imformation.show_class');

// 衣装分類登録
Route::post('/informations/classes', 'App\Http\Controllers\InformationController@store_class')->name('information.store_class');

// 衣装分類更新
Route::post('/informations/classes/{id}', 'App\Http\Controllers\InformationController@update_class')->name('information.update_class');

// 衣装分類削除
Route::delete('/informations/classes/{id}', 'App\Http\Controllers\InformationController@destroy_class')->name('imformation.destroy_class');

// 小道具一覧取得
Route::get('/costumes', 'App\Http\Controllers\CostumeController@index')->name('costume.index');

// 小道具一覧詳細込み取得
Route::get('/costumes_all', 'App\Http\Controllers\CostumeController@index_all')->name('costume.index_all');

// 小道具詳細取得
Route::get('/costumes/{id}', 'App\Http\Controllers\CostumeController@show')->name('costume.show');

// 小道具投稿
Route::post('/costumes', 'App\Http\Controllers\CostumeController@store')->name('costume.store');

// 小道具更新
Route::post('/costumes/{id}', 'App\Http\Controllers\CostumeController@update')->name('costume.update');

// 小道具更新
Route::post('/costumes_deep/{id}', 'App\Http\Controllers\CostumeController@update_deep')->name('costume.update_deep');

// 小道具削除
Route::delete('/costumes/{id}', 'App\Http\Controllers\CostumeController@destroy')->name('costume.destroy');

// 小道具一覧ダウンロード
Route::post('/costumes_list', 'App\Http\Controllers\CostumeController@down')->name('costume.down');

// 小道具メモ投稿
Route::post('/costume_comments', 'App\Http\Controllers\Costume_CommentController@store')->name('costume_comment.store');

// 小道具メモ更新
Route::post('/costume_comments/{id}', 'App\Http\Controllers\Costume_CommentController@update')->name('costume_comments.update');

// 小道具メモ削除
Route::delete('/costume_comments/{id}', 'App\Http\Controllers\Costume_CommentController@destroy')->name('costume_comment.destroy');

// 使用シーン一覧取得
Route::get('/scenes', 'App\Http\Controllers\SceneController@index')->name('scene.index');

// 使用シーン詳細取得
Route::get('/scenes/{id}', 'App\Http\Controllers\SceneController@show')->name('scene.show');

// 使用シーン投稿
Route::post('/scenes', 'App\Http\Controllers\SceneController@store')->name('scene.store');

// 使用シーン更新
Route::post('/scenes/{id}', 'App\Http\Controllers\SceneController@update')->name('scene.update');

// 使用シーン削除
Route::delete('/scenes/{id}', 'App\Http\Controllers\SceneController@destroy')->name('scene.destroy');

// 使用シーンメモ投稿
Route::post('/scene_comments', 'App\Http\Controllers\Scene_CommentController@store')->name('scene_comment.store');

// 使用シーンメモ更新
Route::post('/scene_comments/{id}', 'App\Http\Controllers\Scene_CommentController@update')->name('scene_comments.update');

// 使用シーンメモ削除
Route::delete('/scene_comments/{id}', 'App\Http\Controllers\Scene_CommentController@destroy')->name('scene_comment.destroy');