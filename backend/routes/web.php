<?php

use Illuminate\Support\Facades\Route;

// This is an API-only app — the Vue frontend is a separate app that talks
// to routes/api.php. This route just confirms the backend is alive.
Route::get('/', function () {
    return response()->json(['message' => 'Photography API is running.']);
});
