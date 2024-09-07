<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;


Route::controller(TodoController::class)->group( function(){
    Route::any('todo/{type}/{id?}',  'Todo')->name('todo');
});