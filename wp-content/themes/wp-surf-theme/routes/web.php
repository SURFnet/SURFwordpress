<?php

use Illuminate\Support\Facades\Route;
use SURF\Controllers\ExampleController;

Route::get( '/custom/example', [ ExampleController::class, 'show' ] );
