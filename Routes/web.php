<?php

use App\Http\Middleware\Authenticate;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Modules\GitEnvato\Http\Controllers\GitEnvatoController;

Route::prefix('dashboard')->group(function () {
    Route::middleware([
        SubstituteBindings::class,
        Authenticate::class,
    ])->group(function () {
        Route::get( '/gitenvato-repositories', [ GitEnvatoController::class, 'listRepositories' ] )->name( 'ns.dashboard.gitenvato-repositories' );
        Route::get( '/gitenvato-repositories/create', [ GitEnvatoController::class, 'showCreateForm' ] )->name( 'ns.dashboard.gitenvato-repositories-create' );
        Route::get( '/gitenvato-repositories/edit/{repository}', [ GitEnvatoController::class, 'showEditForm' ] )->name( 'ns.dashboard.gitenvato-repositories-edit' );
    });
});

Route::post( 'webhook/gitenvato', [ GitEnvatoController::class, 'webhook' ] )->name( 'ns.webhook.gitenvato' );