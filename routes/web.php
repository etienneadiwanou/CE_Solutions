<?php

use Illuminate\Support\Facades\Route;

// Toutes les URLs → Vue Router gère la navigation côté client
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');