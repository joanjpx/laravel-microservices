<?php
use Illuminate\Support\Facades\Route;

Route::get('/products', function () {
    return response()->json([
        'products' => [
            ['id' => 101, 'name' => 'Laptop', 'price' => 999.99],
            ['id' => 102, 'name' => 'Smartphone', 'price' => 499.99]
        ]
    ]);
});

Route::get('/info', function () {
    return response()->json([
        'app' => env('APP_NAME', 'Laravel'),
        'version' => '1.0.0',
        'description' => 'Product Service API'
    ]);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP'
    ]);
});
