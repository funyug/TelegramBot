<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('get-me', 'TelegramController@getMe');
Route::get('set-hook', 'TelegramController@setWebHook');
Route::get('remove-hook', 'TelegramController@removeWebHook');
Route::post(env('TELEGRAM_BOT_TOKEN') . '/webhook', 'TelegramController@handleRequest');
