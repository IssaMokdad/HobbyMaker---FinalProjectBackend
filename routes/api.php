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
    Route::post('save-post', 'SavedPostController@savePost');
    Route::get('get-saved-posts', 'SavedPostController@getSavedPost');
    Route::post('unsave-post', 'SavedPostController@unsavePost');
    Route::post('post/create', 'PostController@add');
    Route::get('post/get-one-post', 'PostController@getOnePost');
    Route::get('post/get', 'PostController@get');
    Route::get('post/get-user-post', 'PostController@getUserPosts');
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
    Route::post('comment/edit', 'CommentsController@edit');
});

//---------------------Users Api Routes-----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('users/get-recommendations-anywhere', 'UserController@getUsersWithSameHobbyAnywhere');
    Route::post('user/first-time-login', 'UserController@setFirstTimeLoginToFalse');
    Route::post('user/save-geometry-position', 'UserController@saveGeometryPosition');
    Route::get('user/get-info', 'UserController@getUserInfo');
    Route::post('user/password-change', 'UserController@changePassword');
    Route::post('user/save-bio', 'UserController@saveBio');
    Route::post('user/info-change', 'UserController@editUserInfo');
    Route::get('users/get-recommendations', 'UserController@getUsersWithSameHobbyAndAddress');
    Route::get('user/get-info', 'UserController@getUserInfo');
    Route::post('user/save-profile-picture', 'UserController@saveProfilePicture');
    Route::post('user/save-cover-picture', 'UserController@saveCoverPicture');
});

//---------------------Friends Api Routes----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('friend/get-friends', 'FriendController@getFriends');
    Route::get('/search', 'FriendController@search');
    Route::post('friend/add', 'FriendController@add');
    Route::post('friend/accept', 'FriendController@accept');
    Route::post('friend/remove', 'FriendController@removeFriend');
    
    Route::get('friend/get-friend-requests', 'FriendController@getFriendRequests');
    Route::get('friend/get-pending-requests', 'FriendController@getPendingRequests');
    
    
});

//---------------------Notifications Api Routes----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('get-user-notifications', 'UserNotificationsController@showNotifications');
    Route::post('mark-as-read', 'UserNotificationsController@markNotificationsAsRead');
});

//---------------------Chat Api Routes----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('get-message-notifications', 'ChatController@getAllUnreadMessagesForNotifications');
    Route::get('get-messages', 'ChatController@getMessages');
    // Route::get('get-last-message', 'ChatController@getLastMessage');
    Route::post('mark-messages-as-read', 'ChatController@markMessagesAsRead');
    Route::post('mark-messages-as-read-per-user', 'ChatController@markMessagesAsRead');
    Route::get('get-unread-messages', 'ChatController@getAllUnreadMessages');
    Route::post('send-message', 'ChatController@sendMessage');
});

//---------------------Youtube Videos Api Routes----------------------------------------------

Route::group([
    'middleware' => 'auth:api'
], function() {

    Route::post('save-video', 'YoutubeVideosController@saveVideo');
    Route::post('unsave-video', 'YoutubeVideosController@unsaveVideo');

});

//---------------------Events Api Routes----------------------------------------------
Route::post('get-public-events', 'EventsController@getPublicEvents');
Route::group([
    'middleware' => 'auth:api'
], function() {

    Route::get('get-user-events', 'EventsController@getUserEvents');
    
    Route::post('event-create', 'EventsController@createEvent');
    Route::post('event-edit', 'EventsController@editEvent');
    Route::post('event-delete', 'EventsController@deleteEvent');
});


//---------------------Going Api Routes----------------------------------------------
Route::post('join-event', 'GoingController@joinPublicEvent');
Route::group([
    'middleware' => 'auth:api'
], function() {
   
    Route::get('get-user-events-going-to', 'GoingController@getGoingToEvents');
    Route::post('accept-event-invitation', 'GoingController@acceptInvitation');
    Route::post('refuse-event-invitation', 'GoingController@refuseInvitation');
    Route::get('get-user-events-invitations', 'GoingController@getInvitations');
    Route::post('invite-friend', 'GoingController@invite');
    Route::get('not-invited-friends', 'GoingController@getPeopleNotInvitedToEvent');
});