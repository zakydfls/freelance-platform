<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectApplicantController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectToolController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\TransactionWalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:withdraw wallet')->group(function () {
        Route::get('/dashboard/wallet', [DashboardController::class, 'wallet'])
            ->name('dashboard.wallet');
        Route::get('/dashboard/wallet/withdraw', [DashboardController::class, 'withdraw_wallet'])
            ->name('dashboard.wallet.withdraw');
        Route::post('/dashboard/wallet/withdraw/store', [DashboardController::class, 'withdraw_wallet_store'])
            ->name('dashboard.wallet.withdraw.store');
    });

    Route::middleware('can:topup wallet')->group(function () {
        Route::get('/dashboard/wallet/topup', [DashboardController::class, 'topup_wallet'])
            ->name('dashboard.wallet.topup');
        Route::post('/dashboard/wallet/topup/store', [DashboardController::class, 'topup_wallet_store'])
            ->name('dashboard.wallet.topup.store');
    });

    Route::middleware('can:apply job')->group(function () {
        Route::get('/apply/{project:slug}', [FrontController::class, 'apply_job'])
            ->name('front.apply_job');
        Route::post('/apply/{project:slug}/submit', [FrontController::class, 'apply_job_store'])
            ->name('front.apply_job.store');
        Route::get('/dashboard/proposals', [DashboardController::class, 'proposals'])
            ->name('dashboard.proposals');
        Route::get('/dashboard/proposals_detail/{project}/{projectApplicant}', [DashboardController::class, 'proposals_detail'])
            ->name('dashboard.proposals_detail');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('can:manage_wallet')->group(function () {
            Route::get('/wallet/topups', [TransactionWalletController::class, 'wallet_topups'])
                ->name('topups');
            Route::get('/wallet/withdraws', [TransactionWalletController::class, 'wallet_withdraws'])
                ->name('withdraws');
            Route::resource('wallet_transactions', TransactionWalletController::class);
        });

        Route::middleware('can:manage applicants')->group(function () {
            Route::resource('project_applicants', ProjectApplicantController::class);
        });

        Route::middleware('can:manage projects')->group(function () {
            Route::resource('projects', ProjectController::class);

            Route::post('/project/{projectApplicant}/completed', [ProjectController::class, 'completed'])
                ->name('complete_project.store');
            Route::get('/project/{project}/tools', [ProjectController::class, 'tools'])
                ->name('projects.tools');
            Route::post('/project/{project}/tools/store', [ProjectController::class, 'tools_store'])
                ->name('projects.tools.store');
            Route::resource('project_tools', ProjectToolController::class);
        });

        Route::middleware('can:manage categories')->group(function () {
            Route::resource('categories', CategoryController::class);
        });

        Route::middleware('can:manage tools')->group(function () {
            Route::resource('tools', ToolController::class);
        });
    });
});

require __DIR__ . '/auth.php';
