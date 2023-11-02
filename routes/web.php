<?php

use App\Actions\Prompts\FirstPrompt4;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (FirstPrompt4 $firstPrompt4) {
    return response()->json($firstPrompt4->run('how many leafs are on a tree?'));
    // return $firstPrompt4->run('how many leafs are on a tree?');
    // return view('welcome');
});
