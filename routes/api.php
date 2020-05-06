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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__ . '/auth/auth.php';
require __DIR__ . '/auth/passwordReset.php';

//--------------------Posts Api Routes-----------------------------------------
Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('post/create', 'PostController@add');
    Route::get('post/get', 'PostController@get');
    Route::post('post/delete', 'PostController@delete');
    Route::post('post/edit', 'PostController@edit');
});

//--------------------Likes Api Routes-----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('like/create', 'LikesController@add');
    Route::post('like/remove', 'LikesController@remove');
});

//--------------------Comments Api Routes---------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('comment/create', 'CommentsController@create');
    Route::get('comment/show', 'CommentsController@show');
    Route::post('comment/remove', 'CommentsController@remove');
});

//---------------------Users Api Routes-----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('users/get-recommendations', 'UserController@getUsersWithSameHobbyAndAddress');
    Route::get('user/get-info', 'UserController@getUserInfo');
});

//---------------------Friends Api Routes----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('friend/add', 'FriendController@add');
    Route::get('user/get-info', 'UserController@getUserInfo');
});