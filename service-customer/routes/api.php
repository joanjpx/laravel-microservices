<?php
use Illuminate\Support\Facades\Route;


// Customers Route
Route::get('/customers', function () {
    return response()->json([
        'customers' => [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith']
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
