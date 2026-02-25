<?php

use App\Http\Controllers\Api\V1\BlogPostController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\SponsorController;
use App\Http\Controllers\Api\V1\SupportCampaignController;
use App\Http\Controllers\Api\V1\TransparencyDocumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes v1
Route::prefix('v1')->group(function () {
    // Pages
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    
    // Blog
    Route::get('/blog', [BlogPostController::class, 'index']);
    Route::get('/blog/{slug}', [BlogPostController::class, 'show']);
    Route::post('/blog/{id}/comments', [BlogPostController::class, 'storeComment']);
    
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::post('/products/{id}/inquiries', [ProductController::class, 'storeInquiry']);
    
    // Product Categories
    Route::get('/product-categories', [ProductCategoryController::class, 'index']);
    Route::get('/product-categories/{slug}', [ProductCategoryController::class, 'show']);
    
    // Sponsors
    Route::get('/sponsors', [SponsorController::class, 'index']);
    Route::get('/sponsors/{slug}', [SponsorController::class, 'show']);
    
    // Support Campaigns
    Route::get('/support-campaigns', [SupportCampaignController::class, 'index']);
    Route::get('/support-campaigns/{slug}', [SupportCampaignController::class, 'show']);
    
    // Transparency Documents
    Route::get('/transparency-documents', [TransparencyDocumentController::class, 'index']);
    Route::get('/transparency-documents/{slug}', [TransparencyDocumentController::class, 'show']);
    
    // Contact
    Route::post('/contact', [ContactController::class, 'store']);
    
    // Menus
    Route::get('/menus/{location}', [MenuController::class, 'show']);
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
});

