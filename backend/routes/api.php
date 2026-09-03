<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

// No auth anywhere in this app. The one exception in spirit is the public
// gallery route below: it's not "protected", it's the opposite — the one
// endpoint meant to be shared with people who have no access to anything else.

Route::apiResource('clients', ClientController::class);

Route::apiResource('projects', ProjectController::class);

// Galleries are only ever created/listed through their parent project
// (shallow nesting — Laravel calls this "scoped" when combined with
// implicit model binding, but here it's just two plain routes).
Route::get('projects/{project}/galleries', [GalleryController::class, 'index']);
Route::post('projects/{project}/galleries', [GalleryController::class, 'store']);

// The public, unauthenticated share link a photographer sends to a client.
Route::get('public/galleries/{slug}', [GalleryController::class, 'showPublic']);
