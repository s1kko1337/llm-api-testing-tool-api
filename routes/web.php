<?php

use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    return response((
        date('D M Y')
    ));
});
