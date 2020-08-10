<?php

use Illuminate\Http\Request;

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

// Authentication Routes
Route::post('/login', 'Api\Auth\LoginController@login');

Route::post('/signup', 'Api\Auth\SignUpController@register');

Route::post('/logout', 'Api\Auth\SignUpController@logout');

// Refresh token route
Route::get('/refresh', 'Api\Auth\LoginController@refresh');




// Routes for CRUD operations with User Model
Route::get('/users', [
    'uses' => 'UserController@index',
    'middleware' => 'jwt.auth'
]);

// To get list of teachers
Route::get('/roles', [
    'uses' => 'UserController@roles',
    'middleware' => 'jwt.auth']);

Route::post('/users/create', [
    'uses' => 'UserController@store',
    'middleware' => 'jwt.auth']);

Route::get('/users/{user}', [
    'uses' => 'UserController@show',
    'middleware' => 'jwt.auth']);

Route::put('/users/{user}', [
    'uses' => 'UserController@update',
    'middleware' => 'jwt.auth']);

Route::delete('/users/{user}', [
    'uses' => 'UserController@destroy',
    'middleware' => 'jwt.auth']);


// Assign to course
Route::post('/courses/assign', [
    'uses' => 'AssignmentController@store',
    'middleware' => 'jwt.auth']);

Route::get('/courses/{course}/assignments', [
            'uses' => 'AssignmentController@course_assignments',
            'middleware' => 'jwt.auth']);

Route::get('/users/{user}/assignments', [
            'uses' => 'AssignmentController@user_assignments',
            'middleware' => 'jwt.auth']);



// Routes for CRUD operations with Course Model
Route::get('/courses', [
    'uses' => 'CourseController@index',
    'middleware' => 'jwt.auth']);
// To get list of teachers
Route::get('/teachers', [
    'uses' => 'CourseController@teachers',
    'middleware' => 'jwt.auth']);

Route::post('/courses/create', [
    'uses' => 'CourseController@store',
    'middleware' => 'jwt.auth']);

Route::get('/courses/{course}', [
    'uses' => 'CourseController@show',
    'middleware' => 'jwt.auth']);

Route::put('/courses/{course}', [
    'uses' => 'CourseController@update',
    'middleware' => 'jwt.auth']);

Route::delete('/courses/{course}', [
    'uses' => 'CourseController@destroy',
    'middleware' => 'jwt.auth']);


Route::get('/authUser', [
    'uses' => 'Api\Auth\LoginController@getAuthUser',
    'middleware' => 'jwt.auth'
]);


// Get list of roles
Route::get('/roles', [
    'uses' => 'RoleController@index',
    'middleware' => 'jwt.auth'
]);




